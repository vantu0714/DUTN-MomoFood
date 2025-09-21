<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VNPayController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderCancellationItem;
use App\Models\OrderDetail;
use App\Models\OrderReturnAttachment;
use App\Models\OrderReturnItem;
use App\Models\Promotion;
use App\Models\PromotionUser;
use App\Models\User;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $selectedIds = [];

        if ($request->has('selected_items')) {
            $selectedIds = is_array($request->selected_items)
                ? $request->selected_items
                : explode(',', $request->selected_items);
            session()->put('selected_items', $selectedIds); // lưu lại
        } elseif (session()->has('selected_items')) {
            $selectedIds = session('selected_items');
        }

        // Lấy cart và lọc items
        $cart = Cart::with(['items.product', 'items.productVariant'])->where('user_id', $userId)->first();
        $cartItems = collect();
        $errors = [];

        if ($cart && $cart->items) {
            $items = !empty($selectedIds)
                ? $cart->items->whereIn('id', $selectedIds)
                : $cart->items;

            foreach ($items as $item) {
                $price = $item->discounted_price ?? $item->original_price ?? 0;
                $item->calculated_price = $price;
                $item->item_total = $price * $item->quantity;

                $stock = $item->productVariant
                    ? $item->productVariant->quantity_in_stock
                    : ($item->product->quantity_in_stock ?? 0);

                if ($stock <= 0) {
                    $errors[] = "Sản phẩm " . Str::lower($item->product->product_name) . " bạn chọn đã hết hàng";
                }
                // Gộp tên biến thể
                $item->variant_summary = $item->productVariant && is_array($item->productVariant->variant_values)
                    ? implode(', ', array_filter($item->productVariant->variant_values))
                    : null;

                $cartItems->push($item);
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        //Lấy danh sách tất cả địa chỉ của user
        $savedRecipients = Recipient::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        //Không dùng session: lấy địa chỉ từ request (nếu có), không thì lấy mặc định
        $recipient = null;
        if ($request->has('recipient_id')) {
            $recipient = $savedRecipients->where('id', $request->recipient_id)->first();
        }

        if (!$recipient) {
            $recipient = $savedRecipients->where('is_default', true)->first();
        }

        // Lấy các voucher đang hoạt động
        $vouchers = Promotion::where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        // Kiểm tra mã giảm giá đang lưu trong session
        $promotionCode = session('promotion_code');
        $discount = session('discount', 0);

        if ($promotionCode) {
            $promotion = Promotion::where('code', $promotionCode)
                ->where('status', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if (!$promotion || ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit)) {
                // Nếu mã đã bị xóa / hết hạn / vượt lượt dùng → clear session
                session()->forget(['promotion', 'promotion_code', 'discount']);
                $promotionCode = null;
                $discount = 0;
            }
        }

        //Đọc tree.json
        $json = file_get_contents(public_path('data/dist/tree.json'));
        $rawLocations = json_decode(file_get_contents(public_path('data/dist/tree.json')), true);

        // Chuyển đổi thành mảng chuẩn
        $locations = [];

        foreach ($rawLocations as $provinceCode => $province) {
            $districts = [];
            foreach ($province['quan-huyen'] ?? [] as $districtCode => $district) {
                $wards = [];
                foreach ($district['xa-phuong'] ?? [] as $wardCode => $ward) {
                    $wards[] = [
                        'code' => $ward['code'],
                        'name_with_type' => $ward['name_with_type'],
                    ];
                }
                $districts[] = [
                    'code' => $district['code'],
                    'name_with_type' => $district['name_with_type'],
                    'wards' => $wards,
                ];
            }

            $locations[] = [
                'code' => $province['code'],
                'name_with_type' => $province['name_with_type'],
                'districts' => $districts,
            ];
        }

        return view('clients.order', compact(
            'cart',
            'cartItems',
            'recipient',
            'savedRecipients',
            'vouchers',
            'locations',
            'promotionCode',
            'discount'
        ));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $cart = Cart::with('items.product', 'items.productVariant')->where('user_id', $userId)->firstOrFail();

        // Lấy selected_ids từ request hoặc session
        $selectedIds = [];
        $errors = [];

        if ($request->filled('selected_items')) {
            $selectedIds = is_array($request->selected_items)
                ? $request->selected_items
                : explode(',', $request->selected_items);

            session()->forget('selected_items');
            session()->put('selected_items', $selectedIds);
        } elseif (session()->has('selected_items')) {
            $selectedIds = session('selected_items');
        }

        $cartItems = !empty($selectedIds)
            ? $cart->items->whereIn('id', $selectedIds)
            : $cart->items;

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Không có sản phẩm nào được chọn.');
        }

        // Validate các trường cơ bản
        $request->validate([
            'shipping_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cod,vnpay',
            'note' => 'nullable|string',
            'promotion' => 'nullable|string',
            'selected_items' => 'nullable',
        ]);

        // Kiểm tra địa chỉ nhận hàng
        if ($request->filled('recipient_id')) {

            $request->validate([
                'recipient_id' => 'required|exists:recipients,id',
            ], [
                'recipient_id.required' => 'Vui lòng chọn địa chỉ nhận hàng.',
                'recipient_id.exists' => 'Địa chỉ nhận hàng không hợp lệ.',
            ]);
            $recipient = Recipient::where('user_id', $userId)->findOrFail($request->recipient_id);
        } else {
            $request->validate(
                [
                    'recipient_name' => 'required|string|max:255',
                    'recipient_phone' => 'required|string|max:15',
                    'recipient_address' => 'required|string|max:500',
                ],
                [
                    'recipient_name.required' => 'Vui lòng nhập họ tên người nhận.',
                    'recipient_phone.required' => 'Vui lòng nhập số điện thoại.',
                    'recipient_address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
                ]
            );
            $recipient = Recipient::create([
                'user_id' => $userId,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'note' => $request->note,
                'is_default' => false,
            ]);
        }

        foreach ($cartItems as $item) {
            $stock = $item->productVariant
                ? $item->productVariant->quantity_in_stock
                : ($item->product->quantity_in_stock ?? 0);

            if ($stock <= 0) {
                $errors[] = "Sản phẩm " . Str::lower($item->product->product_name) . " bạn chọn đã hết hàng";
            }
        }

        if (!empty($errors)) {
            return redirect()->route('carts.index')->withErrors($errors);
        }

        // Xử lý thanh toán
        if ($request->payment_method === 'vnpay') {
            $vnpay = new VNPayController();
            return $vnpay->create($request, $recipient);
        }

        DB::beginTransaction();

        try {
            // Tính tổng đơn hàng
            $total = $cartItems->sum(function ($item) {
                return $item->discounted_price * $item->quantity;
            });

            $discount = 0;
            $promotionCode = null;

            if ($request->filled('discount_amount')) {
                $discount = (float) $request->discount_amount;
            } else {
                // Xử lý mã giảm giá từ request hoặc session
                if ($request->filled('promotion') || session()->has('promotion_code')) {
                    $promotionCode = $request->filled('promotion')
                        ? trim($request->promotion)
                        : session('promotion_code');

                    $promotion = Promotion::where('code', $promotionCode)
                        ->where('status', 1)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();

                    if ($promotion && ($promotion->usage_limit === null || $promotion->used_count < $promotion->usage_limit)) {
                        if ($promotion->discount_type === 'percent') {
                            $discount = $total * ($promotion->discount_value / 100);
                            if ($promotion->max_discount_value !== null) {
                                $discount = min($discount, $promotion->max_discount_value);
                            }
                        } else { // fixed
                            $discount = (float) $promotion->discount_value;
                        }

                        // Không cho giảm vượt quá tổng tiền
                        $discount = min($discount, $total);

                        // Cập nhật lại session
                        session()->put('promotion_code', $promotion->code);
                        session()->put('discount', $discount);

                        // Cập nhật số lần dùng
                        $promotion->increment('used_count');
                        PromotionUser::updateOrCreate(
                            ['promotion_id' => $promotion->id, 'user_id' => $userId],
                            ['used_count' => DB::raw('used_count + 1')]
                        );
                    } else {
                        // Nếu mã không hợp lệ hoặc bị xóa thì clear session
                        session()->forget(['promotion', 'promotion_code', 'discount']);
                        $promotionCode = null;
                        $discount = 0;
                    }
                }
            }
            // dd($request->all());


            $grandTotal = $total + $request->shipping_fee - $discount;

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $userId,
                'recipient_id' => $recipient->id,
                'recipient_name' => $recipient->recipient_name,
                'recipient_phone' => $recipient->recipient_phone,
                'recipient_address' => $recipient->recipient_address,
                'note' => $request->note,
                'promotion' => $promotionCode,
                'discount_amount' => $discount,
                'shipping_fee' => $request->shipping_fee,
                'total_price' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'status' => 1,
            ]);

            // Thêm chi tiết đơn hàng
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->discounted_price,
                ]);


                $item->product->quantity_in_stock -= $item->quantity;
                $item->product->save();

                if (!is_null($item->productVariant)) {
                    $item->productVariant->quantity_in_stock -= $item->quantity;
                    $item->productVariant->save();
                }
            }

            // Cập nhật trạng thái VIP
            $totalSpent = Order::where('user_id', $userId)
                ->whereIn('status', [3, 4])
                ->sum('total_price');

            if ($totalSpent >= 5000000) {
                User::where('id', $userId)->update(['is_vip' => true]);
            }

            // Xóa sản phẩm đã đặt khỏi giỏ hàng
            $cart->items()->whereIn('id', $cartItems->pluck('id'))->delete();

            // Xóa session tạm
            session()->forget(['selected_items', 'promotion', 'promotion_code', 'discount']);

            DB::commit();

            return redirect()->route('carts.index')->with('orderSuccess', $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

    public function orderList(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Order::where('user_id', auth()->id())
            ->latest();

        if ($status !== 'all' && is_numeric($status)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(5);

        return view('clients.user.orders', compact('orders'));
    }

    public function createPayment(Request $request)
    {
        try {
            $paymentMethod = $request->payment_method;

            if ($paymentMethod === 'vnpay') {
                $vnpay = new VNPayController();
                return $vnpay->create($request);
            }

            // Trả về redirect nội bộ từ store()
            return $this->store($request);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
    }

    public function orderDetail($id)
    {
        if (session()->has('order')) {
            $order = session('order');
        } else {
            $order = Order::where('id', $id)
                ->where('user_id', auth()->id())
                ->with([
                    'orderDetails.product',
                    'orderDetails.productVariant',
                    'activeOrderDetails.product',
                    'activeOrderDetails.productVariant',
                    'cancelledOrderDetails.product',
                    'cancelledOrderDetails.productVariant',
                    'returnItems.orderDetail.product',
                    'returnItems.attachments',
                    'cancellation.items.orderDetail.product',
                    'cancellation.items.orderDetail.productVariant'
                ])
                ->firstOrFail();
        }

        // Tính tổng giá trị sản phẩm còn lại
        $subtotal = $order->activeOrderDetails->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Thêm logic kiểm tra thời gian hoàn hàng
        $canReturn = false;
        if ($order->status == 4 && $order->completed_at) {
            $returnDeadline = Carbon::parse($order->completed_at)->addHours(24);
            $canReturn = now()->lte($returnDeadline);
        }

        return view('clients.user.show-order', compact('order', 'canReturn', 'subtotal'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'cancelled_items' => 'required|array|min:1',
            'cancelled_items.*' => 'exists:order_details,id',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['orderDetails.product', 'orderDetails.productVariant'])
            ->firstOrFail();

        // Kiểm tra nếu đơn hàng đã được hủy một phần/toàn bộ trước đó
        if ($order->cancellation) {
            return back()->with('error', 'Đơn hàng này đã được hủy một phần trước đó và không thể hủy thêm.');
        }

        // Kiểm tra trạng thái đơn hàng
        if (!in_array($order->status, [1, 2])) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi chưa được xác nhận hoặc đang chuẩn bị.');
        }

        DB::beginTransaction();
        try {
            // Tạo bản ghi hủy đơn hàng
            $cancellationData = [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'cancelled_at' => now(),
            ];

            // Thêm refund_amount nếu cột tồn tại
            if (Schema::hasColumn('order_cancellations', 'refund_amount')) {
                $cancellationData['refund_amount'] = 0;
            }

            $cancellation = OrderCancellation::create($cancellationData);

            $totalCancelledAmount = 0;
            $cancelledItemsCount = 0;
            $cancelledItemsIds = [];

            // Xử lý từng sản phẩm bị hủy
            foreach ($request->cancelled_items as $orderDetailId) {
                $orderDetail = OrderDetail::find($orderDetailId);

                if ($orderDetail && $orderDetail->order_id == $order->id) {
                    // Tạo bản ghi hủy sản phẩm
                    OrderCancellationItem::create([
                        'order_cancellation_id' => $cancellation->id,
                        'order_detail_id' => $orderDetailId,
                    ]);

                    // Tính tổng tiền sản phẩm bị hủy
                    $totalCancelledAmount += $orderDetail->price * $orderDetail->quantity;
                    $cancelledItemsCount++;
                    $cancelledItemsIds[] = $orderDetailId;

                    // Xóa mềm sản phẩm (ẩn khỏi đơn hàng)
                    $orderDetail->delete();

                    // Hoàn lại số lượng tồn kho
                    if ($orderDetail->product) {
                        $orderDetail->product->quantity_in_stock += $orderDetail->quantity;
                        $orderDetail->product->save();
                    }

                    if ($orderDetail->productVariant) {
                        $orderDetail->productVariant->quantity_in_stock += $orderDetail->quantity;
                        $orderDetail->productVariant->save();
                    }
                }
            }

            // Cập nhật tổng tiền đơn hàng
            $order->total_price -= $totalCancelledAmount;

            // Cập nhật refund_amount nếu cột tồn tại
            if (Schema::hasColumn('order_cancellations', 'refund_amount')) {
                $cancellation->refund_amount = $totalCancelledAmount;
                $cancellation->save();
            }

            // Kiểm tra nếu hủy tất cả sản phẩm
            $remainingItemsCount = OrderDetail::where('order_id', $order->id)
                ->whereNotIn('id', $cancelledItemsIds)
                ->count();

            if ($remainingItemsCount === 0) {
                // Hủy toàn bộ đơn hàng
                $order->status = 6; // Hủy đơn
                $order->reason = $request->reason;

                // Xử lý hoàn tiền nếu đã thanh toán
                if ($order->payment_status === 'paid') {
                    $order->payment_status = 'refunded';
                }

                $message = 'Đơn hàng đã được hủy thành công' .
                    ($order->payment_status === 'refunded' ?
                        '. Số tiền ' . number_format($order->total_price + $totalCancelledAmount, 0, ',', '.') .
                        'đ sẽ được hoàn trả trong vòng 3-5 ngày làm việc.' : '.');
            } else {
                // Đánh dấu đơn hàng đã hủy một phần - chỉ nếu cột tồn tại
                if (Schema::hasColumn('orders', 'has_partial_cancellation')) {
                    $order->has_partial_cancellation = true;
                }
                $message = 'Đã hủy ' . $cancelledItemsCount . ' sản phẩm trong đơn hàng. Tổng tiền đã được cập nhật.';
            }

            $order->save();

            DB::commit();

            if ($remainingItemsCount === 0) {
                return redirect()->route('clients.orders')->with('success', $message);
            } else {
                return redirect()->route('clients.orderdetail', $order->id)->with('success', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi hủy đơn hàng: ' . $e->getMessage());
            return back()->with('error', 'Hủy đơn hàng thất bại: ' . $e->getMessage());
        }
    }

    public function applyCoupon(Request $request)
    {
        $code = trim($request->input('promotion'));
        $now = now();
        $userId = Auth::id();

        // Tìm mã giảm giá theo code (không phải promotion_name)
        $promotion = Promotion::where('code', $code)
            ->where('status', 1)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        if (!$promotion) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
        }

        // Kiểm tra giới hạn lượt dùng tổng
        if (!is_null($promotion->usage_limit) && $promotion->used_count >= $promotion->usage_limit) {
            session()->forget(['promotion', 'promotion_code', 'discount']);
            return back()->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }

        // Kiểm tra lượt dùng của người dùng hiện tại
        $userUsage = DB::table('promotion_user')
            ->where('promotion_id', $promotion->id)
            ->where('user_id', $userId)
            ->first();

        if ($userUsage && $userUsage->used_count >= 1) {
            return back()->with('error', 'Bạn đã sử dụng mã này rồi!');
        }

        // Lấy giỏ hàng và tính tổng tiền
        $cart = Cart::with('items')->where('user_id', $userId)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $subtotal = $cart->items->sum(function ($item) {
            return $item->discounted_price * $item->quantity;
        });

        // Kiểm tra đơn hàng tối thiểu
        if ($promotion->min_total_spent && $subtotal < $promotion->min_total_spent) {
            return back()->with('error', 'Đơn hàng cần đạt tối thiểu ' . number_format($promotion->min_total_spent) . 'đ để áp dụng mã.');
        }

        // Kiểm tra điều kiện VIP
        if ($promotion->vip_only && !Auth::user()->is_vip) {
            return back()->with('error', 'Mã giảm giá này chỉ dành cho thành viên VIP.');
        }

        // Tính số tiền giảm
        $discount = 0;
        if ($promotion->discount_type === 'percent') {
            $discount = round($subtotal * ($promotion->discount_value / 100));
            if ($promotion->max_discount_value && $discount > $promotion->max_discount_value) {
                $discount = $promotion->max_discount_value;
            }
        } elseif ($promotion->discount_type === 'fixed') {
            $discount = $promotion->discount_value;
        }

        // Lưu thông tin mã giảm giá vào session
        session([
            'promotion' => [
                'id' => $promotion->id,
                'code' => $promotion->code,
                'name' => $promotion->promotion_name,
                'type' => $promotion->discount_type,
                'value' => $promotion->discount_value,
                'max' => $promotion->max_discount_value,
                'discount' => $discount
            ],
            'discount' => $discount,
            'promotion_code' => $promotion->code
        ]);

        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }


    public function removeCoupon()
    {
        if (session()->has('promotion')) {
            session()->forget(['promotion', 'discount', 'promotion_code']);
            return back()->with('success', 'Đã hủy mã giảm giá.');
        }

        // Nếu không có mã thì chỉ quay lại, không báo gì
        return back();
    }

    public function requestReturn(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('orderDetails.product', 'orderDetails.productVariant')
            ->firstOrFail();

        // Kiểm tra điều kiện hoàn hàng
        if ($order->status != 4 && $order->status != 9) {
            return back()->with('error', 'Chỉ có thể yêu cầu hoàn hàng cho đơn hàng đã hoàn thành hoặc đã giao.');
        }

        $returnDeadline = Carbon::parse($order->completed_at ?? $order->received_at)->addHours(24);
        if (now()->gt($returnDeadline)) {
            return back()->with('error', 'Đã quá thời hạn 24 giờ để yêu cầu hoàn hàng.');
        }

        // Kiểm tra xem có ít nhất một sản phẩm được chọn không
        $hasSelectedItem = false;
        $selectedIndexes = [];

        if ($request->has('return_items')) {
            foreach ($request->return_items as $index => $item) {
                if (isset($item['selected']) && $item['selected'] == '1') {
                    $hasSelectedItem = true;
                    $selectedIndexes[] = $index;
                }
            }
        }

        if (!$hasSelectedItem) {
            return back()->with('error', 'Vui lòng chọn ít nhất một sản phẩm để hoàn trả.');
        }

        $validationRules = [
            'return_items' => 'required|array|min:1',
        ];

        $customMessages = [
            'return_items.required' => 'Vui lòng chọn ít nhất một sản phẩm để hoàn trả.',
        ];

        foreach ($selectedIndexes as $index) {
            $validationRules["return_items.{$index}.order_detail_id"] = 'required|exists:order_details,id';
            $validationRules["return_items.{$index}.quantity"] = 'required|integer|min:1';
            $validationRules["return_items.{$index}.reason"] = 'required|string|max:1000';
            $validationRules["return_items.{$index}.attachments.*"] = 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240';

            $customMessages["return_items.{$index}.quantity.required"] = 'Vui lòng nhập số lượng cho sản phẩm được chọn.';
            $customMessages["return_items.{$index}.reason.required"] = 'Vui lòng nhập lý do cho sản phẩm được chọn.';
        }

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => 7, // Chờ xử lý hoàn hàng
                'return_requested_at' => now()
            ]);

            // Xử lý từng sản phẩm yêu cầu hoàn hàng
            foreach ($request->return_items as $index => $returnItem) {
                if (!isset($returnItem['selected']) || $returnItem['selected'] != '1') {
                    continue;
                }

                $orderDetail = OrderDetail::find($returnItem['order_detail_id']);

                if (!$orderDetail) {
                    throw new \Exception("Chi tiết đơn hàng không tồn tại.");
                }

                // Kiểm tra số lượng hợp lệ
                if ($returnItem['quantity'] > $orderDetail->quantity) {
                    throw new \Exception("Số lượng yêu cầu hoàn trả vượt quá số lượng đã mua cho sản phẩm: " .
                        ($orderDetail->product->product_name ?? ''));
                }

                $returnItemRecord = OrderReturnItem::create([
                    'order_id' => $order->id,
                    'order_detail_id' => $returnItem['order_detail_id'],
                    'quantity' => $returnItem['quantity'],
                    'reason' => $returnItem['reason'],
                    'status' => 'pending'
                ]);

                // Xử lý file đính kèm nếu có
                if ($request->hasFile("return_items.{$index}.attachments")) {
                    foreach ($request->file("return_items.{$index}.attachments") as $file) {
                        if ($file->isValid()) {
                            $path = $file->store('returns/attachments', 'public');

                            OrderReturnAttachment::create([
                                'order_return_item_id' => $returnItemRecord->id,
                                'file_path' => $path,
                                'file_type' => strpos($file->getMimeType(), 'image') !== false ? 'image' : 'video'
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('clients.orderdetail', $order->id)
                ->with('success', 'Yêu cầu hoàn hàng đã được gửi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xử lý yêu cầu hoàn hàng: ' . $e->getMessage());
            return back()->with('error', 'Xử lý yêu cầu thất bại: ' . $e->getMessage());
        }
    }

    public function editReturn($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['orderDetails.product', 'orderDetails.productVariant', 'returnItems.orderDetail', 'returnItems.attachments'])
            ->firstOrFail();

        // Kiểm tra xem đơn hàng có yêu cầu hoàn hàng không
        if ($order->status != 7) {
            return redirect()->route('clients.orderdetail', $order->id)
                ->with('error', 'Chỉ có thể chỉnh sửa yêu cầu hoàn hàng đang chờ xử lý.');
        }

        // Kiểm tra thời hạn chỉnh sửa (24h sau khi nhận hàng)
        $returnDeadline = Carbon::parse($order->received_at)->addHours(24);
        if (now()->gt($returnDeadline)) {
            return redirect()->route('clients.orderdetail', $order->id)
                ->with('error', 'Đã quá thời hạn 24 giờ để chỉnh sửa yêu cầu hoàn hàng.');
        }

        return view('clients.user.edit-return', compact('order'));
    }

    public function updateReturn(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['orderDetails.product', 'orderDetails.productVariant', 'returnItems'])
            ->firstOrFail();

        // Kiểm tra điều kiện chỉnh sửa
        if ($order->status != 7) {
            return back()->with('error', 'Chỉ có thể chỉnh sửa yêu cầu hoàn hàng đang chờ xử lý.');
        }

        $returnDeadline = Carbon::parse($order->received_at)->addHours(24);
        if (now()->gt($returnDeadline)) {
            return back()->with('error', 'Đã quá thời hạn 24 giờ để chỉnh sửa yêu cầu hoàn hàng.');
        }

        $processedItems = $order->returnItems->where('status', '!=', 'pending')->count();
        if ($processedItems > 0) {
            return back()->with('error', 'Không thể cập nhật yêu cầu hoàn hàng vì quản trị viên đã bắt đầu xử lý.');
        }

        $hasSelectedItem = false;
        $selectedIndexes = [];

        if ($request->has('return_items')) {
            foreach ($request->return_items as $index => $item) {
                if (isset($item['selected']) && $item['selected'] == '1') {
                    $hasSelectedItem = true;
                    $selectedIndexes[] = $index;
                }
            }
        }

        if (!$hasSelectedItem) {
            return back()->with('error', 'Vui lòng chọn ít nhất một sản phẩm để hoàn trả.');
        }

        $validationRules = [
            'return_items' => 'required|array|min:1',
        ];

        $customMessages = [
            'return_items.required' => 'Vui lòng chọn ít nhất một sản phẩm để hoàn trả.',
        ];

        foreach ($selectedIndexes as $index) {
            $validationRules["return_items.{$index}.order_detail_id"] = 'required|exists:order_details,id';
            $validationRules["return_items.{$index}.quantity"] = 'required|integer|min:1';
            $validationRules["return_items.{$index}.reason"] = 'required|string|max:1000';
            $validationRules["return_items.{$index}.attachments.*"] = 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240';
            $validationRules["return_items.{$index}.existing_attachments.*"] = 'nullable|string';

            $customMessages["return_items.{$index}.quantity.required"] = 'Vui lòng nhập số lượng cho sản phẩm được chọn.';
            $customMessages["return_items.{$index}.reason.required"] = 'Vui lòng nhập lý do cho sản phẩm được chọn.';
        }

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Lấy tất cả các yêu cầu hoàn hàng hiện có
            $existingReturnItems = OrderReturnItem::where('order_id', $order->id)->get();

            $itemsToKeep = [];

            // Xử lý từng sản phẩm trong request
            foreach ($request->return_items as $index => $returnItem) {
                if (!isset($returnItem['selected']) || $returnItem['selected'] != '1') {
                    continue;
                }

                $orderDetail = OrderDetail::find($returnItem['order_detail_id']);

                if (!$orderDetail) {
                    throw new \Exception('Chi tiết đơn hàng không tồn tại.');
                }

                // Kiểm tra số lượng hợp lệ
                if ($returnItem['quantity'] > $orderDetail->quantity) {
                    throw new \Exception('Số lượng yêu cầu hoàn trả vượt quá số lượng đã mua cho sản phẩm: ' . ($orderDetail->product->product_name ?? ''));
                }

                // Tìm xem đã có yêu cầu hoàn hàng cho sản phẩm này chưa
                $existingReturnItem = $existingReturnItems->where('order_detail_id', $returnItem['order_detail_id'])->first();

                if ($existingReturnItem) {
                    $existingReturnItem->update([
                        'quantity' => $returnItem['quantity'],
                        'reason' => $returnItem['reason'],
                    ]);

                    $returnItemRecord = $existingReturnItem;
                    $itemsToKeep[] = $existingReturnItem->id;
                } else {
                    $returnItemRecord = OrderReturnItem::create([
                        'order_id' => $order->id,
                        'order_detail_id' => $returnItem['order_detail_id'],
                        'quantity' => $returnItem['quantity'],
                        'reason' => $returnItem['reason'],
                        'status' => 'pending',
                    ]);

                    $itemsToKeep[] = $returnItemRecord->id;
                }

                // Xử lý file đính kèm đã tồn tại (nếu có)
                if (isset($returnItem['existing_attachments'])) {
                    foreach ($returnItem['existing_attachments'] as $attachmentId) {
                        $attachment = OrderReturnAttachment::find($attachmentId);
                        if ($attachment && $attachment->order_return_item_id == $returnItemRecord->id) {
                        }
                    }

                    $existingAttachments = OrderReturnAttachment::where('order_return_item_id', $returnItemRecord->id)->get();
                    foreach ($existingAttachments as $attachment) {
                        if (!in_array($attachment->id, $returnItem['existing_attachments'])) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
                        }
                    }
                } else {
                    $existingAttachments = OrderReturnAttachment::where('order_return_item_id', $returnItemRecord->id)->get();
                    foreach ($existingAttachments as $attachment) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }

                // Xử lý file đính kèm mới (nếu có)
                if ($request->hasFile("return_items.{$index}.attachments")) {
                    foreach ($request->file("return_items.{$index}.attachments") as $file) {
                        if ($file->isValid()) {
                            $path = $file->store('returns/attachments', 'public');

                            OrderReturnAttachment::create([
                                'order_return_item_id' => $returnItemRecord->id,
                                'file_path' => $path,
                                'file_type' => strpos($file->getMimeType(), 'image') !== false ? 'image' : 'video',
                            ]);
                        }
                    }
                }
            }

            // Xóa các yêu cầu hoàn hàng không còn được chọn
            $itemsToDelete = OrderReturnItem::where('order_id', $order->id)
                ->whereNotIn('id', $itemsToKeep)
                ->get();

            foreach ($itemsToDelete as $itemToDelete) {
                foreach ($itemToDelete->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
                $itemToDelete->delete();
            }

            DB::commit();

            return redirect()->route('clients.orderdetail', $order->id)
                ->with('success', 'Yêu cầu hoàn hàng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Cập nhật yêu cầu thất bại: ' . $e->getMessage());
        }
    }

    public function cancelReturn(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('returnItems')
            ->firstOrFail();

        // Kiểm tra điều kiện hủy yêu cầu hoàn hàng
        if ($order->status != 7) {
            return back()->with('error', 'Chỉ có thể hủy yêu cầu hoàn hàng đang chờ xử lý.');
        }

        // Kiểm tra xem có sản phẩm nào đã được xử lý chưa
        $processedItems = $order->returnItems->where('status', '!=', 'pending')->count();
        if ($processedItems > 0) {
            return back()->with('error', 'Không thể hủy yêu cầu hoàn hàng vì quản trị viên đã bắt đầu xử lý.');
        }

        DB::beginTransaction();

        try {
            foreach ($order->returnItems as $returnItem) {
                foreach ($returnItem->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            OrderReturnItem::where('order_id', $order->id)->delete();

            $previousStatus = $order->received_at ? 4 : 3;
            $order->update([
                'status' => $previousStatus,
                'return_requested_at' => null
            ]);

            DB::commit();

            return redirect()->route('clients.orderdetail', $order->id)
                ->with('success', 'Yêu cầu hoàn hàng đã được hủy thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hủy yêu cầu thất bại: ' . $e->getMessage());
        }
    }
}

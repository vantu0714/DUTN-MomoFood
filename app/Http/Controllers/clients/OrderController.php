<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VNPayController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Promotion;
use App\Models\PromotionUser;
use App\Models\User;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                ->firstOrFail();
        }

        // Thêm logic kiểm tra thời gian hoàn hàng
        $canReturn = false;
        if ($order->status == 4 && $order->completed_at) {
            $returnDeadline = Carbon::parse($order->completed_at)->addHours(24);
            $canReturn = now()->lte($returnDeadline);
        }

        return view('clients.user.show-order', compact('order', 'canReturn'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $order = Order::where('id', $id)->with('orderDetails.product', 'orderDetails.productVariant')
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status != 1) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi chưa được xác nhận.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 6,
                'reason' => $request->reason
            ]);

            // Xử lý hoàn tiền nếu đã thanh toán
            if ($order->payment_status === 'paid') {
                $order->update([
                    'payment_status' => 'refunded'
                ]);
            }

            // Hoàn lại số lượng tồn kho
            foreach ($order->orderDetails as $orderDetail) {
                $orderDetail->product->quantity_in_stock += $orderDetail->quantity;
                $orderDetail->product->save();

                if (!is_null($orderDetail->productVariant)) {
                    $orderDetail->productVariant->quantity_in_stock += $orderDetail->quantity;
                    $orderDetail->productVariant->save();
                }
            }

            DB::commit();

            if ($order->payment_status === 'refunded') {
                return redirect()->route('clients.orders')->with('success', 'Đơn hàng đã được hủy thành công. Số tiền ' . number_format($order->total_price, 0, ',', '.') . 'đ sẽ được hoàn trả trong vòng 3-5 ngày làm việc.');
            } else {
                return redirect()->route('clients.orders')->with('success', 'Đơn hàng đã được hủy thành công.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
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
            ->firstOrFail();

        // Kiểm tra điều kiện hoàn hàng
        if ($order->status == 5) {
            return back()->with('error', 'Đơn hàng đã được hoàn trả');
        }

        if ($order->status == 7) {
            return back()->with('info', 'Yêu cầu hoàn hàng đang chờ xử lý');
        }

        if ($order->status != 9 || ($order->completed_at && now()->gt(Carbon::parse($order->completed_at)->addHours(24)))) {
            return back()->with('error', 'Không đủ điều kiện hoàn hàng');
        }

        $request->validate([
            'return_reason' => 'required|string'
        ], [
            'return_reason.required' => 'Vui lòng nhập lý do hoàn hàng'
        ]);

        DB::beginTransaction();

        try {
            $order->update([
                'status' => 7,
                'return_reason' => $request->return_reason,
                'return_requested_at' => now()
            ]);

            DB::commit();

            return redirect()->route('clients.orderdetail', $order->id)
                ->with('success', 'Yêu cầu hoàn hàng đã được gửi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xử lý yêu cầu thất bại: ' . $e->getMessage());
        }
    }
}

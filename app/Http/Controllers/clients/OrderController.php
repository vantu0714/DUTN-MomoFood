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
use Illuminate\Support\Facades\Validator;

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

        if ($cart && $cart->items) {
            $items = !empty($selectedIds)
                ? $cart->items->whereIn('id', $selectedIds)
                : $cart->items;

            foreach ($items as $item) {
                $price = $item->discounted_price ?? $item->original_price ?? 0;
                $item->calculated_price = $price;
                $item->item_total = $price * $item->quantity;

                // Gộp tên biến thể
                $item->variant_summary = $item->productVariant && is_array($item->productVariant->variant_values)
                    ? implode(', ', array_filter($item->productVariant->variant_values))
                    : null;

                $cartItems->push($item);
            }
        }

        // Nếu có recipient_id từ form (người dùng chọn địa chỉ cụ thể)
        $recipient = Recipient::where('user_id', $userId)
            ->where('is_default', true)
            ->first();

        if (session()->has('selected_recipient_id')) {
            $recipient = Recipient::where('user_id', $userId)
                ->where('id', session('selected_recipient_id'))
                ->first();
        }
        // Lấy danh sách địa chỉ đã lưu
        $savedRecipients = Recipient::where('user_id', $userId)->get();

        // Lấy các voucher đang hoạt động
        $vouchers = Promotion::where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

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

        session()->forget('selected_recipient_id');

        return view('clients.order', compact(
            'cart',
            'cartItems',
            'recipient',
            'savedRecipients',
            'vouchers',
            'locations'
        ));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $cart = Cart::with('items')->where('user_id', $userId)->firstOrFail();

        // Lấy selected_ids từ request hoặc session
        $selectedIds = [];

        if ($request->filled('selected_items')) {
            $selectedIds = is_array($request->selected_items)
                ? $request->selected_items
                : explode(',', $request->selected_items);
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
            $recipient = Recipient::where('user_id', $userId)->findOrFail($request->recipient_id);
        } else {
            // Validate địa chỉ mới
            $request->validate([
                'recipient_name' => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:15',
                'recipient_address' => 'required|string|max:500',
            ]);

            // Thêm địa chỉ mới
            $recipient = Recipient::create([
                'user_id' => $userId,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'note' => $request->note,
                'is_default' => false,
            ]);
        }

        DB::beginTransaction();

        try {
            // Tính tổng đơn hàng
            $total = $cartItems->sum(function ($item) {
                return $item->discounted_price * $item->quantity;
            });

            $discount = 0;
            $promotionCode = null;

            // Xử lý mã giảm giá
            if ($request->filled('promotion')) {
                $promotion = Promotion::where('promotion_name', trim($request->promotion))
                    ->where('status', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($promotion) {
                    $discount = $promotion->discount_amount;
                    $promotionCode = $promotion->promotion_name;

                    // Cập nhật số lần dùng
                    $promotion->increment('used_count');
                    PromotionUser::updateOrCreate(
                        ['promotion_id' => $promotion->id, 'user_id' => $userId],
                        ['used_count' => DB::raw('used_count + 1')]
                    );
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
            }

            // Cập nhật trạng thái VIP
            $totalSpent = Order::where('user_id', $userId)
                ->whereIn('status', [2, 3, 4])
                ->sum('total_price');

            if ($totalSpent >= 5000000) {
                User::where('id', $userId)->update(['is_vip' => true]);
            }

            // Xóa sản phẩm đã đặt khỏi giỏ hàng
            $cart->items()->whereIn('id', $cartItems->pluck('id'))->delete();
            // Xóa session selected_items
            session()->forget('selected_items');

            DB::commit();

            // Xử lý thanh toán
            if ($request->payment_method === 'vnpay') {
                $vnpay = new VNPayController();
                return $vnpay->create($request, $order);
            } else {
                return redirect()->route('carts.index')->with('orderSuccess', $order->id);
            }
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

        $order = Order::findOrFail($id);

        if ($order->status != 1) {
            return back()->with('error', 'Đơn hàng không thể hủy.');
        }

        $order->status = 6; // hủy đơn
        $order->cancellation_reason = $request->reason;
        $order->save();

        return redirect()->route('clients.orders')->with('success', 'Đơn hàng đã được hủy.');
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->input('promotion');
        $now = Carbon::now();
        $userId = Auth::id();

        $promotion = Promotion::where('promotion_name', $code)
            ->where('status', 1)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        if (!$promotion) {
            return back()->with('error', 'Mã giảm giá không hợp lệ!');
        }

        // Kiểm tra lượt dùng tổng
        if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
            return back()->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }

        // Kiểm tra lượt dùng của user
        $userUsage = DB::table('promotion_user')
            ->where('promotion_id', $promotion->id)
            ->where('user_id', $userId)
            ->first();

        if ($userUsage && $userUsage->used_count >= 1) {
            return back()->with('error', 'Bạn đã sử dụng mã này rồi!');
        }

        // Tính giảm giá
        $cart = Cart::with('items')->where('user_id', $userId)->first();
        $subtotal = $cart ? $cart->items->sum('total_price') : 0;
        $discount = 0;

        //Kiểm tra min_total_spent
        if ($promotion->min_total_spent && $subtotal < $promotion->min_total_spent) {
            return back()->with('error', 'Đơn hàng chưa đạt tối thiểu ' . number_format($promotion->min_total_spent) . 'đ để áp dụng mã.');
        }

        if ($promotion->discount_type === 'percent') {
            $discount = round($subtotal * ($promotion->discount_value / 100));
            if ($promotion->max_discount_value && $discount > $promotion->max_discount_value) {
                $discount = $promotion->max_discount_value;
            }
        } elseif ($promotion->discount_type === 'fixed') {
            $discount = $promotion->discount_value;
        }

        // Lưu vào session
        session()->put('promotion', [
            'id' => $promotion->id,
            'name' => $promotion->promotion_name,
            'type' => $promotion->discount_type,
            'value' => $promotion->discount_value,
            'max' => $promotion->max_discount_value,
            'discount' => $discount
        ]);
        session()->put('discount', $discount);
        session()->put('promotion_name', $promotion->promotion_name);

        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }


    public function removeCoupon()
    {
        session()->forget(['promotion', 'discount', 'promotion_name']);
        return redirect()->route('carts.index')->with('success', 'Đã hủy mã giảm giá.');
    }

    public function requestReturn(Request $request, $id)
    {
        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            // Tìm đơn hàng
            $order = Order::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Kiểm tra điều kiện hoàn hàng
            if ($order->status == 5) {
                $message = 'Đơn hàng đã được hoàn trả';
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : redirect()->back()->with('error', $message);
            }

            if ($order->status == 7) {
                $message = 'Yêu cầu hoàn hàng đang chờ xử lý';
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : redirect()->back()->with('info', $message);
            }

            if ($order->status != 4) {
                $message = 'Chỉ có thể yêu cầu hoàn hàng cho đơn hàng đã hoàn thành';
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : redirect()->back()->with('error', $message);
            }

            // Kiểm tra thời gian hoàn hàng
            if ($order->completed_at) {
                $returnDeadline = Carbon::parse($order->completed_at)->addHours(24);
                if (now()->gt($returnDeadline)) {
                    $message = 'Đã quá 24 giờ kể từ khi hoàn thành đơn hàng';
                    return $isAjax
                        ? response()->json(['success' => false, 'message' => $message], 400)
                        : redirect()->back()->with('error', $message);
                }
            }

            // Validate dữ liệu đầu vào
            $validator = Validator::make($request->all(), [
                'return_reason' => 'required|string|min:10|max:1000'
            ], [
                'return_reason.required' => 'Vui lòng nhập lý do hoàn hàng',
                'return_reason.min' => 'Lý do hoàn hàng phải có ít nhất 10 ký tự',
                'return_reason.max' => 'Lý do hoàn hàng không được vượt quá 1000 ký tự'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                $order->update([
                    'status' => 7,
                    'return_reason' => $request->return_reason,
                    'return_requested_at' => now()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu hoàn hàng đã được gửi thành công!',
                    'redirect' => route('clients.orderdetail', $order->id)
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Return request error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xử lý yêu cầu'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Return request outer error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý yêu cầu hoàn hàng'
            ], 500);
        }
    }

    private function canReturnOrder($order)
    {
        // Kiểm tra trạng thái
        if ($order->status != 4) {
            return [
                'can_return' => false,
                'reason' => 'Chỉ có thể hoàn hàng đơn hàng đã hoàn thành'
            ];
        }

        // Kiểm tra đã hoàn hàng
        if (in_array($order->status, [5, 7])) {
            return [
                'can_return' => false,
                'reason' => 'Đơn hàng đã được/đang được hoàn hàng'
            ];
        }

        // Kiểm tra thời gian
        if ($order->completed_at) {
            $returnDeadline = Carbon::parse($order->completed_at)->addHours(24);
            if (now()->gt($returnDeadline)) {
                return [
                    'can_return' => false,
                    'reason' => 'Đã quá thời hạn hoàn hàng (24 giờ)'
                ];
            }
        }

        return [
            'can_return' => true,
            'reason' => null
        ];
    }
}

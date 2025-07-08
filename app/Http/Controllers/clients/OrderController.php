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

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $selectedIds = [];
        if ($request->has('selected_items')) {
            $selectedItems = $request->input('selected_items');
            $selectedIds = is_array($selectedItems) ? $selectedItems : explode(',', $selectedItems);
        }

        $cart = Cart::with(['items.product', 'items.productVariant'])
            ->where('user_id', $userId)
            ->first();

        $cartItems = collect();
        if ($cart && $cart->items) {
            $cartItems = !empty($selectedIds)
                ? $cart->items->whereIn('id', $selectedIds)
                : $cart->items;
        }

        $recipient = [];
    
        if (auth()->check()) {
            // Lấy địa chỉ mặc định nếu có
            $defaultRecipient = auth()->user()->defaultRecipient;
            
            $recipient = [
                'recipient_name' => $defaultRecipient->recipient_name ?? auth()->user()->name ?? '',
                'recipient_phone' => $defaultRecipient->recipient_phone ?? auth()->user()->phone ?? '',
                'recipient_address' => $defaultRecipient->recipient_address ?? auth()->user()->address ?? '',
                'note' => $defaultRecipient->note ?? '',
            ];
        }
        $savedRecipients = auth()->check() ? auth()->user()->recipients : collect();

        $vouchers = Promotion::where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('clients.order', compact('cart', 'cartItems', 'recipient','savedRecipients', 'vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:15',
            'recipient_address' => 'required|string|max:500',
            'shipping_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cod,vnpay',
            'recipient_id' => 'nullable|exists:recipients,id',
            'save_recipient' => 'nullable|boolean'
        ]);
    
        $userId = Auth::id();
        
        $cart = Cart::with('items')->where('user_id', $userId)->firstOrFail();
        
        // Lấy danh sách sản phẩm được chọn
        $selectedIds = [];
        if ($request->filled('selected_items')) {
            $selectedItems = $request->input('selected_items');
            $selectedIds = is_array($selectedItems) ? $selectedItems : explode(',', $selectedItems);
        }
        
        $cartItems = !empty($selectedIds) 
            ? $cart->items->whereIn('id', $selectedIds) 
            : $cart->items;
    
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Không có sản phẩm nào được chọn.');
        }
    
        $recipientId = null;
        
        if ($request->filled('recipient_id')) {
            $recipientId = $request->recipient_id;
        } 
        // 3.2. Nếu nhập mới và chọn lưu
        $recipient = Recipient::create([
            'user_id' => auth()->id(),
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'recipient_address' => $request->recipient_address,
            'note' => $request->note ?? null,
            'is_default' => $request->boolean('is_default'),
        ]);
        $recipientId = $recipient->id;
        
    
        // 4. CALCULATE ORDER VALUES
        $total = $cartItems->sum(function ($item) {
            return $item->discounted_price * $item->quantity;
        });
    
        $discount = 0;
        $promotionCode = null;
    
        // 4.1. Xử lý mã giảm giá (nếu có)
        if ($request->filled('promotion')) {
            $promotionName = trim($request->promotion);
            $promotion = Promotion::where('promotion_name', $promotionName)
                ->where('status', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
    
            if ($promotion) {
                // ... (giữ nguyên logic xử lý mã giảm giá từ code cũ)
                $promotionCode = $promotion->promotion_name;
                // ... (tính toán discount)
            }
        }
    
        $grandTotal = $total + $request->shipping_fee - $discount;
    
        // 5. CREATE ORDER
        try {
            DB::beginTransaction();
    
            // 5.1. Tạo đơn hàng
            $order = Order::create([
                'user_id' => $userId,
                'recipient_id' => $recipientId,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'note' => $request->note,
                'promotion' => $promotionCode,
                'shipping_fee' => $request->shipping_fee,
                'total_price' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'status' => 1,
            ]);
    
            // 5.2. Tạo chi tiết đơn hàng
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->discounted_price,
                ]);
            }
    
            // 5.3. Xử lý mã giảm giá (nếu có)
            if (isset($promotion)) {
                $promotion->increment('used_count');
                PromotionUser::updateOrCreate(
                    ['promotion_id' => $promotion->id, 'user_id' => $userId],
                    ['used_count' => DB::raw('used_count + 1')]
                );
            }
    
            // 5.4. Cập nhật trạng thái VIP
            $totalSpent = Order::where('user_id', $userId)
                ->whereIn('status', [2, 3, 4])
                ->sum('total_price');
    
            if ($totalSpent >= 5000000) {
                User::where('id', $userId)->update(['is_vip' => true]);
            }
    
            // 5.5. Xóa sản phẩm đã đặt khỏi giỏ hàng
            $cart->items()->whereIn('id', $cartItems->pluck('id'))->delete();
    
            DB::commit();
    
            // 6. XỬ LÝ THANH TOÁN
            if ($request->payment_method === 'vnpay') {
                $vnpay = new VNPayController();
                return $vnpay->create($request);
            }
    
            return redirect()->route('carts.index')->with('success', 'Đặt hàng thành công!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
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
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $items = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('order_id', $id)
            ->select('order_details.*', 'products.product_name as product_name')
            ->get();

        return view('clients.user.show-order', compact('order', 'items'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $order = Order::findOrFail($id);

        if ($order->status != 1) {
            return back()->with('error', 'Đơn hàng không thể hủy.');
        }

        $order->status = 6; // hủy đơn
        $order->cancellation_reason = $request->cancellation_reason;
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
}

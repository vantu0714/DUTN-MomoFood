<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Promotion;
use App\Models\PromotionUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function index()
    {

        $userId = Auth::id();

        $cart = Cart::with('items.product', 'items.productVariant')
            ->where('user_id', $userId)
            ->first();

        $recipient = session()->get('recipient', [
            'recipient_name' => '',
            'recipient_phone' => '',
            'recipient_address' => '',
            'note' => '',
        ]);

        return view('clients.order', compact('cart', 'recipient'));
    }
    public function store(Request $request)
    {


        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:15',
            'recipient_address' => 'required|string|max:500',
            'shipping_fee' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cod,vnpay',
        ]);

        $userId = Auth::id();
        $cart = Cart::with('items')->where('user_id', $userId)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng đang trống.');
        }

        $cartItems = $cart->items;

        session()->put('recipient', $request->only([
            'recipient_name',
            'recipient_phone',
            'recipient_address',
            'note'
        ]));

        // Tính tổng tiền hàng
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->discounted_price * $item->quantity;
        }

        $discount = 0;
        $promotionCode = null;

        if ($request->filled('promotion')) {
            $promotionName = trim($request->promotion);
            $promotion = Promotion::where('promotion_name', $promotionName)
                ->where('status', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($promotion) {
                $promotionCode = $promotion->promotion_name;

                if ($promotion->vip_only) {
                    $totalSpent = Order::where('user_id', $userId)
                        ->where('status', '!=', 6)
                        ->sum('total_price');

                    if ($totalSpent < 1000000) {
                        return redirect()->back()->with('error', 'Mã giảm giá này chỉ dành cho khách hàng VIP.');
                    }
                }

                if ($promotion->min_total_spent !== null && $total < $promotion->min_total_spent) {
                    return redirect()->back()->with('error', 'Bạn cần mua tối thiểu ' . number_format($promotion->min_total_spent, 0, ',', '.') . 'đ để dùng mã này.');
                }

                // Kiểm tra số lượt dùng của người dùng
                $userUsed = PromotionUser::where('promotion_id', $promotion->id)
                    ->where('user_id', $userId)
                    ->first();

                // Kiểm tra giới hạn tổng
                if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
                    return redirect()->back()->with('error', 'Mã giảm giá đã hết lượt sử dụng.');
                }

                // Kiểm tra nếu user đã dùng
                if ($userUsed && $userUsed->used_count >= 1) {
                    return redirect()->back()->with('error', 'Bạn đã sử dụng mã giảm giá này.');
                }

                // Tính giảm giá
                if ($promotion->discount_type === 'percent') {
                    $discount = ($promotion->discount_value / 100) * $total;
                } else {
                    $discount = $promotion->discount_value;
                }

                if ($promotion->max_discount_value !== null) {
                    $discount = min($discount, $promotion->max_discount_value);
                }
            } else {
                return redirect()->back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
            }
        }

        $grandTotal = $total + $request->shipping_fee - $discount;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::id(),
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

            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->discounted_price,
                ]);
            }
            // Tăng số lượt sử dụng mã giảm giá
            if (isset($promotion)) {
                $promotion->increment('used_count');

                PromotionUser::updateOrCreate(
                    ['promotion_id' => $promotion->id, 'user_id' => $userId],
                    ['used_count' => DB::raw('used_count + 1')]
                );
            }

            DB::commit();
            $cart->items()->delete(); // Xóa cart items
            session()->forget('promotion');
            session()->forget('discount');

            //vnpay
            $recipient = [
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'note' => $request->note,
                'shipping_fee' => $request->shipping_fee,
                'promotion' => $request->promotion ?? null,
                'payment_method' => $request->payment_method
            ];
            session(['order_temp' => $recipient]);

            // Nếu chọn VNPAY thì redirect đến trang thanh toán
            if ($request->payment_method === 'vnpay') {
                return redirect()->route('vnpay.payment', ['order_id' => $order->id]);
            }

            // Kết thúc vnpay

            return redirect()->route('carts.index')->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

    public function orderList()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(5);
        return view('clients.user.orders', compact('orders'));
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
}

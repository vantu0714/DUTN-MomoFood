<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function index()
    {

        $cart = session()->get('cart', []);
        return view('clients.order', compact('cart'));
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

        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Giỏ hàng đang trống.');
        }

        // Tính tổng tiền hàng
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Áp dụng mã giảm giá nếu có
        // Áp dụng mã giảm giá nếu có
        $discount = 0;
        $promotionCode = null;

        if ($request->filled('promotion')) {
            $promotion = Promotion::where('promotion_name', $request->promotion)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if (!$promotion) {
                return redirect()->back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
            }

            $promotionCode = $promotion->promotion_name;

            if ($promotion->discount_type === 'percent') {
                $discount = ($promotion->discount_value / 100) * $total;
            } elseif ($promotion->discount_type === 'fixed') {
                $discount = $promotion->discount_value;
            }

            if ($promotion->max_discount_value !== null) {
                $discount = min($discount, $promotion->max_discount_value);
            }
        }


        $grandTotal = $total + $request->shipping_fee - $discount;

        try {
            DB::beginTransaction();

            // Tạo đơn hàng
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
                'status' => 'pending',
            ]);

            // Lưu chi tiết đơn hàng
            foreach ($cartItems as $productId => $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();

            // Xóa giỏ hàng sau khi đặt
            session()->forget('cart');

            if ($request->payment_method === 'vnpay') {
                return redirect()->route('vnpay.payment', ['order_id' => $order->id]);
            }

            return redirect()->route('carts.index')->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }
}

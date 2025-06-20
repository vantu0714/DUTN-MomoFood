<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartClientController extends Controller
{
    public function index()
    {
        $carts = session()->get('cart', []);
        return view('clients.carts.index', compact('carts'));
    }

    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('product_variant_id');

        $cart = session()->get('cart', []);

        if ($variantId) {
            // Nếu có biến thể sản phẩm
            $variant = \App\Models\ProductVariant::findOrFail($variantId);
            $product = $variant->product; // quan hệ product() trong model ProductVariant

            $key = 'variant_' . $variantId; // dùng key riêng để tránh trùng

            if (isset($cart[$key])) {
                $cart[$key]['quantity']++;
            } else {
                $cart[$key] = [
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name'       => $product->product_name,
                    'variant_name'       => $variant->name, // Tên biến thể
                    'product_code'       => $product->product_code,
                    'price'              => $variant->price ?? $product->discounted_price ?? $product->original_price,
                    'image'              => $product->image ?? 'default.jpg',
                    'quantity'           => 1,
                ];
            }
        } else {
            // Nếu không có biến thể
            $product = \App\Models\Product::findOrFail($productId);
            $key = 'product_' . $productId;

            if (isset($cart[$key])) {
                $cart[$key]['quantity']++;
            } else {
                $cart[$key] = [
                    'product_id'         => $product->id,
                    'product_variant_id' => null,
                    'product_name'       => $product->product_name,
                    'variant_name'       => null, // Không có biến thể
                    'product_code'       => $product->product_code,
                    'price'              => $product->discounted_price ?? $product->original_price,
                    'image'              => $product->image ?? 'default.jpg',
                    'quantity'           => 1,
                ];
            }
        }

        session(['cart' => $cart]);

        return redirect()->route('carts.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function updateQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $quantity = max(1, (int) $request->input('quantity'));
            $cart[$id]['quantity'] = $quantity;

            session(['cart' => $cart]);

            // Tính lại tổng
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'subtotal' => number_format($cart[$id]['price'] * $quantity, 0, ',', '.'),
                'total' => number_format($total + 30000, 0, ',', '.') // bao gồm phí ship
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng.');
        }

        return redirect()->back()->with('error', 'Sản phẩm không có trong giỏ hàng.');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Giỏ hàng đã được xóa.');
    }

    public function updateAjax(Request $request)
    {
        $id = $request->id;
        $quantity = max(1, (int) $request->quantity);

        $cart = session('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            session(['cart' => $cart]);

            // Tính toán lại
            $subTotal = $cart[$id]['quantity'] * $cart[$id]['price'];
            $total = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });
            $shipping = 30000;
            $grandTotal = $total + $shipping;

            return response()->json([
                'success' => true,
                'sub_total' => number_format($subTotal, 0, ',', '.'),
                'total' => number_format($total, 0, ',', '.'),
                'grand_total' => number_format($grandTotal, 0, ',', '.'),
            ]);
        }

        return response()->json(['success' => false], 404);
    }
    public function applyCoupon(Request $request)
    {
        $code = $request->input('promotion');
        $now = Carbon::now();

        // Tìm mã khuyến mãi hợp lệ trong DB
        $promotion = Promotion::where('promotion_name', $code)
            ->where('status', 'active')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        if (!$promotion) {
            session()->flash('error', 'Mã giảm giá không hợp lệ!');
            return redirect()->back();
        }

        // Kiểm tra usage_limit
        if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
            session()->flash('error', 'Mã giảm giá đã hết lượt sử dụng!');
            return redirect()->back();
        }
        // TÍNH TỔNG GIỎ HÀNG
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        // TÍNH SỐ TIỀN GIẢM
        $discount = 0;
        if ($promotion->discount_type === 'percent') {
            $discount = round($subtotal * ($promotion->discount_value / 100));
            // Giới hạn giảm tối đa nếu có
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
            'max' => $promotion->max_discount_value
        ]);
        session()->put('discount', $discount); 
        session()->put('promotion_name', $promotion->promotion_name);

        session()->flash('success', 'Áp dụng mã giảm giá thành công!');
        return redirect()->back();
    }
    public function removeCoupon(Request $request)
    {
        session()->forget('promotion');
        session()->forget('discount');
        session()->forget('promotion_name');
        return redirect()->back()->with('success', 'Đã hủy mã giảm giá.');
    }
}

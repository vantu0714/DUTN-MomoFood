<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
    $product = Product::findOrFail($productId);

    $cart = session()->get('cart', []);

    if (isset($cart[$productId])) {
        $cart[$productId]['quantity']++;
    } else {
        $cart[$productId] = [
            'product_name'  => $product->product_name,
            'product_code'  => $product->product_code,
            'price'         => $product->discounted_price ?? $product->original_price,
            'image'         => $product->image ?? 'default.jpg',
            'quantity'      => 1,
        ];
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

    public function applyCoupon(Request $request)
    {
        $coupon = $request->input('coupon_code');
        if ($coupon === 'GIAM10') {
            session()->flash('success', 'Áp dụng mã giảm giá thành công!');
        } else {
            session()->flash('error', 'Mã giảm giá không hợp lệ!');
        }
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Carbon\Carbon;

class CartClientController extends Controller
{
   public function index()
{
    $userId = Auth::id();

    $cart = Cart::with('items.productVariant.product')
        ->where('user_id', $userId)
        ->first();

    $carts = $cart ? $cart->items : collect();

    return view('clients.carts.index', compact('carts'));
}

   public function addToCart(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
    }

    $userId = Auth::id();
    $productId = $request->input('product_id');
    $variantId = $request->input('product_variant_id');
    $quantity = $request->input('quantity', 1);

    $product = Product::findOrFail($productId);
    $variant = $variantId ? ProductVariant::find($variantId) : null;

    // ✅ Tạo cart nếu chưa có
    $cart = Cart::firstOrCreate(
        ['user_id' => $userId],
        ['created_at' => now(), 'updated_at' => now()]
    );

    // ✅ Tính giá
    $originalPrice = $variant ? $variant->price : $product->price;
    $discountedPrice = $originalPrice * 0.9; // ví dụ giảm 10%

    // ✅ Kiểm tra item đã tồn tại chưa
    $query = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $productId);

    if ($variant) {
        $query->where('product_variant_id', $variant->id);
    } else {
        $query->whereNull('product_variant_id');
    }

    $existingItem = $query->first();

    if ($existingItem) {
        $existingItem->quantity += $quantity;
        $existingItem->total_price = $existingItem->discounted_price * $existingItem->quantity;
        $existingItem->save();
    } else {
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'product_variant_id' => $variant?->id,
            'quantity' => $quantity,
            'original_price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'total_price' => $discountedPrice * $quantity,
        ]);
    }

    return redirect()->route('carts.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
}


    public function updateQuantity(Request $request, $id)
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy giỏ hàng.'], 404);
        }

        $item = CartItem::where('cart_id', $cart->id)->where('id', $id)->first();

        if ($item) {
            $quantity = max(1, (int) $request->input('quantity'));
            $item->quantity = $quantity;
            $item->total_price = $item->discounted_price * $quantity;
            $item->save();

            $total = $cart->items()->sum('total_price');

            return response()->json([
                'success'  => true,
                'subtotal' => number_format($item->total_price, 0, ',', '.'),
                'total'    => number_format($total + 30000, 0, ',', '.') // phí ship
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    public function removeFromCart($id)
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $item = CartItem::where('cart_id', $cart->id)->where('id', $id)->first();
            if ($item) {
                $item->delete();
                return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
            }
        }

        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
    }

    public function clearCart()
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }

    public function updateAjax(Request $request)
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json(['success' => false], 404);
        }

        $item = CartItem::where('cart_id', $cart->id)->where('id', $request->id)->first();

        if ($item) {
            $quantity = max(1, (int) $request->quantity);
            $item->quantity = $quantity;
            $item->total_price = $item->discounted_price * $quantity;
            $item->save();

            $subTotal = $item->total_price;
            $total = $cart->items()->sum('total_price');
            $shipping = 30000;
            $grandTotal = $total + $shipping;

            return response()->json([
                'success'     => true,
                'sub_total'   => number_format($subTotal, 0, ',', '.'),
                'total'       => number_format($total, 0, ',', '.'),
                'grand_total' => number_format($grandTotal, 0, ',', '.'),
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->input('promotion');
        $now = Carbon::now();

        $promotion = Promotion::where('promotion_name', $code)
            ->where('status', 'active')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        if (!$promotion) {
            return back()->with('error', 'Mã giảm giá không hợp lệ!');
        }

        if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
            return back()->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }

        $userId = Auth::id();
        $cart = Cart::with('items')->where('user_id', $userId)->first();
        $subtotal = $cart ? $cart->items->sum('total_price') : 0;

        $discount = 0;
        if ($promotion->discount_type === 'percent') {
            $discount = round($subtotal * ($promotion->discount_value / 100));
            if ($promotion->max_discount_value && $discount > $promotion->max_discount_value) {
                $discount = $promotion->max_discount_value;
            }
        } elseif ($promotion->discount_type === 'fixed') {
            $discount = $promotion->discount_value;
        }

        session()->put('promotion', [
            'id'    => $promotion->id,
            'name'  => $promotion->promotion_name,
            'type'  => $promotion->discount_type,
            'value' => $promotion->discount_value,
            'max'   => $promotion->max_discount_value
        ]);
        session()->put('discount', $discount);
        session()->put('promotion_name', $promotion->promotion_name);

        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }

    public function removeCoupon()
    {
        session()->forget(['promotion', 'discount', 'promotion_name']);
        return back()->with('success', 'Đã hủy mã giảm giá.');
    }
}

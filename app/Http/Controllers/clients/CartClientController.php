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
use Illuminate\Support\Facades\DB;

class CartClientController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $cart = Cart::with('items.productVariant.product')
            ->where('user_id', $userId)
            ->first();

        $carts = $cart ? $cart->items : collect();

        $total = $carts->sum(fn($item) => $item->total_price);

        $vouchers = Promotion::where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('clients.carts.index', compact('carts', 'total', 'vouchers'));
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Bạn cần đăng nhập.'], 401);
            }
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }

        $userId = Auth::id();
        $productId = $request->input('product_id');
        $variantId = $request->input('product_variant_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;

        $stock = $variant ? $variant->quantity : $product->quantity;

        //  Hết hàng
        if ($stock <= 0) {
            return response()->json(['message' => 'Sản phẩm đã hết hàng!'], 400);
        }

        // Quá số lượng tồn
        if ($quantity > $stock) {
            return response()->json(['message' => 'Số lượng vượt quá tồn kho!'], 400);
        }

        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $originalPrice = $variant ? ($variant->original_price ?? $variant->price) : ($product->original_price ?? $product->price);
        $discountedPrice = $variant ? ($variant->discounted_price ?? $originalPrice) : ($product->discounted_price ?? $originalPrice);

        $query = CartItem::where('cart_id', $cart->id)->where('product_id', $productId);
        $variant ? $query->where('product_variant_id', $variant->id) : $query->whereNull('product_variant_id');
        $existingItem = $query->first();

        if ($existingItem) {
            if ($existingItem->quantity + $quantity > $stock) {
                return response()->json(['message' => 'Sản phẩm đã hết hàng!'], 400);
            }
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

        return response()->json([
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'cart_count' => $cart->items()->sum('quantity'),
        ]);
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

                // Nếu không còn sản phẩm trong giỏ, xoá mã giảm giá
                if ($cart->items()->count() === 0) {
                    session()->forget(['promotion', 'discount', 'promotion_name']);
                }
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
        if ($cart->items()->count() === 0) {
            session()->forget(['promotion', 'discount', 'promotion_name']);
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
            $stock = $item->product_variant_id
                ? $item->productVariant?->quantity
                : $item->product?->quantity;

            if ($quantity > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vượt quá số lượng tồn kho: ' . $stock
                ]);
            }

            $item->quantity = $quantity;
            $item->total_price = $item->discounted_price * $quantity;
            $item->save();

            $subTotal = $item->total_price;
            $total = $cart->items()->sum('total_price');
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

        //Kiểm tra nếu là mã chỉ dành cho khách VIP
        // if ($promotion->vip_only && (!$user->is_vip ?? false)) {
        //     return back()->with('error', 'Mã này chỉ dành cho khách hàng VIP.');
        // }

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
        return back()->with('success', 'Đã hủy mã giảm giá.');
    }

    public function clear(Request $request)
    {
        $userId = Auth::id();

        // Dùng session:
        session()->forget('cart');

        // Nếu giỏ hàng lưu trong database:
        \App\Models\Cart::where('user_id', $userId)->delete();

        return redirect()->back()->with('success', 'Đã xóa toàn bộ sản phẩm trong giỏ hàng.');
    }

    public function removeSelected(Request $request)
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return back()->with('error', 'Không tìm thấy giỏ hàng.');
        }

        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return back()->with('error', 'Vui lòng chọn sản phẩm cần xóa.');
        }

        CartItem::where('cart_id', $cart->id)
            ->whereIn('id', $selectedItems)
            ->delete();

        return back()->with('success', 'Đã xóa các sản phẩm đã chọn.');
    }
}

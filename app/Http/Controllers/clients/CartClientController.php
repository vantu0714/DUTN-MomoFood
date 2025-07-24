<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
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

        //Nếu có session flash 'orderSuccess', load thông tin đơn hàng
    $order = null;
    if (session('orderSuccess')) {

        $order = Order::with(['orderDetails.product', 'orderDetails.productVariant.attributeValues.attribute', 'orderDetails.product.category'])
            ->find(session('orderSuccess'));
    }
        return view('clients.carts.index', compact('carts', 'total', 'order'));
    }
    public function addToCart(Request $request)
    {
        // Bắt buộc đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', '⚠️ Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }

        $userId = Auth::id();

        $productId = $request->input('product_id');
        $variantId = $request->input('product_variant_id');
        $quantity  = max(1, (int) $request->input('quantity', 1));

        // Lấy sản phẩm và biến thể nếu có
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;

        // Lấy tồn kho thực tế
        $stock = $variant
            ? $variant->quantity_in_stock
            : $product->quantity_in_stock;

        // Kiểm tra tồn kho
        if ($stock <= 0) {
            return redirect()->back()->with('error', '❌ Sản phẩm đã hết hàng.');
        }

        if ($quantity > $stock) {
            return redirect()->back()->with('error', '❌ Số lượng vượt quá tồn kho.');
        }

        // Lấy hoặc tạo giỏ hàng
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        // Tính giá gốc và giá khuyến mãi
        $originalPrice = $variant
            ? ($variant->original_price ?? $variant->price)
            : ($product->original_price ?? $product->price);

        $discountedPrice = $variant
            ? ($variant->discounted_price ?? $originalPrice)
            : ($product->discounted_price ?? $originalPrice);

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->when($variant, fn($q) => $q->where('product_variant_id', $variant->id))
            ->when(!$variant, fn($q) => $q->whereNull('product_variant_id'))
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + $quantity;

            if ($newQuantity > $stock) {
                return redirect()->back()->with('error', '❌ Số lượng vượt quá tồn kho.');
            }

            $item->update([
                'quantity'     => $newQuantity,
                'total_price'  => $discountedPrice * $newQuantity,
            ]);
        } else {
            CartItem::create([
                'cart_id'            => $cart->id,
                'product_id'         => $productId,
                'product_variant_id' => $variant?->id,
                'quantity'           => $quantity,
                'original_price'     => $originalPrice,
                'discounted_price'   => $discountedPrice,
                'total_price'        => $discountedPrice * $quantity,
            ]);
        }

        return redirect()->back()->with('success', ' Đã thêm sản phẩm vào giỏ hàng!');
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

        // Lấy giá và tổng giá đã giảm giá của sản phẩm
        $discountedPrice = $item->discounted_price;
        $totalPrice = $discountedPrice * $quantity;

        // Kiểm tra và cập nhật số lượng và tổng giá mới
        $item->quantity = $quantity;
        $item->total_price = $totalPrice;
        $item->save();

        // Tính lại tổng giá của giỏ hàng
        $total = $cart->items()->sum('total_price');

        return response()->json([
            'success'  => true,
            'subtotal' => number_format($totalPrice, 0, ',', '.'),
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

        //Lấy đúng tồn kho từ biến thể hoặc sản phẩm
        $stock = $item->product_variant_id
            ? ($item->productVariant?->quantity_in_stock ?? 0)
            : ($item->product?->quantity_in_stock ?? 0);

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

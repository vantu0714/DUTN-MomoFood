<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class ProductDetailController extends Controller
{
   public function show($id)
{
    $product = Product::with([
        'category',
        'variants' => function ($q) {
            $q->select('id', 'product_id', 'price', 'quantity_in_stock', 'image', 'status');
        },
        'variants.attributeValues.attribute',
        'comments' => fn($q) => $q->latest(),
        'comments.user'
    ])->findOrFail($id);

    // Nếu là sản phẩm đơn mà hết hàng → 404
    if ($product->product_type === 'simple' && $product->quantity_in_stock <= 0) {
        abort(404, 'Sản phẩm đã hết hàng');
    }

    // Gắn flag is_disabled cho biến thể
    foreach ($product->variants as $variant) {
        $variant->is_disabled = ($variant->status == 0 || $variant->quantity_in_stock <= 0);
    }

    // Sản phẩm liên quan (lấy tất cả biến thể, xử lý disable tương tự)
    $relatedProducts = Product::with([
        'variants' => function ($q) {
            $q->select('id', 'product_id', 'price', 'quantity_in_stock', 'image', 'status');
        }
    ])
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->latest()
        ->take(8)
        ->get();

    foreach ($relatedProducts as $related) {
        foreach ($related->variants as $variant) {
            $variant->is_disabled = ($variant->status == 0 || $variant->quantity_in_stock <= 0);
        }
    }

    // Trung bình rating
    $averageRating = round($product->comments->avg('rating'), 1) ?? 0;

    $hasPurchased = false;
    $hasReviewed = false;

    if (Auth::check()) {
        $userId = Auth::id();

        $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('status', 4);
        })->where('product_id', $product->id)->exists();

        $hasReviewed = $product->comments->where('user_id', $userId)->isNotEmpty();
    }

    return view('clients.product-detail', compact(
        'product',
        'relatedProducts',
        'averageRating',
        'hasPurchased',
        'hasReviewed'
    ));
}

}

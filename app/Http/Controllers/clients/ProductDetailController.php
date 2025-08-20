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
                // KHÔNG lọc biến thể, lấy hết để xử lý ở Blade
            }
        ])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where(function ($q) {
                $q->where(function ($q1) {
                    // sản phẩm đơn -> chỉ lấy đang hiển thị
                    $q1->where('product_type', 'simple')->where('status', 1);
                })->orWhere(function ($q2) {
                    // sản phẩm có biến thể -> vẫn lấy sp cha hiển thị
                    $q2->where('product_type', 'variant')->where('status', 1);
                });
            })
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

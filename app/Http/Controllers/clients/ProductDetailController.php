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
            'variants' => function ($query) {
                $query->where('quantity_in_stock', '>', 0); // Chỉ lấy biến thể còn hàng
            },
            'variants.attributeValues.attribute',
            'comments' => function ($query) {
                $query->latest();
            },
            'comments.user'
        ])->findOrFail($id);

        // Kiểm tra tồn kho nếu là sản phẩm đơn
        if ($product->product_type === 'simple' && $product->quantity_in_stock <= 0) {
            abort(404, 'Sản phẩm đã hết hàng');
        }

        $relatedProducts = Product::with([
            'variants' => fn($q) => $q->where('quantity_in_stock', '>', 0)
        ])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->available() // dùng scope lọc sản phẩm hợp lệ
            ->latest()
            ->take(8)
            ->get();

        $averageRating = round($product->comments->avg('rating'), 1) ?? 0;

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $userId = Auth::id();

            $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', '!=', 'cancelled');
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

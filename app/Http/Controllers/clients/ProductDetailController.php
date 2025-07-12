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
            'variants.attributeValues.attribute',
            'comments' => function ($query) {
                $query->latest(); // sắp xếp bình luận mới nhất trước
            },
            'comments.user'
        ])->findOrFail($id);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->latest()
            ->take(8)
            ->get();

        // Tính trung bình rating
        $averageRating = round($product->comments->avg('rating'), 1) ?? 0;

        // Mặc định
        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $userId = Auth::id();

            // Kiểm tra đã mua hàng chưa
            $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', '!=', 'cancelled'); // hoặc 'completed'
            })
                ->where('product_id', $product->id)
                ->exists();

            // Kiểm tra đã đánh giá chưa
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

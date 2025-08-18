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
        // Lấy sản phẩm + biến thể còn hàng và đang active
        $product = Product::with([
            'category',
            'variants' => fn($q) => $q->activeInStock(),
            'variants.attributeValues.attribute',
            'comments' => fn($q) => $q->latest(),
            'comments.user'
        ])->findOrFail($id);

        // Nếu là sản phẩm đơn mà hết hàng → 404
        if ($product->product_type === 'simple' && $product->quantity_in_stock <= 0) {
            abort(404, 'Sản phẩm đã hết hàng');
        }

        // Sản phẩm liên quan (cùng category, khác id, còn hàng)
        $relatedProducts = Product::with([
            'variants' => fn($q) => $q->activeInStock()
        ])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('product_type', 'simple')
                        ->where('quantity_in_stock', '>', 0);
                })
                    ->orWhere('product_type', 'variant');
            })
            ->latest()
            ->take(8)
            ->get();

        // Trung bình rating
        $averageRating = round($product->comments->avg('rating'), 1) ?? 0;

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $userId = Auth::id();

            // Kiểm tra đã mua sản phẩm chưa (status = 4 = hoàn tất)
            $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 4);
            })->where('product_id', $product->id)->exists();

            // Kiểm tra đã review chưa
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

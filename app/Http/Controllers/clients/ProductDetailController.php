<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductDetailController extends Controller
{

    public function show($id)
    {
    // Lấy sản phẩm cùng category, cùng comments và user
        $product = Product::with([
            'category',
            'comments' => function ($query) {
            $query->latest(); // sắp xếp bình luận mới nhất trước
            },
            'comments.user'
        ])->findOrFail($id);

    // Lấy các sản phẩm liên quan (khác ID, cùng danh mục, status = 1)
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('status', 1)
        ->latest()
        ->take(8)
        ->get();

    // Trả về view với đầy đủ dữ liệu
    return view('clients.product-detail', compact('product', 'relatedProducts'));
    }
}

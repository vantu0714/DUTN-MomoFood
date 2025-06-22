<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductDetailController extends Controller
{
    

     public function show($id)
    {
        // Lấy sản phẩm chính
        $product = Product::with('category')->findOrFail($id);

        // Lấy các sản phẩm liên quan cùng danh mục (ngoại trừ chính nó)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->latest()
            ->take(8)
            ->get();

        return view('clients.product-detail', compact('product', 'relatedProducts'));
    }
}

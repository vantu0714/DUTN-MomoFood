<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductDetailController extends Controller
{
    public function show($id)
    {
        $product = Product::with([
    'category',
    'comments' => function ($query) {
        $query->latest(); // sắp xếp theo created_at DESC
    },
    'comments.user'
])->findOrFail($id);

        return view('clients.product-detail', compact('product'));
    }
}


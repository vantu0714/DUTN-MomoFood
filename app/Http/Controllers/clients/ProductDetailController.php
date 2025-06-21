<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductDetailController extends Controller
{
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('clients.product-detail', compact('product'));
    }
}

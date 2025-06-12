<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('status', 1)->paginate(12);
        return view('shop.index', compact('products'));
    }
}

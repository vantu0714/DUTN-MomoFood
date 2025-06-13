<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('status', 1)->paginate(6);
        return view('clients.shop', compact('products'));

    }
}

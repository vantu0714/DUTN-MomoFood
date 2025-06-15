<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('status', 1)->paginate(12);
        return view('clients.shop', compact('products'));
        $products = Product::with('category')
            ->where('status', 1)
            ->where('quantity', '>', 0)
            ->paginate(6);

        $categories = Category::withCount('products')->get();

        return view('clients.shop', compact('products', 'categories'));
    }


    public function category($id)
    {
        $category = Category::findOrFail($id);
        $products = Product::where('category_id', $category->id)
            ->where('status', 1)
            ->paginate(12);

        $categories = Category::withCount('products')->get();

        return view('clients.shop', compact('products', 'categories'));
    }
}

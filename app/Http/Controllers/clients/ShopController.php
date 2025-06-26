<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('status', 1)
            ->where('quantity', '>', 0);

        if ($request->filled('price_range') && $request->price_range !== 'custom') {
            if (strpos($request->price_range, '-') !== false) {
                [$min, $max] = explode('-', $request->price_range);
                $query->whereBetween('discounted_price', [(int)$min, (int)$max]);
            }
        } else {
            if ($request->filled('min_price') && is_numeric($request->min_price)) {
                $query->where('discounted_price', '>=', $request->min_price);
            }

            if ($request->filled('max_price') && is_numeric($request->max_price)) {
                $query->where('discounted_price', '<=', $request->max_price);
            }
        }

        $products = $query->paginate(9)->appends($request->all());
        $categories = Category::withCount('products')->get();

        return view('clients.shop', compact('products', 'categories'));
    }

    public function category(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $query = Product::where('category_id', $category->id)
            ->where('status', 1)
            ->where('quantity', '>', 0);

        if ($request->filled('price_range') && $request->price_range !== 'custom') {
            if (strpos($request->price_range, '-') !== false) {
                [$min, $max] = explode('-', $request->price_range);
                $query->whereBetween('discounted_price', [(int)$min, (int)$max]);
            }
        } else {
            if ($request->filled('min_price') && is_numeric($request->min_price)) {
                $query->where('discounted_price', '>=', $request->min_price);
            }

            if ($request->filled('max_price') && is_numeric($request->max_price)) {
                $query->where('discounted_price', '<=', $request->max_price);
            }
        }

        $products = $query->paginate(9)->appends($request->all());
        $categories = Category::withCount('products')->get();

        return view('clients.shop', compact('products', 'categories', 'category'));
    }
}

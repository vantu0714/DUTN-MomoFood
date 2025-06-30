<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'variants'])
            ->where('status', 1)
            ->get();

        $filtered = $products->filter(function ($product) {
            // Nếu là sản phẩm có biến thể
            if ($product->product_type === 'variant') {
                return $product->variants->contains(function ($variant) {
                    return $variant->quantity_in_stock > 0;
                });
            }

            // Nếu là sản phẩm đơn giản, kiểm tra quantity_in_stock trực tiếp
            if ($product->product_type === 'simple') {
                return $product->quantity_in_stock > 0;
            }

            return false;
        });


        $page = $request->get('page', 1);
        $perPage = 9;
        $paginated = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = Category::withCount('products')->get();

        return view('clients.shop', [
            'products' => $paginated,
            'categories' => $categories,
        ]);
    }
    public function category(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // Lấy sản phẩm của danh mục, kèm biến thể
        $products = Product::with(['category', 'variants'])
            ->where('status', 1)
            ->where('category_id', $category->id)
            ->get();

        // Lọc tồn kho & lọc giá theo từng biến thể
        $filtered = $products->filter(function ($product) {
            if ($product->product_type === 'variant') {
                return $product->variants->contains(function ($variant) {
                    return $variant->quantity_in_stock > 0;
                });
            }

            if ($product->product_type === 'simple') {
                $variant = $product->variants->first();
                return $variant && $variant->quantity_in_stock > 0;
            }

            return false;
        });
        // Phân trang
        $page = $request->get('page', 1);
        $perPage = 9;
        $paginated = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = Category::withCount('products')->get();

        return view('clients.shop', [
            'products' => $paginated,
            'categories' => $categories,
            'category' => $category
        ]);
    }
}

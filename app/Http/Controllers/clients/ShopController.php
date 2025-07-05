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
            if ($product->product_type === 'variant') {
                return $product->variants->contains(function ($variant) {
                    return $variant->quantity_in_stock > 0;
                });
            }

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

        $categories = Category::withCount([
            'products as available_products_count' => function ($query) {
                $query->where('status', 1)
                    ->where(function ($q) {
                        $q->where(function ($q1) {
                            $q1->where('product_type', 'simple')
                                ->where('quantity_in_stock', '>', 0);
                        })->orWhere(function ($q2) {
                            $q2->where('product_type', 'variant')
                                ->whereHas('variants', function ($q3) {
                                    $q3->where('quantity_in_stock', '>', 0);
                                });
                        });
                    });
            }
        ])->get();


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
                return $product->quantity_in_stock > 0;
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

        $categories = Category::withCount([
            'products as available_products_count' => function ($query) {
                $query->where('status', 1)
                    ->where(function ($q) {
                        $q->where(function ($q1) {
                            $q1->where('product_type', 'simple')
                                ->where('quantity_in_stock', '>', 0);
                        })->orWhere(function ($q2) {
                            $q2->where('product_type', 'variant')
                                ->whereHas('variants', function ($q3) {
                                    $q3->where('quantity_in_stock', '>', 0);
                                });
                        });
                    });
            }
        ])->get();

        return view('clients.shop', [
            'products' => $paginated,
            'categories' => $categories,
            'category' => $category
        ]);
    }
}

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
        $featuredProducts = Product::with(['variants', 'category'])
            ->where('status', 1)
            ->inRandomOrder()
            ->take(6)
            ->get();

        $filtered = $products->filter(function ($product) use ($request) {
            $min = null;
            $max = null;

            // Xử lý theo request price_range
            if ($request->price_range && $request->price_range !== 'custom') {
                [$min, $max] = explode('-', $request->price_range);
            } elseif ($request->price_range === 'custom') {
                $min = $request->min_price;
                $max = $request->max_price;
            }

            $price = null;

            if ($product->product_type === 'variant') {
                $variant = $product->variants->firstWhere('quantity_in_stock', '>', 0);
                if (!$variant) return false;
                $price = $variant->discounted_price ?? $variant->price;
            } elseif ($product->product_type === 'simple') {
                if ($product->quantity_in_stock <= 0) return false;
                $price = $product->discounted_price ?? $product->original_price;
            }

            if ($min !== null && $max !== null) {
                return $price >= $min && $price <= $max;
            }

            return true;
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
            'featuredProducts' => $featuredProducts
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
        $featuredProducts = Product::with(['variants', 'category'])
            ->where('status', 1)
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Lọc tồn kho & lọc giá theo từng biến thể
        $filtered = $products->filter(function ($product) use ($request) {
            $min = null;
            $max = null;

            // Xử lý theo request price_range
            if ($request->price_range && $request->price_range !== 'custom') {
                [$min, $max] = explode('-', $request->price_range);
            } elseif ($request->price_range === 'custom') {
                $min = $request->min_price;
                $max = $request->max_price;
            }

            $price = null;

            if ($product->product_type === 'variant') {
                $variant = $product->variants->firstWhere('quantity_in_stock', '>', 0);
                if (!$variant) return false;
                $price = $variant->discounted_price ?? $variant->price;
            } elseif ($product->product_type === 'simple') {
                if ($product->quantity_in_stock <= 0) return false;
                $price = $product->discounted_price ?? $product->original_price;
            }

            if ($min !== null && $max !== null) {
                return $price >= $min && $price <= $max;
            }

            return true;
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
            'category' => $category,
            'featuredProducts' => $featuredProducts
        ]);
    }
}

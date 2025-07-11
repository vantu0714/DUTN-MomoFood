<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'admin') {
            return redirect('/admin/dashboard')->with('error', 'Admin không được phép truy cập trang chủ.');
        }

        $query = Product::with(['category', 'variants'])
            ->where(function ($q) {
                $q->where(function ($q1) {
                    $q1->where('product_type', 'simple')
                        ->where('quantity_in_stock', '>', 0)
                        ->where('status', 1);
                })->orWhere(function ($q2) {
                    $q2->where('product_type', 'variant')
                        ->where('status', 1)
                        ->whereHas('variants', function ($q3) {
                            $q3->where('quantity_in_stock', '>', 0);
                        });
                });
            });

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        $bestSellingProducts = Product::with('category')
            ->where('product_type', 'simple')
            ->where('status', 1)
            ->where('quantity_in_stock', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get();

        $comments = Comment::with('user')->hasRating()->latest()->take(10)->get();

        return view('clients.home', compact('products', 'categories', 'bestSellingProducts', 'comments'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = Product::where('product_name', 'like', "%$keyword%")
            ->orWhere('product_code', 'like', "%$keyword%")
            ->orWhere('description', 'like', "%$keyword%")
            ->orWhere('ingredients', 'like', "%$keyword%")
            ->get();

        return view('clients.search', compact('products', 'keyword'));
    }

    public function searchAjax(Request $request)
    {
        $keyword = $request->input('keyword');

        if (strlen($keyword) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::with('category')
            ->where('status', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%$keyword%")
                    ->orWhere('product_code', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%")
                    ->orWhere('ingredients', 'like', "%$keyword%");
            })
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
            })
            ->take(8)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->product_code,
                'price' => $product->price,
                'original_price' => $product->original_price ?? $product->price,
                'discount_percentage' => $product->discount_percentage ?? 0,
                'image' => $product->image_url,
                'category' => $product->category->name ?? '',
                'url' => route('product-detail.show', $product->id),
                'quantity_in_stock' => $product->quantity_in_stock ?? 0
            ];
        });

        return response()->json(['products' => $results]);
    }

    public function filterByCategory(Request $request)
    {
        $categoryId = $request->get('category');

        $products = Product::with(['category', 'variants'])
            ->where('status', 1)
            ->where(function ($q) {
                // Sản phẩm đơn còn hàng
                $q->where(function ($q1) {
                    $q1->where('product_type', 'simple')
                        ->where('quantity_in_stock', '>', 0);
                })
                    // hoặc sản phẩm có biến thể còn hàng
                    ->orWhere(function ($q2) {
                    $q2->where('product_type', 'variant')
                        ->whereHas('variants', function ($q3) {
                            $q3->where('quantity_in_stock', '>', 0);
                        });
                });
            })
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->latest()
            ->paginate(12);

        return view('clients.components.filtered-products', compact('products'))->render();
    }
}

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

        $query = Product::with([
            'category',
            'variants' => function ($q) {
                $q->select('id', 'product_id', 'price', 'quantity_in_stock', 'image', 'status');
                // KHÔNG lọc ở đây nữa, để blade xử lý disabled/mờ
            },
            'variants.attributeValues.attribute'
        ])
            ->where(function ($q) {
                $q->where(function ($q1) {
                    // sản phẩm đơn -> chỉ lấy còn hàng + hiển thị
                    $q1->where('product_type', 'simple')
                        ->where('status', 1)
                        ->where('quantity_in_stock', '>', 0);
                })->orWhere(function ($q2) {
                    // sản phẩm có biến thể -> chỉ lấy sp cha hiển thị
                    $q2->where('product_type', 'variant')
                        ->where('status', 1);
                });
            });


        // lọc theo danh mục nếu có
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(12);

        $categories = Category::withCount('products')->get();

        $bestSellingProducts = Product::with('category')
            ->where('status', 1)
            ->where('quantity_in_stock', '>', 0)
            ->where('sold_count', '>', 5)
            ->orderByDesc('sold_count')
            ->take(8)
            ->get();

        $highRatedProducts = Product::with([
            'comments',
            'category',
            'variants.attributeValues.attribute',
            'variants'
        ])
            ->withAvg(['comments as comments_avg_rating' => function ($q) {
                $q->whereNull('parent_id'); 
            }], 'rating')
            ->where('status', 1)
            ->having('comments_avg_rating', '>=', 4)
            ->orderByDesc('comments_avg_rating')
            ->take(6)
            ->get();


        $comments = Comment::with('user')->hasRating()->latest()->take(10)->get();

        return view('clients.home', compact(
            'products',
            'categories',
            'bestSellingProducts',
            'comments',
            'highRatedProducts'
        ));
    }


    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = Product::where('product_name', 'like', "%$keyword%")
            ->orWhere('product_code', 'like', "%$keyword%")
            ->orWhere('description', 'like', "%$keyword%")
            ->orWhere('ingredients', 'like', "%$keyword%")
            ->get();

        $categories = Category::withCount([
            'products as available_products_count' => function ($query) {
                $query->where('quantity_in_stock', '>', 0);
            }
        ])->get();

        return view('clients.search', compact('products', 'keyword', 'categories'));
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
            // Kiểm tra có giá khuyến mãi không
            $hasDiscount = !is_null($product->discounted_price) && $product->discounted_price > 0;

            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->product_code,
                'original_price' => $product->original_price,
                'discounted_price' => $product->discounted_price,
                'has_discount' => $hasDiscount,
                'image' => $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png'),
                'category' => $product->category ? $product->category->category_name : 'Chưa phân loại',
                'url' => route('product-detail.show', $product->id),
                'quantity_in_stock' => $product->quantity_in_stock ?? 0
            ];
        });

        return response()->json(['products' => $results]);
    }

    public function filterByCategory(Request $request)
    {
        $categoryId = $request->get('category');

        $products = Product::with(['category', 'variants.attributeValues.attribute']) // đủ thông tin

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

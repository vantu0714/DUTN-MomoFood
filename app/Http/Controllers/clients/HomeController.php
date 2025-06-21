<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
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
                // Điều kiện cho sản phẩm đơn
                $q->where(function ($q1) {
                    $q1->where('product_type', 'simple')
                        ->where('quantity', '>', 0)
                        ->where('status', 1);
                })
                    // Hoặc điều kiện cho sản phẩm có biến thể CÒN HÀNG
                    ->orWhere(function ($q2) {
                        $q2->where('product_type', 'variant')
                            ->where('status', 1)
                            ->whereHas('variants', function ($q3) {
                                $q3->where('quantity_in_stock', '>', 0);
                            });
                    });
            });


        // Lọc theo danh mục nếu có
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);

        $categories = Category::withCount('products')->get();

        // Đổi bestSellingProducts để không lấy sản phẩm hết hàng
        $bestSellingProducts = Product::with('category')
            ->where('product_type', 'simple')
            ->where('status', 1) // Đã đổi từ 'Còn hàng' sang 1
            ->where('quantity', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('clients.home', compact('products', 'categories', 'bestSellingProducts'));
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
}

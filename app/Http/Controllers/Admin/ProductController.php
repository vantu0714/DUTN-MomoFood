<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Lọc theo trạng thái sản phẩm
        if ($request->filled('status')) {
            $statusFilter = $request->input('status');

            if ($statusFilter === 'Còn hàng') {
                $query->where('status', 'Còn hàng');
            } elseif ($statusFilter === 'Hết hàng') {
                $query->where('status', 'Hết hàng');
            }
        }

        // Lọc theo danh mục (nếu có)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Lấy tổng số sản phẩm "Còn hàng" từ toàn bộ DB
        // Điều này là cần thiết vì $products đã được phân trang
        $availableProductsCount = Product::where('status', 'Còn hàng')->count();

        // Lấy tổng số sản phẩm "Hết hàng" từ toàn bộ DB (tương tự)
        $outOfStockProductsCount = Product::where('status', 'Hết hàng')->count();

          
        $products = $query->paginate(10);

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories', 'availableProductsCount', 'outOfStockProductsCount'));
    }
}
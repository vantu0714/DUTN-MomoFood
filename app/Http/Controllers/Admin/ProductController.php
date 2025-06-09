<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
     public function index()
    {
        // Lấy tất cả sản phẩm, kèm theo thông tin category (nếu có) và phân trang
        $products = Product::with('categories')->paginate(10); // Hiển thị 10 sản phẩm mỗi trang
        return view('admin.products.index', compact('products'));
    }
}

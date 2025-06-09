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
    $products = Product::with('category')->paginate(10); // đúng tên quan hệ là 'category'

    return view('admin.products.index', compact('products'));
}

}

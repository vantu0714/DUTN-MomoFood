<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartClientController extends Controller
{
    public function index()
    {
        // Sửa lỗi bằng cách khai báo biến $carts trước khi dùng compact
        $carts = session()->get('cart', []); // Nếu chưa có gì trong session thì là mảng rỗng

        return view('clients.carts.index', compact('carts'));
    }
}

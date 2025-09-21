<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturnItem;

class ReturnController extends Controller
{
    public function index()
    {
        // Lấy tất cả hàng hoàn
        $returnedItems = OrderReturnItem::with(['order', 'orderDetail'])->get();

        return view('admin.returns.index', compact('returnedItems'));
    }
}

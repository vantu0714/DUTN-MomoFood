<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Comment;

class CommentController extends Controller
{
    // Danh sách sản phẩm có bình luận
    public function index()
{
    $products = Product::whereHas('comments')
        ->withCount('comments')
        ->with(['comments', 'variants.attributeValues.attribute']) // load thêm biến thể
        ->get();

    return view('admin.comments.index', compact('products'));
}


    // Xem chi tiết bình luận của một sản phẩm
    public function show(Product $product)
    {
        $comments = $product->comments()->with('user')->latest()->get();
        return view('admin.comments.show', compact('product', 'comments'));
    }

    // Toggle trạng thái ẩn/hiện bình luận
    public function toggleStatus(Comment $comment)
    {
        $comment->status = $comment->status == 1 ? 0 : 1;
        $comment->save();

        return 
        back()->with('success', 'Cập nhật trạng thái bình luận thành công!');
    }
    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('user', 'product')->latest()->get();
        return view('admin/comments.index', compact('comments'));
    }

    public function create()
    {
        $users = User::all();
        $products = Product::all();
        return view('comments.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'content' => 'required|string',
        ]);

        Comment::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'content' => $request->content,
        ]);

        return redirect()->route('comments.index')->with('success', 'Thêm bình luận thành công!');
    }

    public function show(Comment $comment)
    {
        return view('comments.show', compact('comment'));
    }

    public function edit(Comment $comment)
    {
        $users = User::all();
        $products = Product::all();
        return view('comments.edit', compact('comment', 'users', 'products'));
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'content' => 'required|string',
        ]);

        $comment->update([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'content' => $request->content,
        ]);

        return redirect()->route('comments.index')->with('success', 'Cập nhật bình luận thành công!');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('comments.index')->with('success', 'Xoá bình luận thành công!');
    }
}

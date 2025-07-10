<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        // Kiểm tra người dùng đã mua sản phẩm chưa
        $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('status', '!=', 'cancelled'); // tuỳ status
        })->where('product_id', $productId)->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Bạn cần mua sản phẩm này trước khi đánh giá.');
        }

        // Kiểm tra đã đánh giá chưa
        $alreadyRated = \App\Models\Comment::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($alreadyRated) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        Comment::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'content' => $request->content,
            'rating' => $request->rating,
        ]);

        return back()->with('success', 'Bình luận đã được gửi!');
    }
}

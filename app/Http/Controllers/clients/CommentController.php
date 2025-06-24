<?php 

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5', // ✅ validate rating
        ]);
 // comnet
        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'content' => $request->content,
            'rating' => $request->rating, // ✅ thêm dòng này để lưu rating
        ]);

        return back()->with('success', 'Bình luận đã được gửi!');
    }
}

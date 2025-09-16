<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận mới (có ảnh/video).
     */
    public function store(Request $request)
    {
        $request->validate([
            'content'    => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'images.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video'      => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:10240',
        ]);

        $userId    = Auth::id();
        $productId = $request->product_id;

        // Kiểm tra đã mua hàng chưa
        $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('status', 4); // 4 = đã hoàn thành
        })->where('product_id', $productId)->exists();

        if (!$hasPurchased) {
            return back()->with('error', '⚠️ Bạn cần mua sản phẩm này trước khi đánh giá.');
        }

        // Kiểm tra đã đánh giá chưa
        $alreadyRated = Comment::where('user_id', $userId)
            ->where('product_id', $productId)
            ->whereNull('parent_id') // chỉ tính bình luận gốc
            ->exists();

        if ($alreadyRated) {
            return back()->with('error', '⚠️ Bạn đã đánh giá sản phẩm này rồi.');
        }

        // Upload video nếu có
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('comments/videos', 'public');
        }

        // Tạo bình luận gốc
        $comment = Comment::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'content'    => $request->content,
            'rating'     => $request->rating,
            'video'      => $videoPath,
            'status'     => 1, // mặc định hiển thị
        ]);

        // Lưu ảnh nếu có
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('comments/images', 'public');
                $comment->images()->create(['path' => $path]);
            }
        }

        return back()->with('success', ' Bình luận đã được gửi thành công!');
    }
    public function loadMore(Request $request)
    {
        $productId = $request->product_id;
        $offset    = $request->offset ?? 0;

        $comments = Comment::with(['user', 'images', 'replies.user'])
            ->where('product_id', $productId)
            ->where('status', 1)
            ->whereNull('parent_id')
            ->latest()
            ->skip($offset)
            ->take(5)
            ->get();

        return response()->json([
            'comments' => view('clients.partials.comment_list', compact('comments'))->render()
        ]);
    }
    public function reply(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $parent = Comment::findOrFail($commentId);

        $reply = Comment::create([
            'user_id'    => Auth::id(),
            'product_id' => $parent->product_id,
            'parent_id'  => $parent->id,
            'content'    => $request->content,
            'status'     => 1,
        ]);

        return back()->with('success', '💬 Trả lời đã được gửi thành công!');
    }
}

<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\OrderDetail;
use App\Models\Image; // model Image
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content'    => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'rating'     => 'required|integer|min:1|max:5',
            'images.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video'      => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:10240',
        ]);

        $userId    = Auth::id();
        $productId = $request->product_id;
        $variantId = $request->product_variant_id;

        // ✅ Kiểm tra đã mua sản phẩm/biến thể chưa
        $hasPurchased = OrderDetail::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', 4); // 4 = đã hoàn thành
            })
            ->where('product_id', $productId)
            ->when($variantId, function ($q) use ($variantId) {
                $q->where('product_variant_id', $variantId);
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', '⚠️ Bạn cần mua sản phẩm/biến thể này trước khi đánh giá.');
        }

        // ✅ Kiểm tra đã đánh giá chưa (theo sản phẩm + biến thể)
        $alreadyRated = Comment::where('user_id', $userId)
            ->where('product_id', $productId)
            ->when($variantId, function ($q) use ($variantId) {
                $q->where('product_variant_id', $variantId);
            })
            ->exists();

        if ($alreadyRated) {
            return back()->with('error', '⚠️ Bạn đã đánh giá sản phẩm/biến thể này rồi.');
        }

        // Upload video nếu có
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('comments/videos', 'public');
        }

        // ✅ Lưu comment kèm biến thể
        $comment = Comment::create([
            'user_id'           => $userId,
            'product_id'        => $productId,
            'product_variant_id'=> $variantId, // lưu thêm
            'content'           => $request->content,
            'rating'            => $request->rating,
            'video'             => $videoPath,
            'status'            => 1,
        ]);

        // Upload nhiều ảnh
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('comments/images', 'public');
                $comment->images()->create(['path' => $path]);
            }
        }

        return back()->with('success', '✅ Bình luận đã được gửi thành công!');
    }
}

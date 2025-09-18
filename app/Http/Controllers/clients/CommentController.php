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
            'order_id'          => 'required|exists:orders,id',
            'content'    => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'order_detail_id' => 'required|exists:order_details,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'rating'     => 'required|integer|min:1|max:5',
            'images.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images'              => 'nullable|array|max:5',
            'video'      => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:10240',
        ]);

        $userId    = Auth::id();
        $productId = $request->product_id;
        $variantId = $request->product_variant_id;

        // ✅ Kiểm tra sản phẩm có thuộc đơn hàng đã hoàn thành của user không
        $hasPurchased = OrderDetail::where('id', $request->order_detail_id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', 4); // 4 = đã hoàn thành
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', '⚠️ Bạn cần mua sản phẩm này trong đơn hàng đã hoàn thành trước khi đánh giá.');
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
            'product_variant_id' => $variantId,
            'order_detail_id'    => $request->order_detail_id,
            'order_id'           => $request->order_id,
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

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}

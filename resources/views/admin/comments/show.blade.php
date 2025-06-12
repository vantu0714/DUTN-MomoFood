@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Chi tiết bình luận #{{ $comment->id }}</h2>

    <div class="card p-3">
        <p><strong>Người bình luận:</strong> {{ $comment->user->name ?? 'Ẩn danh' }}</p>
        <p><strong>Nội dung:</strong> {{ $comment->content }}</p>
        <p><strong>Sản phẩm/Bài viết:</strong> {{ $comment->product->name ?? 'Không xác định' }}</p>
        <p><strong>Trạng thái:</strong> 
            @if($comment->status == 1)
                <span class="badge bg-success">Đã duyệt</span>
            @else
                <span class="badge bg-secondary">Chờ duyệt</span>
            @endif
        </p>
        <p><strong>Ngày tạo:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Ngày cập nhật:</strong> {{ $comment->updated_at->format('d/m/Y H:i') }}</p>

        <a href="{{ route('comments.index') }}" class="btn btn-secondary">Quay lại</a>
        <a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-warning">Sửa</a>
    </div>
</div>
@endsection

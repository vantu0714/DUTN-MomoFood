@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách bình luận</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
  <table class="table table-bordered">
    <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>Người dùng</th>
            <th>Nội dung</th>
            <th>Ngày</th>
            <th>Xóa</th>
        </tr>
    </thead>
    <tbody>
        @foreach($comments as $comment)
        <tr>
            <td>{{ $comment->product->name }}</td>
            <td>{{ $comment->user->name }}</td>
            <td>{{ $comment->content }}</td>
            <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <form action="/admin/comments/{{ $comment->id }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

// form client bình luận sau product
<h4>Bình luận:</h4>
{{-- @auth
<form action="/product/{{ $product->id }}/comment" method="POST">
    @csrf
    <textarea name="content" class="form-control mb-2" rows="3" required></textarea>
    <button class="btn btn-primary">Gửi bình luận</button>
</form>
@else
<p><a href="/login">Đăng nhập</a> để bình luận</p>
@endauth

@foreach ($product->comments as $comment)
    <div class="border p-2 my-2">
        <strong>{{ $comment->user->name }}</strong>:
        <p>{{ $comment->content }}</p>
        <small>{{ $comment->created_at->diffForHumans() }}</small>
    </div>
@endforeach  --}}

    </div>
</div>
@endsection

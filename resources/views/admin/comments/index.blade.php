@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách bình luận</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Người bình luận</th>
                    <th>Nội dung</th>
                    <th>Sản phẩm/Bài viết</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comments as $comment)
                <tr>
                    <td>{{ $comment->id }}</td>
                    <td>{{ $comment->user->name ?? 'Ẩn danh' }}</td>
                    <td>{{ Str::limit($comment->content, 50) }}</td>
                    <td>{{ $comment->product->name ?? 'Không xác định' }}</td>
                    <td>
                        @if($comment->status == 1)
                            <span class="badge bg-success">Đã duyệt</span>
                        @else
                            <span class="badge bg-secondary">Chờ duyệt</span>
                        @endif
                    </td>
                    <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="{{ route('comments.show', $comment->id) }}" class="btn btn-sm btn-info">Xem</a>
                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Xoá bình luận này?')">Xoá</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Không có bình luận nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

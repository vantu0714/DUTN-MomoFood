@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách bình luận</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover" >
        <thead>
            <tr>
                <th>#</th>
                <th>Người dùng</th>
                <th>Sản phẩm</th>
                <th>Nội dung</th>
                <th>Số sao</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comments as $key => $comment)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $comment->user->email ?? 'Ẩn danh' }}</td>
                    <td>{{ $comment->product->product_name ?? 'Không tìm thấy' }}</td>
                    <td>{{ $comment->content }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= $comment->rating ? 'text-warning' : 'text-secondary' }}"></i>
                        @endfor
                    </td>
                    <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Chưa có bình luận nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        {{ $comments->links() }}
    </div>
</div>
@endsection

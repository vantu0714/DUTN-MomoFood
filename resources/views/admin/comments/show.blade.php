@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4 fw-bold text-primary">Bình luận cho sản phẩm: {{ $product->product_name }}</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @forelse($comments as $comment)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1"><strong>Người dùng:</strong> {{ $comment->user->email ?? 'Ẩn danh' }}</p>
                            <p class="mb-1"><strong>Nội dung:</strong> {{ $comment->content }}</p>
                            <p class="mb-1"><strong>Số sao:</strong>
                                @php
                                    $rating = is_numeric($comment->rating) ? (int) $comment->rating : 0;
                                @endphp
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star" style="color: {{ $i <= $rating ? '#ffc107' : '#ccc' }}"></i>
                                @endfor
                            </div>
                            </p>
                            <p class="mb-2">
                                <strong>Trạng thái:</strong>
                                @if ($comment->status)
                                    <span class="badge bg-success">Đang hiển thị</span>
                                @else
                                    <span class="badge bg-secondary">Đã ẩn</span>
                                @endif
                            </p>
                        </div>

                        <form action="{{ route('admin.comments.toggle', $comment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-sm {{ $comment->status ? 'btn-warning' : 'btn-success' }}">
                                {{ $comment->status ? 'Ẩn bình luận' : 'Hiện bình luận' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Không có bình luận nào cho sản phẩm này.</div>
        @endforelse
    </div>
@endsection

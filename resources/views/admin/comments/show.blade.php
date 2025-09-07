@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-info">
                💬 Bình luận cho sản phẩm: <span class="text-dark">{{ $product->product_name }}</span>
            </h3>

            <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
                ⬅ Quay lại
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-3 shadow-sm">{{ session('success') }}</div>
        @endif

        @forelse($comments as $comment)
            <div class="card mb-4 shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-start">
                        {{-- Nội dung bình luận --}}
                        <div class="col-md-9">
                            <p class="mb-2"><strong>👤 Người dùng:</strong> {{ $comment->user->email ?? 'Ẩn danh' }}</p>

                            <p class="mb-2"><strong>✍ Nội dung:</strong> {{ $comment->content }}</p>

                            {{-- Hiển thị video + hình ảnh nếu có --}}
                            @if ($comment->video || $comment->images->count() > 0)
                                <div class="mb-3">
                                    <strong>🎥 Video & 🖼 Hình ảnh:</strong><br>
                                    <div class="d-grid gap-2 mt-2"
                                        style="grid-template-columns: repeat(5, 1fr); max-width: 1000px;">

                                        {{-- Video trước --}}
                                        @if ($comment->video)
                                            <video controls class="rounded shadow-sm w-100"
                                                style="height: 120px; object-fit: cover;">
                                                <source src="{{ asset('storage/' . $comment->video) }}" type="video/mp4">
                                                Trình duyệt của bạn không hỗ trợ video.
                                            </video>
                                        @endif

                                        {{-- Sau đó đến ảnh --}}
                                        @foreach ($comment->images as $img)
                                            <img src="{{ asset('storage/' . $img->path) }}" alt="Hình ảnh bình luận"
                                                class="img-thumbnail rounded w-100"
                                                style="height: 120px; object-fit: cover;">
                                        @endforeach
                                    </div>
                                </div>
                            @endif


                            <p class="mb-2"><strong>⭐ Số sao:</strong></p>
                            <div class="mb-3">
                                @php
                                    $rating = is_numeric($comment->rating) ? (int) $comment->rating : 0;
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star fa-lg"
                                        style="color: {{ $i <= $rating ? '#ffc107' : '#e4e5e9' }}"></i>
                                @endfor
                            </div>

                            <p class="mb-0">
                                <strong>📌 Trạng thái:</strong>
                                @if ($comment->status)
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Đang hiển thị</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">Đã ẩn</span>
                                @endif
                            </p>
                        </div>

                        {{-- Nút toggle --}}
                        <div class="col-md-3 text-end">
                            <form action="{{ route('admin.comments.toggle', $comment->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button
                                    class="btn btn-sm {{ $comment->status ? 'btn-warning' : 'btn-success' }} rounded-pill shadow-sm">
                                    {{ $comment->status ? 'Ẩn bình luận' : 'Hiện bình luận' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="alert alert-info rounded-3 shadow-sm">
                Không có bình luận nào cho sản phẩm này.
            </div>
        @endforelse
    </div>
@endsection

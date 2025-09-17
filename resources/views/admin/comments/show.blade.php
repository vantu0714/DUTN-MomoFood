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
                        <div class="col-md-9">
                            <p class="mb-2"><strong>👤 Người dùng:</strong> {{ $comment->user->email ?? 'Ẩn danh' }}</p>
                            <p class="mb-2"><strong>✍ Nội dung:</strong> {{ $comment->content }}</p>

                            {{-- Form trả lời --}}
                            <div class="mt-3">
                                <form action="{{ route('admin.comments.reply', $comment->id) }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" name="content" class="form-control"
                                            placeholder="Nhập câu trả lời của admin...">
                                        <button type="submit" class="btn btn-primary">Trả lời</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Replies --}}
                            @if ($comment->replies->count() > 0)
                                <div class="mt-3 ms-4 border-start ps-3">
                                    <h6 class="fw-bold">Phản hồi:</h6>
                                    @foreach ($comment->replies as $reply)
                                        <div class="mb-2">
                                            <strong>{{ $reply->user->name ?? 'Admin' }}</strong>:
                                            {{ $reply->content }}
                                            <br>
                                            <small class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- ⭐ Số sao --}}
                            <p class="mb-2"><strong>⭐ Số sao:</strong></p>
                            <div class="mb-3">
                                @php $rating = (int) ($comment->rating ?? 0); @endphp
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

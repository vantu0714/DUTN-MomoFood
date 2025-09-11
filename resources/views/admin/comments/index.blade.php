@extends('admin.layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4 text-info fw-bold">📦 Danh sách sản phẩm có bình luận</h2>

        @if (session('success'))
            <div class="alert alert-success rounded-3 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm rounded bg-white">
                <thead class="table-info text-center">
                    <tr>
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Số bình luận</th>
                        <th>Bình luận có Media</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($products as $key => $product)
                        <tr>
                            <td class="fw-semibold">{{ $key + 1 }}</td>

                            {{-- Hình sản phẩm --}}
                            <td>
                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                            </td>

                            {{-- Tên sản phẩm + biến thể --}}
                            <td class="text-start">
                                <strong>{{ $product->product_name }}</strong>

                                {{-- Nếu có biến thể thì hiển thị --}}
                                @php
                                    $variant = $product->variants->first();
                                @endphp

                                @if ($variant)
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @foreach ($variant->attributeValues as $val)
                                            @if (strtolower($val->attribute->name) === 'màu' || strtolower($val->attribute->name) === 'color')
                                                {{-- Nếu là màu thì hiển thị ô màu --}}
                                                <span class="d-inline-block border rounded-circle"
                                                    style="width: 18px; height: 18px; background-color: {{ strtolower($val->value) }};"
                                                    title="{{ $val->attribute->name }}: {{ $val->value }}">
                                                </span>
                                            @else
                                                {{-- Các thuộc tính khác thì hiển thị badge --}}
                                                <span class="badge bg-info">
                                                    {{ $val->attribute->name }}: {{ $val->value }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- Tổng số bình luận --}}
                            <td>
                                <span class="badge bg-primary px-3 py-2">
                                    {{ $product->comments_count }}
                                </span>
                            </td>

                            {{-- Có hình ảnh/video không --}}
                            <td>
                                @php
                                    $hasMedia = $product->comments->contains(function ($c) {
                                        return $c->image || $c->video;
                                    });
                                @endphp

                                @if ($hasMedia)
                                    <span class="badge bg-success">Có media</span>
                                @else
                                    <span class="badge bg-secondary">Chỉ văn bản</span>
                                @endif
                            </td>

                            {{-- Nút xem chi tiết --}}
                            <td>
                                <a href="{{ route('admin.comments.show', $product->id) }}"
                                    class="btn btn-sm btn-outline-info rounded-pill">
                                    <i class="fas fa-comments me-1"></i> Xem bình luận
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    @if ($products->isEmpty())
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Không có sản phẩm nào có bình luận.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

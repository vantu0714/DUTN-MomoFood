@extends('admin.layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4 text-info fw-bold">📦 Sản phẩm có bình luận</h2>

        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm rounded bg-white">
                <thead class="table-info text-center">
                    <tr>
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Số bình luận</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($products as $key => $product)
                        <tr>
                            <td class="fw-semibold">{{ $key + 1 }}</td>
                            <td>
                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td class="text-start">
                                {{ $product->product_name }}

                                {{-- Nếu có biến thể thì hiển thị --}}
                                @php
                                    $variant = $product->variants->first();
                                @endphp

                                @if ($variant)
                                    @php
                                        $variantText = $variant->attributeValues
                                            ->map(fn($val) => $val->attribute->name . ': ' . $val->value)
                                            ->implode(', ');
                                    @endphp

                                    <p class="text-muted fst-italic">
                                        {{ $variantText }}
                                    </p>
                                @endif

                            </td>

                            <td>
                                <span class="badge bg-info text-dark">{{ $product->comments_count }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.comments.show', $product->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-comments me-1"></i> Xem bình luận
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    @if ($products->isEmpty())
                        <tr>
                            <td colspan="5" class="text-muted text-center">Không có sản phẩm nào có bình luận.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

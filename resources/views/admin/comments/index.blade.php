@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-info fw-bold">📦 Sản phẩm có bình luận</h2>

    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm rounded bg-white">
            <thead class="table-info text-center">
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Khối lượng</th>
                    <th>Số bình luận</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($products as $key => $product)
                    <tr>
                        <td class="fw-semibold">{{ $key + 1 }}</td>
                        <td class="text-start">{{ $product->product_name }}</td>
                        <td>{{ $product->quantity_in_stock }} gram</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $product->comments_count }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.comments.show', $product->id) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-comments me-1"></i> Xem bình luận
                            </a>
                        </td>
                    </tr>
                @endforeach

                @if($products->isEmpty())
                    <tr>
                        <td colspan="5" class="text-muted text-center">Không có sản phẩm nào có bình luận.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

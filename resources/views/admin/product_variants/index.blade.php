@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách sản phẩm biến thể</h2>

    @foreach ($groupedVariants as $productId => $variants)
        @php
            $product = $variants->first()->product;
        @endphp

        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <strong>{{ $product->product_name }}</strong>
                <span>Mã SP: {{ $product->product_code }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Biến thể</th>
                            <th>SKU</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Ảnh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $variant)
                            <tr>
                                <td>
                                    @if ($variant->attributeValues->count())
                                        @foreach ($variant->attributeValues as $val)
                                            <span class="badge bg-secondary">{{ $val->attribute->name }}: {{ $val->value }}</span>
                                        @endforeach
                                    @else
                                        <em>Không có</em>
                                    @endif
                                </td>
                                <td>{{ $variant->sku ?? '-' }}</td>
                                <td>{{ number_format($variant->price, 0, ',', '.') }} đ</td>
                                <td>{{ $variant->quantity_in_stock }}</td>
                                <td>
                                    @if ($variant->image)
                                        <img src="{{ asset('storage/' . $variant->image) }}" alt="Ảnh" width="60">
                                    @else
                                        <em>Không có</em>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.product_variants.edit', $variant->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                                    <form action="{{ route('admin.product_variants.destroy', $variant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xoá?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="mt-4">
        {{ $variantsPaginated->links() }}
    </div>
</div>
@endsection

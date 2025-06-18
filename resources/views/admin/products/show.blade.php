@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white border-bottom-0">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-box text-primary me-2"></i>Chi tiết sản phẩm: {{ $product->product_name }}
            </h5>
        </div>
        <div class="card-body row">
            <div class="col-md-4">
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow" alt="Ảnh sản phẩm">
            </div>
            <div class="col-md-8">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Mã sản phẩm:</th>
                        <td>{{ $product->product_code }}</td>
                    </tr>
                    <tr>
                        <th>Danh mục:</th>
                        <td>{{ $product->category->category_name }}</td>
                    </tr>
                    <tr>
                        <th>Mô tả:</th>
                        <td>{{ $product->description }}</td>
                    </tr>
                    <tr>
                        <th>Thành phần:</th>
                        <td>{{ $product->ingredients }}</td>
                    </tr>
                    <tr>
                        <th>Ngày hết hạn:</th>
                        <td>{{ optional($product->expiration_date)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Giá:</th>
                        <td>
                            <strong>{{ number_format($product->original_price) }}đ</strong>
                            @if($product->discounted_price)
                                <span class="text-danger ms-3">{{ number_format($product->discounted_price) }}đ</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Số lượng:</th>
                        <td>{{ $product->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if ($product->quantity > 0)
                                <span class="text-success fw-bold">Còn hàng</span>
                            @else
                                <span class="text-danger fw-bold">Hết hàng</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Danh sách biến thể -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0">
            <h6 class="fw-semibold text-primary">
                <i class="fas fa-layer-group me-2"></i>Biến thể sản phẩm
            </h6>
        </div>
        <div class="card-body p-0">
            @if($product->variants->isEmpty())
                <div class="p-3 text-muted">Sản phẩm này chưa có biến thể nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Ảnh</th>
                                <th>SKU</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th>Thuộc tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                                <tr>
                                    <td>
                                        @if($variant->image)
                                            <img src="{{ asset('storage/' . $variant->image) }}" width="50" height="50" class="rounded shadow-sm" style="object-fit: cover;">
                                        @else
                                            <span class="text-muted">Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $variant->sku }}</td>
                                    <td>{{ number_format($variant->price) }}đ</td>
                                    <td>{{ $variant->quantity_in_stock }}</td>
                                    <td>
                                        @if($variant->quantity_in_stock > 0)
                                            <span class="text-success">Còn hàng</span>
                                        @else
                                            <span class="text-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($variant->attributeValues as $attrValue)
                                            <span class="badge bg-light text-dark border rounded me-1">
                                                {{ $attrValue->attribute->name }}: {{ $attrValue->value }}
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

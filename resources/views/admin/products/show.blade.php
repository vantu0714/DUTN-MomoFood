@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Breadcrumb và nút quay lại -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}"
                                class="text-decoration-none">Sản phẩm</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <!-- Header với tên sản phẩm -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-gradient-primary text-white p-4 rounded-3 shadow-lg">
                    <h3 class="mb-0 fw-bold text-white">
                        <i class="fas fa-box me-2"></i>{{ $product->product_name }}
                    </h3>
                    <p class="mb-0 mt-2 text-white-50">Mã sản phẩm: {{ $product->product_code }}</p>
                </div>
            </div>
        </div>

        <!-- Thông tin chính sản phẩm -->
        <div class="row mb-4">
            <div class="col-lg-5 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="position-relative d-inline-block">
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded-3 shadow-lg"
                                alt="Ảnh sản phẩm" style="max-height: 300px; object-fit: cover;">
                            @if ($product->quantity_in_stock <= 0)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger fs-6">Hết hàng</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-6">
                <div class="card h-100 shadow-lg border-0 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white border-0 position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-10"></div>
                        <h6 class="mb-0 fw-bold text-white position-relative">
                            <i class="fas fa-info-circle me-2 text-white-50"></i>Thông tin chi tiết
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <!-- Category -->
                            <div class="col-sm-6">
                                <div class="info-item">
                                    <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                        <i class="fas fa-tag me-1"></i>Danh mục
                                    </label>
                                    <div class="fw-semibold">
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle fs-6 px-3 py-2 rounded-pill">
                                            {{ $product->category->category_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Quantity -->
                            <div class="col-sm-6">
                                <div class="info-item">
                                    <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                        <i class="fas fa-boxes me-1"></i>Số lượng tồn kho
                                    </label>
                                    <div class="fw-bold fs-5 d-flex align-items-center">
                                        @if ($product->quantity_in_stock > 10)
                                            <span class="text-success me-2">{{ $product->quantity_in_stock }}</span>
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($product->quantity_in_stock > 0)
                                            <span class="text-warning me-2">{{ $product->quantity_in_stock }}</span>
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        @else
                                            <span class="text-danger me-2">0</span>
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Price and Status Section -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                                <i class="fas fa-money-bill-wave me-1"></i>Giá bán
                                            </label>
                                            <div
                                                class="price-display p-2 bg-light rounded-3 border-start border-3 border-primary">
                                                @if ($product->product_type === 'variant' && $product->variants->count())
                                                    @php
                                                        $minPrice = $product->variants->min('price');
                                                        $maxPrice = $product->variants->max('price');
                                                    @endphp

                                                    @if ($minPrice === $maxPrice)
                                                        <span
                                                            class="text-primary fw-bold fs-5">{{ number_format($minPrice) }}đ</span>
                                                    @else
                                                        <span class="text-primary fw-bold fs-5">
                                                            {{ number_format($minPrice) }}đ -
                                                            {{ number_format($maxPrice) }}đ
                                                        </span>
                                                    @endif
                                                @else
                                                    @if ($product->discounted_price)
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            <span
                                                                class="text-decoration-line-through text-secondary small me-2">
                                                                {{ number_format($product->original_price) }}đ
                                                            </span>
                                                            <span class="text-danger fw-bold fs-5 me-2">
                                                                {{ number_format($product->discounted_price) }}đ
                                                            </span>
                                                            <span
                                                                class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill small">
                                                                -{{ round((($product->original_price - $product->discounted_price) / $product->original_price) * 100) }}%
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-primary fw-bold fs-5">
                                                            {{ number_format($product->original_price) }}đ
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                                <i class="fas fa-clipboard-check me-1"></i>Trạng thái
                                            </label>
                                            <div class="status-display p-2 bg-light rounded-3">
                                                @if ($product->status == 0)
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fs-6 px-3 py-2 rounded-pill">
                                                        <i class="fas fa-eye-slash me-1"></i>Ẩn
                                                    </span>
                                                @else
                                                    @if ($product->quantity_in_stock > 0)
                                                        <span
                                                            class="badge bg-success-subtle text-success border border-success-subtle fs-6 px-3 py-2 rounded-pill">
                                                            <i class="fas fa-check-circle me-1"></i>Còn hàng
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 px-3 py-2 rounded-pill">
                                                            <i class="fas fa-times-circle me-1"></i>Hết hàng
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Expiration Date -->
                            @if ($product->expiration_date)
                                <div class="col-sm-6">
                                    <div class="info-item">
                                        <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                            <i class="fas fa-calendar-alt me-1"></i>Ngày hết hạn
                                        </label>
                                        <div
                                            class="fw-semibold text-dark bg-warning-subtle p-2 rounded-2 border border-warning-subtle">
                                            <i class="fas fa-clock me-2 text-warning"></i>
                                            {{ $product->expiration_date->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Origin -->
                            @if ($product->origin)
                                <div class="col-sm-6">
                                    <div class="info-item">
                                        <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                            <i class="fas fa-map-marker-alt me-1"></i>Xuất xứ
                                        </label>
                                        <div
                                            class="fw-semibold text-dark bg-info-subtle p-2 rounded-2 border border-info-subtle">
                                            <i class="fas fa-globe-asia me-2 text-info"></i>
                                            {{ $product->origin->name }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Description -->
                            @if ($product->description)
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label text-muted fw-semibold mb-2 small text-uppercase">
                                            <i class="fas fa-align-left me-1"></i>Mô tả
                                        </label>
                                        <div
                                            class="p-3 bg-light rounded-3 border-start border-3 border-info position-relative">
                                            <div class="position-absolute top-0 end-0 p-2">
                                                <i class="fas fa-quote-right text-muted opacity-25 fs-4"></i>
                                            </div>
                                            <p class="mb-0 text-dark lh-lg">{{ $product->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer with subtle gradient -->
                    <div class="card-footer bg-light border-0 text-center py-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Thông tin được cập nhật tự động
                        </small>
                    </div>
                </div>
            </div>


        </div>

        <!-- Danh sách biến thể -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-light border-bottom-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold text-primary mb-0">
                        <i class="fas fa-layer-group me-2"></i>Biến thể sản phẩm
                    </h6>
                    <span class="badge bg-primary">{{ $product->variants->count() }} biến thể</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($product->variants->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-boxes text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted">Chưa có biến thể</h6>
                        <p class="text-muted mb-0">Sản phẩm này chưa có biến thể nào.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 text-dark fw-bold">Ảnh</th>
                                    <th class="border-0 text-dark fw-bold">SKU</th>
                                    <th class="border-0 text-dark fw-bold">Giá</th>
                                    <th class="border-0 text-dark fw-bold">Số lượng</th>
                                    <th class="border-0 text-dark fw-bold">Trạng thái</th>
                                    <th class="border-0 text-dark fw-bold">Thuộc tính</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->variants as $variant)
                                    <tr class="align-middle">
                                        <td class="py-3">
                                            @if ($variant->image)
                                                <div class="position-relative">
                                                    <img src="{{ asset('storage/' . $variant->image) }}" width="60"
                                                        height="60" class="rounded-3 shadow-sm border"
                                                        style="object-fit: cover;">
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded-3 border"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <code
                                                class="bg-dark text-white px-2 py-1 rounded fw-semibold">{{ $variant->sku }}</code>
                                        </td>
                                        <td class="py-3">
                                            <span
                                                class="fw-semibold text-primary">{{ number_format($variant->price) }}đ</span>
                                        </td>
                                        <td class="py-3">
                                            @if ($variant->quantity_in_stock > 10)
                                                <span
                                                    class="text-success fw-semibold">{{ $variant->quantity_in_stock }}</span>
                                            @elseif($variant->quantity_in_stock > 0)
                                                <span
                                                    class="text-warning fw-semibold">{{ $variant->quantity_in_stock }}</span>
                                            @else
                                                <span class="text-danger fw-semibold">0</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if ($variant->status == 0)
                                                <span class="badge bg-secondary text-white border">
                                                    <i class="fas fa-eye-slash me-1"></i>Ẩn
                                                </span>
                                            @else
                                                @if ($variant->quantity_in_stock > 0)
                                                    <span class="badge bg-success text-white border">
                                                        <i class="fas fa-check-circle me-1"></i>Còn hàng
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger text-white border">
                                                        <i class="fas fa-times-circle me-1"></i>Hết hàng
                                                    </span>
                                                @endif
                                            @endif
                                        </td>

                                        <td class="py-3">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($variant->attributeValues as $attrValue)
                                                    <span
                                                        class="badge bg-secondary text-white border rounded-pill px-3 py-2">
                                                        <small
                                                            class="text-light">{{ $attrValue->attribute->name }}:</small>
                                                        <strong class="ms-1">{{ $attrValue->value }}</strong>
                                                    </span>
                                                @endforeach
                                            </div>
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

@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    Chỉnh sửa sản phẩm
                </h1>
                <p class="text-muted mb-0">Cập nhật thông tin sản phẩm: {{ $product->product_name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    Quay lại
                </a>
                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye me-1"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <!-- Product Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow-sm"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Product Image">
                            </div>
                            <div class="col-md-10">
                                <h5 class="mb-1">{{ $product->product_name }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($product->description, 100) }}</p>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-primary">ID: #{{ $product->id }}</span>
                                    <span class="badge bg-info">{{ $product->category->category_name }}</span>
                                    <span class="badge {{ $product->quantity_in_stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->quantity_in_stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Form -->
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                Thông tin cơ bản
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="product_name" class="form-label fw-semibold">Tên sản phẩm <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                        id="product_name" name="product_name"
                                        value="{{ old('product_name', $product->product_name) }}"
                                        placeholder="Nhập tên sản phẩm">
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="product_code" class="form-label fw-semibold">Mã sản phẩm</label>
                                    <input type="text" class="form-control @error('product_code') is-invalid @enderror"
                                        id="product_code" name="product_code"
                                        value="{{ old('product_code', $product->product_code) }}"
                                        placeholder="Nhập mã sản phẩm">
                                    @error('product_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label fw-semibold">Danh mục <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id">
                                        <option value="">Chọn danh mục</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @php
                                    $hasVariants = $product->variants && $product->variants->isNotEmpty();
                                @endphp

                                @if (!$hasVariants)
                                    <div class="col-md-6 mb-3">
                                        <label for="quantity_in_stock" class="form-label fw-semibold">Số lượng <span
                                                class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('quantity_in_stock') is-invalid @enderror"
                                            id="quantity_in_stock" name="quantity_in_stock"
                                            value="{{ old('quantity_in_stock', $product->quantity_in_stock) }}"
                                            placeholder="Nhập số lượng" min="0">
                                        @error('quantity_in_stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Số lượng biến thể</label>
                                        <div class="form-control bg-light text-muted">
                                            Sản phẩm có biến thể - chỉnh sửa số lượng trong phần biến thể.
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label for="origin_id" class="form-label fw-semibold">Xuất xứ <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('origin_id') is-invalid @enderror" id="origin_id"
                                        name="origin_id">
                                        <option value="">Chọn xuất xứ</option>
                                        @foreach ($origins as $origin)
                                            <option value="{{ $origin->id }}"
                                                {{ old('origin_id', $product->origin_id) == $origin->id ? 'selected' : '' }}>
                                                {{ $origin->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('origin_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="4" placeholder="Nhập mô tả sản phẩm">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pricing Information -->
                    @if (!isset($product) || $product->product_type !== 'variant')
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-dollar-sign me-2 text-primary"></i>
                                    Thông tin giá
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Giá gốc --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="original_price_display" class="form-label fw-semibold">
                                            Giá gốc <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            {{-- Hiển thị cho người dùng nhập (định dạng đẹp) --}}
                                            <input type="text" class="form-control" id="original_price_display"
                                                placeholder="Nhập giá gốc"
                                                value="{{ old('original_price', number_format($product->original_price ?? 0, 0, ',', '.')) }}"
                                                oninput="formatCurrency(this)" autocomplete="off">

                                            {{-- Input thật được submit --}}
                                            <input type="hidden" name="original_price" id="original_price"
                                                value="{{ old('original_price', $product->original_price ?? 0) }}">

                                            <span class="input-group-text">VND</span>
                                        </div>

                                        <small class="text-muted">Giá tăng theo bước 1.000 VND</small>

                                        @error('original_price')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- Phần trăm giảm giá --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="discount_percent" class="form-label fw-semibold">Phần trăm giảm
                                            giá</label>
                                        <div class="input-group">
                                            @php
                                                $discountPercent =
                                                    isset($product) &&
                                                    $product->original_price > 0 &&
                                                    $product->discounted_price
                                                        ? round(
                                                            (($product->original_price - $product->discounted_price) /
                                                                $product->original_price) *
                                                                100,
                                                            1,
                                                        )
                                                        : null;
                                            @endphp
                                            <input type="number" class="form-control" id="discount_percent"
                                                name="discount_percent"
                                                value="{{ old('discount_percent', $discountPercent) }}"
                                                placeholder="Nhập phần trăm giảm" min="0" max="99.9"
                                                step="0.1" oninput="calculateDiscountedPrice()">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-muted">Để trống nếu không có khuyến mãi (tối đa 100%)</small>
                                    </div>
                                </div>
                                {{-- Hiển thị giá hiện tại --}}
                                <div class="row mt-2" id="current_price_display">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning p-2">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Giá hiện tại:</strong>
                                            @if (!empty($product->discounted_price) && $product->discounted_price < $product->original_price)
                                                <span class="text-decoration-line-through" id="current_original_price">
                                                    {{ number_format($product->original_price) }} VND
                                                </span>
                                                → <strong id="current_discounted_price">
                                                    {{ number_format($product->discounted_price) }} VND
                                                </strong>
                                                (Giảm <span id="current_discount_percent">{{ $discountPercent }}%</span>)
                                            @else
                                                <strong id="current_discounted_price">
                                                    {{ number_format($product->original_price ?? 0) }} VND
                                                </strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- Hiển thị kết quả sau khi nhập giảm giá --}}
                                <div class="row mt-2" id="discounted_price_display" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-info p-2">
                                            <i class="fas fa-calculator me-2"></i>
                                            <strong>Giá sau khi áp dụng giảm giá:</strong> <span id="final_price">0
                                                VND</span><br>
                                            <small>Tiết kiệm: <span id="savings_amount">0 VND</span> (<span
                                                    id="savings_percent">0%</span>)</small>
                                        </div>
                                    </div>
                                </div>
                                {{-- Hidden input để gửi giá khuyến mãi --}}
                                <input type="hidden" id="discounted_price" name="discounted_price"
                                    value="{{ old('discounted_price', $product->discounted_price ?? '') }}">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Product Image -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-image me-2 text-primary"></i>
                                Hình ảnh sản phẩm
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow-sm"
                                    id="imagePreview" style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                    alt="Product Image">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label fw-semibold">Thay đổi hình ảnh</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Chỉ chấp nhận file ảnh (JPG, PNG, GIF)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Product Statistics -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>
                                Thống kê sản phẩm
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Ngày tạo:</span>
                                <span class="fw-semibold">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Cập nhật lần cuối:</span>
                                <span class="fw-semibold">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Trạng thái:</span>
                                @if ($product->status == 0)
                                    <span class="badge bg-secondary">Ẩn</span>
                                @else
                                    <span
                                        class="badge {{ $product->quantity_in_stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->quantity_in_stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    Cập nhật sản phẩm
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Hủy bỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Product Variants Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-layer-group me-2 text-primary"></i>
                                Biến thể sản phẩm
                            </h6>
                            <div class="d-flex gap-2">

                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="toggleVariantSection()">
                                    <i class="fas fa-eye me-1"></i>
                                    <span id="toggleVariantText">Ẩn/Hiện</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="variantSection">
                        @if ($product->variants->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                <p class="mb-2">Sản phẩm này chưa có biến thể nào.</p>

                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 fw-semibold text-dark">
                                                <input type="checkbox" class="form-check-input" id="selectAllVariants">
                                            </th>
                                            <th class="border-0 fw-semibold text-dark">Ảnh</th>
                                            <th class="border-0 fw-semibold text-dark">SKU</th>
                                            <th class="border-0 fw-semibold text-dark">Mã sản phẩm</th>
                                            <th class="border-0 fw-semibold text-dark">Giá</th>
                                            <th class="border-0 fw-semibold text-dark">Số lượng</th>
                                            <th class="border-0 fw-semibold text-dark">Trạng thái</th>
                                            <th class="border-0 fw-semibold text-dark">Thuộc tính</th>

                                            <th class="border-0 fw-semibold text-dark text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->variants as $variant)
                                            <tr class="align-middle">
                                                <td>
                                                    <input type="checkbox" class="form-check-input variant-checkbox"
                                                        value="{{ $variant->id }}">
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        @if ($variant->image)
                                                            <img src="{{ asset('storage/' . $variant->image) }}"
                                                                class="rounded shadow-sm" width="50" height="50"
                                                                style="object-fit: cover;" alt="Variant Image">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold">{{ $variant->sku }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark">{{ $product->product_code ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="fw-semibold text-success">{{ number_format($variant->price) }}đ</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="quantity-badge fw-bold 
                                                        @if ($variant->quantity_in_stock <= 5) text-danger border-danger
                                                        @elseif($variant->quantity_in_stock <= 20) text-warning border-warning
                                                        @else text-success border-success @endif border rounded px-2 py-1">
                                                        <i class="fas fa-cubes me-1"></i>
                                                        {{ number_format($variant->quantity_in_stock) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($variant->status == 0)
                                                        <span
                                                            class="status-badge fw-bold text-secondary border border-secondary rounded px-2 py-1">
                                                            <i class="fas fa-eye-slash me-1"></i>Đã ẩn
                                                        </span>
                                                    @else
                                                        @if ($variant->quantity_in_stock > 0)
                                                            <span
                                                                class="status-badge status-available fw-bold text-success border border-success rounded px-2 py-1">
                                                                <i class="fas fa-check me-1"></i>Còn hàng
                                                            </span>
                                                        @else
                                                            <span
                                                                class="status-badge status-out-of-stock fw-bold text-danger border border-danger rounded px-2 py-1">
                                                                <i class="fas fa-times me-1"></i>Hết hàng
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($variant->attributeValues as $attrValue)
                                                            <span class="badge bg-primary text-white rounded px-2 py-1">
                                                                {{ $attrValue->attribute->name }}: {{ $attrValue->value }}
                                                            </span>
                                                        @endforeach
                                                        @if ($variant->attributeValues->isEmpty())
                                                            <span class="text-muted small">Không có thuộc tính</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="tooltip" title="Chỉnh sửa"
                                                            onclick="editVariant({{ $variant->id }}, '{{ $product->product_code }}')">

                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if ($variant->status == 1)
                                                            <!-- Đang hiện thì cho phép Ẩn -->
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="tooltip" title="Ẩn biến thể"
                                                                onclick="toggleVariantStatus({{ $variant->id }}, 'hide')">
                                                                <i class="fas fa-toggle-off"></i>
                                                            </button>
                                                        @else
                                                            <!-- Đang ẩn thì cho phép Hiện -->
                                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                                data-bs-toggle="tooltip" title="Hiện biến thể"
                                                                onclick="toggleVariantStatus({{ $variant->id }}, 'show')">
                                                                <i class="fas fa-toggle-on"></i>
                                                            </button>
                                                        @endif

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
        </div>
    </div>
@endsection

<!-- Modal chỉnh sửa biến thể -->
<div class="modal fade" id="editVariantModal" tabindex="-1" aria-labelledby="editVariantModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editVariantForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editVariantModalLabel">Chỉnh sửa biến thể</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <input type="hidden" id="baseProductCode" value="{{ $product->code ?? '' }}">
                <div class="modal-body">
                    <div class="mb-3">

                        <label for="editSku" class="form-label">SKU</label>
                        <input type="text" class="form-control" name="sku" id="editSku" readonly>
                    </div>
                    <!-- Chọn Vị -->
                    <!-- Vị: cho phép sửa nhưng giữ giá trị cũ -->
                    <div class="mb-3">
                        <label for="editMainAttributeName" class="form-label">Vị</label>
                        <input type="text" class="form-control" name="main_attribute_name"
                            id="editMainAttributeName" required>
                        <input type="hidden" name="main_attribute_id" id="editMainAttributeId">
                    </div>
                    @php
                        $sizeAttr = $attributes->firstWhere('name', 'Khối lượng');
                        $currentSubAttributeId = optional(
                            optional($variant)->values?->first(
                                fn($v) => $v->attribute && $v->attribute->name === 'Khối lượng',
                            ),
                        )->id;
                    @endphp


                    <div class="mb-3">
                        <label for="editSubAttributeId" class="form-label">Khối lượng</label>
                        <select class="form-select" name="sub_attribute_id" id="editSubAttributeId" required>
                            @if ($sizeAttr && $sizeAttr->values)
                                @foreach ($sizeAttr->values as $value)
                                    <option value="{{ $value->id }}"
                                        {{ $currentSubAttributeId == $value->id ? 'selected' : '' }}>
                                        {{ $value->value }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled>Không có khối lượng nào</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editPrice" class="form-label">Giá (VND)</label>
                        <input type="number" class="form-control" name="price" id="editPrice" min="0"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="editQuantity" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" name="quantity_in_stock" id="editQuantity"
                            min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh biến thể</label>
                        <div class="mb-2" id="currentVariantImageWrapper" style="display: none;">
                            <img id="currentVariantImage" src="" alt="Ảnh hiện tại" class="img-thumbnail"
                                width="100">
                        </div>
                        <input type="file" class="form-control" name="image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let globalProductCode = '';

    // Hiện/ẩn vùng biến thể
    window.toggleVariantSection = function() {
        const section = document.getElementById('variantSection');
        const toggleText = document.getElementById('toggleVariantText');
        const isHidden = section.style.display === 'none';
        section.style.display = isHidden ? 'block' : 'none';
        toggleText.innerText = isHidden ? 'Ẩn' : 'Hiện';
    };

    // Sửa biến thể
    window.editVariant = function(variantId, productCode) {
        globalProductCode = productCode;

        fetch(`/admin/product-variants/${variantId}`)
            .then(response => {
                if (!response.ok) throw new Error('Không thể lấy dữ liệu biến thể');
                return response.json();
            })
            .then(variant => {
                // Gán dữ liệu
                document.getElementById('editPrice').value = parsePrice(variant.price);

                document.getElementById('editQuantity').value = variant.quantity_in_stock ?? 0;

                if (variant.main_attribute) {
                    document.getElementById('editMainAttributeId').value = variant.main_attribute.id ?? '';
                    document.getElementById('editMainAttributeName').value = variant.main_attribute.name ?? '';
                }

                if (variant.sub_attribute) {
                    const subSelect = document.getElementById('editSubAttributeId');
                    const targetId = variant.sub_attribute.id ?? '';

                    // Chọn option đúng ID
                    [...subSelect.options].forEach(opt => {
                        opt.selected = (opt.value == targetId);
                    });
                }


                if (variant.image_url) {
                    document.getElementById('currentVariantImage').src = variant.image_url;
                    document.getElementById('currentVariantImageWrapper').style.display = 'block';
                } else {
                    document.getElementById('currentVariantImageWrapper').style.display = 'none';
                }

                // Gán action form
                document.getElementById('editVariantForm').action = `/admin/product-variants/${variantId}`;

                // Gán SKU (hoặc tự tạo nếu rỗng)
                if (!variant.sku || variant.sku.trim() === '') {
                    generateSku(); // Sẽ lấy từ globalProductCode
                } else {
                    document.getElementById('editSku').value = variant.sku;
                }

                // Hiển thị modal
                const modalEl = document.getElementById('editVariantModal');
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (!modal) modal = new bootstrap.Modal(modalEl);
                modal.show();
            })
            .catch(error => {
                console.error(error);
                alert('Lỗi khi lấy dữ liệu biến thể');
            });
    };

    function parsePrice(raw) {
        if (typeof raw === 'string') {
            return parseInt(raw.replace(/[.,]/g, ''), 10);
        }
        return raw || 0;
    }

    // Tạo SKU từ mã sản phẩm, vị và size
    function generateSku() {
        const mainName = document.getElementById('editMainAttributeName')?.value?.trim() || '';
        const subSelect = document.getElementById('editSubAttributeId');
        const subText = subSelect?.options[subSelect.selectedIndex]?.text || '';
        const baseCode = globalProductCode || document.getElementById('baseProductCode')?.value?.trim() || '';

        const slugify = (str) => str
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-zA-Z0-9]/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
            .toUpperCase();

        const parts = [baseCode];

        if (mainName) parts.push(slugify(mainName));
        if (subText) parts.push(slugify(subText));

        document.getElementById('editSku').value = parts.join('-');
    }

    // Tự động cập nhật SKU khi thay đổi Vị hoặc Khối lượng
    document.addEventListener('DOMContentLoaded', function() {
        const mainAttrInput = document.getElementById('editMainAttributeName');
        const subAttrSelect = document.getElementById('editSubAttributeId');

        if (mainAttrInput) {
            mainAttrInput.addEventListener('input', generateSku);
        }

        if (subAttrSelect) {
            subAttrSelect.addEventListener('change', generateSku);
        }
    });

    function toggleVariantStatus(variantId, actionType) {
        let confirmMsg = actionType === "hide" ?
            "Bạn có chắc muốn ẩn biến thể này không?" :
            "Bạn có chắc muốn hiện biến thể này không?";

        Swal.fire({
            title: 'Xác nhận',
            text: confirmMsg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/product-variants/${variantId}/toggle-status`, {
                        method: "PATCH",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content"),
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            action_type: actionType
                        })
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const text = await res.text();
                            throw new Error(text);
                        }
                        return res.json();
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Thành công!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        console.error("❌ Lỗi toggle:", err);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: "Có lỗi xảy ra khi thay đổi trạng thái biến thể!",
                            icon: 'error'
                        });
                    });
            }
        });
    }

    // sửa sản phẩm 
    document.addEventListener('DOMContentLoaded', function() {
        // ===== Xử lý sản phẩm đơn =====
        const displayPriceInput = document.getElementById('original_price_display');
        const hiddenPriceInput = document.getElementById('original_price');
        const discountPercentInput = document.getElementById('discount_percent');
        const discountedPriceInput = document.getElementById('discounted_price');

        const currentPriceDisplay = document.getElementById('current_price_display');
        const discountedPriceDisplay = document.getElementById('discounted_price_display');

        const spanCurrentOriginal = document.getElementById('current_original_price');
        const spanCurrentDiscounted = document.getElementById('current_discounted_price');
        const spanCurrentPercent = document.getElementById('current_discount_percent');
        const spanFinalPrice = document.getElementById('final_price');
        const spanSavings = document.getElementById('savings_amount');
        const spanSavingsPercent = document.getElementById('savings_percent');

        function formatCurrency(input) {
            let raw = input.value.replace(/[^\d]/g, '');
            if (raw) {
                input.value = parseInt(raw).toLocaleString('vi-VN');
            } else {
                input.value = '';
            }
        }

        function parseCurrency(value) {
            return parseFloat((value || '').replace(/[^\d]/g, '')) || 0;
        }

        function calculateDiscountedPrice() {
            const original = parseCurrency(displayPriceInput.value);
            const percent = parseFloat(discountPercentInput.value) || 0;

            hiddenPriceInput.value = original; // cập nhật input hidden

            if (percent >= 100) {
                alert("Phần trăm giảm giá phải nhỏ hơn 100%");
                discountPercentInput.value = '';
                discountedPriceInput.value = '';
                discountedPriceDisplay.style.display = 'none';
                return;
            }

            if (original > 0 && percent > 0) {
                const discountAmount = original * (percent / 100);
                const finalPrice = original - discountAmount;

                discountedPriceInput.value = Math.round(finalPrice);

                // Cập nhật hiển thị
                if (spanCurrentOriginal) spanCurrentOriginal.textContent = original.toLocaleString('vi-VN') +
                    ' VND';
                if (spanCurrentDiscounted) spanCurrentDiscounted.textContent = finalPrice.toLocaleString(
                    'vi-VN') + ' VND';
                if (spanCurrentPercent) spanCurrentPercent.textContent = percent + '%';
                if (spanFinalPrice) spanFinalPrice.textContent = finalPrice.toLocaleString('vi-VN') + ' VND';
                if (spanSavings) spanSavings.textContent = discountAmount.toLocaleString('vi-VN') + ' VND';
                if (spanSavingsPercent) spanSavingsPercent.textContent = percent + '%';

                discountedPriceDisplay.style.display = 'block';
            } else {
                discountedPriceInput.value = '';
                discountedPriceDisplay.style.display = 'none';
            }
        }

        if (displayPriceInput && discountPercentInput) {
            displayPriceInput.addEventListener('input', function() {
                formatCurrency(displayPriceInput);
                calculateDiscountedPrice();
            });

            discountPercentInput.addEventListener('input', calculateDiscountedPrice);

            // Gọi khi trang load để hiển thị đúng nếu có giá và phần trăm sẵn
            formatCurrency(displayPriceInput);
            calculateDiscountedPrice();
        }

        // ===== Xử lý form cập nhật biến thể =====
        const form = document.getElementById('editVariantForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Bỏ dấu chấm ngăn cách số tiền trong input giá
                const inputPrice = document.getElementById('original_price');
                if (inputPrice) {
                    inputPrice.value = parseCurrency(inputPrice.value);
                }

                const formData = new FormData(form);
                const url = form.action;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type');
                        if (!response.ok) {
                            if (contentType && contentType.includes('application/json')) {
                                const data = await response.json();
                                Swal.fire({
                                    title: 'Thất bại!',
                                    text: data?.message || 'Có lỗi xảy ra',
                                    icon: 'error'
                                });
                            } else {
                                console.error(await response.text());
                                Swal.fire({
                                    title: 'Thất bại!',
                                    text: 'Cập nhật thất bại (xem console)',
                                    icon: 'error'
                                });
                            }
                            return;
                        }

                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Cập nhật thành công!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            bootstrap.Modal.getInstance(document.getElementById(
                                'editVariantModal'))?.hide();
                            setTimeout(() => location.reload(), 500);
                        });

                    })
                    .catch(error => {
                        console.error('❌ Fetch Error:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi không xác định khi cập nhật.',
                            icon: 'error'
                        });
                    });

            });

            // Auto-SKU
            document.getElementById('editMainAttributeName')?.addEventListener('input', generateSku);
            document.getElementById('editSubAttributeId')?.addEventListener('change', generateSku);
        }

    });
    // Thêm event listener cho cả hai input
    document.getElementById('original_price').addEventListener('input', calculateDiscountedPrice);
    document.getElementById('discount_percent').addEventListener('input', calculateDiscountedPrice);
</script>

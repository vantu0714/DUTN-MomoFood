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
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    Quay lại
                </a>
                <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">
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
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="width: 100px; height: 100px; object-fit: cover;"
                                     alt="Product Image">
                            </div>
                            <div class="col-md-10">
                                <h5 class="mb-1">{{ $product->product_name }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($product->description, 100) }}</p>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-primary">ID: #{{ $product->id }}</span>
                                    <span class="badge bg-info">{{ $product->category->category_name }}</span>
                                    <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
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
                                    <label for="product_name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
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
                                    <label for="category_id" class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
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

                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label fw-semibold">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" 
                                           value="{{ old('quantity', $product->quantity) }}" 
                                           placeholder="Nhập số lượng" min="0">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ingredients" class="form-label fw-semibold">Thành phần</label>
                                    <input type="text" class="form-control @error('ingredients') is-invalid @enderror" 
                                           id="ingredients" name="ingredients" 
                                           value="{{ old('ingredients', $product->ingredients) }}" 
                                           placeholder="Nhập thành phần sản phẩm">
                                    @error('ingredients')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" 
                                              placeholder="Nhập mô tả sản phẩm">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-dollar-sign me-2 text-primary"></i>
                                Thông tin giá
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="original_price" class="form-label fw-semibold">Giá gốc <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('original_price') is-invalid @enderror" 
                                               id="original_price" name="original_price" 
                                               value="{{ old('original_price', $product->original_price) }}" 
                                               placeholder="Nhập giá gốc" min="0">
                                        <span class="input-group-text">đ</span>
                                        @error('original_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="discounted_price" class="form-label fw-semibold">Giá khuyến mãi</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('discounted_price') is-invalid @enderror" 
                                               id="discounted_price" name="discounted_price" 
                                               value="{{ old('discounted_price', $product->discounted_price) }}" 
                                               placeholder="Nhập giá khuyến mãi" min="0">
                                        <span class="input-group-text">đ</span>
                                        @error('discounted_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Để trống nếu không có khuyến mãi</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-plus-circle me-2 text-primary"></i>
                                Thông tin bổ sung
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiration_date" class="form-label fw-semibold">Ngày hết hạn</label>
                                    <input type="date" class="form-control @error('expiration_date') is-invalid @enderror" 
                                           id="expiration_date" name="expiration_date" 
                                           value="{{ old('expiration_date', $product->expiration_date ? \Carbon\Carbon::parse($product->expiration_date)->format('Y-m-d') : '') }}">
                                    @error('expiration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="view" class="form-label fw-semibold">Lượt xem</label>
                                    <input type="number" class="form-control @error('view') is-invalid @enderror" 
                                           id="view" name="view" 
                                           value="{{ old('view', $product->view) }}" 
                                           placeholder="Số lượt xem" min="0" readonly>
                                    @error('view')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chỉ đọc - Cập nhật tự động</small>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     class="img-fluid rounded shadow-sm" 
                                     id="imagePreview"
                                     style="max-width: 200px; max-height: 200px; object-fit: cover;"
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Tổng lượt xem:</span>
                                <span class="fw-semibold text-info">{{ number_format($product->view) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Trạng thái:</span>
                                <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </span>
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
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
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
                                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                                    <i class="fas fa-plus me-1"></i>
                                    Thêm biến thể
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleVariantSection()">
                                    <i class="fas fa-eye me-1"></i>
                                    <span id="toggleVariantText">Ẩn/Hiện</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="variantSection">
                        @if($product->variants->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                <p class="mb-2">Sản phẩm này chưa có biến thể nào.</p>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                                    <i class="fas fa-plus me-1"></i>
                                    Thêm biến thể đầu tiên
                                </button>
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
                                            <th class="border-0 fw-semibold text-dark">Thành phần</th>
                                            <th class="border-0 fw-semibold text-dark text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr class="align-middle">
                                                <td>
                                                    <input type="checkbox" class="form-check-input variant-checkbox" value="{{ $variant->id }}">
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        @if($variant->image)
                                                            <img src="{{ asset('storage/' . $variant->image) }}" 
                                                                 class="rounded shadow-sm" 
                                                                 width="50" height="50" 
                                                                 style="object-fit: cover;" 
                                                                 alt="Variant Image">
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
                                                    <span class="badge bg-light text-dark">{{ $product->product_code ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-success">{{ number_format($variant->price) }}đ</span>
                                                </td>
                                                <td>
                                                    <span class="quantity-badge fw-bold 
                                                        @if ($variant->quantity_in_stock <= 5) text-danger border-danger
                                                        @elseif($variant->quantity_in_stock <= 20) text-warning border-warning
                                                        @else text-success border-success @endif border rounded px-2 py-1">
                                                        <i class="fas fa-cubes me-1"></i>
                                                        {{ number_format($variant->quantity_in_stock) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($variant->quantity_in_stock > 0)
                                                        <span class="status-badge status-available fw-bold text-success border border-success rounded px-2 py-1">
                                                            <i class="fas fa-check me-1"></i>Còn hàng
                                                        </span>
                                                    @else
                                                        <span class="status-badge status-out-of-stock fw-bold text-danger border border-danger rounded px-2 py-1">
                                                            <i class="fas fa-times me-1"></i>Hết hàng
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($variant->attributeValues as $attrValue)
                                                            <span class="badge bg-primary text-white rounded px-2 py-1">
                                                                {{ $attrValue->attribute->name }}: {{ $attrValue->value }}
                                                            </span>
                                                        @endforeach
                                                        @if($variant->attributeValues->isEmpty())
                                                            <span class="text-muted small">Không có thuộc tính</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($variant->ingredients)
                                                        <span class="text-muted small">{{ Str::limit($variant->ingredients, 30) }}</span>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="tooltip" title="Xem chi tiết"
                                                                onclick="viewVariant({{ $variant->id }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                data-bs-toggle="tooltip" title="Chỉnh sửa"
                                                                onclick="editVariant({{ $variant->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="tooltip" title="Xóa biến thể"
                                                                onclick="deleteVariant({{ $variant->id }}, '{{ $variant->sku }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    {{-- @if(!$product->variants->isEmpty())
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Tổng: {{ $product->variants->count() }} biến thể
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteSelectedVariants()">
                                        <i class="fas fa-trash me-1"></i>
                                        Xóa đã chọn
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>

        <!-- Add Variant Modal -->
        {{-- <div class="modal fade" id="addVariantModal" tabindex="-1" aria-labelledby="addVariantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVariantModalLabel">
                            <i class="fas fa-plus me-2"></i>Thêm biến thể mới
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addVariantForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="variant_sku" class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="variant_sku" name="sku" placeholder="Nhập SKU biến thể">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="variant_price" class="form-label fw-semibold">Giá <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="variant_price" name="price" placeholder="Nhập giá">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="variant_quantity" class="form-label fw-semibold">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="variant_quantity" name="quantity_in_stock" placeholder="Số lượng">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="variant_image" class="form-label fw-semibold">Hình ảnh</label>
                                    <input type="file" class="form-control" id="variant_image" name="image" accept="image/*">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="variant_ingredients" class="form-label fw-semibold">Thành phần</label>
                                    <textarea class="form-control" id="variant_ingredients" name="ingredients" rows="3" placeholder="Nhập thành phần biến thể"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Thêm biến thể
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
    </div>
    <!-- Scripts -->
    <script>
        // Preview image function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }     
    </script>
    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }

        .badge {
            font-weight: 500;
        }

        #imagePreview {
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        #imagePreview:hover {
            border-color: #0d6efd;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .btn-lg {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }
        }
    </style>
@endsection
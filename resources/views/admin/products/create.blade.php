@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1 text-primary fw-bold">
                            <i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm mới
                        </h2>
                        <p class="text-muted mb-0">Tạo sản phẩm mới cho cửa hàng của bạn</p>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Main Form Card -->
                <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i>Thông tin sản phẩm
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form id="product-form" action="{{ route('admin.products.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Cột trái -->
                                <div class="col-md-6">
                                    <!-- Tên sản phẩm -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-tag text-primary me-2"></i>Tên sản phẩm
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="product_name"
                                            class="form-control form-control-lg border-2" placeholder="Nhập tên sản phẩm..."
                                            value="{{ old('product_name') }}">
                                        @error('product_name')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Mã sản phẩm -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-barcode text-primary me-2"></i>Mã sản phẩm
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="product_code"
                                            class="form-control form-control-lg border-2" placeholder="Nhập mã sản phẩm..."
                                            value="{{ old('product_code') }}">
                                        @error('product_code')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Danh mục -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-list text-primary me-2"></i>Danh mục
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="category_id" class="form-select form-select-lg border-2">
                                            <option value="">-- Chọn danh mục --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Loại sản phẩm -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-layer-group text-primary me-2"></i>Loại sản phẩm
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="product_type" class="form-select form-select-lg border-2" required>
                                            <option value="">-- Chọn loại sản phẩm --</option>
                                            <option value="simple" {{ old('product_type') == 'simple' ? 'selected' : '' }}>
                                                Không có biến thể
                                            </option>
                                            <option value="variant"
                                                {{ old('product_type') == 'variant' ? 'selected' : '' }}>
                                                Có biến thể
                                            </option>
                                        </select>
                                        @error('product_type')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Ảnh sản phẩm -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-image text-primary me-2"></i>Ảnh sản phẩm
                                        </label>
                                        <div
                                            class="upload-area border-2 border-dashed rounded-lg p-4 text-center position-relative">
                                            <input type="file" name="image" class="form-control d-none" id="imageInput"
                                                accept="image/*">
                                            <label for="imageInput" class="cursor-pointer d-block">
                                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                <p class="mb-0 text-muted">Nhấp để chọn ảnh hoặc kéo thả ảnh vào đây</p>
                                                <small class="text-muted">PNG, JPG, JPEG (Tối đa 2MB)</small>
                                            </label>
                                            <div id="imagePreview" class="mt-3 d-none">
                                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                                    style="max-width: 200px;">
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                    onclick="removeImage()">
                                                    <i class="fas fa-trash me-1"></i>Xóa ảnh
                                                </button>
                                            </div>
                                        </div>
                                        @error('image')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Cột phải -->
                                <div class="col-md-6">
                                    <!-- Giá gốc -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-money-bill-wave text-primary me-2"></i>Giá gốc
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" step="1000" name="original_price"
                                                id="original_price" class="form-control border-2" placeholder="0"
                                                value="{{ old('original_price') }}">
                                            <span class="input-group-text bg-primary text-white fw-semibold">VND</span>
                                        </div>
                                        <div class="price-display mt-2">
                                            <small class="text-muted">Giá hiển thị: </small>
                                            <span id="original_price_display" class="fw-semibold text-primary">0
                                                VND</span>
                                        </div>
                                        @error('original_price')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <!-- % Giảm giá -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-percentage text-primary me-2"></i>Phần trăm giảm giá
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" step="0.1" id="discount_percent"
                                                class="form-control border-2" placeholder="0" min="0"
                                                max="100">
                                            <span class="input-group-text bg-warning text-dark fw-semibold">%</span>
                                        </div>
                                        <small class="text-muted">Để trống nếu không có giảm giá</small>
                                    </div>
                                    <!-- Giá khuyến mãi -->
                                    <div class="form-group mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-tags text-primary me-2"></i>Giá khuyến mãi
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" step="any" name="discounted_price"
                                                id="discounted_price" class="form-control border-2" placeholder="0"
                                                value="{{ old('discounted_price') }}">
                                            <span class="input-group-text bg-success text-white fw-semibold">VND</span>
                                        </div>
                                        <div class="price-display mt-2">
                                            <small class="text-muted">Giá hiển thị: </small>
                                            <span id="discounted_price_display" class="fw-semibold text-success">0
                                                VND</span>
                                        </div>
                                        <small class="text-warning mt-1 d-block">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Giá khuyến mãi không được lớn hơn giá gốc
                                        </small>
                                        @error('discounted_price')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <!-- Số lượng -->
                                    <div class="form-group mb-4" id="stockQuantityWrapper">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-boxes text-primary me-2"></i>Số lượng trong kho
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="quantity_in_stock" id="quantity_in_stock"
                                            class="form-control form-control-lg border-2" placeholder="Nhập số lượng"
                                            value="{{ old('quantity_in_stock') }}" min="1" required>
                                        @error('quantity_in_stock')
                                            <div class="text-danger mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Mô tả sản phẩm -->
                            <div class="form-group mb-4" id="stockQuantityWrapper">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-align-left text-primary me-2"></i>Mô tả sản phẩm
                                </label>
                                <textarea name="description" class="form-control border-2" rows="4"
                                    placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Buttons -->
                            <div class="form-group mb-0">
                                <div class="d-flex gap-3 justify-content-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                                        <i class="fas fa-save me-2"></i>Lưu sản phẩm
                                    </button>
                                    <a href="{{ route('admin.products.index') }}"
                                        class="btn btn-outline-secondary btn-lg px-5 py-3">
                                        <i class="fas fa-times me-2"></i>Hủy bỏ
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const originalInput = document.getElementById('original_price');
            const percentInput = document.getElementById('discount_percent');
            const discountInput = document.getElementById('discounted_price');
            const originalDisplay = document.getElementById('original_price_display');
            const discountedDisplay = document.getElementById('discounted_price_display');
            const productTypeSelect = document.querySelector('select[name="product_type"]');
            const stockQuantityWrapper = document.getElementById('stockQuantityWrapper');
            const stockQuantityInput = document.getElementById('quantity_in_stock');
            const uploadArea = document.querySelector('.upload-area');
            const imageInput = document.getElementById('imageInput');
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const form = document.getElementById('product-form');

            // Format tiền
            function formatVND(amount) {
                if (!amount || amount === 0) return '0 VND';
                return new Intl.NumberFormat('vi-VN').format(amount) + ' VND';
            }

            // Cập nhật giá khuyến mãi khi nhập phần trăm
            function updateDiscountedPrice() {
                const original = parseFloat(originalInput.value) || 0;
                const percent = parseFloat(percentInput.value) || 0;

                originalDisplay.textContent = formatVND(original);

                if (original > 0 && percent > 0) {
                    const discounted = original * (1 - percent / 100);
                    discountInput.value = Math.round(discounted);
                    discountedDisplay.textContent = formatVND(discounted);
                } else {
                    discountedDisplay.textContent = formatVND(parseFloat(discountInput.value) || 0);
                }
            }

            // Cập nhật giá khuyến mãi khi nhập trực tiếp
            function updateDiscountedDisplay() {
                const discounted = parseFloat(discountInput.value) || 0;
                discountedDisplay.textContent = formatVND(discounted);
            }

            // Reset phần trăm nếu người dùng chỉnh giá khuyến mãi trực tiếp
            discountInput.addEventListener('input', function() {
                percentInput.value = '';
                updateDiscountedDisplay();
            });

            // Ẩn/hiện ô nhập số lượng theo loại sản phẩm
            function toggleStockQuantity() {
                const isVariant = productTypeSelect.value === 'variant';
                stockQuantityWrapper.style.display = isVariant ? 'none' : 'block';

                if (isVariant) {
                    // Nếu là sản phẩm có biến thể, xóa name để không gửi
                    stockQuantityInput.removeAttribute('name');
                } else {
                    stockQuantityInput.setAttribute('name', 'quantity_in_stock');
                }
            }
            // Upload ảnh
            function handleImageUpload(input) {
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Xóa ảnh
            window.removeImage = function() {
                imageInput.value = '';
                preview.classList.add('d-none');
            };

            // Drag & Drop
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight(e) {
                uploadArea.classList.add('border-primary', 'bg-light');
            }

            function unhighlight(e) {
                uploadArea.classList.remove('border-primary', 'bg-light');
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    handleImageUpload(imageInput);
                }
            }

            // Validate trước khi submit
            form.addEventListener('submit', function(e) {
                const original = parseFloat(originalInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                if (original > 0 && discount > original) {
                    e.preventDefault();
                    alert('Giá khuyến mãi không được lớn hơn giá gốc!');
                    discountInput.focus();
                }
            });

            // Event bindings
            originalInput.addEventListener('input', updateDiscountedPrice);
            percentInput.addEventListener('input', updateDiscountedPrice);
            imageInput.addEventListener('change', function() {
                handleImageUpload(this);
            });
            productTypeSelect.addEventListener('change', toggleStockQuantity);

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
                uploadArea.addEventListener(event, preventDefaults, false);
            });
            ['dragenter', 'dragover'].forEach(event => {
                uploadArea.addEventListener(event, highlight, false);
            });
            ['dragleave', 'drop'].forEach(event => {
                uploadArea.addEventListener(event, unhighlight, false);
            });
            uploadArea.addEventListener('drop', handleDrop, false);

            // Khởi tạo ban đầu
            toggleStockQuantity();
            updateDiscountedPrice();
        });
    </script>
@endpush

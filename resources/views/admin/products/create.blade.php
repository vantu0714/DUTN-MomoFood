@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid px-0 py-4">
        <div class="row">
            <div class="col-12 px-0">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-2 text-dark fw-bold">
                            <i class="fas fa-plus-circle text-primary me-2"></i>Thêm sản phẩm mới
                        </h1>
                        <p class="text-muted mb-0">Tạo và quản lý sản phẩm cho cửa hàng của bạn</p>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>

                <!-- Success Alert -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                            <div>
                                <strong>Thành công!</strong> {{ session('success') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Main Form -->
                <form id="product-form" action="{{ route('admin.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Đã xảy ra lỗi:</strong>
                            <ul class="mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-times-circle me-1 text-danger"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Left Column - Basic Information -->
                        <div class="col-lg-8">
                            <!-- Basic Information Card -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="mb-0 text-dark fw-semibold">
                                        <i class="fas fa-info-circle text-primary me-2"></i>Thông tin cơ bản
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <!-- Product Name -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-tag text-primary me-2"></i>Tên sản phẩm
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="product_name"
                                                class="form-control form-control-lg @error('product_name') is-invalid @enderror"
                                                placeholder="Nhập tên sản phẩm..." value="{{ old('product_name') }}"
                                                required>
                                            @error('product_name')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Product Code -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-barcode text-primary me-2"></i>Mã sản phẩm
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="product_code"
                                                class="form-control form-control-lg @error('product_code') is-invalid @enderror"
                                                placeholder="Nhập mã sản phẩm..." value="{{ old('product_code') }}"
                                                required>
                                            @error('product_code')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Category -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-list text-primary me-2"></i>Danh mục
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="category_id"
                                                class="form-select form-select-lg @error('category_id') is-invalid @enderror"
                                                required>
                                                <option value="">-- Chọn danh mục --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Product Type -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-layer-group text-primary me-2"></i>Loại sản phẩm
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="product_type"
                                                class="form-select form-select-lg @error('product_type') is-invalid @enderror"
                                                required>
                                                <option value="">-- Chọn loại sản phẩm --</option>
                                                <option value="simple"
                                                    {{ old('product_type') == 'simple' ? 'selected' : '' }}>
                                                    Sản phẩm đơn giản
                                                </option>
                                                <option value="variant"
                                                    {{ old('product_type') == 'variant' ? 'selected' : '' }}>
                                                    Sản phẩm có biến thể
                                                </option>
                                            </select>
                                            @error('product_type')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Card -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="mb-0 text-dark fw-semibold">
                                        <i class="fas fa-align-left text-primary me-2"></i>Mô tả sản phẩm
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6"
                                        placeholder="Nhập mô tả chi tiết về sản phẩm, tính năng, và thông tin khác...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pricing Card -->
                            <div class="card shadow-sm border-0" id="pricingCard">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="mb-0 text-dark fw-semibold">
                                        <i class="fas fa-dollar-sign text-primary me-2"></i>Thông tin giá cả & tồn kho
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <!-- Original Price -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-money-bill-wave text-primary me-2"></i>Giá gốc
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" step="1000" name="original_price"
                                                    id="original_price"
                                                    class="form-control @error('original_price') is-invalid @enderror"
                                                    placeholder="0" value="{{ old('original_price') }}" required>
                                                <span class="input-group-text bg-primary text-white fw-semibold">VND</span>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">Hiển thị: </small>
                                                <span id="original_price_display" class="fw-semibold text-primary">0
                                                    VND</span>
                                            </div>
                                            @error('original_price')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Discount Percentage -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-percentage text-primary me-2"></i>Giảm giá
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" step="0.1" id="discount_percent"
                                                    class="form-control" placeholder="0" min="0" max="100">
                                                <span class="input-group-text bg-warning text-dark fw-semibold">%</span>
                                            </div>
                                            <small class="text-muted">Tính toán tự động giá khuyến mãi</small>
                                        </div>

                                        <!-- Discounted Price -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-tags text-primary me-2"></i>Giá khuyến mãi
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" step="any" name="discounted_price"
                                                    id="discounted_price"
                                                    class="form-control @error('discounted_price') is-invalid @enderror"
                                                    placeholder="0" value="{{ old('discounted_price') }}">
                                                <span class="input-group-text bg-success text-white fw-semibold">VND</span>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">Hiển thị: </small>
                                                <span id="discounted_price_display" class="fw-semibold text-success">0
                                                    VND</span>
                                            </div>
                                            @error('discounted_price')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Stock Quantity -->
                                        <div class="col-md-6" id="stockQuantityWrapper">
                                            <label class="form-label fw-semibold text-dark">
                                                <i class="fas fa-boxes text-primary me-2"></i>Số lượng
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="quantity_in_stock" id="quantity_in_stock"
                                                class="form-control form-control-lg @error('quantity_in_stock') is-invalid @enderror"
                                                placeholder="Nhập số lượng" value="{{ old('quantity_in_stock') }}"
                                                min="0" required>
                                            @error('quantity_in_stock')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Image Upload -->
                        <div class="col-lg-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="mb-0 text-dark fw-semibold">
                                        <i class="fas fa-image text-primary me-2"></i>Hình ảnh sản phẩm
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div
                                        class="upload-area border-2 border-dashed border-light rounded-3 p-4 text-center position-relative bg-light">
                                        <input type="file" name="image" class="form-control d-none" id="imageInput"
                                            accept="image/*">
                                        <label for="imageInput" class="cursor-pointer d-block h-100">
                                            <div id="uploadPlaceholder">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted mb-2">Chọn hình ảnh</h6>
                                                <p class="text-muted mb-0">Nhấp để chọn hoặc kéo thả ảnh vào đây</p>
                                                <small class="text-muted">PNG, JPG, JPEG (Tối đa 2MB)</small>
                                            </div>
                                        </label>
                                        <div id="imagePreview" class="d-none">
                                            <img id="previewImg" src="" alt="Preview"
                                                class="img-fluid rounded-3 mb-3" style="max-height: 300px;">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="removeImage()">
                                                <i class="fas fa-trash me-1"></i>Xóa ảnh
                                            </button>
                                        </div>
                                    </div>
                                    @error('image')
                                        <div class="text-danger mt-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center gap-3">
                                <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                                    <i class="fas fa-save me-2"></i>Lưu sản phẩm
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg px-5 py-3"
                                    onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i>Đặt lại
                                </button>
                                <a href="{{ route('admin.products.index') }}"
                                    class="btn btn-outline-danger btn-lg px-5 py-3">
                                    <i class="fas fa-times me-2"></i>Hủy bỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .upload-area {
            transition: all 0.3s ease;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-area:hover {
            border-color: #007bff !important;
            background-color: #f8f9fa !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            border-color: #007bff;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .price-display {
            font-size: 0.9rem;
        }

        .alert {
            border-radius: 0.75rem;
        }

        .card-header {
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }

        .invalid-feedback {
            display: block;
        }

        @media (max-width: 768px) {

            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const originalInput = document.getElementById('original_price');
            const percentInput = document.getElementById('discount_percent');
            const discountInput = document.getElementById('discounted_price');
            const originalDisplay = document.getElementById('original_price_display');
            const discountedDisplay = document.getElementById('discounted_price_display');

            const productTypeSelect = document.querySelector('select[name="product_type"]');
            const stockQuantityWrapper = document.getElementById('stockQuantityWrapper') || document.querySelector(
                'input[name="quantity_in_stock"]').closest('.col-md-6');
            const stockQuantityInput = document.getElementById('quantity_in_stock');

            const form = document.getElementById('product-form');
            const uploadArea = document.querySelector('.upload-area');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');

            // Lấy các wrapper cho pricing fields
            const pricingCard = document.getElementById('pricingCard');
            const originalPriceWrapper = originalInput.closest('.col-md-4');
            const discountPercentWrapper = percentInput.closest('.col-md-4');
            const discountedPriceWrapper = discountInput.closest('.col-md-4');

            // Format VND currency
            function formatVND(amount) {
                return (!amount || amount === 0) ?
                    '0 VND' :
                    new Intl.NumberFormat('vi-VN').format(amount) + ' VND';
            }

            // Update discounted price based on original price and discount percentage
            function updateDiscountedPrice() {
                const original = parseFloat(originalInput.value) || 0;
                const percentRaw = percentInput.value; // lấy nguyên chuỗi để kiểm tra rỗng
                const percent = parseFloat(percentRaw) || 0;

                originalDisplay.textContent = formatVND(original);

                if (original > 0 && percentRaw !== '' && percent > 0) {
                    const discounted = original * (1 - percent / 100);
                    discountInput.value = Math.round(discounted);
                    discountedDisplay.textContent = formatVND(discounted);
                } else {
                    // Nếu phần trăm rỗng hoặc <= 0 → reset
                    discountInput.value = '';
                    discountedDisplay.textContent = '0 VND';
                }
            }
            // Toggle price and stock fields based on product type
            function togglePriceAndStockFields() {
                const isVariant = productTypeSelect.value === 'variant';

                const fields = [{
                        wrapper: originalPriceWrapper,
                        input: originalInput,
                        name: 'original_price'
                    },
                    {
                        wrapper: discountPercentWrapper,
                        input: percentInput,
                        name: null
                    },
                    {
                        wrapper: discountedPriceWrapper,
                        input: discountInput,
                        name: 'discounted_price'
                    },
                    {
                        wrapper: stockQuantityWrapper,
                        input: stockQuantityInput,
                        name: 'quantity_in_stock'
                    }
                ];

                // Show/hide pricing card
                if (isVariant) {
                    pricingCard.style.display = 'none';
                } else {
                    pricingCard.style.display = 'block';
                }

                fields.forEach(({
                    wrapper,
                    input,
                    name
                }) => {
                    if (isVariant) {
                        if (wrapper) wrapper.style.display = 'none';
                        if (name) input.removeAttribute('name');
                        input.removeAttribute('required');
                    } else {
                        if (wrapper) wrapper.style.display = 'block';
                        if (name) input.setAttribute('name', name);
                        if (name === 'quantity_in_stock' || name === 'original_price') {
                            input.setAttribute('required', 'required');
                        }
                    }
                });
            }

            // Handle multiple image upload with preview
            function handleImageUpload(input) {
                const files = input.files;

                if (files.length > 0) {
                    imagePreview.innerHTML = '';
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');

                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'position-relative d-inline-block me-2 mb-2';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-thumbnail';
                            img.style.maxWidth = '120px';
                            img.style.maxHeight = '120px';

                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className =
                                'btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle';
                            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                            removeBtn.style.width = '25px';
                            removeBtn.style.height = '25px';
                            removeBtn.style.padding = '0';
                            removeBtn.style.transform = 'translate(50%, -50%)';

                            removeBtn.onclick = function() {
                                const dt = new DataTransfer();
                                Array.from(imageInput.files).forEach((f, i) => {
                                    if (i !== index) dt.items.add(f);
                                });
                                imageInput.files = dt.files;
                                handleImageUpload(imageInput);
                            };

                            wrapper.appendChild(img);
                            wrapper.appendChild(removeBtn);
                            imagePreview.appendChild(wrapper);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    removeAllImages();
                }
            }

            // Remove all images
            function removeAllImages() {
                imageInput.value = '';
                imagePreview.classList.add('d-none');
                uploadPlaceholder.classList.remove('d-none');
                imagePreview.innerHTML = '';
            }

            // Drag and drop functionality
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

            // Form validation
            let allowSubmit = false;


            form.addEventListener('submit', function(e) {
                if (allowSubmit) return true; // Cho submit nếu đã xác nhận

                const isVariant = productTypeSelect.value === 'variant';
                const original = parseFloat(originalInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;

                if (!isVariant && discount > 0 && discount >= original) {
                    e.preventDefault();
                    alert('Giá khuyến mãi phải nhỏ hơn giá gốc!');
                    discountInput.focus();
                    return false;
                }

                allowSubmit = true;
                form.submit(); // Submit lại lần nữa
            });


            // Reset form function
            window.resetForm = function() {
                if (confirm('Bạn có chắc chắn muốn đặt lại form? Tất cả dữ liệu sẽ bị xóa.')) {
                    form.reset();
                    removeAllImages();
                    originalDisplay.textContent = '0 VND';
                    discountedDisplay.textContent = '0 VND';
                    togglePriceAndStockFields();
                }
            };

            // Event listeners
            productTypeSelect.addEventListener('change', togglePriceAndStockFields);
            imageInput.addEventListener('change', function() {
                handleImageUpload(this);
            });

            // Drag and drop events
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

            // Price calculation events
            percentInput.addEventListener('input', updateDiscountedPrice);
            originalInput.addEventListener('input', updateDiscountedPrice);
            discountInput.addEventListener('input', function() {
                const price = this.value || 0;
                discountedDisplay.textContent = formatVND(price);
            });

            // Initialize on load
            togglePriceAndStockFields();
            updateDiscountedPrice();
        });

        // Global remove image function (for backward compatibility)
        function removeImage() {
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');

            imageInput.value = '';
            imagePreview.classList.add('d-none');
            uploadPlaceholder.classList.remove('d-none');
            imagePreview.innerHTML = '';
        }
    </script>
@endsection

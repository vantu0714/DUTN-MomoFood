@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            Thêm biến thể cho sản phẩm
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Product Selector -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="productSelector" class="form-label fw-bold">
                                    <i class="fas fa-search me-1"></i>
                                    Chọn sản phẩm:
                                </label>
                                <select id="productSelector" class="form-select form-select-lg shadow-sm">
                                    <option value="">-- Chọn sản phẩm để thêm biến thể --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_name }}
                                            ({{ $product->product_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <form action="{{ route('admin.product_variants.storeMultiple') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @foreach ($products as $product)
                                @php
                                    $existingCombinations = [];
                                    foreach ($product->variants as $variant) {
                                        $flavor = optional(
                                            $variant->attributeValues->firstWhere('attribute.name', 'Vị') ??
                                                $variant->attributeValues->firstWhere('attribute.name', 'Đạng'),
                                        )->value;

                                        $size = optional(
                                            $variant->attributeValues->firstWhere('attribute.name', 'Size'),
                                        )->value;

                                        if ($flavor && $size) {
                                            $existingCombinations[] = strtolower($flavor . '|' . $size);
                                        }
                                    }
                                @endphp
                                <div class="product-variant-group d-none" data-product-id="{{ $product->id }}">
                                    <input type="hidden" class="existing-variants" value='@json($existingCombinations)'>
                                    <!-- Product Info Card -->
                                    <div class="card border-start border-primary border-4 mb-4">
                                        <div class="card-body bg-light">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h4 class="text-primary mb-1">
                                                        <i class="fas fa-tag me-2"></i>
                                                        {{ $product->product_name }}
                                                    </h4>
                                                    <p class="text-muted mb-0">
                                                        <span class="badge bg-secondary">{{ $product->product_code }}</span>
                                                        <span class="ms-3">Giá gốc: <strong
                                                                class="text-success">{{ number_format($product->original_price) }}₫</strong></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Existing Variants - Updated to show more info --}}
                                    @if ($product->variants->count())
                                        <div class="card mb-4">
                                            <div class="card-header bg-info text-white">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-list me-2"></i>
                                                    Biến thể hiện có ({{ $product->variants->count() }})
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Hình ảnh</th>
                                                                <th>SKU</th>
                                                                <th>Vị</th>
                                                                <th>Size</th>
                                                                <th>Giá</th>
                                                                <th>Tồn kho</th>
                                                                <th>Trạng thái</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($product->variants as $variant)
                                                                @php
                                                                    $flavor =
                                                                        $variant->attributeValues->firstWhere(
                                                                            'attribute.name',
                                                                            'Vị',
                                                                        )?->value ??
                                                                        $variant->attributeValues->firstWhere(
                                                                            'attribute.name',
                                                                            'Đạng',
                                                                        )?->value;
                                                                    $size = $variant->attributeValues->firstWhere(
                                                                        'attribute.name',
                                                                        'Size',
                                                                    )?->value;
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        @if ($variant->image)
                                                                            <img src="{{ asset('storage/' . $variant->image) }}"
                                                                                alt="Variant Image" class="img-thumbnail"
                                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                                        @else
                                                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                                                style="width: 50px; height: 50px; border-radius: 4px;">
                                                                                <i class="fas fa-image text-muted"></i>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <code
                                                                            class="bg-light text-dark px-2 py-1 rounded">{{ $variant->sku ?? '—' }}</code>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-primary">{{ $flavor ?? '—' }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-secondary">{{ $size ?? '—' }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <strong
                                                                            class="text-success">{{ number_format($variant->price) }}₫</strong>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $variant->quantity_in_stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                                                            {{ $variant->quantity_in_stock }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        @if ($variant->is_active ?? true)
                                                                            <span class="badge bg-success">Hoạt động</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">Tạm
                                                                                ngưng</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="hidden" name="products[{{ $product->id }}][product_id]"
                                        value="{{ $product->id }}">
                                    <input type="hidden" class="original-price" value="{{ $product->original_price }}">
                                    <input type="hidden" class="product-code" value="{{ $product->product_code }}">

                                    <!-- New Variants -->
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0">
                                                <i class="fas fa-plus-circle me-2"></i>
                                                Thêm biến thể mới
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="variants-container">
                                                <div class="variant-item mb-4">
                                                    <div class="card border-success">
                                                        <div class="card-header bg-light border-success">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0 text-success">
                                                                    <i class="fas fa-cube me-2"></i>
                                                                    Biến thể #1
                                                                </h6>
                                                                <button type="button"
                                                                    class="btn btn-outline-danger btn-sm remove-variant">
                                                                    <i class="fas fa-trash"></i> Xóa biến thể
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Main Attribute -->
                                                            <div class="row mb-4">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-bold">
                                                                        <i class="fas fa-tags me-1"></i>
                                                                        Thuộc tính chính (Vị)
                                                                    </label>
                                                                    <input type="hidden"
                                                                        name="products[{{ $product->id }}][variants][0][main_attribute][name]"
                                                                        value="Vị">
                                                                    <input type="text"
                                                                        name="products[{{ $product->id }}][variants][0][main_attribute][value]"
                                                                        class="form-control form-control-lg shadow-sm main-attribute-input"
                                                                        placeholder="Nhập vị (ví dụ: Cay, Ngọt, Chua...)"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <!-- Sub Attributes -->
                                                            <div class="sub-attributes-group">
                                                                <label class="form-label fw-bold mb-3">
                                                                    <i class="fas fa-cogs me-1"></i>
                                                                    Các lựa chọn Size
                                                                </label>

                                                                <div class="sub-attribute-row mb-3">
                                                                    <div class="card border-light">
                                                                        <div class="card-body">
                                                                            <div class="row align-items-end">
                                                                                <div class="col-md-2">
                                                                                    <label
                                                                                        class="form-label small">Size</label>
                                                                                    <select
                                                                                        name="products[{{ $product->id }}][variants][0][sub_attributes][0][attribute_value_id]"
                                                                                        class="form-select size-select"
                                                                                        required>
                                                                                        <option value="">Chọn size
                                                                                        </option>
                                                                                        @foreach ($sizeValues as $size)
                                                                                            <option
                                                                                                value="{{ $size->id }}">
                                                                                                {{ $size->value }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <label class="form-label small">Giá
                                                                                        (₫)
                                                                                    </label>
                                                                                    <input type="number"
                                                                                        name="products[{{ $product->id }}][variants][0][sub_attributes][0][price]"
                                                                                        class="form-control price-input"
                                                                                        placeholder="0" min="0"
                                                                                        step="1000" required>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <label class="form-label small">Số
                                                                                        lượng</label>
                                                                                    <input type="number"
                                                                                        name="products[{{ $product->id }}][variants][0][sub_attributes][0][quantity_in_stock]"
                                                                                        class="form-control"
                                                                                        placeholder="0" min="0"
                                                                                        required>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <label class="form-label small">Hình
                                                                                        ảnh</label>
                                                                                    <input type="file"
                                                                                        name="products[{{ $product->id }}][variants][0][sub_attributes][0][image]"
                                                                                        class="form-control"
                                                                                        accept="image/*">
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <label
                                                                                        class="form-label small">SKU</label>
                                                                                    <input type="text"
                                                                                        name="products[{{ $product->id }}][variants][0][sub_attributes][0][sku]"
                                                                                        class="form-control sku-input bg-light"
                                                                                        readonly>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-danger btn-sm remove-sub-attribute w-100">
                                                                                        <i class="fas fa-minus"></i> Xóa
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <button type="button"
                                                                class="btn btn-outline-primary add-sub-attribute">
                                                                <i class="fas fa-plus me-1"></i> Thêm size khác
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center mt-4">
                                                <button type="button" class="btn btn-success btn-lg add-variant-btn">
                                                    <i class="fas fa-plus-circle me-2"></i>
                                                    Thêm biến thể mới
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="text-center mt-4" id="submit-section" style="display: none;">
                                <button type="submit" class="btn btn-primary btn-lg px-5" id="submit-btn">
                                    <i class="fas fa-save me-2"></i>
                                    Lưu tất cả biến thể
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('admins/assets/js/variants.js') }}"></script>
@endpush

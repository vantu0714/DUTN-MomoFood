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
                                            $variant->attributeValues->firstWhere('attribute.name', 'khối lượng'),
                                        )->value;

                                        if ($flavor && $size) {
                                            $existingCombinations[] = strtolower($flavor . '|' . $size);
                                        }
                                    }
                                @endphp
                                <div class="product-variant-group" data-product-id="{{ $product->id }}"
                                    style="display: none;">
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
                                                                <th>khối lượng</th>
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
                                                                        'Khối lượng',
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
                                                                    Các lựa chọn khối lượng
                                                                </label>

                                                                <div class="sub-attribute-row mb-3">
                                                                    <div class="card border-light">
                                                                        <div class="card-body">
                                                                            <div class="row align-items-end">
                                                                                <div class="col-md-2">
                                                                                    <label class="form-label small">khối
                                                                                        lượng</label>

                                                                                    <select
                                                                                        name="products[{{ $product->id }}][variants][0][sub_attributes][0][attribute_value_id]"
                                                                                        class="form-select weight-select"
                                                                                        required>
                                                                                        <option value="">Chọn khối
                                                                                            lượng
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
                                                                <i class="fas fa-plus me-1"></i> Thêm khối lượng khác
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
                            <div class="text-center mt-4" id="submit-section">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // === Tiện ích ===
            const normalize = str => (str || '').toString().trim().toLowerCase().replace(/\s+/g, '-');
            const buildSku = (code, flavor, weight) =>
                `${code}-${normalize(flavor)}-${normalize(weight)}`.toUpperCase();

            // === Hiển thị nhóm biến thể theo sản phẩm ===
            function handleProductSelection() {
                const selector = document.getElementById('productSelector');
                const groups = document.querySelectorAll('.product-variant-group');
                const submitSection = document.getElementById('submit-section');

                selector?.addEventListener('change', function() {
                    const selectedId = this.value;
                    groups.forEach(group => {
                        if (group.dataset.productId === selectedId) {
                            group.style.display = 'block';
                            submitSection.style.display = 'block';
                        } else {
                            group.style.display = 'none';
                        }
                    });
                });
            }

            // === Tự động sinh SKU khi thay đổi vị / khối lượng ===
            function handleSkuAutoGenerate() {
                document.body.addEventListener('change', function(e) {
                    if (e.target.matches('.weight-select, .size-select')) {
                        const row = e.target.closest('.sub-attribute-row');
                        const group = e.target.closest('.product-variant-group');

                        const flavorInput = group.querySelector('.main-attribute-input');
                        const weightSelect = row.querySelector('.weight-select, .size-select');
                        const skuInput = row.querySelector('.sku-input');
                        const productCode = group.querySelector('.product-code')?.value;

                        const flavor = flavorInput?.value;
                        const weight = weightSelect?.selectedOptions[0]?.text;

                        if (productCode && flavor && weight) {
                            skuInput.value = buildSku(productCode, flavor, weight);
                        }
                    }
                });
            }

            // === Thêm biến thể mới ===
            function handleAddVariant() {
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.add-variant-btn');
                    if (!btn) return;

                    const group = btn.closest('.product-variant-group');
                    const container = group.querySelector('.variants-container');
                    const productId = group.dataset.productId;
                    const variantItems = container.querySelectorAll('.variant-item');
                    const newIndex = variantItems.length;

                    const firstVariant = variantItems[0];
                    const newVariant = firstVariant.cloneNode(true);

                    // Reset input trong biến thể mới
                    newVariant.querySelectorAll('input, select').forEach(input => {
                        input.value = '';
                        input.classList.remove('is-invalid');
                    });

                    // Cập nhật lại chỉ số name
                    const subAttrRows = newVariant.querySelectorAll('.sub-attribute-row');
                    subAttrRows.forEach((row, subIndex) => {
                        row.querySelectorAll('[name]').forEach(el => {
                            el.name = el.name
                                .replace(/\[variants]\[\d+]/g, `[variants][${newIndex}]`)
                                .replace(/\[sub_attributes]\[\d+]/g,
                                    `[sub_attributes][${subIndex}]`);
                        });
                    });

                    newVariant.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/\[variants]\[\d+]/g, `[variants][${newIndex}]`);
                    });

                    // Cập nhật tiêu đề
                    const header = newVariant.querySelector('.card-header h6');
                    if (header) {
                        header.innerHTML = `<i class="fas fa-cube me-2"></i> Biến thể #${newIndex + 1}`;
                    }

                    container.appendChild(newVariant);
                });
            }

            // === Xoá biến thể ===
            function handleRemoveVariant() {
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.remove-variant');
                    if (!btn) return;

                    const item = btn.closest('.variant-item');
                    const container = item.closest('.variants-container');
                    if (container.querySelectorAll('.variant-item').length > 1) {
                        item.remove();
                    } else {
                        alert('Phải có ít nhất 1 biến thể.');
                    }
                });
            }

            // === Thêm khối lượng ===
            function handleAddSubAttribute() {
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.add-sub-attribute');
                    if (!btn) return;

                    const variantItem = btn.closest('.variant-item');
                    const subAttrGroup = variantItem.querySelector('.sub-attributes-group');
                    const subAttrRows = subAttrGroup.querySelectorAll('.sub-attribute-row');
                    const newIndex = subAttrRows.length;

                    const firstRow = subAttrRows[0];
                    const newRow = firstRow.cloneNode(true);

                    // Reset input
                    newRow.querySelectorAll('input, select').forEach(input => {
                        input.value = '';
                        input.classList.remove('is-invalid');
                    });

                    // Cập nhật lại name
                    newRow.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/\[sub_attributes]\[\d+]/g,
                            `[sub_attributes][${newIndex}]`);
                    });

                    subAttrGroup.appendChild(newRow);
                });
            }

            // === Xoá khối lượng ===
            function handleRemoveSubAttribute() {
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.remove-sub-attribute');
                    if (!btn) return;

                    const row = btn.closest('.sub-attribute-row');
                    const group = row.closest('.sub-attributes-group');

                    if (group.querySelectorAll('.sub-attribute-row').length > 1) {
                        row.remove();
                    } else {
                        alert('Phải có ít nhất 1 khối lượng cho mỗi biến thể.');
                    }
                });
            }

            // === Validate trước khi submit ===
            function handleFormValidation() {
                const submitBtn = document.getElementById('submit-btn');
                if (!submitBtn) return;

                submitBtn.addEventListener('click', function(e) {
                    const selectedGroup = [...document.querySelectorAll('.product-variant-group')]
                        .find(el => el.offsetParent !== null);

                    if (!selectedGroup) {
                        console.warn('Không tìm thấy nhóm sản phẩm đang hiển thị!');
                        e.preventDefault();
                        return;
                    }

                    const flavorInput = selectedGroup.querySelector('.main-attribute-input');
                    const flavor = flavorInput?.value.trim();
                    if (!flavor) {
                        flavorInput.classList.add('is-invalid');
                        flavorInput.focus();
                        alert('Vui lòng nhập thuộc tính vị!');
                        e.preventDefault();
                        return;
                    }

                    const subRows = selectedGroup.querySelectorAll('.sub-attribute-row');
                    const seenCombos = new Set();

                    for (let row of subRows) {
                        const weightSelect = row.querySelector('.weight-select, .size-select');
                        const weightValue = weightSelect?.value;
                        const weightText = weightSelect?.selectedOptions[0]?.text.trim();

                        if (!weightValue) {
                            weightSelect.classList.add('is-invalid');
                            alert('Vui lòng chọn khối lượng!');
                            e.preventDefault();
                            return;
                        }

                        const key = `${normalize(flavor)}|${normalize(weightText)}`;
                        if (seenCombos.has(key)) {
                            alert(`Biến thể "${flavor}" và khối lượng "${weightText}" đã bị trùng!`);
                            e.preventDefault();
                            return;
                        }

                        seenCombos.add(key);
                    }
                    selectedGroup.closest('form').submit();
                });

            }

            // === Gọi tất cả hàm xử lý ===
            function init() {
                handleProductSelection();
                handleSkuAutoGenerate();
                handleAddVariant();
                handleRemoveVariant();
                handleAddSubAttribute();
                handleRemoveSubAttribute();
                handleFormValidation();
            }

            init(); // Khởi động
        });
    </script>

{{-- @push('scripts')
    <script src="{{ asset('admins/assets/js/variants.js') }}"></script>
@endpush --}}

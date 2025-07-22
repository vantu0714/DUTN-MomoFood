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
                                    <optgroup label="Sản phẩm chưa có biến thể">
                                        @foreach ($products->where('variants_count', 0) as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }} ({{ $product->product_code }}) - Chưa có biến
                                                thể
                                            </option>
                                        @endforeach
                                    </optgroup>

                                    <optgroup label="Sản phẩm đã có biến thể">
                                        @foreach ($products->where('variants_count', '>', 0) as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }} ({{ $product->product_code }}) - Đã có
                                                {{ $product->variants_count }} biến thể
                                            </option>
                                        @endforeach
                                    </optgroup>
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
                                                        <span
                                                            class="badge bg-secondary">{{ $product->product_code }}</span>
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
                                                                                        class="form-control quantity-input"
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
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="text-center mt-4" id="submit-section">
                                <button type="submit" class="btn btn-primary btn-lg px-5" id="save-variants">
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

        // === Tự động sinh SKU ===
        function handleSkuAutoGenerate() {
            document.body.addEventListener('change', function(e) {
                if (
                    e.target.matches('.weight-select, .size-select') ||
                    e.target.matches('.main-attribute-input')
                ) {
                    const row = e.target.closest('.sub-attribute-row');
                    const variantItem = e.target.closest('.variant-item');
                    const group = e.target.closest('.product-variant-group');

                    const flavorInput = variantItem.querySelector('.main-attribute-input');
                    const weightSelect = row.querySelector('.weight-select, .size-select');
                    const skuInput = row.querySelector('.sku-input');
                    const productCode = group.querySelector('.product-code')?.value;

                    const flavor = flavorInput?.value.trim();
                    const weight = weightSelect?.selectedOptions[0]?.text.trim();

                    if (productCode && flavor && weight) {
                        skuInput.value = buildSku(productCode, flavor, weight);
                    }
                }
            });
        }

        // === Thêm biến thể ===
        function handleAddVariant() {
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.add-variant-btn');
                if (!btn) return;

                const group = btn.closest('.product-variant-group');
                if (!group) return;

                const container = group.querySelector('.variants-container');
                const productId = group.dataset.productId;
                const variantItems = container.querySelectorAll('.variant-item');
                const newIndex = variantItems.length;
                const firstVariant = variantItems[0];
                if (!firstVariant) return;

                const newVariant = firstVariant.cloneNode(true);
                newVariant.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else {
                        el.value = '';
                    }
                    el.classList.remove('is-invalid');
                });

                newVariant.querySelectorAll('[name]').forEach(el => {
                    const oldName = el.name;
                    const updatedName = oldName
                        .replace(/products\[\d+]\[variants]\[\d+]/,
                            `products[${productId}][variants][${newIndex}]`)
                        .replace(/\[sub_attributes]\[\d+]/g, `[sub_attributes][0]`);
                    el.name = updatedName;
                });

                const header = newVariant.querySelector('.card-header h6');
                if (header) {
                    header.innerHTML = `<i class="fas fa-cube me-2"></i> Biến thể #${newIndex + 1}`;
                }

                container.appendChild(newVariant);
                updateDisabledWeights(group);
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
                    updateDisabledWeights(container.closest('.product-variant-group'));
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

                newRow.querySelectorAll('input, select').forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid');
                });

                newRow.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/\[sub_attributes]\[\d+]/g,
                        `[sub_attributes][${newIndex}]`);
                });

                subAttrGroup.appendChild(newRow);
                updateDisabledWeights(variantItem.closest('.product-variant-group'));
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
                    updateDisabledWeights(group.closest('.product-variant-group'));
                } else {
                    alert('Phải có ít nhất 1 khối lượng cho mỗi biến thể.');
                }
            });
        }

        // === Cập nhật khối lượng đã chọn để ẩn option ===
        function updateDisabledWeights(group) {
            group.querySelectorAll('.variant-item').forEach(variant => {
                const usedWeights = new Set();
                const rows = variant.querySelectorAll('.sub-attribute-row');

                // Thu thập các weight đã chọn trong cùng 1 biến thể
                rows.forEach(row => {
                    const select = row.querySelector('.weight-select, .size-select');
                    if (select?.value) {
                        usedWeights.add(select.value);
                    }
                });

                // Disable trong cùng biến thể nếu trùng
                rows.forEach(row => {
                    const select = row.querySelector('.weight-select, .size-select');
                    const currentValue = select?.value;
                    const options = select?.querySelectorAll('option') || [];
                    options.forEach(option => {
                        if (option.value === '') return;
                        option.disabled = usedWeights.has(option.value) && option
                            .value !== currentValue;
                    });
                });
            });
        }


        // === Validate trước khi submit ===
        function handleFormValidation() {
            const submitBtn = document.getElementById('save-variants');
            if (!submitBtn) return;

            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const selectedGroup = document.querySelector(
                    '.product-variant-group:not([style*="display: none"])');
                if (!selectedGroup) return;

                const variantItems = selectedGroup.querySelectorAll('.variant-item');

                const flavorMap = new Map();

                for (let i = 0; i < variantItems.length; i++) {
                    const variant = variantItems[i];
                    const flavorInput = variant.querySelector('.main-attribute-input');
                    const flavor = normalize(flavorInput?.value);

                    // Kiểm tra ô vị
                    if (!flavor) {
                        flavorInput?.classList.add('is-invalid');
                        alert(`Vui lòng nhập vị cho biến thể thứ ${i + 1}`);
                        return;
                    }

                    if (flavorMap.has(flavor)) {
                        flavorInput?.classList.add('is-invalid');
                        alert(`Vị "${flavorInput.value}" bị trùng!`);
                        return;
                    } else {
                        flavorMap.set(flavor, true);
                        flavorInput?.classList.remove('is-invalid');
                    }

                    const subRows = variant.querySelectorAll('.sub-attribute-row');
                    const weightSet = new Set();
                    const existingVariants = JSON.parse(selectedGroup.querySelector(
                        '.existing-variants')?.value || '[]');

                    for (let row of subRows) {
                        const weightSelect = row.querySelector('.weight-select, .size-select');
                        const selectedWeight = normalize(weightSelect?.selectedOptions[0]?.text);

                        const priceInput = row.querySelector('.price-input');
                        const quantityInput = row.querySelector('.quantity-input');

                        // Kiểm tra đã chọn khối lượng chưa
                        if (!selectedWeight) {
                            weightSelect?.classList.add('is-invalid');
                            alert(`Vui lòng chọn khối lượng cho vị "${flavorInput.value}"`);
                            return;
                        }

                        // Trùng khối lượng trong cùng vị
                        if (weightSet.has(selectedWeight)) {
                            weightSelect?.classList.add('is-invalid');
                            alert(
                                `Khối lượng "${selectedWeight}" bị trùng trong cùng vị "${flavorInput.value}"`
                            );
                            return;
                        } else {
                            weightSet.add(selectedWeight);
                            weightSelect?.classList.remove('is-invalid');
                        }

                        // Trùng với dữ liệu đã có
                        if (existingVariants.some(item =>
                                normalize(item.flavor) === flavor && normalize(item.size) ===
                                selectedWeight)) {
                            weightSelect?.classList.add('is-invalid');
                            alert(
                                `Khối lượng "${selectedWeight}" cho vị "${flavorInput.value}" đã tồn tại trong hệ thống!`
                            );
                            return;
                        }

                        // Kiểm tra giá
                        const price = parseFloat(priceInput?.value);
                        if (!priceInput || isNaN(price) || price <= 0) {
                            priceInput?.classList.add('is-invalid');
                            alert(
                                `Giá không hợp lệ cho vị "${flavorInput.value}" và khối lượng "${selectedWeight}"`
                            );
                            return;
                        } else {
                            priceInput?.classList.remove('is-invalid');
                        }

                        // Kiểm tra số lượng
                        const quantity = parseInt(quantityInput?.value);
                        if (!quantityInput || isNaN(quantity) || quantity <= 0) {
                            quantityInput?.classList.add('is-invalid');
                            alert(
                                `Số lượng không hợp lệ cho vị "${flavorInput.value}" và khối lượng "${selectedWeight}"`
                            );
                            return;
                        } else {
                            quantityInput?.classList.remove('is-invalid');
                        }
                    }
                }

                // Nếu tất cả hợp lệ → Submit form
                selectedGroup.closest('form').submit();
            });
        }

        function handleLiveFlavorDuplicateCheck() {
            document.body.addEventListener('input', function(e) {
                if (!e.target.classList.contains('main-attribute-input')) return;

                const currentInput = e.target;
                const currentValue = normalize(currentInput.value);
                const allFlavorInputs = document.querySelectorAll('.main-attribute-input');

                let isDuplicate = false;

                allFlavorInputs.forEach(input => {
                    const value = normalize(input.value);

                    if (
                        value !== '' &&
                        value === currentValue &&
                        input !== currentInput
                    ) {
                        isDuplicate = true;
                    }
                });

                // Xử lý hiển thị lỗi
                if (isDuplicate) {
                    currentInput.classList.add('is-invalid');
                    // Nếu chưa có thông báo lỗi thì thêm
                    if (!currentInput.nextElementSibling || !currentInput.nextElementSibling.classList
                        .contains('invalid-feedback')) {
                        const msg = document.createElement('div');
                        msg.classList.add('invalid-feedback');
                        msg.textContent = 'Vị này đã được thêm ở biến thể khác!';
                        currentInput.insertAdjacentElement('afterend', msg);
                    }
                } else {
                    currentInput.classList.remove('is-invalid');
                    const next = currentInput.nextElementSibling;
                    if (next && next.classList.contains('invalid-feedback')) {
                        next.remove();
                    }
                }
            });
        }




        // === Khởi chạy toàn bộ ===
        function init() {
            handleProductSelection();
            handleSkuAutoGenerate();
            handleAddVariant();
            handleRemoveVariant();
            handleAddSubAttribute();
            handleRemoveSubAttribute();
            handleFormValidation();
            handleLiveFlavorDuplicateCheck();
        }

        init(); // Bắt đầu
    });
</script>
<style>
    /* CSS cải thiện layout form - thêm vào cuối file CSS hiện tại */

    /* Container chính */
    .container-fluid {
        padding: 20px;
    }

    /* Card chính */
    .card {
        margin-bottom: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Header card */
    .card-header {
        padding: 20px;
        border-radius: 15px 15px 0 0 !important;
    }

    .card-body {
        padding: 30px;
    }

    /* Product selector */
    #productSelector {
        margin-bottom: 30px;
        padding: 15px;
        font-size: 16px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    #productSelector:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    /* Product variant groups */
    .product-variant-group {
        margin-bottom: 40px;
        clear: both;
    }

    /* Product info card */
    .product-variant-group .card:first-child {
        margin-bottom: 25px;
    }

    /* Existing variants table */
    .table-responsive {
        margin: 20px 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 15px;
        border: none;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
    }

    /* Variant items */
    .variant-item {
        margin-bottom: 30px;
        clear: both;
    }

    .variant-item .card {
        border: 2px solid #e9ecef;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .variant-item .card:hover {
        border-color: #007bff;
        box-shadow: 0 5px 25px rgba(0, 123, 255, 0.15);
    }

    /* Card headers trong variant */
    .variant-item .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 15px 20px;
    }

    /* Form groups */
    .row.mb-4 {
        margin-bottom: 25px !important;
    }

    .row.mb-3 {
        margin-bottom: 20px !important;
    }

    /* Sub attributes */
    .sub-attributes-group {
        margin-top: 25px;
    }

    .sub-attribute-row {
        margin-bottom: 20px;
    }

    .sub-attribute-row .card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .sub-attribute-row .card:hover {
        border-color: #28a745;
        background-color: #f8fff9;
    }

    /* Form controls trong sub-attribute */
    .sub-attribute-row .form-control,
    .sub-attribute-row .form-select {
        margin-bottom: 0;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #ced4da;
    }

    .sub-attribute-row .form-control:focus,
    .sub-attribute-row .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
    }

    /* Labels */
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #495057;
    }

    .form-label.small {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 5px;
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 10px 20px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .add-sub-attribute {
        margin-top: 15px;
        margin-bottom: 20px;
    }

    .remove-variant,
    .remove-sub-attribute {
        white-space: nowrap;
    }

    /* Submit section */
    #submit-section {
        margin-top: 40px;
        padding: 30px 0;
        border-top: 2px solid #e9ecef;
    }

    #submit-btn {
        padding: 15px 40px;
        font-size: 18px;
        font-weight: 600;
        border-radius: 50px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        transition: all 0.4s ease;
    }

    #submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 123, 255, 0.3);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .sub-attribute-row .row {
            margin: 0;
        }

        .sub-attribute-row .col-md-2 {
            margin-bottom: 15px;
        }

        .table-responsive {
            font-size: 14px;
        }
    }

    /* Spacing cho columns */
    .row>[class*="col-"] {
        padding-left: 10px;
        padding-right: 10px;
    }

    /* Badge styling */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    /* Image thumbnails */
    .img-thumbnail {
        border-radius: 8px;
        padding: 2px;
    }

    /* Code styling */
    code {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.875rem;
    }
</style>

{{-- @push('scripts')
    <script src="{{ asset('admins/assets/js/variants.js') }}"></script>
@endpush --}}
{{-- <div class="text-center mt-4">
                                                <button type="button" class="btn btn-success btn-lg add-variant-btn">
                                                    <i class="fas fa-plus-circle me-2"></i>
                                                    Thêm biến thể mới
                                                </button>
                                            </div> --}}

@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2>Thêm biến thể cho sản phẩm</h2>

        <div class="mb-4">
            <label for="productSelector" class="form-label">Chọn sản phẩm:</label>
            <select id="productSelector" class="form-select">
                <option value="">-- Chọn sản phẩm --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->product_code }})</option>
                @endforeach
            </select>
        </div>

        <form method="POST" action="{{ route('admin.product_variants.storeMultiple') }}" enctype="multipart/form-data">
            @csrf

            @foreach ($products as $product)
                @php
                    $existingCombinations = [];
                    foreach ($product->variants as $variant) {
                        $flavor = $variant->attributeValues->firstWhere('attribute.name', 'Đạng')?->value;
                        $size = $variant->attributeValues->firstWhere('attribute.name', 'Size')?->value;
                        if ($flavor && $size) {
                            $existingCombinations[] = strtolower($flavor . '|' . $size);
                        }
                    }
                @endphp

                <div class="product-variant-group border p-3 mb-4 d-none" data-product-id="{{ $product->id }}">
                    <input type="hidden" class="existing-variants" value='@json($existingCombinations)'>
                    <h4>Sản phẩm: <strong>{{ $product->product_name }}</strong> ({{ $product->product_code }})</h4>
                    {{-- Biến thể đã có --}}
                    @if ($product->variants->count())
                        <div class="mb-3">
                            <label><strong>Biến thể đã có:</strong></label>
                            <ul class="list-group small">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $flavor =
                                            $variant->attributeValues->firstWhere('attribute.name', 'Vị')?->value ??
                                            $variant->attributeValues->firstWhere('attribute.name', 'Đạng')?->value;
                                        $size = $variant->attributeValues->firstWhere('attribute.name', 'Size')?->value;
                                    @endphp
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            Vị: <strong>{{ $flavor ?? '—' }}</strong>,
                                            Size: <strong>{{ $size ?? '—' }}</strong>
                                        </span>
                                        <span>
                                            Giá: {{ number_format($variant->price) }}₫,
                                            SL: {{ $variant->quantity_in_stock }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <input type="hidden" name="products[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                    <input type="hidden" class="original-price" value="{{ $product->original_price }}">
                    <input type="hidden" class="product-code" value="{{ $product->product_code }}">

                    <div class="variants-container">
                        <div class="variant-item border p-3 mb-3">
                            <h5>Biến thể #1</h5>
                            <div class="mb-3">
                                <label>Thuộc tính chính (Vị)</label>
                                <input type="text"
                                    name="products[{{ $product->id }}][variants][0][main_attribute][name]" value="Vị"
                                    class="form-control" readonly>
                                <input type="text"
                                    name="products[{{ $product->id }}][variants][0][main_attribute][value]"
                                    class="form-control mt-2" placeholder="Nhập ví dụ: Cay">
                            </div>

                            <div class="sub-attributes-group mb-3">
                                <label>Các lựa chọn (Size, Giá, Số lượng, Ảnh, SKU)</label>
                                <div class="row sub-attribute-row mb-2 align-items-end">
                                    <div class="col-md-2">
                                        <select
                                            name="products[{{ $product->id }}][variants][0][sub_attributes][0][attribute_value_id]"
                                            class="form-select">
                                            @foreach ($sizeValues as $size)
                                                <option value="{{ $size->id }}">{{ $size->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number"
                                            name="products[{{ $product->id }}][variants][0][sub_attributes][0][price]"
                                            class="form-control" placeholder="Giá">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number"
                                            name="products[{{ $product->id }}][variants][0][sub_attributes][0][quantity_in_stock]"
                                            class="form-control" placeholder="Số lượng">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="file"
                                            name="products[{{ $product->id }}][variants][0][sub_attributes][0][image]"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text"
                                            name="products[{{ $product->id }}][variants][0][sub_attributes][0][sku]"
                                            class="form-control sku-input" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-sub-attribute">Xoá lựa
                                            chọn</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary add-sub-attribute">+ Thêm lựa
                                chọn</button>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-variant float-end">🗑 Xoá
                                biến thể</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-info btn-sm mt-2 add-variant-btn">+ Thêm biến thể</button>
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary mt-4">Lưu tất cả</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelector = document.getElementById('productSelector');
            const allVariantGroups = document.querySelectorAll('.product-variant-group');

            productSelector.addEventListener('change', function() {
                const selectedId = this.value;
                allVariantGroups.forEach(group => {
                    group.classList.add('d-none');
                    if (group.dataset.productId === selectedId) {
                        group.classList.remove('d-none');
                    }
                });
            });

            const buildSku = (productCode, flavor, size) => {
                return `${productCode}-${flavor}-${size}`.toUpperCase().replace(/\s+/g, '-');
            };

            document.querySelectorAll('.product-variant-group').forEach((group) => {
                const productCode = group.querySelector('.product-code').value;
                const originalPrice = parseFloat(group.querySelector('.original-price').value);
                const existingVariants = JSON.parse(group.querySelector('.existing-variants').value ||
                '[]');
                let variantIndex = 1;

                const attachEvents = (variantItem, variantIdx) => {
                    const mainInput = variantItem.querySelector(
                        'input[name*="[main_attribute][value]"]');

                    const updateSkus = () => {
                        const flavor = mainInput.value.trim().toLowerCase();
                        variantItem.querySelectorAll('.sub-attribute-row').forEach(row => {
                            const sizeSelect = row.querySelector('select');
                            const sizeText = sizeSelect?.selectedOptions[0].text.trim()
                                .toLowerCase();
                            const skuInput = row.querySelector('.sku-input');
                            const sku = buildSku(productCode, flavor, sizeText);
                            skuInput.value = sku;
                        });
                    };

                    mainInput.addEventListener('input', updateSkus);
                    variantItem.querySelectorAll('select').forEach(select => {
                        select.addEventListener('change', updateSkus);
                    });

                    variantItem.querySelectorAll('.remove-sub-attribute').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const row = e.target.closest('.sub-attribute-row');
                            const group = row.parentElement;
                            if (group.querySelectorAll('.sub-attribute-row').length > 1)
                                row.remove();
                        });
                    });

                    variantItem.querySelector('.add-sub-attribute')?.addEventListener('click', () => {
                        const container = variantItem.querySelector('.sub-attributes-group');
                        const rows = container.querySelectorAll('.sub-attribute-row');
                        const newRow = rows[0].cloneNode(true);
                        const subIdx = rows.length;

                        newRow.querySelectorAll('input').forEach(input => input.value = '');
                        newRow.querySelector('select').selectedIndex = 0;

                        newRow.querySelectorAll('input, select').forEach(el => {
                            el.name = el.name.replace(/sub_attributes\[\d+\]/g,
                                `sub_attributes[${subIdx}]`);
                        });

                        container.appendChild(newRow);
                        updateSkus();
                    });

                    variantItem.querySelector('.remove-variant')?.addEventListener('click', () => {
                        if (group.querySelectorAll('.variant-item').length > 1) variantItem
                            .remove();
                    });

                    updateSkus();
                };

                group.querySelector('.add-variant-btn').addEventListener('click', () => {
                    const container = group.querySelector('.variants-container');
                    const template = container.querySelector('.variant-item');
                    const newVariant = template.cloneNode(true);
                    const newIdx = variantIndex++;

                    newVariant.querySelectorAll('input, select').forEach(el => {
                        if (el.type !== 'file') el.value = '';
                        el.name = el.name.replace(/variants\[\d+\]/g, `variants[${newIdx}]`)
                            .replace(/sub_attributes\[\d+\]/g, 'sub_attributes[0]');
                    });

                    newVariant.querySelector('h5').textContent = `Biến thể #${newIdx + 1}`;
                    container.appendChild(newVariant);
                    attachEvents(newVariant, newIdx);
                });

                attachEvents(group.querySelector('.variant-item'), 0);
            });

            document.querySelector('form').addEventListener('submit', function(e) {
                let valid = true;

                document.querySelectorAll('.product-variant-group:not(.d-none)').forEach(group => {
                    const existingVariants = JSON.parse(group.querySelector('.existing-variants')
                        ?.value || '[]');
                    const flavorInput = group.querySelector(
                        'input[name*="[main_attribute][value]"]');
                    const flavor = flavorInput?.value.trim().toLowerCase();

                    group.querySelectorAll('.sub-attribute-row').forEach(row => {
                        const sizeText = row.querySelector('select')?.selectedOptions[0]
                            ?.text.trim().toLowerCase();
                        const price = parseFloat(row.querySelector('input[name*="[price]"]')
                            ?.value || 0);
                        const originalPrice = parseFloat(group.querySelector(
                            '.original-price').value);

                        const key = `${flavor}|${sizeText}`;
                        if (existingVariants.includes(key)) {
                            alert(`Biến thể ${flavor} - ${sizeText} đã tồn tại.`);
                            valid = false;
                        }

                        if (!isNaN(price) && price < originalPrice) {
                            alert('Giá biến thể không được thấp hơn giá gốc.');
                            valid = false;
                        }
                    });
                });

                if (!valid) e.preventDefault();
            });
        });
    </script>
@endpush

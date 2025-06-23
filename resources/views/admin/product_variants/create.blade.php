@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Thêm biến thể cho sản phẩm: <strong>{{ $product->product_name }}</strong></h2>

    <form method="POST" action="{{ route('admin.product_variants.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" id="original_price" value="{{ $product->original_price }}">
        <input type="hidden" id="product_code" value="{{ $product->product_code }}">

        <div id="variants-container">
            <div class="variant-item border p-3 mb-4">
                <h5>Biến thể #1</h5>

                <div class="mb-3">
                    <label>Thuộc tính chính (Vị)</label>
                    <input type="text" name="variants[0][main_attribute][name]" value="Vị" class="form-control" readonly>
                    <input type="text" name="variants[0][main_attribute][value]" class="form-control mt-2" placeholder="Nhập ví dụ: Cay">
                </div>

                <div class="sub-attributes-group mb-3">
                    <label>Các lựa chọn (Size, Giá, Số lượng)</label>
                    <div class="row sub-attribute-row mb-2">
                        <div class="col-md-3">
                            <select name="variants[0][sub_attributes][0][attribute_value_id]" class="form-select">
                                @foreach ($sizeValues as $size)
                                    <option value="{{ $size->id }}">{{ $size->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="variants[0][sub_attributes][0][price]" class="form-control" placeholder="Giá">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="variants[0][sub_attributes][0][quantity]" class="form-control" placeholder="Số lượng">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger remove-sub-attribute">Xoá</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary add-sub-attribute">+ Thêm lựa chọn</button>
                </div>

                <div class="mb-3">
                    <label>Ảnh biến thể</label>
                    <input type="file" name="variants[0][image]" class="form-control">
                </div>

                <div class="mb-3">
                    <label>SKU</label>
                    <input type="text" name="variants[0][sku]" class="form-control sku-input" readonly>
                </div>

                <button type="button" class="btn btn-danger remove-variant">Xoá biến thể</button>
            </div>
        </div>

        <button type="button" class="btn btn-info mb-3" id="add-variant">+ Thêm biến thể</button>
        <br>
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let variantIndex = 1;

    function generateSKU(variantItem) {
        const productCode = document.getElementById('product_code').value;
        const mainAttr = variantItem.querySelector('input[name*="[main_attribute][value]"]').value.trim();
        const skuInput = variantItem.querySelector('input[name*="[sku]"]');

        let sku = productCode;
        if (mainAttr) {
            sku += '-' + mainAttr.toUpperCase().replace(/\s+/g, '-');
        }

        skuInput.value = sku;
    }

    function attachSKUEvents(variantItem) {
        const mainInput = variantItem.querySelector('input[name*="[main_attribute][value]"]');
        mainInput?.addEventListener('input', () => generateSKU(variantItem));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const variantsContainer = document.getElementById('variants-container');

        const firstVariant = document.querySelector('.variant-item');
        attachSKUEvents(firstVariant);
        generateSKU(firstVariant);

        document.getElementById('add-variant').addEventListener('click', function () {
            const template = document.querySelector('.variant-item');
            const clone = template.cloneNode(true);

            clone.querySelector('h5').textContent = `Biến thể #${variantIndex + 1}`;

            clone.querySelectorAll('input, select').forEach(el => {
                if (el.type !== 'file') el.value = '';
                let name = el.getAttribute('name');
                if (name) {
                    name = name.replace(/variants\[\d+]/, `variants[${variantIndex}]`);
                    name = name.replace(/sub_attributes\[\d+]/g, 'sub_attributes[0]');
                    el.setAttribute('name', name);
                }
            });

            const subRows = clone.querySelectorAll('.sub-attribute-row');
            for (let i = 1; i < subRows.length; i++) subRows[i].remove();

            variantsContainer.appendChild(clone);
            attachSKUEvents(clone);
            generateSKU(clone);
            variantIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant')) {
                const items = document.querySelectorAll('.variant-item');
                if (items.length > 1) e.target.closest('.variant-item').remove();
            }

            if (e.target.classList.contains('add-sub-attribute')) {
                const variantItem = e.target.closest('.variant-item');
                const group = variantItem.querySelector('.sub-attributes-group');
                const rows = group.querySelectorAll('.sub-attribute-row');
                const lastRow = rows[rows.length - 1];
                const newRow = lastRow.cloneNode(true);

                const variantIdx = variantItem.querySelector('input[name^="variants["]').name.match(/variants\[(\d+)]/)[1];
                const subIdx = rows.length;
                const fields = ['attribute_value_id', 'price', 'quantity'];

                newRow.querySelectorAll('input, select').forEach((el, i) => {
                    el.value = '';
                    el.name = `variants[${variantIdx}][sub_attributes][${subIdx}][${fields[i]}]`;
                });

                group.insertBefore(newRow, e.target);
            }

            if (e.target.classList.contains('remove-sub-attribute')) {
                const group = e.target.closest('.sub-attributes-group');
                const rows = group.querySelectorAll('.sub-attribute-row');
                if (rows.length > 1) e.target.closest('.sub-attribute-row').remove();
            }
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            const originalPrice = parseFloat(document.getElementById('original_price').value);
            let valid = true;
            document.querySelectorAll('input[name*="[price]"]').forEach(input => {
                const price = parseFloat(input.value);
                if (!isNaN(price) && price < originalPrice) {
                    valid = false;
                    alert('Giá biến thể không được thấp hơn giá gốc: ' + originalPrice + '₫');
                }
            });
            if (!valid) e.preventDefault();
        });
    });
</script>
@endpush

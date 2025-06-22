@php $disableMapScript = true; @endphp
@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Thêm thành phần vào Combo</h2>

    <form action="{{ route('admin.combo_items.store') }}" method="POST">
        @csrf

        {{-- Combo --}}
        <div class="mb-3">
            <label class="form-label">Chọn Combo có sẵn (hoặc để trống để thêm mới)</label>
            <select name="combo_id" class="form-control">
                <option value="">-- Không chọn --</option>
                @foreach($combos as $combo)
                    <option value="{{ $combo->id }}">{{ $combo->product_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hoặc thêm Combo mới</label>
            <input type="text" name="new_combo_name" class="form-control" placeholder="Nhập tên combo mới nếu muốn thêm">
        </div>

        <hr>

        {{-- Dòng thành phần --}}
        <div id="combo-items-wrapper">
            <div class="combo-item-row row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Loại</label>
                    <select name="items[0][itemable_type]" class="form-control itemable-type" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="product">Sản phẩm đơn</option>
                        <option value="variant">Sản phẩm có biến thể</option>
                    </select>
                </div>

                <div class="col-md-4 product-select" style="display: none;">
                    <label class="form-label">Sản phẩm đơn</label>
                    <select name="items[0][product_id]" class="form-control">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 variant-select" style="display: none;">
                    <label class="form-label">Biến thể</label>
                    <select name="items[0][variant_id]" class="form-control">
                        <option value="">-- Chọn biến thể --</option>
                        @foreach($variants as $variant)
                            <option value="{{ $variant->id }}">
                                {{ $variant->product->product_name ?? 'Không rõ' }} -
                                @foreach($variant->attributeValues as $val)
                                    {{ $val->value }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Số lượng</label>
                    <input type="number" name="items[0][quantity]" class="form-control" min="1" value="1" required>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-row">X</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-combo-item" class="btn btn-outline-primary mb-3">+ Thêm thành phần</button>

        <div>
            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('admin.combo_items.index') }}" class="btn btn-secondary">Huỷ</a>
        </div>
    </form>
</div>

<script>
    let itemIndex = 1;

    function bindEvents(row) {
        row.querySelector('.itemable-type').addEventListener('change', function () {
            const type = this.value;
            const parent = this.closest('.combo-item-row');
            parent.querySelector('.product-select').style.display = type === 'product' ? 'block' : 'none';
            parent.querySelector('.variant-select').style.display = type === 'variant' ? 'block' : 'none';
        });

        row.querySelector('.btn-remove-row').addEventListener('click', function () {
            row.remove();
        });
    }

    document.getElementById('add-combo-item').addEventListener('click', function () {
        const wrapper = document.getElementById('combo-items-wrapper');
        const newRow = wrapper.firstElementChild.cloneNode(true);
        newRow.querySelectorAll('select, input').forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/\[\d+\]/, `[${itemIndex}]`);
            input.setAttribute('name', newName);
            input.value = '';
        });

        newRow.querySelector('.product-select').style.display = 'none';
        newRow.querySelector('.variant-select').style.display = 'none';

        bindEvents(newRow);
        wrapper.appendChild(newRow);
        itemIndex++;
    });

    // Bind first row
    bindEvents(document.querySelector('.combo-item-row'));
</script>
@endsection

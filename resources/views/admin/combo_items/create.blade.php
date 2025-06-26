@php $disableMapScript = true; @endphp
@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2>Tạo Combo Mới</h2>
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>⚠️ Lưu ý:</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif
        <form action="{{ route('admin.combo_items.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tên Combo</label>
                <input type="text" name="new_combo_name" class="form-control" placeholder="Nhập tên combo mới"
                    value="{{ old('new_combo_name') }}" required>
            </div>

            <hr>

            <div id="combo-items-wrapper">
                @php $oldItems = old('items') ?? [0 => []]; @endphp
                @foreach ($oldItems as $index => $oldItem)
                    <div class="combo-item-row row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Loại</label>
                            <select name="items[{{ $index }}][itemable_type]" class="form-control itemable-type"
                                required>
                                <option value="">-- Chọn loại --</option>
                                <option value="product"
                                    {{ ($oldItem['itemable_type'] ?? '') === 'product' ? 'selected' : '' }}>
                                    Sản phẩm đơn
                                </option>
                                <option value="variant"
                                    {{ ($oldItem['itemable_type'] ?? '') === 'variant' ? 'selected' : '' }}>
                                    Sản phẩm có biến thể
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 product-select"
                            style="display: {{ ($oldItem['itemable_type'] ?? '') === 'product' ? 'block' : 'none' }};">
                            <label class="form-label">Sản phẩm đơn</label>
                            <select name="items[{{ $index }}][product_id]" class="form-control product-id-select">
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-price="{{ $product->discounted_price ?? $product->original_price }}"
                                        data-stock="{{ $product->quantity_in_stock }}"
                                        {{ ($oldItem['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                        ({{ number_format($product->discounted_price ?? $product->original_price) }} đ)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 variant-select" 
                        
                            style="display: {{ ($oldItem['itemable_type'] ?? '') === 'variant' ? 'block' : 'none' }};"> 
                            <label class="form-label">Biến thể</label>
                            <select name="items[{{ $index }}][variant_id]" class="form-control variant-id-select">
                                <option value="">-- Chọn biến thể --</option>
                                @foreach ($variants as $variant)
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                        data-stock="{{ $variant->quantity_in_stock }}"
                                        {{ ($oldItem['variant_id'] ?? '') == $variant->id ? 'selected' : '' }}>
                                        {{ $variant->product->product_name ?? 'Không rõ' }} -
                                        {{ $variant->attributeValues->pluck('value')->implode(', ') }}
                                        ({{ number_format($variant->price) }} đ)
                                    </option>
                                @endforeach
                            </select>
                            
                        </div>

                        <div class="col-md-1">
                            <label class="form-label">SL</label>
                            <input type="number" name="items[{{ $index }}][quantity]"
                                class="form-control quantity-input" min="1" value="{{ $oldItem['quantity'] ?? 1 }}"
                                required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <label class="form-label">Giá</label>
                            <div class="form-text combo-item-price text-success fw-bold">0 đ</div>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-row">X</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-combo-item" class="btn btn-outline-primary mb-3">+ Thêm thành phần</button>

            <div class="mb-3">
                <label class="form-label">Số lượng Combo</label>
                <input type="number" name="combo_quantity" class="form-control" min="1"
                    value="{{ old('combo_quantity', 1) }}">
                <div class="mb-2 text-info" id="max-combo-hint"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tổng giá thành phần</label>
                <input type="text" id="total-price" class="form-control text-danger fw-bold" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Giá bán combo (tuỳ chọn)</label>
                <input type="number" name="manual_price" class="form-control" id="manual_price_input"
                    value="{{ old('manual_price') }}">
                <small class="form-text text-muted">
                    Nếu để trống, hệ thống sẽ tự tính từ thành phần.<br>
                    <strong>Gợi ý giá bán (lợi nhuận {{ config('combo.suggested_profit_percent', 20) }}%):</strong>
                    <span class="text-success fw-bold" id="suggested-price">0 đ</span><br>
                    <strong>Lợi nhuận dự kiến:</strong>
                    <span class="text-danger fw-bold" id="expected-profit">0 đ</span>
                </small>
            </div>

            <input type="hidden" name="original_price" id="original_price_input">
            <input type="hidden" name="discounted_price" id="discounted_price_input">

            <div>
                <button type="submit" class="btn btn-success">Tạo Combo</button>
                <a href="{{ route('admin.combo_items.index') }}" class="btn btn-secondary">Huỷ</a>
            </div>
        
        </form>
    </div>

    <script>
        let itemIndex = {{ count($oldItems) }};

        function updateItemPrice(row) {
            const type = row.querySelector('.itemable-type').value;
            let selectedOption = null;
            if (type === 'product') {
                selectedOption = row.querySelector('.product-select select').selectedOptions[0];
            } else if (type === 'variant') {
                selectedOption = row.querySelector('.variant-select select').selectedOptions[0];
            }

            const quantity = parseInt(row.querySelector('.quantity-input').value) || 1;
            const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
            const total = price * quantity;

            row.querySelector('.combo-item-price').innerText = new Intl.NumberFormat().format(total) + ' đ';

            updateTotalPrice();
            calculateMaxComboQuantity();
            updateSuggestedPrice();
        }

        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('.combo-item-row').forEach(row => {
                const text = row.querySelector('.combo-item-price')?.innerText || '';
                const value = parseInt(text.replace(/[^\d]/g, '')) || 0;
                total += value;
            });

            document.getElementById('total-price').value = new Intl.NumberFormat().format(total) + ' đ';
            document.getElementById('original_price_input').value = total;
            document.getElementById('discounted_price_input').value = total;

            const manualPriceInput = document.getElementById('manual_price_input');
            if (manualPriceInput && !manualPriceInput.value) {
                manualPriceInput.value = total;
            }
        }

        function updateSuggestedPrice() {
            const total = parseInt(document.getElementById('original_price_input').value || 0);
            const percent = 20;
            const suggested = Math.ceil(total * (1 + percent / 100));
            const manualInput = document.getElementById('manual_price_input');
            const actualPrice = parseInt(manualInput?.value || suggested);

            document.getElementById('suggested-price').innerText = new Intl.NumberFormat().format(suggested) + ' đ';
            const profit = actualPrice - total;
            document.getElementById('expected-profit').innerText = new Intl.NumberFormat().format(profit > 0 ? profit : 0) +
                ' đ';
        }

        function calculateMaxComboQuantity() {
            let maxCombo = Infinity;

            document.querySelectorAll('.combo-item-row').forEach(row => {
                const type = row.querySelector('.itemable-type')?.value;
                const quantity = parseInt(row.querySelector('.quantity-input')?.value) || 1;
                let selected = null;

                if (type === 'product') {
                    selected = row.querySelector('.product-select select')?.selectedOptions[0];
                } else if (type === 'variant') {
                    selected = row.querySelector('.variant-select select')?.selectedOptions[0];
                }

                // Bỏ qua nếu chưa chọn item hoặc không có dữ liệu tồn kho
                if (!selected || !selected.dataset || selected.dataset.stock === undefined) {
                    return;
                }

                const stock = parseInt(selected.dataset.stock);

                // Nếu tồn kho hợp lệ và số lượng > 0 thì tính toán
                if (!isNaN(stock) && quantity > 0) {
                    const possible = Math.floor(stock / quantity);
                    maxCombo = Math.min(maxCombo, possible);
                }
            });

            maxCombo = isFinite(maxCombo) ? maxCombo : 0;

            // Cập nhật UI gợi ý số combo tối đa
            document.getElementById('max-combo-hint').innerHTML =
                `<strong>Combo tối đa có thể tạo:</strong> ${maxCombo} combo (dựa trên tồn kho hiện tại)`;

            // Giới hạn input số lượng combo
            const comboInput = document.querySelector('[name="combo_quantity"]');
            if (comboInput) {
                comboInput.max = maxCombo;
                if (parseInt(comboInput.value) > maxCombo) {
                    comboInput.value = maxCombo;
                }
            }

            return maxCombo;
        }


        function bindEvents(row) {
            const typeSelect = row.querySelector('.itemable-type');
            const quantityInput = row.querySelector('.quantity-input');

            typeSelect.addEventListener('change', function() {
                const type = this.value;
                const parent = this.closest('.combo-item-row');
                parent.querySelector('.product-select').style.display = type === 'product' ? 'block' : 'none';
                parent.querySelector('.variant-select').style.display = type === 'variant' ? 'block' : 'none';
                updateItemPrice(parent);
            });

            row.querySelectorAll('select, input').forEach(el => {
                el.addEventListener('change', () => updateItemPrice(row));
                el.addEventListener('input', () => updateItemPrice(row));
                el.addEventListener('input', () => updateSuggestedPrice());
                el.addEventListener('change', () => updateSuggestedPrice());
            });

            row.querySelector('.btn-remove-row').addEventListener('click', function() {
                const rows = document.querySelectorAll('.combo-item-row');
                if (rows.length > 1) {
                    row.remove();
                    updateTotalPrice();
                    calculateMaxComboQuantity();
                    updateSuggestedPrice();
                } else {
                    alert("Cần ít nhất 1 thành phần trong combo.");
                }
            });

            updateItemPrice(row);
        }

        document.getElementById('add-combo-item').addEventListener('click', function() {
            const wrapper = document.getElementById('combo-items-wrapper');
            const firstRow = wrapper.querySelector('.combo-item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(input => {
                const oldName = input.getAttribute('name');
                const newName = oldName.replace(/\[\d+\]/, `[${itemIndex}]`);
                input.setAttribute('name', newName);
                input.value = input.classList.contains('quantity-input') ? 1 : '';
            });

            newRow.querySelector('.product-select').style.display = 'none';
            newRow.querySelector('.variant-select').style.display = 'none';
            newRow.querySelector('.combo-item-price').innerText = '0 đ';

            bindEvents(newRow);
            wrapper.appendChild(newRow);
            itemIndex++;
            calculateMaxComboQuantity();
        });

        document.querySelectorAll('.combo-item-row').forEach(row => {
            bindEvents(row); // Gán sự kiện

            // 🚀 Kích hoạt thủ công sự kiện "change" để cập nhật lại hiển thị select
            const typeSelect = row.querySelector('.itemable-type');
            if (typeSelect?.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
        updateSuggestedPrice();

        document.querySelector('form').addEventListener('submit', function(e) {
            const maxAllowed = calculateMaxComboQuantity();
            const quantityInput = document.querySelector('[name="combo_quantity"]');
            const entered = parseInt(quantityInput.value) || 0;

            if (entered > maxAllowed) {
                alert(`Số lượng combo vượt quá tồn kho thành phần. Tối đa có thể tạo là: ${maxAllowed} combo.`);
                e.preventDefault();
            }
        });
    </script>
@endsection

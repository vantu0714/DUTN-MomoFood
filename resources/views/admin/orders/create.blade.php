@extends('admin.layouts.app')

@section('title', 'Tạo đơn hàng')

@section('content')
    <div class="container">
        <h2 class="mb-4">Tạo đơn hàng mới</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            {{-- Chọn khách hàng --}}
            <div class="mb-3">
                <label for="user_id" class="form-label">Khách hàng</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">-- Chọn khách hàng --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->fullname }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Thông tin người nhận --}}
            <div class="col-md-4 mb-3">
                <label for="recipient_name" class="form-label">Tên người nhận</label>
                <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="recipient_phone" class="form-label">SĐT người nhận</label>
                <input type="text" name="recipient_phone" class="form-control" value="{{ old('recipient_phone') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="recipient_address" class="form-label">Địa chỉ người nhận</label>
                <input type="text" name="recipient_address" class="form-control" value="{{ old('recipient_address') }}" required>
            </div>

            {{-- Danh sách sản phẩm --}}
            <h4>Thêm sản phẩm</h4>
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-success" onclick="addProductRow()">+ Thêm sản phẩm</button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="product-rows"></tbody>
            </table>

            {{-- Phí vận chuyển --}}
            <div class="mb-3 col-md-4">
                <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                <input type="number" name="shipping_fee" id="shipping_fee" class="form-control" value="{{ old('shipping_fee', 0) }}" min="0" step="1000" required>
            </div>

            {{-- Tổng tiền --}}
            <div class="d-flex justify-content-end mt-3">
                <strong class="fs-5">Tổng tiền đơn hàng: <span id="order-total">0đ</span></strong>
            </div>

            {{-- Phương thức thanh toán --}}
            <div class="mb-3">
                <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="">-- Chọn phương thức thanh toán --</option>
                    <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                    <option value="momo" {{ old('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                </select>
            </div>

            {{-- Trạng thái thanh toán --}}
            <div class="mb-3">
                <label for="payment_status" class="form-label">Trạng thái thanh toán</label>
                <select name="payment_status" id="payment_status" class="form-select" required>
                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                </select>
            </div>

            {{-- Trạng thái đơn hàng --}}
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái đơn hàng</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            {{-- Mã giảm giá --}}
            <div class="mb-3">
                <label for="promotion" class="form-label">Mã giảm giá (nếu có)</label>
                <input type="text" name="promotion" id="promotion" class="form-control" value="{{ old('promotion') }}">
            </div>

            {{-- Ghi chú --}}
            <div class="mb-3">
                <label for="note" class="form-label">Ghi chú</label>
                <textarea name="note" id="note" class="form-control">{{ old('note') }}</textarea>
            </div>

            {{-- Lý do hủy --}}
            <div class="mb-3">
                <label for="cancellation_reason" class="form-label">Lý do hủy đơn (nếu có)</label>
                <input type="text" name="cancellation_reason" id="cancellation_reason" class="form-control" value="{{ old('cancellation_reason') }}">
            </div>

            {{-- Submit --}}
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    let products = @json($products);
    let index = 0;

    function addProductRow() {
        const row = document.createElement('tr');

        let options = `<option value="">-- Chọn sản phẩm --</option>`;
        products.forEach(product => {
            options += `<option value="${product.id}">${product.product_name}</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="products[${index}][product_id]" class="form-select"
                    onchange="loadVariants(this, ${index}); updateSubtotal(${index})" required>
                    ${options}
                </select>
            </td>
            <td>
                <select name="products[${index}][product_variant_id]" class="form-select"
                    onchange="updateSubtotal(${index})">
                    <option value="">-- Không có biến thể --</option>
                </select>
            </td>
            <td>
                <input type="number" name="products[${index}][quantity]" min="1" value="1" class="form-control"
                    oninput="updateSubtotal(${index})" required>
            </td>
            <td>
                <input type="text" class="form-control" id="subtotal-${index}" value="0đ" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Xóa</button>
            </td>
        `;

        document.getElementById('product-rows').appendChild(row);
        index++;
    }

    function loadVariants(selectElement, index) {
        const productId = selectElement.value;
        const product = products.find(p => p.id == productId);

        const variantSelect = document.querySelector(`select[name="products[${index}][product_variant_id]"]`);
        variantSelect.innerHTML = `<option value="">-- Không có biến thể --</option>`;

        if (product?.variants?.length > 0) {
            product.variants.forEach(variant => {
                variantSelect.innerHTML += `<option value="${variant.id}">${variant.name} (${variant.price.toLocaleString()}đ)</option>`;
            });
        }
    }

    function updateSubtotal(index) {
        const productId = document.querySelector(`select[name="products[${index}][product_id]"]`).value;
        const variantId = document.querySelector(`select[name="products[${index}][product_variant_id]"]`).value;
        const quantity = parseInt(document.querySelector(`input[name="products[${index}][quantity]"]`)?.value || 0);

        const product = products.find(p => p.id == productId);
        let price = 0;

        if (variantId && product?.variants?.length > 0) {
            const variant = product.variants.find(v => v.id == variantId);
            price = variant?.price || 0;
        } else {
            if (product) {
                price = product.discounted_price > 0 ? product.discounted_price : product.original_price;
            }
        }

        const subtotal = price * quantity;
        const subtotalInput = document.getElementById(`subtotal-${index}`);
        if (subtotalInput) {
            subtotalInput.value = subtotal.toLocaleString('vi-VN') + 'đ';
        }

        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        for (let i = 0; i < index; i++) {
            const subtotalInput = document.getElementById(`subtotal-${i}`);
            if (subtotalInput) {
                const value = subtotalInput.value.replace(/[^\d]/g, '');
                total += parseInt(value) || 0;
            }
        }

        const shipping = parseInt(document.getElementById('shipping_fee')?.value || 0);
        total += shipping;
        document.getElementById('order-total').innerText = total.toLocaleString('vi-VN') + 'đ';
    }

    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
        calculateTotal();
    }

    window.onload = () => {
        addProductRow();
        document.getElementById('shipping_fee').addEventListener('input', calculateTotal);
    };

    document.querySelector('form').addEventListener('submit', function (e) {
        const selectedProducts = document.querySelectorAll('select[name^="products"][name$="[product_id]"]');
        let valid = false;

        selectedProducts.forEach(sp => {
            if (sp.value) valid = true;
        });

        if (!valid) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một sản phẩm.');
        }
    });
</script>
@endsection

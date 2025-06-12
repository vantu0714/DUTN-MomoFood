@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Tạo đơn hàng</h2>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <!-- Thông tin người nhận -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="recipient_name" class="form-label">Tên người nhận</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="recipient_phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="recipient_phone" name="recipient_phone" required>
            </div>
            <div class="col-md-6">
                <label for="recipient_address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="recipient_address" name="recipient_address" required>
            </div>
        </div>

        <!-- Mã khuyến mãi và phí vận chuyển -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="promotion" class="form-label">Mã khuyến mãi</label>
                <input type="text" class="form-control" id="promotion" name="promotion">
            </div>
            <div class="col-md-6">
                <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                <input type="number" step="0.01" class="form-control" id="shipping_fee" name="shipping_fee" value="0" required>
            </div>
        </div>

        <!-- Thanh toán và trạng thái -->
        <div class="mb-3">
            <label for="payment_method" class="form-label">Phương thức thanh toán</label>
            <input type="text" class="form-control" id="payment_method" name="payment_method" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="payment_status" class="form-label">Trạng thái thanh toán</label>
                <select class="form-select" id="payment_status" name="payment_status" required>
                    <option value="unpaid">Chưa thanh toán</option>
                    <option value="paid">Đã thanh toán</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Trạng thái đơn hàng</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pending">Chờ xử lý</option>
                    <option value="processing">Đang xử lý</option>
                    <option value="completed">Hoàn tất</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Ghi chú</label>
            <input type="text" class="form-control" id="note" name="note">
        </div>

        <div class="mb-3">
            <label for="cancellation_reason" class="form-label">Lý do hủy (nếu có)</label>
            <input type="text" class="form-control" id="cancellation_reason" name="cancellation_reason">
        </div>

        <hr>
        <h4 class="mt-4">Danh sách sản phẩm</h4>

        <table class="table table-bordered" id="products-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="products[0][product_id]" class="product-select form-select" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="products[0][price]" class="price form-control" readonly></td>
                    <td><input type="number" name="products[0][quantity]" class="quantity form-control" value="1" min="1"></td>
                    <td><input type="number" class="total form-control" readonly></td>
                    <td><button type="button" class="btn btn-danger remove-row">X</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary mb-4" id="add-row">+ Thêm sản phẩm</button>

        <div class="mb-3">
            <label for="total_price" class="form-label">Tổng tiền tạm tính</label>
            <input type="number" step="0.01" class="form-control" id="total_price" name="total_price" readonly>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success">Tạo đơn hàng</button>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let rowIndex = 1;

    const productPrices = {
        @foreach($products as $product)
            "{{ $product->id }}": {{ $product->discounted_price }},
        @endforeach
    };

    function updateRow(row) {
        const select = row.querySelector('.product-select');
        const priceInput = row.querySelector('.price');
        const quantityInput = row.querySelector('.quantity');
        const totalInput = row.querySelector('.total');

        const productId = select.value;
        const price = productPrices[productId] || 0;
        const quantity = parseInt(quantityInput.value) || 0;

        priceInput.value = price;
        totalInput.value = price * quantity;

        updateTotalPrice();
    }

    function updateTotalPrice() {
        let total = 0;
        document.querySelectorAll('#products-table .total').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        const shippingFee = parseFloat(document.querySelector('#shipping_fee').value) || 0;
        document.querySelector('#total_price').value = total + shippingFee;
    }

    document.querySelector('#add-row').addEventListener('click', () => {
        const tableBody = document.querySelector('#products-table tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td>
                <select name="products[${rowIndex}][product_id]" class="product-select form-select" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="products[${rowIndex}][price]" class="price form-control" readonly></td>
            <td><input type="number" name="products[${rowIndex}][quantity]" class="quantity form-control" value="1" min="1"></td>
            <td><input type="number" class="total form-control" readonly></td>
            <td><button type="button" class="btn btn-danger remove-row">X</button></td>
        `;

        tableBody.appendChild(newRow);

        newRow.querySelector('.product-select').addEventListener('change', () => updateRow(newRow));
        newRow.querySelector('.quantity').addEventListener('input', () => updateRow(newRow));
        newRow.querySelector('.remove-row').addEventListener('click', () => {
            newRow.remove();
            updateTotalPrice();
        });

        rowIndex++;
    });

    document.querySelectorAll('#products-table tbody tr').forEach(row => {
        row.querySelector('.product-select').addEventListener('change', () => updateRow(row));
        row.querySelector('.quantity').addEventListener('input', () => updateRow(row));
        row.querySelector('.remove-row').addEventListener('click', () => {
            row.remove();
            updateTotalPrice();
        });
    });

    document.querySelector('#shipping_fee').addEventListener('input', updateTotalPrice);
</script>
@endsection

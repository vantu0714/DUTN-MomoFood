@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Tạo đơn hàng</h2>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

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

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="promotion" class="form-label">Mã khuyến mãi</label>
                <input type="text" class="form-control" id="promotion" name="promotion">
            </div>
            <div class="col-md-6">
                <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                <input type="number" step="0.01" class="form-control" id="shipping_fee" name="shipping_fee" required>
            </div>
        </div>

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
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="product-rows">
                <tr>
                    <td>
                        <select name="products[0][product_id]" class="form-select product-select" required>
                            <option value="">-- Chọn --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->discounted_price }}">
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="products[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                    </td>
                    <td class="price">0</td>
                    <td class="total">0</td>
                    <td><button type="button" class="btn btn-danger remove-row">X</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary mb-4" id="add-row">+ Thêm sản phẩm</button>

        <div class="mb-3">
            <label for="total_price" class="form-label">Tổng tiền</label>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let rowIndex = 1;

    // Cập nhật thành tiền cho một dòng sản phẩm
    function updateRowTotal(row) {
        const selectedOption = row.find('.product-select option:selected');
        const price = parseFloat(selectedOption.data('price')) || 0;
        const quantity = parseInt(row.find('.quantity-input').val()) || 0;
        const rowTotal = price * quantity;

        row.find('.price').text(price.toLocaleString('vi-VN'));
        row.find('.total').text(rowTotal.toLocaleString('vi-VN'));

        updateGrandTotal();
    }

    // Tính tổng tất cả thành tiền
    function updateGrandTotal() {
        let total = 0;

        $('#products-table tbody tr').each(function () {
            const rowTotal = parseFloat($(this).find('.total').text().replace(/\./g, '').replace(',', '.')) || 0;
            total += rowTotal;
        });

        // Gán tổng vào input
        $('#total_price').val(total.toFixed(2));
    }

    // Xử lý khi thay đổi sản phẩm hoặc số lượng
    $(document).on('change', '.product-select, .quantity-input', function () {
        const row = $(this).closest('tr');
        updateRowTotal(row);
    });

    // Thêm dòng sản phẩm mới
    $('#add-row').click(function () {
        const firstRow = $('#products-table tbody tr:first');
        const newRow = firstRow.clone();

        // Cập nhật tên input theo rowIndex mới
        newRow.find('select, input').each(function () {
            const name = $(this).attr('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${rowIndex}]`);
                $(this).attr('name', newName);
            }
        });

        // Reset các giá trị
        newRow.find('select').val('');
        newRow.find('input[type="number"]').val(1);
        newRow.find('.price').text('0');
        newRow.find('.total').text('0');

        $('#products-table tbody').append(newRow);
        rowIndex++;
    });

    // Xoá dòng sản phẩm
    $(document).on('click', '.remove-row', function () {
        if ($('#products-table tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateGrandTotal();
        }
    });

    // Tính lại khi trang load
    $(document).ready(function () {
        $('#products-table tbody tr').each(function () {
            updateRowTotal($(this));
        });
    });
</script>
@endsection

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
                <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name') }}"
                    required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="recipient_phone" class="form-label">SĐT người nhận</label>
                <input type="text" name="recipient_phone" class="form-control" value="{{ old('recipient_phone') }}"
                    required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="recipient_address" class="form-label">Địa chỉ người nhận</label>
                <input type="text" name="recipient_address" class="form-control" value="{{ old('recipient_address') }}"
                    required>
            </div>

            {{-- Danh sách sản phẩm và phiên bản --}}
            <h4>Thêm sản phẩm</h4>
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-success" onclick="addProductRow()">+ Thêm sản phẩm</button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="product-rows">
                    <!-- JS sẽ thêm dòng tại đây -->
                </tbody>
            </table>


            {{-- Gửi --}}
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection

<script>
    let products = @json($products);
    let index = 0;

    function addProductRow() {
        const row = document.createElement('tr');

        let options = `<option value="">-- Chọn sản phẩm --</option>`;
        products.forEach(product => {
            let rawPrice = product.price ?? 0;
            let formattedPrice = Number(rawPrice).toLocaleString();
            options += `<option value="${product.id}">${product.product_name} - ${formattedPrice}đ</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="products[${index}][product_id]" class="form-select" required>
                    ${options}
                </select>
            </td>
            <td>
                <input type="number" name="products[${index}][quantity]" min="1" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Xóa</button>
            </td>
        `;

        document.getElementById('product-rows').appendChild(row);
        index++;
    }

    // Tự động thêm 1 dòng khi trang load
    window.onload = () => addProductRow();
</script>

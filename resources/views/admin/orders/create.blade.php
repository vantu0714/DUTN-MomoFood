@extends('admin.layouts.master') {{-- Layout admin của bạn --}}

@section('title', 'Tạo đơn hàng')

@section('content')
<div class="container">
    <h2 class="mb-4">Tạo đơn hàng mới</h2>

    {{-- Hiển thị lỗi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
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
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->email }}</option>
                @endforeach
            </select>
        </div>

        {{-- Thông tin người nhận --}}
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="recipient_name" class="form-label">Tên người nhận</label>
                <input type="text" name="recipient_name" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="recipient_phone" class="form-label">SĐT người nhận</label>
                <input type="text" name="recipient_phone" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="recipient_address" class="form-label">Địa chỉ người nhận</label>
                <input type="text" name="recipient_address" class="form-control" required>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        <h5 class="mt-4">Chọn sản phẩm</h5>
        <table class="table table-bordered" id="product-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="product-body">
                <tr>
                    <td>
                        <select name="product_variants[0][id]" class="form-select" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($variants as $variant)
                                <option value="{{ $variant->id }}">
                                    {{ $variant->product->product_name }} - {{ $variant->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control price" step="0.01" disabled placeholder="Tự động">
                    </td>
                    <td>
                        <input type="number" name="product_variants[0][quantity]" class="form-control" min="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary mb-3" id="add-product">+ Thêm sản phẩm</button>

        {{-- Gửi --}}
        <div>
            <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let rowCount = 1;

    document.getElementById('add-product').addEventListener('click', function () {
        let newRow = `
        <tr>
            <td>
                <select name="product_variants[${rowCount}][id]" class="form-select" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($variants as $variant)
                        <option value="{{ $variant->id }}">
                            {{ $variant->product->product_name }} - {{ $variant->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control price" step="0.01" disabled placeholder="Tự động">
            </td>
            <td>
                <input type="number" name="product_variants[${rowCount}][quantity]" class="form-control" min="1" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
            </td>
        </tr>
        `;
        document.getElementById('product-body').insertAdjacentHTML('beforeend', newRow);
        rowCount++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection

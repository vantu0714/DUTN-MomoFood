@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa đơn hàng')

@section('content')
    <div class="container">
        <h2 class="mb-4">Chỉnh sửa đơn hàng</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Khach hang --}}
            <div class="mb-3">
                <label class="form-label">Khách hàng</label>
                <select name="user_id" class="form-select" required>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $order->user_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->fullname }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Thong tin nguoi nhan --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tên người nhận</label>
                    <input type="text" name="recipient_name" class="form-control"
                        value="{{ old('recipient_name', $order->recipient_name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">SĐT người nhận</label>
                    <input type="text" name="recipient_phone" class="form-control"
                        value="{{ old('recipient_phone', $order->recipient_phone) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Địa chỉ người nhận</label>
                    <input type="text" name="recipient_address" class="form-control"
                        value="{{ old('recipient_address', $order->recipient_address) }}" required>
                </div>
            </div>

            {{-- Phuong thuc thanh toan --}}
            <div class="mb-3">
                <label class="form-label">Phương thức thanh toán</label>
                <select name="payment_method" class="form-select" required>
                    <option value="cod" {{ $order->payment_method == 'cod' ? 'selected' : '' }}>COD</option>
                    <option value="momo" {{ $order->payment_method == 'momo' ? 'selected' : '' }}>MoMo</option>
                </select>
            </div>

            {{-- Trang thai thanh toan --}}
            <div class="mb-3">
                <label class="form-label">Trạng thái thanh toán</label>
                <select name="payment_status" class="form-select" required>
                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Chưa thanh toán
                    </option>
                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                </select>
            </div>

            {{-- Trang thai don hang --}}
            <div class="mb-3">
                <label class="form-label">Trạng thái đơn hàng</label>
                <select name="status" class="form-select" required>
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            {{-- Ma giam gia --}}
            <div class="mb-3">
                <label class="form-label">Mã giảm giá</label>
                <input type="text" name="promotion" class="form-control"
                    value="{{ old('promotion', $order->promotion_code) }}">
            </div>

            {{-- Ghi chu --}}
            <div class="mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea name="note" class="form-control">{{ old('note', $order->note) }}</textarea>
            </div>

            {{-- Ly do huy --}}
            <div class="mb-3">
                <label class="form-label">Lý do hủy đơn (nếu có)</label>
                <input type="text" name="cancellation_reason" class="form-control"
                    value="{{ old('cancellation_reason', $order->cancellation_reason) }}">
            </div>

            {{-- Submit --}}
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Cập nhật đơn hàng</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
@endsection

@extends('clients.layouts.app')

@section('content')

    <body style="margin-top: 200px;">
        <div class="container-xl px-4 mt-4" style="margin-top: 200px;">
            <nav class="nav nav-borders">
                <a class="nav-link active ms-0" href="{{ route('clients.info') }}">Thông tin</a>
                <a class="nav-link" href="{{ route('clients.changepassword') }}">Đổi
                    mật khẩu</a>
                <a class="nav-link" href="{{ route('clients.orders') }}">Đơn hàng</a>
                <a href="#" class="nav-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </nav>
            <hr class="mt-0 mb-4">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card-header">
                        <strong>Chi tiết đơn hàng #{{ $order->id }}</strong> — Ngày đặt:
                        {{ $order->created_at->format('d/m/Y') }}
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
                                <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }}₫</p>
                                <p><strong>Thanh toán:</strong> {{ $order->payment_method ?? 'Chưa có' }}</p>
                                <p><strong>Mã giảm giá:</strong> {{ $order->promotion ?? 'Không có' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? Auth::user()->name }}</p>
                                <p><strong>Địa chỉ giao hàng:</strong>
                                    {{ $order->recipient_address ?? Auth::user()->address }}</p>
                                <p><strong>SĐT:</strong> {{ $order->recipient_phone ?? Auth::user()->phone }}</p>
                                <p><strong>Phí vận chuyển:</strong> {{ $order->shipping_fee }}</p>
                            </div>
                        </div>

                        <h5 class="mt-4">Sản phẩm trong đơn hàng</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->product_name ?? '[Đã xoá]' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                            <td class="text-end">
                                                {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-3">
                            <a href="{{ route('clients.orders') }}" class="btn btn-secondary">Quay lại</a>
                            @if ($order->status === 'Chờ xử lý')
                                <div class="mt-4">
                                    <button class="btn btn-danger"
                                        onclick="document.getElementById('cancel-form').classList.remove('d-none')">
                                        Huỷ đơn hàng
                                    </button>
                                </div>

                                <form id="cancel-form" class="mt-3 d-none" method="POST"
                                    action="{{ route('clients.cancelOrder', $order->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="cancel_reason" class="form-label">Lý do huỷ đơn hàng</label>
                                        <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger">Xác nhận huỷ</button>
                                    <button type="button" class="btn btn-secondary"
                                        onclick="document.getElementById('cancel-form').classList.add('d-none')">Đóng</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </body>
@endsection

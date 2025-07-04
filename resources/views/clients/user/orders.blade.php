@extends('clients.layouts.app')

@section('content')
    @php
        $statusLabels = [
            1 => 'Chưa xác nhận',
            2 => 'Đã xác nhận',
            3 => 'Đang giao',
            4 => 'Hoàn thành',
            5 => 'Hoàn hàng',
            6 => 'Hủy đơn',
        ];

        $paymentStatusLabels = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
        ];

        $currentStatus = request()->get('status', 'all');
    @endphp

    <div class="container-xl px-4" style="margin-top: 200px">
        <nav class="nav nav-borders">
            <a class="nav-link active ms-0" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
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
                <div class="bg-light rounded-3 p-4 mb-4 shadow-sm">
                    <h3 class="mb-0 text-dark fw-semibold display-6">Đơn hàng của bạn</h3>
                </div>

                <div class="bg-gradient-light bg-opacity-10 rounded-3 p-4 mb-4 border">
                    <div class="d-flex flex-column">
                        <h5 class="mb-3 text-dark fw-semibold">
                            <i class="fas fa-filter me-2"></i>Lọc theo trạng thái
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('clients.orders', ['status' => 'all']) }}"
                                class="btn {{ $currentStatus == 'all' ? 'btn-primary active shadow-sm' : 'btn-outline-primary' }} transition-all">
                                <i class="fas fa-list me-1"></i>Tất cả
                            </a>
                            @foreach ($statusLabels as $statusId => $statusLabel)
                                <a href="{{ route('clients.orders', ['status' => $statusId]) }}"
                                    class="btn {{ $currentStatus == $statusId ? 'btn-primary active shadow-sm' : 'btn-outline-primary' }} transition-all">
                                    {{ $statusLabel }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted small">
                        <i class="fas fa-receipt me-1"></i>
                        Hiển thị {{ $orders->count() }} đơn hàng
                        @if ($currentStatus != 'all')
                            với trạng thái: <strong>{{ $statusLabels[$currentStatus] ?? 'Không xác định' }}</strong>
                        @endif
                    </div>

                    @if ($currentStatus != 'all')
                        <a href="{{ route('clients.orders') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                        </a>
                    @endif
                </div>

                @if ($orders->isEmpty())
                    <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                        <div class="text-muted mb-3" style="font-size: 48px;">📦</div>
                        @if ($currentStatus != 'all')
                            <p class="text-muted">Không có đơn hàng nào với trạng thái
                                "{{ $statusLabels[$currentStatus] ?? 'Không xác định' }}".</p>
                            <a href="{{ route('clients.orders') }}" class="btn btn-primary mt-2">
                                Xem tất cả đơn hàng
                            </a>
                        @else
                            <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                        @endif
                    </div>
                @else
                    @foreach ($orders as $order)
                        <div class="card mb-4 shadow-sm border-0 transition-all hover:translate-y-[-2px]">
                            <div class="card-header bg-light border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-center h-100">
                                    <div class="d-flex align-items-center h-100" style="font-size: 1.05rem">
                                        <span class="fw-bold text-dark" style="font-family: 'Open Sans', sans-serif">
                                            Đơn hàng {{ $order->order_code }}
                                        </span>
                                        <span class="text-muted mx-3">|</span>
                                        <span class="text-dark">
                                            Ngày: {{ $order->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="text-muted mx-3">|</span>
                                        <span class="text-dark">
                                            Người nhận: {{ $order->recipient_name }}
                                        </span>
                                    </div>
                                    <div class="ms-4">
                                        <span
                                            class="badge rounded-pill px-3 py-2 fs-6
                                                    @if ($order->status == 1) bg-warning text-dark
                                                    @elseif($order->status == 2) bg-info text-white
                                                    @elseif($order->status == 3) bg-success text-white
                                                    @elseif($order->status == 4) bg-info text-white
                                                    @elseif(in_array($order->status, [5, 6])) bg-danger text-white @endif">
                                            {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6 col-lg-3">
                                        <span class="text-uppercase text-muted small mb-1 d-block">Trạng thái thanh
                                            toán</span>
                                        <span
                                            class="badge rounded-pill px-3 py-2
                                            {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                        </span>
                                    </div>

                                    <div class="col-md-6 col-lg-3">
                                        <span class="text-uppercase text-muted small mb-1 d-block">Phương thức thanh
                                            toán</span>
                                        <span class="fw-semibold text-dark">{{ $order->payment_method }}</span>
                                    </div>

                                    <div class="col-md-6 col-lg-3">
                                        <span class="text-uppercase text-muted small mb-1 d-block">Phí vận chuyển</span>
                                        <span class="fw-semibold text-dark">
                                            {{ number_format($order->shipping_fee, 0, ',', '.') }}₫
                                        </span>
                                    </div>

                                    <div class="col-md-6 col-lg-3">
                                        <span class="text-uppercase text-muted small mb-1 d-block">Tổng tiền</span>
                                        <span class="fw-bold text-success">
                                            {{ number_format($order->total_price, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-light border-top text-end p-3">
                                <a href="{{ route('clients.orderdetail', $order->id) }}"
                                    class="btn btn-primary px-4 py-2 transition-all hover:translate-y-[-1px]">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

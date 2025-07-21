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
            7 => 'Chờ xử lý hoàn hàng',
            8 => 'Hoàn hàng thất bại',
        ];

        $paymentStatusLabels = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'refunded' => 'Hoàn tiền',
        ];

        $currentStatus = request()->get('status', 'all');
    @endphp

    <div class="container-xl px-4" style="margin-top: 150px">
        <nav class="nav nav-borders">
            <a class="nav-link text-dark" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link text-dark" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link active ms-0 fw-semibold text-decoration-none"
                style="color: rgb(219, 115, 91); border-bottom: 2px solid rgb(219, 115, 91)"
                href="{{ route('clients.orders') }}">Đơn hàng</a>
            <a href="#" class="nav-link text-dark"
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

                <div class="bg-light bg-opacity-10 rounded-3 p-4 mb-4 border">
                    <div class="d-flex flex-column">
                        <h5 class="mb-3 text-dark fw-semibold">
                            <i class="fas fa-filter me-2"></i>Lọc theo trạng thái
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('clients.orders', ['status' => 'all']) }}"
                                class="btn {{ $currentStatus == 'all' ? 'text-white fw-semibold shadow-sm' : 'btn-outline-primary' }}"
                                style="{{ $currentStatus == 'all' ? 'background-color: rgb(219, 115, 91); border-color: rgb(219, 115, 91)' : 'border-color: rgb(219, 115, 91); color: rgb(219, 115, 91)' }}">
                                <i class="fas fa-list me-1"></i>Tất cả
                            </a>
                            @foreach ($statusLabels as $statusId => $statusLabel)
                                <a href="{{ route('clients.orders', ['status' => $statusId]) }}"
                                    class="btn {{ $currentStatus == $statusId ? 'text-white fw-semibold shadow-sm' : 'btn-outline-primary' }}"
                                    style="{{ $currentStatus == $statusId ? 'background-color: rgb(219, 115, 91); border-color: rgb(219, 115, 91)' : 'border-color: rgb(219, 115, 91); color: rgb(219, 115, 91)' }}">
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
                            <a href="{{ route('clients.orders') }}" class="btn text-white mt-2"
                                style="background-color: rgb(219, 115, 91); border-color: rgb(219, 115, 91)">
                                Xem tất cả đơn hàng
                            </a>
                        @else
                            <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                        @endif
                    </div>
                @else
                    @foreach ($orders as $order)
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header bg-light border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-center h-100">
                                    <div class="d-flex align-items-center h-100 fs-6">
                                        <span class="fw-bold text-dark">
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
                                                @elseif($order->status == 2) bg-primary text-white
                                                @elseif($order->status == 3) bg-success text-white
                                                @elseif($order->status == 4) bg-info text-white
                                                @elseif($order->status == 5) bg-secondary text-white
                                                @elseif($order->status == 6) bg-danger text-white
                                                @elseif($order->status == 7) bg-purple text-white
                                                @elseif($order->status == 8) bg-dark text-white @endif">
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
                                            {{ $order->payment_status == 'paid'
                                                ? 'bg-success'
                                                : ($order->payment_status == 'refunded'
                                                    ? 'bg-info'
                                                    : 'bg-warning') }}">
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
                                <a href="{{ route('clients.orderdetail', $order->id) }}" class="btn text-white px-4 py-2"
                                    style="background-color: rgb(219, 115, 91); border-color: rgb(219, 115, 91)">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        {{ $orders->appends(request()->query())->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .pagination {
                display: flex;
                justify-content: center;
                padding-left: 0;
                list-style: none;
                flex-wrap: wrap;
            }

            .page-item {
                margin: 0 2px;
            }

            .pagination .page-link {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                padding: 0;
                font-size: 1rem;
                color: rgb(219, 115, 91) !important;
                border: 2px solid rgb(219, 115, 91) !important;
                border-radius: 8px;
                background-color: #fff !important;
                transition: all 0.2s ease;
                text-decoration: none;
            }

            .pagination .page-link:hover {
                color: rgb(190, 90, 68) !important;
                background-color: #fff7f0 !important;
                border-color: rgb(219, 115, 91) !important;
            }

            .pagination .page-item.active .page-link {
                background-color: rgb(219, 115, 91) !important;
                border-color: rgb(219, 115, 91) !important;
                color: #fff !important;
                font-weight: bold;
            }

            .pagination .page-item.active .page-link:hover {
                background-color: rgb(219, 115, 91) !important;
                color: #fff !important;
            }

            .pagination .page-item.disabled .page-link {
                color: #ccc !important;
                border-color: #ddd !important;
                background-color: #fff !important;
            }

            .pagination .page-item.disabled .page-link:hover {
                color: #ccc !important;
                background-color: #fff !important;
                border-color: #ddd !important;
            }

            .btn-outline-primary:hover {
                background-color: #fff7f0 !important;
                border-color: rgb(219, 115, 91) !important;
                color: rgb(190, 90, 68) !important;
            }

            .btn-outline-primary:focus {
                box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25) !important;
            }

            .bg-purple {
                background-color: #6f42c1 !important;
            }

            .bg-dark {
                background-color: #343a40 !important;
            }
        </style>
    @endpush
@endsection

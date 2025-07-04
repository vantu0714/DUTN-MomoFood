@extends('clients.layouts.app')

@section('content')
    @push('styles')
        <style>
            .order-code {
                font-family: 'open-sans', sans-serif !important;
                letter-spacing: 1px;
            }

            .info-label {
                font-size: 12px;
                letter-spacing: 0.5px;
            }

            .status-badge {
                font-size: 12px;
                letter-spacing: 0.5px;
            }

            .order-card {
                transition: all 0.3s ease;
            }

            .order-card:hover {
                transform: translateY(-2px);
            }

            .btn-view-order:hover {
                transform: translateY(-1px);
            }

            .empty-orders-icon {
                font-size: 48px;
            }

            /* Status color variants */
            .status-1 {
                background-color: #fff3cd;
                color: #856404;
            }

            .status-2 {
                background-color: #d1ecf1;
                color: #0c5460;
            }

            .status-3 {
                background-color: #d4edda;
                color: #155724;
            }

            .status-4 {
                background-color: #d1ecf1;
                color: #0c5460;
            }

            .status-5 {
                background-color: #f8d7da;
                color: #721c24;
            }

            .status-6 {
                background-color: #f8d7da;
                color: #721c24;
            }

            .payment-status-paid {
                background-color: #d4edda;
                color: #155724;
            }

            .payment-status-unpaid {
                background-color: #fff3cd;
                color: #856404;
            }

            .filter-section {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 24px;
                border: 1px solid #dee2e6;
            }

            .filter-btn {
                transition: all 0.3s ease;
                border-radius: 8px;
                padding: 8px 16px;
                font-size: 14px;
                font-weight: 500;
            }

            .filter-btn.active {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            }

            .results-count {
                font-size: 14px;
                color: #6c757d;
                font-weight: 500;
            }
        </style>
    @endpush
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

    <body style="margin-top: 200px">
        <div class="container-xl px-4 mt-4">
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
                    <!-- Orders Header -->
                    <div class="bg-light rounded-3 p-4 mb-4 shadow-sm">
                        <h3 class="mb-0 text-dark fw-semibold display-6">Đơn hàng của bạn</h3>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="d-flex flex-column">
                            <h5 class="mb-3 text-dark fw-semibold">
                                <i class="fas fa-filter me-2"></i>Lọc theo trạng thái
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('clients.orders', ['status' => 'all']) }}"
                                    class="btn filter-btn {{ $currentStatus == 'all' ? 'btn-primary active' : 'btn-outline-primary' }}">
                                    <i class="fas fa-list me-1"></i>Tất cả
                                </a>
                                @foreach ($statusLabels as $statusId => $statusLabel)
                                    <a href="{{ route('clients.orders', ['status' => $statusId]) }}"
                                        class="btn filter-btn {{ $currentStatus == $statusId ? 'btn-primary active' : 'btn-outline-primary' }}">
                                        {{ $statusLabel }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="results-count">
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
                        <!-- Empty Orders State -->
                        <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                            <div class="empty-orders-icon text-muted mb-3">📦</div>
                            @if ($currentStatus != 'all')
                                <p class="text-muted fs-5">Không có đơn hàng nào với trạng thái
                                    "{{ $statusLabels[$currentStatus] ?? 'Không xác định' }}".</p>
                                <a href="{{ route('clients.orders') }}" class="btn btn-primary mt-2">
                                    Xem tất cả đơn hàng
                                </a>
                            @else
                                <p class="text-muted fs-5">Bạn chưa có đơn hàng nào.</p>
                            @endif
                        </div>
                    @else
                        @foreach ($orders as $order)
                            <!-- Order Card -->
                            <div class="card order-card mb-4 shadow-sm border-0">
                                <!-- Order Header -->
                                <div class="card-header bg-light border-bottom">
                                    <h4 class="order-code mb-2 fw-bold fs-5 text-dark text-truncate">
                                        Đơn hàng {{ $order->order_code }}
                                    </h4>
                                    <p class="text-muted small mb-0">
                                        Đặt ngày {{ $order->created_at->format('d/m/Y') }}
                                    </p>
                                </div>

                                <!-- Order Content -->
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <!-- Order Status -->
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex flex-column">
                                                <span class="info-label text-uppercase text-muted fw-medium small mb-1">
                                                    Trạng thái đơn hàng
                                                </span>
                                                <span class="fw-semibold">
                                                    <span
                                                        class="badge rounded-pill status-badge status-{{ $order->status }} px-3 py-2">
                                                        {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Payment Status -->
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex flex-column">
                                                <span class="info-label text-uppercase text-muted fw-medium small mb-1">
                                                    Trạng thái thanh toán
                                                </span>
                                                <span class="fw-semibold">
                                                    <span
                                                        class="badge rounded-pill status-badge payment-status-{{ $order->payment_status }} px-3 py-2">
                                                        {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex flex-column">
                                                <span class="info-label text-uppercase text-muted fw-medium small mb-1">
                                                    Phương thức thanh toán
                                                </span>
                                                <span class="fw-semibold text-dark">{{ $order->payment_method }}</span>
                                            </div>
                                        </div>

                                        <!-- Shipping Fee -->
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex flex-column">
                                                <span class="info-label text-uppercase text-muted fw-medium small mb-1">
                                                    Phí vận chuyển
                                                </span>
                                                <span class="fw-semibold text-dark">
                                                    {{ number_format($order->shipping_fee, 0, ',', '.') }}₫
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Total Price -->
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex flex-column">
                                                <span class="info-label text-uppercase text-muted fw-medium small mb-1">
                                                    Tổng tiền
                                                </span>
                                                <span class="fw-bold text-success fs-5">
                                                    {{ number_format($order->total_price, 0, ',', '.') }}₫
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Actions -->
                                <div class="card-footer bg-light border-top text-end p-3">
                                    <a href="{{ route('clients.orderdetail', $order->id) }}"
                                        class="btn btn-primary btn-view-order px-4 py-2">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

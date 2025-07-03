@extends('clients.layouts.app')

@section('content')
    @push('styles')
        <style>
            .pagination {
                display: flex;
                justify-content: center;
                gap: 0.25rem;
                margin-top: 1rem;
                flex-wrap: wrap;
            }

            .page-item .page-link {
                padding: 0.375rem 0.75rem;
                border: 1px solid #28a745;
                color: #28a745;
                background-color: #fff;
                border-radius: 0.25rem;
                transition: all 0.2s ease;
            }

            .page-item:hover .page-link {
                background-color: #e9f7ef;
                color: #1e7e34;
                border-color: #1e7e34;
            }

            .page-item.active .page-link {
                background-color: #28a745;
                color: #fff;
                border-color: #28a745;
            }

            .page-item.disabled .page-link {
                color: #6c757d;
                pointer-events: none;
                background-color: #f8f9fa;
                border-color: #dee2e6;
            }

            /* Enhanced Order List Styles */
            .order-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
                margin-bottom: 20px;
                border: 1px solid #e9ecef;
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .order-card:hover {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
                transform: translateY(-2px);
            }

            .order-header {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 20px;
                border-bottom: 1px solid #dee2e6;
            }

            . .order-code {
                font-family: 'Roboto Mono', monospace !important;
                letter-spacing: 1px;
                font-weight: bold;
                font-size: 18px;
                color: #495057;
                margin: 0 0 8px 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                word-break: keep-all;
            }


            .order-date {
                color: #6c757d;
                font-size: 14px;
                margin: 8px 0 0 0;
                padding: 0;
                line-height: 1.2;
                display: block;
            }

            .order-content {
                padding: 20px;
            }

            .order-info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }

            .info-item {
                display: flex;
                flex-direction: column;
            }

            .info-label {
                font-size: 12px;
                font-weight: 500;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 5px;
            }

            .info-value {
                font-size: 14px;
                font-weight: 600;
                color: #495057;
            }

            .status-badge {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

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

            .price-highlight {
                font-size: 18px;
                font-weight: 700;
                color: #28a745;
            }

            .order-actions {
                border-top: 1px solid #dee2e6;
                padding: 15px 20px;
                background: #f8f9fa;
                text-align: right;
            }

            .btn-view-order {
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }

            .btn-view-order:hover {
                background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
                color: white;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            }

            .empty-orders {
                text-align: center;
                padding: 60px 20px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            }

            .empty-orders-icon {
                font-size: 48px;
                color: #dee2e6;
                margin-bottom: 20px;
            }

            .empty-orders-text {
                color: #6c757d;
                font-size: 16px;
            }

            .orders-header {
                background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
                padding: 30px;
                border-radius: 12px;
                margin-bottom: 30px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            }

            .orders-title {
                margin: 0;
                color: #495057;
                font-weight: 600;
                font-size: 28px;
            }

            @media (max-width: 768px) {
                .order-info-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .order-header,
                .order-content,
                .order-actions {
                    padding: 15px;
                }

                .orders-header {
                    padding: 20px;
                    margin-bottom: 20px;
                }

                .orders-title {
                    font-size: 24px;
                }
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
    @endphp

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
                    <div class="orders-header">
                        <h3 class="orders-title">Đơn hàng của bạn</h3>
                    </div>

                    @if ($orders->isEmpty())
                        <div class="empty-orders">
                            <div class="empty-orders-icon">📦</div>
                            <p class="empty-orders-text">Bạn chưa có đơn hàng nào.</p>
                        </div>
                    @else
                        @foreach ($orders as $order)
                            <div class="order-card">
                                <div class="order-header">
                                    <h4 class="order-code">Đơn hàng {{ $order->order_code }}</h4>
                                    <p class="order-date">Đặt ngày {{ $order->created_at->format('d/m/Y') }}</p>
                                </div>

                                <div class="order-content">
                                    <div class="order-info-grid">
                                        <div class="info-item">
                                            <span class="info-label">Trạng thái đơn hàng</span>
                                            <span class="info-value">
                                                <span class="status-badge status-{{ $order->status }}">
                                                    {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                                                </span>
                                            </span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">Trạng thái thanh toán</span>
                                            <span class="info-value">
                                                <span class="status-badge payment-status-{{ $order->payment_status }}">
                                                    {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                                </span>
                                            </span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">Phương thức thanh toán</span>
                                            <span class="info-value">{{ $order->payment_method }}</span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">Phí vận chuyển</span>
                                            <span
                                                class="info-value">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">Tổng tiền</span>
                                            <span class="info-value price-highlight">
                                                {{ number_format($order->total_price, 0, ',', '.') }}₫
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-actions">
                                    <a href="{{ route('clients.orderdetail', $order->id) }}" class="btn-view-order">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

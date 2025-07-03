@extends('clients.layouts.app')

@section('content')
    @push('styles')
        <style>
            .order-detail-container {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                margin-bottom: 30px;
            }

            .order-detail-header {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
                padding: 30px;
                position: relative;
                overflow: hidden;
            }

            .order-detail-header::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 100px;
                height: 100px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(30px, -30px);
            }

            .order-detail-header::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 60px;
                height: 60px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(-20px, 20px);
            }

            .order-detail-title {
                font-size: 24px;
                font-weight: 700;
                font-family: system-ui, -apple-system, sans-serif;
                margin: 0;
                position: relative;
                z-index: 1;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                font-display: swap;
            }

            .order-detail-date {
                font-size: 14px;
                margin-top: 8px;
                opacity: 0.9;
                position: relative;
                z-index: 1;
            }

            .order-detail-body {
                padding: 30px;
            }

            .info-section {
                margin-bottom: 40px;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                margin-bottom: 30px;
            }

            .info-card {
                background: #f0f9f4;
                border: 1px solid #c6f6d5;
                border-radius: 12px;
                padding: 20px;
                transition: all 0.3s ease;
                position: relative;
            }

            .info-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(40, 167, 69, 0.15);
                border-color: #28a745;
            }

            .info-card-title {
                font-size: 14px;
                font-weight: 600;
                color: #28a745;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 15px;
                border-bottom: 2px solid #c6f6d5;
                padding-bottom: 8px;
            }

            .info-item {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 12px;
                padding: 8px 0;
            }

            .info-item:last-child {
                margin-bottom: 0;
            }

            .info-label {
                font-weight: 500;
                color: #6c757d;
                font-size: 14px;
                flex: 1;
            }

            .info-value {
                font-weight: 600;
                color: #495057;
                font-size: 14px;
                text-align: right;
                flex: 1;
                margin-left: 15px;
            }

            .status-badge {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 25px;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .status-1 {
                background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
                color: #d63031;
            }

            .status-2 {
                background: linear-gradient(135deg, #74b9ff, #0984e3);
                color: white;
            }

            .status-3 {
                background: linear-gradient(135deg, #55efc4, #00cec9);
                color: #2d3436;
            }

            .status-4 {
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
            }

            .status-5 {
                background: linear-gradient(135deg, #fdcb6e, #e17055);
                color: white;
            }

            .status-6 {
                background: linear-gradient(135deg, #fab1a0, #e17055);
                color: white;
            }

            .payment-status-paid {
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
            }

            .payment-status-unpaid {
                background: linear-gradient(135deg, #fdcb6e, #f39c12);
                color: white;
            }

            .price-highlight {
                font-size: 18px;
                font-weight: 800;
                color: #28a745;
                text-shadow: 0 1px 2px rgba(40, 167, 69, 0.2);
            }

            .cancellation-reason {
                background: #fff5f5;
                border: 1px solid #fed7d7;
                border-radius: 8px;
                padding: 15px;
                margin-top: 10px;
                color: #c53030;
                font-style: italic;
            }

            .products-section {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                margin-top: 30px;
            }

            .products-header {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                padding: 20px 30px;
                border-bottom: 1px solid #dee2e6;
            }

            .products-title {
                font-size: 20px;
                font-weight: 700;
                color: #495057;
                margin: 0;
            }

            .products-table {
                margin: 0;
            }

            .products-table thead th {
                background: #f8f9fa;
                border: none;
                color: #495057;
                font-weight: 600;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 20px 15px;
            }

            .products-table tbody td {
                padding: 20px 15px;
                border-top: 1px solid #f1f3f4;
                vertical-align: middle;
            }

            .products-table tbody tr {
                transition: background-color 0.2s ease;
            }

            .products-table tbody tr:hover {
                background-color: #f0f9f4;
            }

            .product-name {
                font-weight: 600;
                color: #495057;
                font-size: 15px;
            }

            .product-variant {
                color: #6c757d;
                font-size: 13px;
                background: #e9ecef;
                padding: 4px 8px;
                border-radius: 6px;
                display: inline-block;
            }

            .quantity-badge {
                background: #28a745;
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-weight: 600;
                font-size: 13px;
            }

            .price-cell {
                font-weight: 600;
                color: #495057;
            }

            .total-price-cell {
                font-weight: 700;
                color: #28a745;
                font-size: 16px;
            }

            .action-buttons {
                display: flex;
                gap: 15px;
                justify-content: flex-end;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e9ecef;
            }

            .btn-back {
                background: linear-gradient(135deg, #6c757d, #495057);
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }

            .btn-back:hover {
                background: linear-gradient(135deg, #495057, #343a40);
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            }

            .btn-cancel {
                background: linear-gradient(135deg, #e74c3c, #c0392b);
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn-cancel:hover {
                background: linear-gradient(135deg, #c0392b, #a93226);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
            }

            .cancel-form-container {
                background: #fff5f5;
                border: 1px solid #fed7d7;
                border-radius: 12px;
                padding: 25px;
                margin-top: 20px;
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .cancel-form-title {
                color: #c53030;
                font-weight: 700;
                margin-bottom: 15px;
                font-size: 16px;
            }

            .cancel-textarea {
                border: 2px solid #fed7d7;
                border-radius: 8px;
                padding: 12px;
                font-size: 14px;
                transition: border-color 0.3s ease;
                resize: vertical;
                min-height: 100px;
            }

            .cancel-textarea:focus {
                border-color: #e53e3e;
                box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
                outline: none;
            }

            .btn-confirm-cancel {
                background: linear-gradient(135deg, #e53e3e, #c53030);
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                margin-top: 15px;
            }

            .btn-confirm-cancel:hover {
                background: linear-gradient(135deg, #c53030, #9c1c1c);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
            }

            @media (max-width: 768px) {
                .order-detail-header {
                    padding: 20px;
                }

                .order-detail-body {
                    padding: 20px;
                }

                .info-grid {
                    grid-template-columns: 1fr;
                    gap: 20px;
                }

                .info-card {
                    padding: 15px;
                }

                .products-header {
                    padding: 15px 20px;
                }

                .products-table thead th,
                .products-table tbody td {
                    padding: 15px 10px;
                }

                .action-buttons {
                    flex-direction: column;
                    align-items: stretch;
                }

                .order-detail-title {
                    font-size: 20px;
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
                    <div class="order-detail-container">
                        <div class="order-detail-header">
                            <h1 class="order-detail-title">Chi tiết đơn hàng #{{ $order->order_code }}</h1>
                            <p class="order-detail-date">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</p>
                        </div>

                        <div class="order-detail-body">
                            <div class="info-section">
                                <div class="info-grid">
                                    <div class="info-card">
                                        <div class="info-card-title">Thông tin đơn hàng</div>
                                        <div class="info-item">
                                            <span class="info-label">Trạng thái đơn hàng:</span>
                                            <span class="info-value">
                                                <span class="status-badge status-{{ $order->status }}">
                                                    {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                                                </span>
                                            </span>
                                        </div>
                                        @if ($order->status == 6 && $order->cancellation_reason)
                                            <div class="cancellation-reason">
                                                <strong>Lý do hủy đơn:</strong> {{ $order->cancellation_reason }}
                                            </div>
                                        @endif
                                        <div class="info-item">
                                            <span class="info-label">Phương thức thanh toán:</span>
                                            <span class="info-value">{{ $order->payment_method ?? 'Chưa có' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Phí vận chuyển:</span>
                                            <span
                                                class="info-value">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Tổng tiền:</span>
                                            <span
                                                class="info-value price-highlight">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                                        </div>
                                    </div>

                                    <div class="info-card">
                                        <div class="info-card-title">Thông tin giao hàng</div>
                                        <div class="info-item">
                                            <span class="info-label">Trạng thái thanh toán:</span>
                                            <span class="info-value">
                                                <span class="status-badge payment-status-{{ $order->payment_status }}">
                                                    {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                                </span>
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Người nhận:</span>
                                            <span
                                                class="info-value">{{ $order->recipient_name ?? Auth::user()->name }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Địa chỉ giao hàng:</span>
                                            <span
                                                class="info-value">{{ $order->recipient_address ?? Auth::user()->address }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">SĐT:</span>
                                            <span
                                                class="info-value">{{ $order->recipient_phone ?? Auth::user()->phone }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Mã giảm giá:</span>
                                            <span class="info-value">{{ $order->promotion ?? 'Không có' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="products-section">
                                <div class="products-header">
                                    <h5 class="products-title">Sản phẩm trong đơn hàng</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table products-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sản phẩm</th>
                                                <th>Biến thể</th>
                                                <th>Số lượng</th>
                                                <th class="text-end">Đơn giá</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->orderDetails as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="product-name">
                                                        {{ $item->product->product_name ?? '[Đã xoá]' }}</td>
                                                    <td>
                                                        @if ($item->productVariant && $item->productVariant->sku)
                                                            <span
                                                                class="product-variant">{{ $item->productVariant->sku }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="quantity-badge">{{ $item->quantity }}</span>
                                                    </td>
                                                    <td class="text-end price-cell">
                                                        {{ number_format($item->price, 0, ',', '.') }}₫</td>
                                                    <td class="text-end total-price-cell">
                                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="{{ route('clients.orders') }}" class="btn-back">Quay lại</a>
                                @if (in_array($order->status, [1]))
                                    <button type="button" class="btn-cancel"
                                        onclick="document.getElementById('cancel-form').classList.toggle('d-none')">
                                        Hủy đơn hàng
                                    </button>
                                @endif
                            </div>

                            @if (in_array($order->status, [1]))
                                <div id="cancel-form" class="d-none">
                                    <div class="cancel-form-container">
                                        <h6 class="cancel-form-title">Hủy đơn hàng</h6>
                                        <form action="{{ route('clients.ordercancel', $order->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="cancellation_reason" class="form-label"><strong>Lý do hủy đơn
                                                        hàng:</strong></label>
                                                <textarea name="cancellation_reason" id="cancellation_reason" class="form-control cancel-textarea" required
                                                    placeholder="Vui lòng nhập lý do hủy đơn hàng..."></textarea>
                                            </div>
                                            <button type="submit" class="btn-confirm-cancel">Xác nhận hủy</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

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
                    <h3 class="mb-4">Đơn hàng của bạn</h3>
                    @if ($orders->isEmpty())
                        <p>Bạn chưa có đơn hàng nào.</p>
                    @else
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái đơn hàng</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th>Phí vận chuyển</th>
                                    <th>Tổng tiền</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>#{{ $order->order_code }}</td>
                                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $statusLabels[$order->status] ?? 'Không xác định' }}</td>
                                        <td>{{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}</td>
                                        <td>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                                        <td>{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                                        <td>
                                            <a href="{{ route('clients.orderdetail', $order->id) }}"
                                                class="btn btn-sm btn-primary">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

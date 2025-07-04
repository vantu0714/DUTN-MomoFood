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

        $statusClasses = [
            1 => 'bg-warning text-dark',
            2 => 'bg-primary',
            3 => 'bg-info',
            4 => 'bg-success',
            5 => 'bg-secondary',
            6 => 'bg-danger',
        ];

        $paymentStatusClasses = [
            'unpaid' => 'bg-warning text-dark',
            'paid' => 'bg-success',
        ];

        // Tính toán các giá trị
        $subtotal = $order->orderDetails->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $calculatedDiscount = $subtotal + $order->shipping_fee - $order->total_price;
        $calculatedDiscount = max(0, $calculatedDiscount);
    @endphp

    <div class="container mb-5" style="margin-top: 200px">
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

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0">
                <h4 class="mb-0">Chi tiết đơn hàng #{{ $order->order_code }}</h4>
                <small class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</small>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Thông tin đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted small">Mã đơn hàng:</span>
                                            <span class="fw-bold">{{ $order->order_code }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted small">Ngày đặt:</span>
                                            <span class="fw-bold">{{ $order->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted small">Trạng thái:</span>
                                            <span>
                                                <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                                    {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted small">Thanh toán:</span>
                                            <span
                                                class="fw-bold">{{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($order->status == 6 && $order->cancellation_reason)
                                    <div class="alert alert-danger p-2 mb-0">
                                        <strong>Lý do hủy:</strong> {{ $order->cancellation_reason }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Thông tin nhận hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Trạng thái thanh toán:</span>
                                    <span>
                                        <span
                                            class="badge {{ $paymentStatusClasses[$order->payment_status] ?? 'bg-secondary' }}">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Người nhận:</strong></p>
                                    <p class="mb-0">{{ $order->recipient_name ?? Auth::user()->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Địa chỉ nhận hàng:</strong></p>
                                    <p class="mb-0">{{ $order->recipient_address ?? Auth::user()->address }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Số điện thoại:</strong></p>
                                    <p class="mb-0">{{ $order->recipient_phone ?? Auth::user()->phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Sản phẩm trong đơn hàng</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Ảnh</th>
                                    <th width="30%">Sản phẩm</th>
                                    <th width="15%">Biến thể</th>
                                    <th width="10%">SL</th>
                                    <th width="12%" class="text-end">Đơn giá</th>
                                    <th width="13%" class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                                    alt="{{ $item->product->product_name }}" class="img-thumbnail"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $item->product->product_name ?? '[Đã xoá]' }}</td>
                                        <td>
                                            @if ($item->productVariant && $item->productVariant->sku)
                                                <span
                                                    class="badge bg-light text-dark">{{ $item->productVariant->sku }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Tổng giá sản phẩm:</span>
                                    <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                                </div>

                                @if ($order->promotion && $calculatedDiscount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold">Giảm giá ({{ $order->promotion }}):</span>
                                        <span
                                            class="text-danger">-{{ number_format($calculatedDiscount, 0, ',', '.') }}₫</span>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                </div>

                                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                    <span class="fw-bold">Tổng thanh toán:</span>
                                    <span
                                        class="fw-bold text-success">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('clients.orders') }}" class="btn btn-secondary">Quay lại</a>

                    @if (in_array($order->status, [1]))
                        <button type="button" class="btn btn-danger"
                            onclick="document.getElementById('cancel-form').classList.toggle('d-none')">
                            <i class="fas fa-trash-alt me-2"></i>Hủy đơn hàng
                        </button>
                    @endif
                </div>

                @if (in_array($order->status, [1]))
                    <div id="cancel-form" class="d-none mt-3">
                        <div class="card border-danger">
                            <div class="card-header bg-danger">
                                <h6 class="mb-0 text-white">Hủy đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clients.ordercancel', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="cancellation_reason" class="form-label fw-bold text-danger">Lý do hủy
                                            đơn hàng:</label>
                                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" required
                                            placeholder="Vui lòng nhập lý do hủy đơn hàng..." rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger text-white">
                                        <i class="fas fa-times-circle me-2"></i>Xác nhận hủy
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

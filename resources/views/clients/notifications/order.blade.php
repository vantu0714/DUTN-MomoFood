@extends('clients.layouts.app')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4">Chi tiết thông báo đơn hàng</h4>

        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-start">
                <!-- Ảnh sản phẩm đầu tiên trong đơn -->
                <img src="{{ $order->orderDetails->first()->product->image ?? asset('clients/img/no-image.png') }}"
                    alt="Ảnh sản phẩm" class="me-3 rounded" style="width: 100px; height: 100px; object-fit: cover;">

                <!-- Nội dung -->
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-success mb-2">Đơn hàng đã hoàn tất</h6>

                    <p class="mb-1">
                        Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được giao thành công.
                    </p>

                    <p class="mb-2">
                        Vui lòng đánh giá sản phẩm trước ngày
                        <strong>{{ $order->completed_at?->addDays(30)->format('d-m-Y') }}</strong>.
                    </p>

                    <small class="text-muted">
                        Thời gian hoàn tất: {{ $order->completed_at?->format('H:i d-m-Y') }}
                    </small>
                </div>

                <!-- Nút hành động -->
                <div class="ms-3">
                    <a href="{{ route('notifications.order.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                        Đánh giá sản phẩm
                    </a>

                </div>
            </div>
        </div>
    </div>
@endsection

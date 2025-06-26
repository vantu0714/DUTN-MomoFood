@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid my-4">
        <h3 class="text-center text-primary">Chi tiết đơn hàng #{{ $order->id }}</h3>
        <h5 class="text-center">{{ $order->order_code}}</h5>

        {{-- Người nhận --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-primary text-white fw-bold">Thông tin người nhận</div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4"><strong>Họ tên:</strong> {{ $order->recipient_name }}</div>
                    <div class="col-md-4"><strong>SĐT:</strong> {{ $order->recipient_phone }}</div>
                    <div class="col-md-4"><strong>Địa chỉ:</strong> {{ $order->recipient_address }}</div>
                    <div class="col-12"><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</div>
                </div>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-info text-white fw-bold">Danh sách sản phẩm</div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Hình ảnh</th>
                            <th>Biến thể</th>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                            <tr>
                                <td class="text-center">
                                    @php
                                        $variant = $detail->productVariant;
                                        $product = $variant->product ?? $detail->product;
                                        $image = $variant?->image ?? ($product?->image ?? null);
                                    @endphp

                                    @if ($image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="Ảnh sản phẩm" width="60"
                                            class="img-thumbnail">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>{{ $detail->productVariant->name ?? 'Không có biến thể' }}</td>
                                <td>
                                    {{ $detail->productVariant->product->product_name ?? ($detail->product->product_name ?? 'Không rõ sản phẩm') }}
                                </td>
                                <td>{{ number_format($detail->price) }}đ</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->price * $detail->quantity) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Thông tin đơn hàng --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-success text-white fw-bold">Thông tin đơn hàng</div>
            <div class="card-body">
                @php
                    $totalBeforeDiscount = $order->orderDetails->sum(fn($item) => $item->price * $item->quantity);
                    $discount = $totalBeforeDiscount + $order->shipping_fee - $order->total_price;
                    $statusLabels = [
                        1 => ['label' => 'Chưa xác nhận', 'class' => 'secondary'],
                        2 => ['label' => 'Đã xác nhận', 'class' => 'info'],
                        3 => ['label' => 'Đang giao', 'class' => 'primary'],
                        4 => ['label' => 'Hoàn thành', 'class' => 'success'],
                        5 => ['label' => 'Hoàn hàng', 'class' => 'dark'],
                        6 => ['label' => 'Hủy đơn', 'class' => 'danger'],
                    ];
                    $status = $statusLabels[$order->status] ?? ['label' => 'Không rõ', 'class' => 'light'];
                @endphp

                <div class="row gy-3">
                    <div class="col-md-6"><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }}đ</div>
                    <div class="col-md-6"><strong>Mã giảm giá:</strong> {{ $order->promotion ?? 'Không có' }}</div>
                    @if ($discount > 0)
                        <div class="col-md-6"><strong>Giảm giá:</strong> -{{ number_format($discount) }}đ</div>
                    @endif
                    <div class="col-md-6"><strong>Tổng tiền:</strong> <span
                            class="text-danger fw-bold">{{ number_format($order->total_price) }}đ</span></div>
                    <div class="col-md-6">
                        <strong>Thanh toán:</strong>
                        @if ($order->payment_status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái đơn hàng:</strong>
                        <span class="badge bg-{{ $status['class'] }}">{{ $status['label'] }}</span>
                    </div>
                    @if ($order->status == 6 && $order->cancellation_reason)
                        <div class="col-12"><strong>Lý do hủy:</strong> {{ $order->cancellation_reason }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quay lại --}}
        <div class="text-end">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
@endsection

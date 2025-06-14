@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>

        <div class="card mb-4">
            <div class="card-header">Thông tin người nhận</div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> {{ $order->recipient_name }}</p>
                <p><strong>SĐT:</strong> {{ $order->recipient_phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->recipient_address }}</p>
                <p><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Danh sách sản phẩm</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
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
                                {{-- Tên biến thể --}}
                                <td>
                                    {{ $detail->productVariant->name ?? 'Không có biến thể' }}
                                </td>

                                {{-- Tên sản phẩm --}}
                                <td>
                                    @if ($detail->productVariant && $detail->productVariant->product)
                                        {{ $detail->productVariant->product->product_name }}
                                    @elseif ($detail->product)
                                        {{ $detail->product->product_name }}
                                    @else
                                        Không rõ sản phẩm
                                    @endif
                                </td>

                                <td>{{ number_format($detail->price) }}đ</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->price * $detail->quantity) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Thông tin đơn hàng</div>
            <div class="card-body">
                <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }}đ</p>
                @if ($order->promotion)
                    <p><strong>Mã giảm giá:</strong> {{ $order->promotion }}</p>
                @endif
                <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price) }}đ</p>
                <p><strong>Trạng thái đơn hàng:</strong> {{ ucfirst($order->status) }}</p>
            </div>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
@endsection

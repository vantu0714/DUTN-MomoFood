@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid my-4">
        <h3 class="text-center text-primary">Chi tiết đơn hàng #{{ $order->id }}</h3>
        <h5 class="text-center">{{ $order->order_code }}</h5>

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
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                            @php
                                $variant = $detail->productVariant;
                                $product = $variant?->product ?? $detail->product;
                                $image = $variant?->image ?? ($product?->image ?? null);
                                $productName = $product?->product_name ?? 'Không rõ sản phẩm';
                                $price = $detail->price;
                                $quantity = $detail->quantity;
                                $lineTotal = $price * $quantity;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start gap-2">
                                        @if ($image)
                                            <img src="{{ asset('storage/' . $image) }}" alt="Ảnh sản phẩm" width="60"
                                                class="rounded border">
                                        @else
                                            <div class="text-muted" style="width: 60px;">Không có ảnh</div>
                                        @endif

                                        <div>
                                            <div class="fw-semibold">{{ $productName }}</div>

                                            {{-- Hiển thị các thuộc tính của biến thể --}}
                                            @if ($variant && $variant->values->count())
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    @foreach ($variant->values as $value)
                                                        <span class="badge bg-info text-white px-2 py-1">
                                                            {{ $value->attribute->name }}: {{ $value->value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">₫{{ number_format($price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $quantity }}</td>
                                <td class="text-end text-danger fw-bold">₫{{ number_format($lineTotal, 0, ',', '.') }}</td>
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

                    $paymentMethodMap = [
                        'cod' => ['label' => 'Thanh toán khi nhận hàng (COD)', 'class' => 'secondary'],
                        'vnpay' => ['label' => 'Thanh toán qua VNPAY', 'class' => 'info'],
                        // thêm các phương thức khác nếu có
                    ];
                    $payment_method = $paymentMethodMap[$order->payment_method] ?? [
                        'label' => 'Không rõ',
                        'class' => 'light',
                    ];
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
                        <strong>Trạng thái thanh toán:</strong>
                        @if ($order->payment_status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái đơn hàng:</strong>

                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                            class="d-inline-block">
                            @csrf
                            @method('PATCH')

                            @php
                                $statusOptions = [
                                    1 => ['label' => 'Chưa xác nhận', 'class' => 'secondary'],
                                    2 => ['label' => 'Đã xác nhận', 'class' => 'info'],
                                    3 => ['label' => 'Đang giao', 'class' => 'primary'],
                                    4 => ['label' => 'Hoàn thành', 'class' => 'success'],
                                    5 => ['label' => 'Hoàn hàng', 'class' => 'dark'],
                                    6 => ['label' => 'Hủy đơn', 'class' => 'danger'],
                                ];
                            @endphp

                            <select name="status" class="form-select form-select-sm mt-2" onchange="this.form.submit()">
                                @foreach ($statusOptions as $key => $info)
                                    @php
                                        $canSelect = $key == $order->status || $key == $order->status + 1;
                                    @endphp
                                    <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}
                                        {{ $canSelect ? '' : 'disabled' }}>
                                        {{ $info['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <strong>Phương thức thanh toán:</strong>
                        <span class="badge bg-{{ $payment_method['class'] }}">{{ $payment_method['label'] }}</span>
                    </div>
                    @if ($order->status == 6 && $order->cancellation_reason)
                        <div class="col-12"><strong>Lý do hủy:</strong> {{ $order->cancellation_reason }}</div>
                    @endif
                    @if ($order->status == 1)
                        <div class="mt-3">
                            <button class="btn btn-danger"
                                onclick="document.getElementById('cancel-form').classList.toggle('d-none')">
                                Hủy đơn
                            </button>

                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST"
                                class="mt-3 d-none" id="cancel-form">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="cancellation_reason" class="form-label">Lý do hủy đơn</label>
                                    <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Nhập lý do..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                            </form>
                        </div>
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

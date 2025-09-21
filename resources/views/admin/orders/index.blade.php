@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Order Stats -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="mb-4">Quản lý đơn hàng</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Tìm kiếm đơn hàng</h5>
                <form method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-select" name="payment_status">
                                <option value="">Danh mục</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh
                                    toán</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa
                                    thanh toán</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="order_status">
                                <option value="all">Trạng thái</option>
                                <option value="1" {{ request('order_status') == '1' ? 'selected' : '' }}>Chưa xác nhận
                                </option>
                                <option value="2" {{ request('order_status') == '2' ? 'selected' : '' }}>Đã xác nhận
                                </option>
                                <option value="3" {{ request('order_status') == '3' ? 'selected' : '' }}>Đang giao
                                </option>
                                <option value="4" {{ request('order_status') == '4' ? 'selected' : '' }}>Hoàn thành
                                </option>
                                <option value="5" {{ request('order_status') == '5' ? 'selected' : '' }}>Hoàn hàng
                                </option>
                                <option value="6" {{ request('order_status') == '6' ? 'selected' : '' }}>Hủy đơn
                                </option>
                                <option value="7" {{ request('order_status') == '7' ? 'selected' : '' }}>Chờ xử lý
                                    hoàn hàng
                                </option>
                                <option value="8" {{ request('order_status') == '8' ? 'selected' : '' }}>Hoàn hàng
                                    thất bại
                                </option>
                                <option value="9" {{ request('order_status') == '9' ? 'selected' : '' }}>Đã giao hàng
                                </option>
                                <option value="10" {{ request('order_status') == '10' ? 'selected' : '' }}>Không xác
                                    nhận
                                </option>
                                <option value="11" {{ request('order_status') == '11' ? 'selected' : '' }}>Giao hàng
                                    thất bại
                                </option>
                                <option value="12" {{ request('order_status') == '11' ? 'selected' : '' }}>Hoàn hàng
                                    một phần
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                                    placeholder="Nhập tên người nhận, mã đơn hàng...">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-filter me-1"></i>
                                    Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- Order List -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách đơn hàng</h5>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã đơn hàng</th>
                                    <th>TT Người Đặt</th>
                                    <th>TT Người Nhận</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức thanh toán</th>
                                    <th>Trạng thái đơn hàng</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->order_code }}</td>
                                        <td>
                                            {{ $order->user->name ?? 'Khách vãng lai' }}<br>
                                            {{ $order->user->email ?? '' }}<br>
                                            {{ $order->user->phone ?? '' }}
                                        </td>
                                        <td>
                                            {{ $order->recipient_name }}<br>
                                            {{ $order->recipient_phone }}<br>
                                            {{ $order->recipient_address }}
                                        </td>
                                        <td>
                                            {{-- Ẩn tổng tiền và hiển thị "Đã hủy đơn" nếu đơn hàng bị hủy --}}
                                            @if ($order->status == 6)
                                                <span class="badge bg-danger">Đã hủy đơn</span>
                                            @else
                                                {{ number_format($order->total_price, 0, ',', '.') }}đ
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge
                    {{ $order->payment_method == 'cod' ? 'bg-primary' : 'bg-success' }}">
                                                {{ $order->payment_method == 'cod' ? 'Thanh toán khi nhận hàng' : 'Thanh toán qua VnPay' }}
                                            </span>

                                            @if ($order->payment_status == 'refunded')
                                                <div class="mt-1 small">
                                                    <i class="fas fa-undo me-1"></i> Đã hoàn tiền
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusLabels = [
                                                    1 => ['label' => 'Chưa xác nhận', 'class' => 'warning'],
                                                    2 => ['label' => 'Đã xác nhận', 'class' => 'info'],
                                                    3 => ['label' => 'Đang giao', 'class' => 'primary'],
                                                    4 => ['label' => 'Hoàn thành', 'class' => 'success'],
                                                    5 => ['label' => 'Hoàn hàng', 'class' => 'secondary'],
                                                    6 => ['label' => 'Hủy đơn', 'class' => 'danger'],
                                                    7 => ['label' => 'Chờ xử lý hoàn hàng', 'class' => 'warning'],
                                                    8 => ['label' => 'Hoàn hàng thất bại', 'class' => 'danger'],
                                                    9 => ['label' => 'Đã giao hàng', 'class' => 'success'],
                                                    10 => ['label' => 'Không xác nhận', 'class' => 'danger'],
                                                    11 => ['label' => 'Giao hàng thất bại', 'class' => 'danger'],
                                                    12 => ['label' => 'Hoàn hàng một phần', 'class' => 'warning'],
                                                ];
                                            @endphp

                                            <span class="badge bg-{{ $statusLabels[$order->status]['class'] }}">
                                                {{ $statusLabels[$order->status]['label'] }}
                                            </span>
                                        </td>

                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                                </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

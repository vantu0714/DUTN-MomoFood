@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Order Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="mb-4">Quản lý đơn hàng</h1>
                    
                    {{-- <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">TỔNG ĐƠN HÀNG</h5>
                                    <h2 class="mb-0">{{ $totalOrders }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">ĐÃ HOÀN THÀNH</h5>
                                    <h2 class="mb-0">{{ $completedOrders }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">ĐANG XỬ LÝ</h5>
                                    <h2 class="mb-0">{{ $processingOrders }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">ĐÃ HỦY</h5>
                                    <h2 class="mb-0">{{ $cancelledOrders }}</h2>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Tìm kiếm đơn hàng</h5>
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select">
                        <option selected>Danh mục</option>
                        <option>Tất cả</option>
                        <option>Đã thanh toán</option>
                        <option>Chưa thanh toán</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option selected>Trạng thái</option>
                        <option>Tất cả</option>
                        <option>Đang xử lý</option>
                        <option>Đã hoàn thành</option>
                        <option>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Nhập tên sản phẩm, mã đơn hàng...">
                        <button class="btn btn-primary" type="button">Tìm kiếm</button>
                    </div>
                </div>
            </div>
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
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>ORD{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
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
                                <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $order->payment_method == 'cod' ? 'Giao hàng nhận tiền' : 'Đã thanh toán' }}
                                    </span>
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
                                        ];
                                        $status = $statusLabels[$order->status] ?? ['label' => 'Không rõ', 'class' => 'dark'];
                                    @endphp
                                
                                    <span class="badge bg-{{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">Xem</a>
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Sửa</a>
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
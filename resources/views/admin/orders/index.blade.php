@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh sách đơn hàng</h2>

        <a href="{{ route('orders.create') }}" class="btn btn-primary mb-3">Thêm đơn hàng</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Người nhận</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->recipient_name }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $order->status }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.show', ['id' => $order->id]) }}" class="btn btn-sm btn-primary">Xem</a>
                                <a href="{{route('orders.edit', ['id' => $order->id]) }}" class="btn btn-sm btn-warning">Sửa</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Không có đơn hàng nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection

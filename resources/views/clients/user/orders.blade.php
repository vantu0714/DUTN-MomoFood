@extends('clients.layouts.app')

@section('content')

    <body style="margin-top: 200px;">
        <div class="container-xl px-4 mt-4" style="margin-top: 200px;">
            <nav class="nav nav-borders">
                <a class="nav-link active ms-0" href="{{ route('clients.info') }}"target="__blank">Thông tin</a>
                <a class="nav-link" href="{{ route('clients.changepassword') }}" target="__blank">Đổi
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
                                    <th>#Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $order->status }}</td>
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
                </div>
            </div>
        </div>
    </body>
@endsection

@extends('clients.layouts.app')

@section('content')
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
                <div class="col-xl-4">
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header">Ảnh đại diện</div>
                        <div class="card-body text-center">
                            <img class="img-account-profile rounded-circle mb-2"
                                src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="mt-3">
                                <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                                <p class="text-muted small">Thành viên từ {{ Auth::user()->created_at->format('d-m-Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-header">Thông tin người dùng</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="small mb-1">Họ và tên</label>
                                <input class="form-control" type="text" value="{{ Auth::user()->name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1">Email</label>
                                <input class="form-control" type="email" value="{{ Auth::user()->email }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1">Số điện thoại</label>
                                <input class="form-control" type="text" value="{{ Auth::user()->phone }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1">Địa chỉ</label>
                                <input class="form-control" type="text" value="{{ Auth::user()->address }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1">Cập nhật lần cuối</label>
                                <input class="form-control" type="text"
                                    value="{{ Auth::user()->updated_at->format('d-m-Y') }}" readonly>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('clients.edit') }}" class="btn btn-primary">
                                    Sửa thông tin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

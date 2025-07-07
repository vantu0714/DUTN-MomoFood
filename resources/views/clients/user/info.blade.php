@extends('clients.layouts.app')

@section('content')
    <div class="container-xl px-4" style="margin-top: 150px">
        <nav class="nav nav-borders">
            <a class="nav-link active ms-0"
                style="color: rgb(219, 115, 91); font-weight: 600; border-bottom: 2px solid rgb(219, 115, 91)"
                href="{{ route('clients.info') }}">Thông tin</a>

            <a class="nav-link text-dark" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link text-dark" href="{{ route('clients.orders') }}">Đơn hàng</a>

            <a href="#" class="nav-link text-dark"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
            </a>
        </nav>
        <hr class="mt-0 mb-4">

        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-circle me-2"></i>Thông tin tài khoản
                        </h5>
                    </div>
                    <div class="card-body text-center px-4">
                        <div class="position-relative d-inline-block mb-3">
                            <img class="rounded-circle border border-4 shadow-sm" src="{{ auth()->user()->avatar_url }}"
                                alt="{{ auth()->user()->name }}" width="150" height="150" style="object-fit: cover">
                        </div>
                        <h4 class="mt-2 mb-2 fw-bold">{{ Auth::user()->name }}</h4>
                        <div class="d-flex justify-content-center align-items-center text-muted small">
                            <i class="bi bi-calendar-check me-1"></i>
                            <span>Thành viên từ {{ Auth::user()->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Chi tiết thông tin
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="d-flex align-items-center mb-3">
                                <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Họ và tên</div>
                                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Email</div>
                                        <div class="fw-semibold">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="d-flex align-items-center mb-3">
                                <i class="bi bi-telephone me-2"></i>Thông tin liên hệ
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Số điện thoại</div>
                                        <div class="fw-semibold">{{ Auth::user()->phone ?: 'Chưa cập nhật' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Địa chỉ</div>
                                        <div class="fw-semibold">{{ Auth::user()->address ?: 'Chưa cập nhật' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('clients.edit') }}" class="btn px-4"
                                style="background-color: rgb(219, 115, 91); border-color: rgb(219, 115, 91); color: white">
                                <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <style>
        .nav-borders .nav-link.active {
            border-bottom-style: solid !important;
        }
    </style>
@endsection

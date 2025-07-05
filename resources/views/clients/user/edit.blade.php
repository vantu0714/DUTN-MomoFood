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

        <form action="{{ route('clients.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold" style="color: rgb(219, 115, 91)">
                                <i class="bi bi-person-circle me-2"></i>Ảnh đại diện
                            </h5>
                        </div>
                        <div class="card-body text-center d-flex flex-column">
                            <div class="position-relative mx-auto mb-3">
                                <img id="avatarPreview" class="rounded-circle border border-4 shadow"
                                    src="{{ auth()->user()->avatar_url }}" width="150" height="150"
                                    style="object-fit: cover; border-color: rgb(219, 115, 91)">
                                <button type="button" class="btn btn-sm position-absolute rounded-circle p-0"
                                    style="background-color: rgb(219, 115, 91); bottom: 10px; right: 10px; width: 32px; height: 32px"
                                    onclick="document.getElementById('avatarInput').click()">
                                    <i class="bi bi-camera text-white"></i>
                                </button>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="d-none"
                                    onchange="previewAvatar(event)">
                            </div>
                            <small class="text-muted mt-auto">JPG/PNG tối đa 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold" style="color: rgb(219, 115, 91)">
                                <i class="bi bi-pencil-square me-2"></i>Thông tin cá nhân
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="d-flex align-items-center mb-3" style="color: rgb(219, 115, 91)">
                                    <i class="bi bi-person-lines-fill me-2"></i>Thông tin cơ bản
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Họ và tên</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ Auth::user()->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Email</label>
                                        <input type="email" class="form-control" name="email"
                                            value="{{ Auth::user()->email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="d-flex align-items-center mb-3" style="color: rgb(219, 115, 91)">
                                    <i class="bi bi-telephone-fill me-2"></i>Liên hệ
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Số điện thoại</label>
                                        <input type="tel" class="form-control" name="phone"
                                            value="{{ Auth::user()->phone }}" placeholder="Nhập số điện thoại">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Địa chỉ</label>
                                        <input type="text" class="form-control" name="address"
                                            value="{{ Auth::user()->address }}" placeholder="Nhập địa chỉ">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between border-top pt-4 mt-3">
                                <a href="{{ route('clients.info') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn text-white" style="background-color: rgb(219, 115, 91)">
                                    <i class="bi bi-save me-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script>
        function previewAvatar(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('avatarPreview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('success') }}',
                confirmButtonColor: 'rgb(219, 115, 91)'
            });
        </script>
    @endif

    <style>
        .nav-borders .nav-link {
            padding-bottom: 0.5rem;
        }

        .nav-borders .nav-link.active {
            font-weight: 600;
        }

        .card {
            border-radius: 0.5rem;
        }
    </style>
@endsection

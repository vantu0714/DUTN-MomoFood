@extends('clients.layouts.app')

@section('content')
    <div class="container-xl px-4" style="margin-top: 150px">
        <nav class="nav nav-borders">
            <a class="nav-link" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link active ms-0"
                style="color: rgb(219, 115, 91); font-weight: 600; border-bottom: 2px solid rgb(219, 115, 91)"
                href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link" href="{{ route('clients.orders') }}">Đơn hàng</a>
            <a href="#" class="nav-link"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
            </a>
        </nav>
        <hr class="mt-0 mb-4">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="mb-0 fw-bold" style="color: rgb(219, 115, 91)">
                            <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('clients.updatepassword') }}">
                            @csrf
                            <div class="mb-4">
                                <h6 class="d-flex align-items-center mb-3" style="color: rgb(219, 115, 91)">
                                    <i class="bi bi-key me-2"></i>Thông tin mật khẩu
                                </h6>

                                <div class="form-group mb-3 position-relative">
                                    <label class="small mb-1">Mật khẩu hiện tại</label>
                                    <div class="input-group">
                                        <input id="currentPassword" name="currentPassword" type="password"
                                            class="form-control p-3 bg-light rounded border-0"
                                            placeholder="Nhập mật khẩu hiện tại">
                                        <span class="input-group-text bg-light border-0 position-absolute end-0 h-100"
                                            style="cursor: pointer; z-index: 10;"
                                            onclick="togglePassword('currentPassword', 'currentPasswordIcon')">
                                            <i id="currentPasswordIcon" class="bi bi-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group mb-3 position-relative">
                                    <label class="small mb-1">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input id="newPassword" name="newPassword" type="password"
                                            class="form-control p-3 bg-light rounded border-0"
                                            placeholder="Nhập mật khẩu mới">
                                        <span class="input-group-text bg-light border-0 position-absolute end-0 h-100"
                                            style="cursor: pointer; z-index: 10;"
                                            onclick="togglePassword('newPassword', 'newPasswordIcon')">
                                            <i id="newPasswordIcon" class="bi bi-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group mb-3 position-relative">
                                    <label class="small mb-1">Xác nhận mật khẩu mới</label>
                                    <div class="input-group">
                                        <input id="confirmPassword" name="confirmPassword" type="password"
                                            class="form-control p-3 bg-light rounded border-0"
                                            placeholder="Xác nhận mật khẩu mới">
                                        <span class="input-group-text bg-light border-0 position-absolute end-0 h-100"
                                            style="cursor: pointer; z-index: 10;"
                                            onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')">
                                            <i id="confirmPasswordIcon" class="bi bi-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button class="btn px-4" type="submit"
                                    style="background-color: rgb(219, 115, 91); border-color: rgb(219, 115, 91); color: white">
                                    <i class="bi bi-check-circle me-2"></i>Cập nhật mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        }
    </script>

    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: '{{ session('success') }}',
                confirmButtonColor: 'rgb(219, 115, 91)'
            });
        </script>
    @endif

    @if ($errors->any())
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: 'rgb(219, 115, 91)'
            });
        </script>
    @endif

    <style>
        .nav-borders .nav-link {
            padding: 0.5rem 1rem;
            color: #495057;
        }

        .nav-borders .nav-link.active {
            color: rgb(219, 115, 91);
            border-bottom: 2px solid;
            font-weight: 600;
        }

        .card {
            border-radius: 0.5rem;
        }

        .form-control {
            border: 1px solid #ced4da !important;
        }

        .form-control:focus {
            border-color: #ced4da !important;
            box-shadow: 0 0 0 0.25rem rgba(219, 115, 91, 0.25) !important;
        }

        .input-group-text {
            background-color: #f8f9fa !important;
            border: 1px solid #ced4da !important;
        }
    </style>
@endsection

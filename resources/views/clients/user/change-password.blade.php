@extends('clients.layouts.app')

@section('content')
    <div class="container-xl px-4" style="margin-top: 50px">
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('clients.updatepassword') }}">
                            @csrf
                            <div class="mb-4">
                                <h6 class="d-flex align-items-center mb-3">
                                    <i class="bi bi-key me-2"></i>Thông tin mật khẩu
                                </h6>

                                <div class="form-group mb-4">
                                    <label class="mb-2">Mật khẩu hiện tại</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-lock text-orange"></i>
                                        </span>
                                        <input id="currentPassword" name="currentPassword" type="password"
                                            class="form-control border-start-0 border-end-0 border-orange ps-0"
                                            placeholder="Nhập mật khẩu hiện tại">
                                        <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                            onclick="togglePassword('currentPassword', 'currentPasswordIcon')"
                                            style="cursor: pointer;">
                                            <i id="currentPasswordIcon" class="bi bi-eye-slash text-orange"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="mb-2">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-lock text-orange"></i>
                                        </span>
                                        <input id="newPassword" name="newPassword" type="password"
                                            class="form-control border-start-0 border-end-0 border-orange ps-0"
                                            placeholder="Nhập mật khẩu mới">
                                        <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                            onclick="togglePassword('newPassword', 'newPasswordIcon')"
                                            style="cursor: pointer;">
                                            <i id="newPasswordIcon" class="bi bi-eye-slash text-orange"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="mb-2">Xác nhận mật khẩu mới</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-shield-lock text-orange"></i>
                                        </span>
                                        <input id="confirmPassword" name="confirmPassword" type="password"
                                            class="form-control border-start-0 border-end-0 border-orange ps-0"
                                            placeholder="Xác nhận mật khẩu mới">
                                        <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                            onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')"
                                            style="cursor: pointer;">
                                            <i id="confirmPasswordIcon" class="bi bi-eye-slash text-orange"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button class="btn btn-orange px-4 fw-semibold rounded-3" type="submit">
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
        )
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

    @push('styles')
        <style>
            /* Style chung giống các trang trước */
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
                border-radius: 0;
                border: none;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                background-color: white;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                padding: 1.5rem;
            }

            /* Input group style giống trang register */
            .input-group {
                display: flex;
                align-items: stretch !important;
            }

            .input-group-text {
                height: 48px !important;
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 48px;
                background-color: #f8f9fa !important;
                margin: 0;
                padding: 0 12px;
                border-color: rgb(219, 115, 91) !important;
            }

            .form-control {
                height: 48px !important;
                padding: 0 16px;
                font-size: 16px;
                line-height: 48px !important;
                margin: 0;
                display: flex;
                align-items: center;
                border-color: rgb(219, 115, 91) !important;
            }

            .input-group-text i {
                font-size: 18px;
                line-height: 1;
                color: rgb(219, 115, 91);
            }

            .form-control:focus {
                border-color: rgb(219, 115, 91) !important;
                box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25) !important;
            }

            /* Xử lý border cho input group */
            .input-group .form-control {
                border-left: 0 !important;
                border-right: 0 !important;
            }

            .input-group .input-group-text:first-child {
                border-right: 0 !important;
            }

            .input-group .input-group-text:last-child {
                border-left: 0 !important;
            }

            /* Button style */
            .btn-orange {
                background-color: rgb(219, 115, 91);
                border-color: rgb(219, 115, 91);
                color: white;
                transition: all 0.3s ease;
                padding: 0.5rem 1.5rem;
            }

            .btn-orange:hover {
                background-color: rgb(199, 95, 71);
                border-color: rgb(199, 95, 71);
                color: white;
                transform: translateY(-2px);
            }

            /* Layout adjustments */
            .container-xl {
                padding-top: 100px;
                padding-bottom: 50px;
            }

            .form-group label {
                font-weight: 500;
                margin-bottom: 0.5rem;
            }

            .card-body {
                padding: 2rem;
            }
        </style>
    @endpush
@endsection

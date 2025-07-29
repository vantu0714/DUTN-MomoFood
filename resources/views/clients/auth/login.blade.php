@extends('clients.layouts.app')

@push('styles')
    <style>
        .login-card {
            background-color: transparent;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-left {
            background-color: #ffffff;
            border-radius: 0;
        }

        .login-right {
            background-color: rgb(219, 115, 91);
            border-radius: 0;
        }

        .btn-orange {
            background-color: rgb(219, 115, 91);
            border-color: rgb(219, 115, 91);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-orange:hover {
            background-color: rgb(199, 95, 71);
            border-color: rgb(199, 95, 71);
            color: white;
            transform: translateY(-2px);
        }

        .text-orange {
            color: rgb(219, 115, 91) !important;
        }

        .border-orange {
            border-color: rgb(219, 115, 91) !important;
        }

        /* FIX CHÍNH: Căn chỉnh input group */
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
            background-color: #f8f9fa;
            margin: 0;
            padding: 0 12px;
        }

        .form-control {
            height: 48px !important;
            padding: 0 16px;
            font-size: 16px;
            line-height: 48px !important;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .input-group-text i {
            font-size: 18px;
            line-height: 1;
            color: rgb(219, 115, 91);
        }

        .form-control:focus {
            border-color: rgb(219, 115, 91);
            box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25);
        }

        /* Xử lý border cho input có 2 addon */
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

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
            margin-bottom: 15px;
            border: 3px solid #fff;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center pt-5 pb-4" style="min-height: 80vh; margin-top: 100px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 login-card shadow-sm border-0">
                    <div class="row g-0 h-100">
                        <div class="col-md-7 login-left p-5">
                            <div class="mb-4">
                                <h2 class="fw-bold mb-2">Đăng nhập</h2>
                                <p class="text-muted mb-0">Chào mừng bạn quay lại</p>
                            </div>

                            @if (session('error'))
                                <div class="alert alert-danger text-center border-0 rounded-4 mb-4"
                                    style="font-size: 0.9rem;">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-envelope text-orange"></i>
                                        </span>
                                        <input type="email"
                                            class="form-control border-start-0 border-orange ps-0 rounded-end-3"
                                            name="email" placeholder="Nhập địa chỉ email" value="{{ old('email') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-lock text-orange"></i>
                                        </span>
                                        <input type="password"
                                            class="form-control border-start-0 border-end-0 border-orange ps-0"
                                            id="password" name="password" placeholder="Mật khẩu" required>
                                        <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                            onclick="togglePassword()" style="cursor: pointer;">
                                            <i class="bi bi-eye text-orange" id="togglePasswordIcon"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <button type="submit" class="btn btn-orange px-4 fw-semibold rounded-3">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                                    </button>
                                    <a href="{{ route('password.request') }}" class="text-orange text-decoration-none">
                                        Quên mật khẩu?
                                    </a>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <p class="text-muted mb-3">Hoặc đăng nhập bằng</p>
                                <a href="{{ url('/auth/google') }}" class="btn btn-danger rounded-pill px-4">
                                    <i class="bi bi-google me-2"></i>Google
                                </a>
                            </div>
                        </div>

                        <div
                            class="col-md-5 login-right text-white p-5 d-flex align-items-center justify-content-center flex-column">
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="bi bi-person-plus display-1 text-white opacity-75"></i>
                                </div>
                                <h3 class="fw-bold mb-3 text-white">Tạo tài khoản mới</h3>
                                <p class="text-white-50 mb-4 px-3">
                                    Đăng ký ngay hôm nay để trải nghiệm các dịch vụ tuyệt vời và nhận những ưu đãi đặc biệt!
                                </p>
                                <a href="{{ route('register') }}"
                                    class="btn btn-light btn-lg rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-person-plus me-2"></i>Đăng ký ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const icon = document.getElementById("togglePasswordIcon");
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        }
    </script>
@endsection

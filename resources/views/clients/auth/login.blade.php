@extends('clients.layouts.app')

@push('styles')
    <style>
        .login-card {
            background-color: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
        }

        .login-left {
            background-color: #ffffff;
            padding: 40px;
            height: 100%;
        }

        .login-right {
            background-color: #9cd62b;
            color: white;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .btn-green {
            background-color: #9cd62b;
            color: white;
            border: none;
        }

        .btn-green:hover {
            background-color: #8ec027;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center"
        style="padding-top: 140px; padding-bottom: 20px; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 login-card shadow">
                    <div class="row">
                        <div class="col-md-7 login-left">
                            <h2>Login</h2>
                            <p class="text-muted">Đăng Nhập</p>

                            @if (session('error'))
                                <div class="alert alert-danger text-center" style="font-size: 0.9rem;">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" novalidate>
                                @csrf
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fa fa-user text-success"></i></span>
                                    <input type="email" class="form-control" name="email"
                                        placeholder="Nhâp địa chỉ email" value="{{ old('email') }}" required>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text"><i class="fa fa-lock text-success"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Mật khẩu" required>
                                    <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                <div class="input-group mb-4">
                                    <a href="{{ url('/auth/google') }}" class="btn btn-danger">
                                        <i class="fab fa-google"></i> Đăng nhập với Google
                                    </a>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-green px-4">Đăng nhập</button>
                                    <a href="{{ route('password.request') }}" class="text-success">Quên mật khẩu</a>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-5 login-right">
                            <h2>Đăng ký tài khoản</h2>
                            <p class="text-white text-center px-2">Đăng ký ngay hôm nay để có các ưu đãi đặc biệt!</p>
                            <a href="{{ route('register') }}" class="btn btn-light mt-3">Đăng ký</a>
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
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }
    </script>
@endsection

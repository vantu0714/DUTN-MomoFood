@extends('clients.layouts.app')

@push('styles')
    <style>
        body {
            background-color: #f9f9f9;
        }

        .reset-card {
            background-color: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .reset-left {
            background-color: #ffffff;
            padding: 40px;
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
        style="padding-top: 180px; padding-bottom: 1%; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 reset-card shadow">
                    <div class="reset-left">
                        <h2>Đặt lại mật khẩu</h2>
                        <p class="text-muted">Nhập mật khẩu mới cho tài khoản của bạn</p>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                            <div class="mb-3">
                                <label for="password">Mật khẩu mới</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Nhập mật khẩu mới" required>
                                    <span class="input-group-text" onclick="togglePassword('password')"
                                        style="cursor: pointer;">
                                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" placeholder="Xác nhận lại mật khẩu" required>
                                    <span class="input-group-text" onclick="togglePassword('password_confirmation')"
                                        style="cursor: pointer;">
                                        <i class="fa fa-eye" id="togglePasswordIconConfirm"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-green px-4">Đặt lại mật khẩu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = id === 'password' ? document.getElementById("togglePasswordIcon") : document.getElementById(
                "togglePasswordIconConfirm");

            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }
    </script>
@endsection

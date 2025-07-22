@extends('clients.layouts.app')

@push('styles')
    <style>
        .reset-card {
            background-color: transparent;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .reset-left {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 0;
        }

        /* Sử dụng lại các style từ trang register */
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
            background-color: #f8f9fa;
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
            border-color: rgb(219, 115, 91);
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

        /* Điều chỉnh layout */
        .reset-left {
            padding: 3rem;
        }

        .reset-left h2 {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .reset-left p.text-muted {
            margin-bottom: 2rem;
        }

        .mb-3 label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center pt-5 pb-4" style="min-height: 80vh; margin-top: 100px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 reset-card shadow-sm">
                    <div class="reset-left">
                        <h2 class="fw-bold mb-2">Đặt lại mật khẩu</h2>
                        <p class="text-muted mb-4">Nhập mật khẩu mới cho tài khoản của bạn</p>

                        <form method="POST" action="{{ route('password.update') }}" novalidate>
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                            <div class="mb-3">
                                <label for="password">Mật khẩu mới</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                        <i class="bi bi-lock text-orange"></i>
                                    </span>
                                    <input type="password" id="password" name="password"
                                        class="form-control border-start-0 border-end-0 border-orange ps-0"
                                        placeholder="Nhập mật khẩu mới" required>
                                    <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                        onclick="togglePassword('password')" style="cursor: pointer;">
                                        <i class="bi bi-eye text-orange" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                        <i class="bi bi-shield-lock text-orange"></i>
                                    </span>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control border-start-0 border-end-0 border-orange ps-0"
                                        placeholder="Xác nhận lại mật khẩu" required>
                                    <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                        onclick="togglePassword('password_confirmation')" style="cursor: pointer;">
                                        <i class="bi bi-eye text-orange" id="togglePasswordIconConfirm"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-orange px-4 fw-semibold rounded-3">
                                    <i class="bi bi-key me-2"></i>Đặt lại mật khẩu
                                </button>
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
            const icon = id === 'password' ?
                document.getElementById("togglePasswordIcon") :
                document.getElementById("togglePasswordIconConfirm");

            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        }
    </script>
@endsection

@if (session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: 'rgb(219, 115, 91)',
            }).then(() => {
                fetch("{{ route('reset.session.clear') }}")
                    .then(() => window.location.href = "{{ route('login') }}");
            });
        };
    </script>
@endif

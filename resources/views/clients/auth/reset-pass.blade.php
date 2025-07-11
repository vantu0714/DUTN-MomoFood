@extends('clients.layouts.app')

@push('styles')
    <style>
        .reset-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .reset-left {
            background-color: #ffffff;
            padding: 40px;
        }

        .btn-custom {
            background-color: rgb(219, 115, 91);
            border-color: rgb(219, 115, 91);
            color: white;
        }

        .btn-custom:hover,
        .btn-custom:focus {
            background-color: rgb(200, 100, 75);
            border-color: rgb(200, 100, 75);
            color: white;
        }

        .btn-custom:focus {
            box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25);
        }

        .form-control:focus {
            border-color: rgb(219, 115, 91);
            box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25);
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
                        <form method="POST" action="{{ route('password.update') }}" novalidate>
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
                                <button type="submit" class="btn btn-custom px-4">Đặt lại mật khẩu</button>
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

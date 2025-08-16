@extends('clients.layouts.app')

@push('styles')
    <style>
        .register-card {
            background-color: transparent;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .register-left {
            background-color: #ffffff;
            border-radius: 0;
        }

        .register-right {
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
            /* Quan trọng nhất */
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
            /* Chỉ padding horizontal */
            font-size: 16px;
            line-height: 48px !important;
            /* Line-height = height để center text */
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

        input[type="file"] {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center pt-5 pb-4" style="min-height: 80vh; margin-top: 100px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 register-card shadow-sm border-0">
                    <div class="row g-0 h-100">
                        <div class="col-md-7 register-left p-5">
                            <div class="mb-4">
                                <h2 class="fw-bold mb-2">Đăng ký</h2>
                                <p class="text-muted mb-0">Tạo tài khoản mới để bắt đầu</p>
                            </div>

                            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-person text-orange"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control border-start-0 border-orange ps-0 rounded-end-3"
                                            name="name" placeholder="Nhập họ tên" value="{{ old('name') }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

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
                                    @error('email')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-lock text-orange"></i>
                                        </span>
                                        <input type="password"
                                            class="form-control border-start-0 border-end-0 border-orange ps-0"
                                            id="password" name="password" placeholder="Nhập mật khẩu" required>
                                        <span class="input-group-text bg-light border-start-0 border-orange rounded-end-3"
                                            onclick="togglePassword()" style="cursor: pointer;">
                                            <i class="bi bi-eye text-orange" id="togglePasswordIcon"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 border-orange rounded-start-3">
                                            <i class="bi bi-shield-lock text-orange"></i>
                                        </span>
                                        <input type="password"
                                            class="form-control border-start-0 border-orange ps-0 rounded-end-3"
                                            name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <button type="submit" class="btn btn-orange px-4 fw-semibold rounded-3">
                                        <i class="bi bi-person-plus me-2"></i>Đăng ký
                                    </button>
                                    <a href="{{ route('login') }}" class="text-orange text-decoration-none">
                                        Đã có tài khoản?
                                    </a>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <p class="text-muted mb-3">Hoặc đăng ký bằng</p>
                                <a href="{{ url('/auth/google') }}" class="btn btn-danger rounded-pill px-4">
                                    <i class="bi bi-google me-2"></i>Google
                                </a>
                            </div>
                        </div>

                        <div
                            class="col-md-5 register-right text-white p-5 d-flex align-items-center justify-content-center flex-column">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="bi bi-camera display-4 text-white opacity-75 mb-3"></i>
                                    <h4 class="fw-bold mb-3">Ảnh đại diện</h4>
                                </div>

                                <img id="avatarPreview" class="avatar-preview"
                                    src="{{ asset('images/default-avatar.png') }}" alt="Avatar">

                                <div class="mb-3">
                                    <label for="avatar" class="btn btn-light btn-lg rounded-pill px-4 fw-semibold">
                                        <i class="bi bi-upload me-2"></i>Tải ảnh mới
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg"
                                        onchange="previewAvatar(event)">
                                </div>

                                @error('avatar')
                                    <div class="text-danger mt-2 bg-light rounded p-2" style="font-size: 0.875rem;">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <p class="mt-3 text-white-50 small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Ảnh JPG hoặc PNG, không lớn hơn 5MB
                                </p>
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

        function previewAvatar(event) {
            const file = event.target.files[0];
            const preview = document.getElementById("avatarPreview");

            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Vui lòng chọn file ảnh JPG hoặc PNG!');
                    event.target.value = '';
                    return;
                }

                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Kích thước file không được vượt quá 5MB!');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.onerror = function() {
                    alert('Có lỗi xảy ra khi đọc file!');
                    event.target.value = '';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = "{{ asset('images/default-avatar.png') }}";
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const emailInput = document.querySelector('input[name="email"]');
            const emailValue = emailInput.value;

            if (!emailValue.endsWith('@gmail.com')) {
                e.preventDefault();
                alert('Chỉ chấp nhận địa chỉ email Gmail (@gmail.com)');
                emailInput.focus();
            }
        });
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
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('login') }}';
                }
            });
        };
    </script>
@endif

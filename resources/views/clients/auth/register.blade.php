@extends('clients.layouts.app')

@push('styles')
    <style>
        body {
            background-color: #f9f9f9;
        }

        .register-card {
            background-color: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .register-left {
            background-color: #ffffff;
            padding: 40px;
        }

        .register-right {
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

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 2px solid #fff;
            background-color: #fff;
        }

        input[type="file"] {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center"
        style="padding-top: 180px; padding-bottom: 1%; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 register-card shadow">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="row">
                            {{-- Form đăng ký --}}
                            <div class="col-md-7 register-left">
                                <h2>Đăng ký</h2>
                                <p class="text-muted">Tạo tài khoản mới</p>

                                {{-- Name --}}
                                <div class="mb-3">
                                    <label for="name">Họ và tên</label>
                                    <input type="text" class="form-control" name="name" placeholder="Nhập họ tên"
                                        required>
                                    @error('name')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Nhập email"
                                        required>
                                    @error('email')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Address --}}
                                <div class="mb-3">
                                    <label for="address">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address" placeholder="Nhập địa chỉ"
                                        required>
                                    @error('address')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div class="mb-3">
                                    <label for="phone">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="phone"
                                        placeholder="Nhập số điện thoại" required>
                                    @error('phone')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="mb-3">
                                    <label for="password">Mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Nhập mật khẩu" required>
                                        <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                                            <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-3">
                                    <label for="password_confirmation">Nhập lại mật khẩu</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Xác nhận mật khẩu" required>
                                </div>

                                <div class="row">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('login') }}" class="btn btn-green px-4">Quay lại đăng
                                            nhập</a>
                                        <button type="submit" class="btn btn-green px-4">Đăng ký</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Avatar --}}
                            <div class="col-md-5 register-right">
                                <h4>Ảnh đại diện</h4>
                                <img id="avatarPreview" class="avatar-preview"
                                    src="{{ asset('images/default-avatar.png') }}" alt="Avatar">
                                <label for="avatar" class="btn btn-light">Tải ảnh mới</label>
                                <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg"
                                    onchange="previewAvatar(event)">
                                @error('avatar')
                                    <div class="text-danger mt-2" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                                <p class="mt-2 text-white">Ảnh JPG hoặc PNG, không lớn hơn 5MB</p>
                            </div>
                        </div>
                    </form>
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

        function previewAvatar(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById("avatarPreview").src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function previewAvatar(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById("avatarPreview").src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
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
                confirmButtonColor: '#9cd62b',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('login') }}';
                }
            });
        };
    </script>
@endif

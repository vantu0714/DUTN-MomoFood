@extends('clients.layouts.app')

@section('content')
    <div class="container-xl px-4" style="margin-top: 200px">
        <!-- Phần navigation giữ nguyên như trước -->
        <nav class="nav nav-borders">
            <a class="nav-link" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link active ms-0" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link" href="{{ route('clients.orders') }}">Đơn hàng</a>
            <a href="#" class="nav-link"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>
        <hr class="mt-0 mb-4">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('clients.updatepassword') }}">
                            @csrf
                            <div class="mb-4">
                                <h6 class="d-flex align-items-center text-primary mb-3">
                                    <i class="bi bi-key me-2"></i>Thông tin mật khẩu
                                </h6>

                                <!-- Trường mật khẩu hiện tại -->
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

                                <!-- Trường mật khẩu mới -->
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

                                <!-- Trường xác nhận mật khẩu -->
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
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="bi bi-check-circle me-2"></i>Cập nhật mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Phần thông báo SweetAlert giữ nguyên -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#d33'
            });
        </script>
    @endif
@endsection

@include('clients.layouts.header')
<div class="main_content_iner">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_box mb_30">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="modal-content cs_modal">
                                <div class="modal-header theme_bg_1 justify-content-center">
                                    <h5 class="modal-title text_white">Đăng Ký</h5>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Họ và tên</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Nhập họ và tên" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Nhập địa chỉ email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Mật khẩu</label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password"
                                                    class="form-control" placeholder="Nhập mật khẩu" required>
                                                <span class="input-group-text" onclick="togglePassword()"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn_1 full_width text-center mb-3">Đăng
                                            ký</button>
                                        <p class="text-center">
                                            Đă có tài khoản?
                                            <a data-bs-toggle="modal" data-bs-target="#login" data-bs-dismiss="modal"
                                                href="{{ route('login') }}">Đăng nhập</a>
                                        </p>
                                        <div class="text-center">
                                            <a href="{{ route('password.request') }}" data-bs-toggle="modal" data-bs-target="#forgot_password"
                                                data-bs-dismiss="modal" class="pass_forget_btn">
                                                Quên mật khẩu
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
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

        // Đổi icon
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    }
</script>

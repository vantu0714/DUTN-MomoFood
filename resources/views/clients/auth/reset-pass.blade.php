@include('clients.layouts.header')

<div class="main_content_iner ">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_box mb_30">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="modal-content cs_modal">
                                <div class="modal-header justify-content-center theme_bg_1">
                                    <h5 class="modal-title">Đổi mật khẩu mới</h5>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Mật khẩu</label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password"
                                                    placeholder="Mật khẩu mới" class="form-control" required>
                                                <span class="input-group-text" onclick="togglePassword()"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Nhập lại mật
                                                khẩu</label>
                                            <div class="input-group">
                                                <input type="password" id="password_confirmation"
                                                    name="password_confirmation" class="form-control"
                                                    placeholder="Xác nhận mật khẩu" required>
                                                <span class="input-group-text" onclick="togglePassword()"
                                                    style="cursor: pointer;">
                                                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <button type="submit">Đặt lại mật khẩu</button>
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

@include('clients.layouts.header')

<div class="main_content_iner ">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_box mb_30">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="modal-content cs_modal">
                                <div class="modal-header theme_bg_1">
                                    <h5 class="modal-title text_white">Reset Password</h5>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        <input type="password" name="password" placeholder="Mật khẩu mới" required>
                                        <input type="password" name="password_confirmation"
                                            placeholder="Xác nhận mật khẩu" required>
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

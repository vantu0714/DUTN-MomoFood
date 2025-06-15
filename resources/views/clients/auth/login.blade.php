@include('clients.layouts.header')

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="white_box mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="modal-content cs_modal">
                        <div class="modal-header justify-content-center theme_bg_1">
                            <h5 class="modal-title">Đăng nhập</h5>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        class="form-control" placeholder="Nhập địa chỉ email" required autofocus>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Nhập mật khẩu" required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="submit" class="btn_1 full_width text-center mb-3">Đăng nhập</button>
                                <div class="text-center">
                                    <p class="mb-1">
                                        Chưa có tài khoản?
                                        <a data-bs-toggle="modal" data-bs-target="#sing_up" data-bs-dismiss="modal"
                                            href="/register">Đăng ký</a>
                                    </p>
                                    <a href="{{ route('password.request') }}" data-bs-toggle="modal"
                                        data-bs-target="#forgot_password" data-bs-dismiss="modal"
                                        class="pass_forget_btn">Quên mật khẩu</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

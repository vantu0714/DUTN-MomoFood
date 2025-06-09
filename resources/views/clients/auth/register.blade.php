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
                                    <h5 class="modal-title text_white">Register</h5>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Full Name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Enter your email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Password" required>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" id="check_box" class="form-check-input"
                                                name="newsletter">
                                            <label class="form-check-label" for="check_box">Keep me up to date</label>
                                        </div>
                                        <button type="submit" class="btn_1 full_width text-center mb-3">Sign
                                            Up</button>
                                        <p class="text-center">
                                            Already have an account?
                                            <a data-bs-toggle="modal" data-bs-target="#login" data-bs-dismiss="modal"
                                                href="/login">Log in</a>
                                        </p>
                                        <div class="text-center">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#forgot_password"
                                                data-bs-dismiss="modal" class="pass_forget_btn">
                                                Forgot Password?
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

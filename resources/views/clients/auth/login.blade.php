{{-- @extends('clients.layouts.app') --}}
@include('clients.layouts.header')
{{-- @section('content') --}}
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="white_box mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="modal-content cs_modal">
                        <div class="modal-header justify-content-center theme_bg_1">
                            <h5 class="modal-title">Log in</h5>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        placeholder="Enter your email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Enter your password" required>
                                </div>
                                <button type="submit" class="btn_1 full_width text-center mb-3">Log in</button>
                                <div class="text-center">
                                    <p class="mb-1">
                                        Need an account?
                                        <a data-bs-toggle="modal" data-bs-target="#sing_up" data-bs-dismiss="modal"
                                            href="/register">Sign Up</a>
                                    </p>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgot_password"
                                        data-bs-dismiss="modal" class="pass_forget_btn">Forgot Password?</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @endsection --}}

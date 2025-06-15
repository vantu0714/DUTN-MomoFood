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
                                    <h5 class="modal-title text_white">Forget Password</h5>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf
                                        <div class>
                                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                                        </div>
                                        <button type="submit" class="btn_1 full_width text-center">Send</button>
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

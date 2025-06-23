<!-- Navbar Start -->
<div class="container-fluid fixed-top">

    <!-- Topbar -->
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i><a href="#"
                        class="text-white">1 Trịnh Văn Bô, Hà Nội</a></small>
                <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#"
                        class="text-white">momofood@gmail.com</a></small>
            </div>
            <div class="top-link pe-2">
                <a href="#" class="text-white"><small class="text-white mx-2">Chính sách bảo mật</small></a>
                <a href="#" class="text-white"><small class="text-white mx-2">Điều khoản sử dụng</small></a>
                <a href="#" class="text-white"><small class="text-white ms-2">Bán hàng và hoàn tiền</small></a>
            </div>
        </div>
    </div>

    <!-- Main navbar -->
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl w-100">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="navbar-brand">
                <img class="img-logo" src="{{ asset('clients/img/logo_datn.png') }}" alt="">
            </a>

            <!-- Collapsible menu - luôn hiện -->
            <div class="d-flex align-items-center justify-content-between flex-grow-1 ms-5">
                <!-- Main menu -->
                <ul class="navbar-nav d-flex flex-row mx-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link active">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('shop.index') }}" class="nav-link">Cửa hàng</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}" class="nav-link">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}" class="nav-link">Ưu đãi</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contacts.index') }}" class="nav-link">Liên hệ</a>
                    </li>
                </ul>

                <!-- Right side icons -->
                <div class="d-flex align-items-center">
                    <!-- Search -->
                    <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4"
                        data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search text-primary"></i>
                    </button>

                    <!-- Cart -->
                    <a href="{{ route('carts.index') }}" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span id="cart-count"
                            class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                            style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
                            {{ $cartCount }}
                        </span>

                    </a>
                    @auth
                        <div class="position-relative d-flex">
                            <a href="{{ route('clients.info') }}" class="d-flex align-items-center" id="userDropdown"
                                role="button">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('admins/assets/img/client_img.png') }}"
                                    alt="avatar" width="40" height="40" style="border-radius: 50%;">
                            </a>
                        </div>
                    @endauth

                    @guest
                        <div class="position-relative d-flex">
                            <a href="{{ route('login') }}" class="d-flex align-items-center">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar End -->

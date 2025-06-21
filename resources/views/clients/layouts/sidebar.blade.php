<!-- Navbar start -->
<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#"
                        class="text-white">1 Trịnh Văn Bô, Hà Nội</a></small>
                <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#"
                        class="text-white">momofood@gmail.com</a></small>
            </div>
            <div class="top-link pe-2">
                <a href="#" class="text-white"><small class="text-white mx-2">Chính sách bảo mật</small>/</a>
                <a href="#" class="text-white"><small class="text-white mx-2">Điều khoản sử dụng</small>/</a>
                <a href="#" class="text-white"><small class="text-white ms-2">Bán hàng và hoàn tiền</small></a>
            </div>
        </div>
    </div>
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img class="img-logo" src="{{ asset('clients/img/logo_datn.png') }}" alt="">
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="{{ route('home') }}" class="nav-item nav-link active">Trang chủ</a>
                    <a href="{{ route('shop.index') }}" class="nav-item nav-link">Cửa hàng</a>
                    <a href="{{ route('news.index') }}" class="nav-item nav-link">Tin tức</a>
                    <a href="{{ route('news.index') }}" class="nav-item nav-link">Ưu đãi</a>
                    <a href="{{ route('contacts.index') }}" class="nav-item nav-link">Liên hệ</a>
                </div>
                <div class="d-flex m-3 me-0">
                    <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4"
                        data-bs-toggle="modal" data-bs-target="#searchModal"><i
                            class="fas fa-search text-primary"></i></button>
                    <a href="{{ route('carts.index') }}" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                           <span id="cart-count"
    class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
    style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
    {{ array_sum(array_column(session('cart', []), 'quantity')) }}
</span>


                    </a>
                    @auth
                        <div class="dropdown dropdown-hover position-relative d-flex">
                            <a href="#" class="dropdown-toggle d-flex align-items-center" id="userDropdown"
                                role="button">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('admins/assets/img/client_img.png') }}"
                                    alt="avatar" width="40" height="40" style="border-radius: 50%;">
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown" style="top: 100%; left: 0;">
                                <li><a class="dropdown-item" href="{{ route('clients.info') }}">Trang cá nhân</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Đăng xuất
                                    </a>
                                </li>
                            </ul>
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

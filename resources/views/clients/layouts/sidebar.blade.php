<!-- Navbar Start -->
<div class="container-fluid fixed-top p-0">

    <!-- Topbar -->
    <div class="bg-momo-gradient py-2 border-bottom border-white border-opacity-25">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="top-info ps-2">
                <small class="me-4 text-white">
                    <i class="fas fa-map-marker-alt me-1 text-warning"></i>
                    1 Trịnh Văn Bô, Hà Nội
                </small>
                <small class="me-4 text-white">
                    <i class="fas fa-envelope me-1 text-warning"></i>
                    momofood@gmail.com
                </small>
            </div>
            <div class="top-link pe-2">
                <a href="#" class="text-white me-3">Chính sách bảo mật</a>
                <a href="#" class="text-white me-3">Điều khoản sử dụng</a>
                <a href="#" class="text-white">Bán hàng và hoàn tiền</a>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <div class="bg-white py-3 shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center me-4">
                <img src="{{ asset('clients/img/logo_datn.png') }}" alt="Logo" style="max-height: 55px;">
            </a>

            <!-- Menu -->
            <ul class="navbar-nav flex-row gap-4 mx-auto">
                <li class="nav-item"><a href="{{ route('home') }}" class="nav-link active text-dark fw-semibold">Trang chủ</a></li>
                <li class="nav-item"><a href="{{ route('shop.index') }}" class="nav-link text-dark fw-semibold">Cửa hàng</a></li>
                <li class="nav-item"><a href="{{ route('news.index') }}" class="nav-link text-dark fw-semibold">Tin tức</a></li>
                <li class="nav-item"><a href="{{ route('news.index') }}" class="nav-link text-dark fw-semibold">Ưu đãi</a></li>
                <li class="nav-item"><a href="{{ route('contacts.index') }}" class="nav-link text-dark fw-semibold">Liên hệ</a></li>
            </ul>

            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                <!-- Search -->
                <form action="{{ route('clients.search') }}" method="GET">
                    <div class="input-group rounded shadow-sm">
                        <input type="search" name="keyword" class="form-control border" placeholder="Tìm kiếm..." style="border-radius: 20px 0 0 20px;">
                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 0 20px 20px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Cart -->
                <a href="{{ route('carts.index') }}" class="position-relative">
                    <i class="bi bi-cart3 fa-2x text-danger"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">
                        {{ $cartCount }}
                    </span>
                </a>

                <!-- Auth -->
                @auth
                    <a href="{{ route('clients.info') }}">
                        <img src="{{ auth()->user()->avatar_url }}" onerror="this.src='{{ auth()->user()->getDefaultAvatar() }}'"
                             width="40" height="40" style="border-radius: 50%;">
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-danger">
                        <i class="fas fa-user fa-2x"></i>
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>
<!-- Navbar End -->


<!-- Style -->
<style>
    .bg-momo-gradient {
        background: linear-gradient(to right, #db735b, #d56a58);
    }

    .navbar-nav .nav-link {
        transition: all 0.2s ease-in-out;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: #dc3545 !important;
    }

    .input-group input[type="search"]::placeholder {
        font-size: 0.9rem;
        color: #888;
    }
</style>

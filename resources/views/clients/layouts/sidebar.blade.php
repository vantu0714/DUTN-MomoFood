<!-- Navbar với active state động -->
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

            <!-- Collapsible menu -->
            <div class="d-flex align-items-center justify-content-between flex-grow-1 ms-5">
                <!-- Main menu -->
                <ul class="navbar-nav d-flex flex-row mx-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('shop.index') }}"
                            class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                            Cửa hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}"
                            class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
                            Tin tức
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}"
                            class="nav-link {{ request()->url() == route('news.index') && request()->has('promotion') ? 'active' : '' }}">
                            Ưu đãi
                        </a>
                        <a href="{{ route('gioithieu.index') }}" class="nav-link">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contacts.index') }}"
                            class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                            Liên hệ
                        </a>
                    </li>
                </ul>

                <!-- Right side icons -->
                <div class="d-flex align-items-center">
                    <!-- Search Form -->
                    <form action="{{ route('clients.search') }}" method="GET" class="d-flex align-items-center me-4"
                        style="max-width: 300px;">
                        <div class="input-group">
                            <input type="search" name="keyword" class="form-control border-secondary"
                                placeholder="Tìm kiếm..." required>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

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
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                    width="40" height="40" style="border-radius: 50%;"
                                    onerror="this.src='{{ auth()->user()->getDefaultAvatar() }}'">
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

<script>
    // JavaScript solution để xử lý active state
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        const currentUrl = window.location.pathname;

        // Xóa tất cả active classes
        navLinks.forEach(link => {
            link.classList.remove('active');
        });

        // Thêm active class cho link hiện tại
        navLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;

            // Kiểm tra exact match hoặc partial match
            if (linkPath === currentUrl ||
                (linkPath !== '/' && currentUrl.startsWith(linkPath))) {
                link.classList.add('active');
            }

            // Xử lý đặc biệt cho trang chủ
            if (currentUrl === '/' && linkPath === '/') {
                link.classList.add('active');
            }
        });
    });

    // Xử lý khi click vào nav link
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Xóa active từ tất cả links
            document.querySelectorAll('.navbar-nav .nav-link').forEach(l => {
                l.classList.remove('active');
            });

            // Thêm active vào link được click
            this.classList.add('active');
        });
    });
</script>

<style>
    /* CSS để đảm bảo active state hoạt động đúng */
    .navbar .navbar-nav .nav-link {
        position: relative;
        transition: all 0.3s ease;
    }

    .navbar .navbar-nav .nav-link.active {
        color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }

    /* Đảm bảo active state không bị override bởi hover */
    .navbar .navbar-nav .nav-link.active:hover {
        color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.15) !important;
    }
</style>

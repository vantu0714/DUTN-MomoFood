<!-- Navbar với active state động -->
<div class="container-fluid fixed-top">
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
                        <div class="input-group search-group">
                            <input type="search" name="keyword" class="form-control search-input"
                                placeholder="Tìm kiếm..." required>
                            <button type="submit" class="btn btn-search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Cart -->
                    <a href="{{ route('carts.index') }}" class="position-relative me-4 my-auto">
                        <i class="bi bi-cart3 fa-2x text-danger"></i>
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
        </div>
    </div>
</div>
<!-- Navbar End -->

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
    /* Màu nền cam nhạt */
    .bg-momo-gradient {
        background: linear-gradient(to right, #db735b, #d56a58);
    }

    /* NAVIGATION LINKS */
    .navbar-nav .nav-link {
        font-weight: 500;
        color: #333 !important;
        transition: all 0.3s ease-in-out;
        padding: 10px 15px;
        position: relative;
    }

    /* Hover effect */
    .navbar-nav .nav-link:hover {
        color: #b54d00 !important;
    }

    /* Active state */
    .navbar-nav .nav-link.active {
        color: #b54d00 !important;
        border-bottom: 2px solid #b54d00;
        background-color: transparent;
    }

    /* Tách topbar và navbar bằng border */
    .topbar {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Style cho thanh tìm kiếm */
    .search-group {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .search-input {
        border: none;
        padding: 10px 15px;
        background-color: #f8f9fa;
    }

    .search-input:focus {
        outline: none;
        box-shadow: none;
        background-color: #fff;
    }

    .btn-search {
        background: linear-gradient(to right, #db735b, #d56a58);
        color: white;
        border: none;
        padding: 0 15px;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        background: linear-gradient(to right, #d56a58, #cf5f55);
        color: white;
    }

    /* Optional: căn giữa vertically nếu cần */
    .navbar-brand img {
        max-height: 55px;
    }
</style>

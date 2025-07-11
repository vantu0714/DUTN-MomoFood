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
                    <div class="search-container me-4" style="max-width: 300px; position: relative;">
                        <form action="{{ route('clients.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="input-group search-group">
                                <input type="search" name="keyword" id="search-input" class="form-control search-input"
                                    placeholder="Tìm kiếm..." required autocomplete="off">
                                <button type="submit" class="btn btn-search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Dropdown kết quả tìm kiếm -->
                        <div id="search-dropdown" class="search-dropdown">
                            <div class="search-results"></div>
                        </div>
                    </div>

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

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');
        const searchResults = document.querySelector('.search-results');
        let searchTimeout;

        // Xử lý tìm kiếm khi nhập
        searchInput.addEventListener('input', function() {
            const keyword = this.value.trim();

            clearTimeout(searchTimeout);

            if (keyword.length < 2) {
                hideDropdown();
                return;
            }

            searchTimeout = setTimeout(() => {
                searchProducts(keyword);
            }, 300);
        });

        // Ẩn dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                hideDropdown();
            }
        });

        // Ẩn dropdown khi focus ra khỏi input
        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                hideDropdown();
            }, 200);
        });

        // Hiện dropdown khi focus vào input (nếu có nội dung)
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                showDropdown();
            }
        });

        function searchProducts(keyword) {
            fetch(`{{ route('clients.search.ajax') }}?keyword=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    displayResults(data.products);
                })
                .catch(error => {
                    console.error('Lỗi tìm kiếm:', error);
                });
        }

        function displayResults(products) {
            if (products.length === 0) {
                searchResults.innerHTML =
                    '<div class="search-item no-results">Không tìm thấy sản phẩm nào</div>';
            } else {
                searchResults.innerHTML = products.map(product => {
                    const hasDiscount = product.discount_percentage > 0;
                    const isInStock = product.quantity_in_stock > 0;

                    return `
            <div class="search-item ${!isInStock ? 'out-of-stock' : ''}" onclick="window.location.href='${product.url}'">
                <div class="search-item-image-container">
                    <img src="${product.image}"
                         alt="${product.name}"
                         class="search-item-image"
                         onerror="this.src='https://via.placeholder.com/150x150?text=No+Image';">
                    ${hasDiscount ? `<span class="search-item-discount">-${product.discount_percentage}%</span>` : ''}
                    ${!isInStock ? '<span class="search-item-stock-status">Hết hàng</span>' : ''}
                </div>
                <div class="search-item-content">
                    <div class="search-item-name">${product.name}</div>
                    <div class="search-item-code">Mã: ${product.code}</div>
                    <div class="search-item-info">
                        <span class="search-item-category">${product.category}</span>
                        <div class="search-item-price-container">
                            <span class="search-item-price">${formatPrice(product.price)}</span>
                            ${hasDiscount ? `<span class="search-item-original-price">${formatPrice(product.original_price)}</span>` : ''}
                        </div>
                    </div>
                </div>
            </div>
            `;
                }).join('');
            }
            showDropdown();
        }

        function showDropdown() {
            searchDropdown.style.display = 'block';
        }

        function hideDropdown() {
            searchDropdown.style.display = 'none';
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(price);
        }
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

    /* Dropdown tìm kiếm */
    .search-container {
        position: relative;
    }

    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 5px;
    }

    .search-results {
        padding: 8px 0;
    }

    .search-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .search-item:hover {
        background-color: #f8f9fa;
    }

    .search-item:last-child {
        border-bottom: none;
    }

    .search-item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .search-item-content {
        flex: 1;
        min-width: 0;
    }

    .search-item-name {
        font-weight: 500;
        color: #333;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-item-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-item-category {
        font-size: 12px;
        color: #666;
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .search-item-price {
        font-weight: 600;
        color: #d56a58;
        font-size: 14px;
    }

    .no-results {
        text-align: center;
        color: #666;
        font-style: italic;
        padding: 20px;
        border-bottom: none !important;
    }

    .no-results:hover {
        background-color: transparent !important;
        cursor: default !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-dropdown {
            left: -50px;
            right: -50px;
        }
    }
</style>

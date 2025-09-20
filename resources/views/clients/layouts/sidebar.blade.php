<!-- Navbar với active state động -->
@vite('resources/js/app.js')
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
                <a href="#" class="text-white">Thông báo</a>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <div class="bg-white shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center me-4">
                <img src="{{ asset('clients/img/logo_datn.png') }}" alt="Logo" style="max-height: 95px;">
            </a>

            <!-- Collapsible menu -->
            <div class="d-flex align-items-center justify-content-between flex-grow-1 ms-5">
                <!-- Main menu -->
                <ul class="navbar-nav d-flex flex-row mx-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            TRANG CHỦ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('shop.index') }}"
                            class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                            CỬA HÀNG
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}"
                            class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
                            TIN TỨC
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('gioithieu.index') }}" class="nav-link"> GIỚI THIỆU </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('contacts.index') }}"
                            class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                            LIÊN HỆ
                        </a>
                    </li> --}}
                </ul>

                <!-- Right side icons -->
                <div class="d-flex align-items-center">
                    <!-- Search Form -->
                    <div class="search-container me-4" style="min-width: 400px; position: relative;">
                        <form action="{{ route('clients.search') }}" method="GET" class="d-flex align-items-center">
                            <div class="input-group search-group">
                                <input type="search" name="keyword" id="search-input" class="form-control search-input"
                                    placeholder="Tìm kiếm sản phẩm..." required autocomplete="off">
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
<!-- Notifications -->
<li class="nav-item dropdown me-4 list-unstyled">
    <a class="nav-link position-relative" href="#" id="orderNotiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-bell fa-2x text-warning"></i>
        <span id="order-noti-count"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            0
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow"
        aria-labelledby="orderNotiDropdown"
        style="width: 350px; max-height: 400px; overflow-y: auto;">
        <li class="dropdown-header fw-bold">Thông báo đơn hàng</li>
        <div id="order-noti-items"></div>
        <li>
            <a class="dropdown-item text-center" href="{{ route('notifications.orders.index') }}">
            Xem tất cả
        </a>
        </li>
    </ul>
</li>

                    <!-- Nút mở chat -->
                    {{-- <li class="nav-item position-relative">
                        <a href="javascript:void(0)" id="chatToggle" class="nav-link">
                            <i class="fas fa-comments"></i>
                        </a>
                    </li> --}}


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
                                <i class="fas fa-user fa-2x" style="color: #db735b"></i>
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
                    const hasDiscount = product.has_discount;

                    return `
        <div class="search-item" onclick="window.location.href='${product.url}'">
            <div class="search-item-image-container">
                <img src="${product.image}"
                     alt="${product.name}"
                     class="search-item-image"
                     onerror="this.src='https://via.placeholder.com/150x150?text=No+Image';">
            </div>
            <div class="search-item-content">
                <h3 class="search-item-name">${product.name}</h3>
                <div class="search-item-category">${product.category}</div>
                <div class="search-item-price-section">
                    ${hasDiscount ?
                        `<div class="search-item-price-group">
                            <span class="search-item-price current">${formatPrice(product.discounted_price)}</span>
                            <span class="search-item-price original">${formatPrice(product.original_price)}</span>
                        </div>`
                        :
                        `<span class="search-item-price current">${formatPrice(product.original_price)}</span>`
                    }
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
        width: 100%;
    }

    .search-input {
        border: none;
        padding: 12px 20px;
        background-color: #f8f9fa;
        font-size: 14px;
        width: 100%;
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
        padding: 0 18px;
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
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        max-height: 450px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 5px;
    }

    .search-results {
        padding: 4px 0;
    }

    .search-item {
        display: flex;
        padding: 12px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #f1f3f4;
        gap: 12px;
        align-items: flex-start;
    }

    .search-item:hover {
        background-color: #f8f9fa;
    }

    .search-item:last-child {
        border-bottom: none;
    }

    .search-item-image-container {
        flex-shrink: 0;
        width: 60px;
        height: 60px;
    }

    .search-item-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .search-item-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .search-item-name {
        font-size: 14px;
        font-weight: 500;
        color: #1f2937;
        margin: 0;
        line-height: 1.3;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .search-item-category {
        font-size: 12px;
        color: #6b7280;
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
    }

    .search-item-price-section {
        margin-top: auto;
    }

    .search-item-price-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-item-price.current {
        font-weight: 600;
        color: #d56a58;
        font-size: 14px;
    }

    .search-item-price.original {
        font-size: 12px;
        color: #9ca3af;
        text-decoration: line-through;
        font-weight: 400;
    }

    .no-results {
        text-align: center;
        color: #6b7280;
        font-style: italic;
        padding: 24px 20px;
        border-bottom: none !important;
        cursor: default !important;
    }

    .no-results:hover {
        background-color: transparent !important;
    }

    /* Scrollbar styling */
    .search-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .search-dropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .search-dropdown::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .search-dropdown::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-container {
            min-width: 250px !important;
        }

        .search-dropdown {
            left: -20px;
            right: -20px;
            max-height: 350px;
        }

        .search-item {
            padding: 10px 12px;
            gap: 10px;
        }

        .search-item-image-container {
            width: 50px;
            height: 50px;
        }

        .search-item-name {
            font-size: 13px;
        }

        .search-item-code {
            font-size: 11px;
            padding: 1px 4px;
        }

        .search-item-header {
            gap: 4px;
            align-items: flex-start;
        }

        .search-item-price-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .search-item-price.current {
            font-size: 13px;
        }

        .search-item-category {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .search-dropdown {
            left: -30px;
            right: -30px;
            max-height: 300px;
        }

        .search-item {
            padding: 8px 10px;
            gap: 8px;
        }

        .search-item-image-container {
            width: 45px;
            height: 45px;
        }

        .search-item-name {
            font-size: 12px;
        }

        .search-item-category {
            font-size: 10px;
        }

        .search-item-price.current {
            font-size: 12px;
        }

        .search-item-price.original {
            font-size: 11px;
        }
    }
</style>


<script>
    function loadOrderNotifications() {
        fetch("{{ route('order.notifications.fetch') }}?ts=" + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                let html = "";

                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(noti => {
                        html += `
                            <li class="dropdown-item">
                                <a href="javascript:void(0)" onclick="markNotificationAsRead(${noti.id}, '${noti.link}')">
                                    <div><strong>${noti.message}</strong></div>
                                    <small>${noti.time ?? ''}</small>
                                </a>
                            </li>
                        `;
                    });
                } else {
                    html = '<li class="dropdown-item">Chưa có thông báo</li>';
                }

                document.getElementById("order-noti-items").innerHTML = html;

                // cập nhật số badge
                const count = data.count ?? 0;
                document.getElementById("order-noti-count").innerText = count > 0 ? count : '';
            })
            .catch(err => console.error("Lỗi load thông báo:", err));
    }

    function markNotificationAsRead(id, link) {
        fetch(`/notifications/read/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(() => {
            // Giảm badge ngay tại frontend
            let badge = document.getElementById("order-noti-count");
            let current = parseInt(badge.innerText) || 0;
            if (current > 0) {
                badge.innerText = (current - 1) > 0 ? (current - 1) : '';
            }

            // Sau đó chuyển hướng
            window.location.href = link;
        })
        .catch(err => console.error("Lỗi đọc thông báo:", err));
    }

    // load ngay khi vào trang
    loadOrderNotifications();

    // load lại mỗi 10 giây
    setInterval(loadOrderNotifications, 10000);
</script>

<!-- Sidebar Chat -->

<div id="chatSidebar" class="chat-sidebar">
    <div class="chat-header">
        <div class="header-info">
            <div class="status-indicator"></div>
            <span>Hỗ trợ trực tuyến</span>
        </div>
        <button id="closeChat">×</button>
    </div>
    <div class="chat-messages" id="chatMessages">
        <!-- Welcome message -->
        <div class="welcome-message">
            <img src="{{ asset('/admins/assets/img/avt_admin.png') }}" alt="Admin">
            <p>Xin chào! Tôi có thể giúp gì cho bạn?</p>
        </div>
    </div>
    <div class="chat-input">
        <input type="text" id="chatMessageInput" placeholder="Aa...">
        <button id="sendMessage">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<style>
    .chat-sidebar {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 380px;
        height: 550px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        z-index: 10000;
        transform: translateY(110%);
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        opacity: 0;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .chat-sidebar.open {
        transform: translateY(0);
        opacity: 1;
    }

    .chat-header {
        padding: 16px 20px;
        background: linear-gradient(135deg, #FFE5B4 0%, #F08080 100%);
        color: #ffffff;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
    }

    .header-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 600;
    }

    .status-indicator {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .chat-header button {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #ffffff;
        font-size: 24px;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .chat-header button:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: linear-gradient(to bottom, #f8f9fa, #ffffff);
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 transparent;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    .welcome-message {
        text-align: center;
        padding: 20px;
        opacity: 0.7;
    }

    .welcome-message img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .welcome-message p {
        color: #6b7280;
        font-size: 14px;
    }

    .message-container {
        display: flex;
        align-items: flex-end;
        margin: 12px 0;
        width: 100%;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-container.me {
        justify-content: flex-end;
    }

    .message-container.other {
        justify-content: flex-start;
    }

    .message {
        max-width: 75%;
        padding: 12px 16px;
        border-radius: 18px;
        line-height: 1.5;
        font-size: 14px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
        position: relative;
        word-wrap: break-word;
    }

    .message:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .me .message {
        background: linear-gradient(135deg, #FFE5B4 0%, #F08080 100%);
        color: #ffffff;
        border-bottom-right-radius: 6px;
    }

    .other .message {
        background: #f3f4f6;
        color: #1f2937;
        border-bottom-left-radius: 6px;
    }

    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 8px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .me .avatar {
        order: 1;
    }

    .other .avatar {
        order: -1;
    }

    .chat-input {
        display: flex;
        padding: 12px;
        border-top: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 0 0 20px 20px;
        gap: 10px;
    }

    .chat-input input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 25px;
        outline: none;
        font-size: 14px;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .chat-input input:focus {
        background: #ffffff;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .chat-input button {
        padding: 0 16px;
        border: none;
        background: linear-gradient(135deg, #FFE5B4 0%, #F08080 100%);
        color: #ffffff;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        border-radius: 50%;
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .chat-input button:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .chat-input button:active {
        transform: scale(0.95);
    }

    /* Responsive */
    @media (max-width: 480px) {
        .chat-sidebar {
            width: 100%;
            height: 100%;
            right: 0;
            bottom: 0;
            border-radius: 0;
        }

        .chat-header {
            border-radius: 0;
        }

        .chat-input {
            border-radius: 0;
        }
    }

    /* Animation for typing indicator */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
        background: #f3f4f6;
        border-radius: 18px;
        border-bottom-left-radius: 6px;
        width: fit-content;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #9ca3af;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
        }
        30% {
            transform: translateY(-10px);
        }
    }

    /* Time stamp */
    .message-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
        text-align: right;
    }

    .me .message-time {
        color: rgba(255, 255, 255, 0.7);
    }
</style>


<nav class="sidebar vertical-scroll  ps-container ps-theme-default ps-active-y">
    <div class="logo text-center py-1 position-relative">
        <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('admins/assets/img/image copy.png') }}" alt="Logo" style="max-width: 100px;">
        </a>
        <div class="sidebar_close_icon d-lg-none position-absolute" style="top: 10px; right: 10px;">
            <i class="ti-close"></i>
        </div>
    </div>


    <ul id="sidebar_menu">
        <li class="mm-active">
            <a class="has-arrow" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/dashboard.svg') }}" alt>
                </div>
                <span>Thống kê</span>
            </a>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Quản lí người dùng</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.users.index') }}">Danh sách người dùng</a></li>
                <li><a href="{{ route('admin.users.create') }}">Thêm mới người dùng</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/3.svg') }}" alt>
                </div>
                <span>Quản lí danh mục</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.categories.index') }}">Danh sách danh mục</a></li>
                <li><a href="">Thêm mới danh mục</a></li>
            </ul>
        </li>

        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/4.svg') }}" alt>
                </div>
                <span>Quản lí sản phẩm</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>
                <li><a href="Groups.html">Thêm</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/4.svg') }}" alt>
                </div>
                <span>Quản lí thuộc tính </span>
            </a>
            <ul>
                <li><a href="{{ route('admin.product_variants.index') }}">Danh sách thuộc tính</a></li>
                <li><a href="Groups.html">Thêm</a></li>
            </ul>
        </li>
        {{-- <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/4.svg') }}" alt>
                </div>
                <span>Quản lí Combo</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.combo_items.index') }}">Danh sách Combo</a></li>
                <li><a href="Groups.html">Thêm</a></li>
            </ul>
        </li> --}}
        <li class>
            <a href="#" class="has-arrow" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/5.svg') }}" alt>
                </div>
                <span>Quản lí mã giảm giá</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.promotions.index') }}">Danh sách mã giảm giá</a></li>
                <li><a href="{{ route('admin.promotions.create') }}">Thêm mã giảm giá</a></li>
            </ul>
        </li>
        <li class>
            <a href="#" class="has-arrow" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/6.svg') }}" alt>
                </div>
                <span>Quản lí đơn hàng</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.orders.index') }}">Danh sách đơn hàng</a></li>
                <li><a href="#">Thêm đơn hàng</a></li>
            </ul>
        </li>

        <li class>
            <a href="{{ route('admin.comments.index') }}" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('admins/assets/img/menu-icon/8.svg') }}" alt>
                </div>
                <span>Quản lí bình luận</span>
            </a>
        </li>
    </ul>
</nav>

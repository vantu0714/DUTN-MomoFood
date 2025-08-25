<!DOCTYPE html>
<html lang="zxx">

<!-- Mirrored from demo.dashboardpack.com/sales-html/ by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 24 May 2024 07:23:13 GMT -->

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin</title>


    <link rel="stylesheet" href="{{ asset('admins/assets/css/bootstrap1.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/themefy_icon/themify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/niceselect/css/nice-select.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/owl_carousel/css/owl.carousel.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/gijgo/gijgo.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/font_awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/tagsinput/tagsinput.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/datepicker/date-picker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/vectormap-home/vectormap-2.0.2.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/scroll/scrollable.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/datatable/css/jquery.dataTables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/datatable/css/responsive.dataTables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/datatable/css/buttons.dataTables.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/text_editor/summernote-bs4.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/morris/morris.css') }}">

    <link rel="stylesheet" href="{{ asset('admins/assets/vendors/material_icon/material-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('admins/assets/css/metisMenu.css') }}">

    <link rel="stylesheet" href="{{ asset('admins/assets/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/assets/css/colors/default.css') }}" id="colorSkinCSS">
    <link rel="stylesheet" href="{{ asset('admins/assets/css/products.css') }}">
   <link rel="stylesheet" href="{{ asset('admins/assets/css/variants.css') }}">


</head>

<body class="crm_body_bg">

    <section class="main_content dashboard_part large_header_bg">

        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="header_iner d-flex justify-content-between align-items-center">
                        <div class="sidebar_icon d-lg-none">
                            <i class="ti-menu"></i>
                        </div>
                        <div class="serach_field-area d-flex align-items-center">
                            <div class="search_inner">
                                <form action="#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Search here...">
                                    </div>
                                    <button type="submit"> <img
                                            src="{{ asset('admins/assets/img/icon/icon_search.svg') }}" alt> </button>
                                </form>
                            </div>
                            <span class="f_s_14 f_w_400 ml_25 white_text text_white">Apps</span>
                        </div>
                        <div class="header_right d-flex justify-content-between align-items-center">
                            <div class="header_notification_warp d-flex align-items-center">
                            </div>
                            <div class="profile_info">
                                <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('path/to/default-avatar.png') }}"
                                    alt="avatar" width="100" style="border-radius: 50%;">

                                <div class="profile_info_iner">
                                    <div class="profile_author_name">
                                        <p>Admin</p>
                                        <h5>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h5>

                                    </div>
                                    <div class="profile_info_details">
                                        <a href="{{ route('admin.info') }}">Thông tin cá nhân </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                        <a href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Đăng xuất
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

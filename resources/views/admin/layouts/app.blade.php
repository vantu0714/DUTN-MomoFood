@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@stack('page-css')

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0">

        @yield('content') {{-- nội dung dashboard sẽ hiển thị ở đây --}}

    </div>
</div>


@yield('scripts')
@include('admin.layouts.footer')
@stack('page-js')


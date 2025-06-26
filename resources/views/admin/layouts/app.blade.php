@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@stack('page-css')

<div class="main_content_iner " >
    <div class="container-fluid p-0" >

        @yield('content') {{-- nội dung dashboard sẽ hiển thị ở đây --}}

    </div>
</div>


@stack('scripts')
@include('admin.layouts.footer')
@stack('page-js')


@include('clients.layouts.header')
@include('clients.layouts.sidebar')


<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0">

        @yield('content') {{-- nội dung dashboard sẽ hiển thị ở đây --}}

    </div>
</div>


@include('clients.layouts.footer')

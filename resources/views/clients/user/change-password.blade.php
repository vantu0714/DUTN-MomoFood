@extends('clients.layouts.app')

@section('content')

    <body style="margin-top: 200px;">
        <div class="container-xl px-4 mt-4">
            <nav class="nav nav-borders">
                <a class="nav-link active ms-0" href="{{ route('clients.info') }}"target="__blank">Thông tin</a>
                <a class="nav-link" href="{{ route('clients.changepassword') }}" target="__blank">Đổi
                    mật khẩu</a>
                <a class="nav-link" href="{{ route('clients.orders') }}">Đơn hàng</a>
                <a href="#" class="nav-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </nav>
            <hr class="mt-0 mb-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">Đổi mật khẩu</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('clients.updatepassword') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="small mb-1" for="currentPassword">Mật khẩu hiện tại</label>
                                    <input class="form-control" id="currentPassword" type="password" name="currentPassword"
                                        placeholder="Nhập mật khẩu hiện tại">
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="newPassword">Mật khẩu mới</label>
                                    <input class="form-control" id="newPassword" type="password" name="newPassword"
                                        placeholder="Nhập mật khẩu mới">
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="confirmPassword">Xác nhận mật khẩu mới</label>
                                    <input class="form-control" id="confirmPassword" type="password" name="confirmPassword"
                                        placeholder="Xác nhận mật khẩu mới">
                                </div>
                                <button class="btn btn-primary" type="submit">Cập nhật</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript"></script>


        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('clients.info') }}";
                    }
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#d33'
                });
            </script>
        @endif
    </body>
@endsection

@extends('clients.layouts.app')

@section('content')

    <body style="margin-top: 200px;">
        <div class="container-xl px-4 mt-4" style="margin-top: 200px;">
            <nav class="nav nav-borders">
                <a class="nav-link active ms-0" href="{{ route('clients.info') }}">Thông tin</a>
                <a class="nav-link" href="{{ route('clients.changepassword') }}">Đổi
                    mật khẩu</a>
            </nav>
            <hr class="mt-0 mb-4">
            <form action="{{ route('clients.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Ảnh đại diện</div>
                            <div class="card-body text-center">
                                <img id="avatarPreview" class="img-account-profile rounded-circle mb-2"
                                    src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                    style="width: 150px; height: 150px; object-fit: cover;">

                                <div class="small font-italic text-muted mb-3">
                                    Ảnh JPG hoặc PNG không lớn hơn 5 MB
                                </div>

                                <input type="file" id="avatarInput" name="avatar" accept="image/*"
                                    style="display: none;" onchange="previewAvatar(event)">

                                <button class="btn btn-primary" type="button"
                                    onclick="document.getElementById('avatarInput').click();">
                                    Tải ảnh mới
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">Thông tin người dùng</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="small mb-1" for="inputUsername">Họ và tên</label>
                                    <input class="form-control" id="inputUsername" type="text" name="name"
                                        placeholder="Enter your username" value="{{ Auth::user()->name }}">
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1" for="inputLocation">Địa chỉ</label>
                                    <input class="form-control" id="inputLocation" type="text" name="address"
                                        placeholder="Enter your location" value="{{ Auth::user()->address }}">
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1" for="inputEmailAddress">Email</label>
                                    <input class="form-control" id="inputEmailAddress" type="email" name="email"
                                        placeholder="Enter your email address" value="{{ Auth::user()->email }}">
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1" for="inputPhone">Số điện thoại</label>
                                    <input class="form-control" id="inputPhone" type="tel" name="phone"
                                        placeholder="Enter your phone number" value="{{ Auth::user()->phone }}">
                                </div>

                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">Lưu</button>
                                    <a href="{{ route('clients.info') }}" class="btn btn-secondary">Quay lại</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script>
            document.getElementById('avatarInput').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        document.getElementById('avatarPreview').src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            });
        </script>
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript"></script>

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('clients.info') }}";
                        }
                    });
                });
            </script>
        @endif
    </body>
@endsection

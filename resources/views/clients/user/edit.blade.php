@extends('clients.layouts.app')

@section('content')

    <body style="margin-top: 200px;">
        <div class="container-xl px-4 mt-4" style="margin-top: 200px;">
            <!-- Account page navigation-->
            <nav class="nav nav-borders">
                <a class="nav-link active ms-0" href="{{ route('clients.info') }}"target="__blank">Thông tin</a>
                <a class="nav-link" href="#" target="__blank">Đổi
                    mật khẩu</a>
            </nav>
            <hr class="mt-0 mb-4">
            <form action="{{ route('clients.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <!-- Cột ảnh đại diện -->
                    <div class="col-xl-4">
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Ảnh đại diện</div>
                            <div class="card-body text-center">
                                <!-- Ảnh hiện tại hoặc xem trước -->
                                <img id="avatarPreview" class="img-account-profile rounded-circle mb-2"
                                    src="{{ Storage::url(Auth::user()->avatar) }}" alt="Ảnh đại diện"
                                    style="width: 150px; height: 150px; object-fit: cover;">

                                <!-- Mô tả -->
                                <div class="small font-italic text-muted mb-3">
                                    Ảnh JPG hoặc PNG không lớn hơn 5 MB
                                </div>

                                <!-- Input file ẩn -->
                                <input type="file" id="avatarInput" name="avatar" accept="image/*"
                                    style="display: none;" onchange="previewAvatar(event)">

                                <!-- Nút chọn ảnh -->
                                <button class="btn btn-primary" type="button"
                                    onclick="document.getElementById('avatarInput').click();">
                                    Tải ảnh mới
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cột thông tin người dùng -->
                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">Thông tin người dùng</div>
                            <div class="card-body">
                                <!-- Họ và tên -->
                                <div class="mb-3">
                                    <label class="small mb-1" for="inputUsername">Họ và tên</label>
                                    <input class="form-control" id="inputUsername" type="text" name="name"
                                        placeholder="Enter your username" value="{{ Auth::user()->name }}">
                                </div>

                                <!-- Địa chỉ -->
                                <div class="mb-3">
                                    <label class="small mb-1" for="inputLocation">Địa chỉ</label>
                                    <input class="form-control" id="inputLocation" type="text" name="address"
                                        placeholder="Enter your location" value="{{ Auth::user()->address }}">
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label class="small mb-1" for="inputEmailAddress">Email</label>
                                    <input class="form-control" id="inputEmailAddress" type="email" name="email"
                                        placeholder="Enter your email address" value="{{ Auth::user()->email }}">
                                </div>

                                <!-- Số điện thoại -->
                                <div class="mb-3">
                                    <label class="small mb-1" for="inputPhone">Số điện thoại</label>
                                    <input class="form-control" id="inputPhone" type="tel" name="phone"
                                        placeholder="Enter your phone number" value="{{ Auth::user()->phone }}">
                                </div>

                                <!-- Nút Lưu & Quay lại -->
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
        <script type="text/javascript"></script>
    </body>
@endsection

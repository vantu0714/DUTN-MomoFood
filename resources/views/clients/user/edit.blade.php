@extends('clients.layouts.app')

@section('content')
    <div class="container-xl px-4" style="margin-top: 200px">
        <nav class="nav nav-borders">
            <a class="nav-link" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link active ms-0" href="{{ route('clients.edit') }}">Chỉnh sửa thông tin</a>
            <a class="nav-link" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
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

        <form action="{{ route('clients.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row mt-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-person-circle me-2"></i>Ảnh đại diện
                            </h5>
                        </div>
                        <div class="card-body text-center px-4">
                            <div class="position-relative d-inline-block mb-3">
                                <img id="avatarPreview" class="rounded-circle border border-4 border-primary shadow-sm"
                                    src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" width="150"
                                    height="150" style="object-fit: cover;">
                                <button type="button" class="btn btn-primary btn-sm position-absolute rounded-circle"
                                    style="bottom: 10px; right: 10px; width: 36px; height: 36px;"
                                    onclick="document.getElementById('avatarInput').click()">
                                    <i class="bi bi-camera"></i>
                                </button>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*"
                                    style="display: none;" onchange="previewAvatar(event)">
                            </div>
                            <div class="small text-muted mb-3">
                                Ảnh JPG hoặc PNG không lớn hơn 5MB
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa thông tin
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="d-flex align-items-center text-primary mb-3">
                                    <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded">
                                            <label class="small text-muted mb-1 d-block">Họ và tên</label>
                                            <input type="text" class="form-control border-0 bg-light p-0 fw-semibold"
                                                name="name" value="{{ Auth::user()->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded">
                                            <label class="small text-muted mb-1 d-block">Email</label>
                                            <input type="email" class="form-control border-0 bg-light p-0 fw-semibold"
                                                name="email" value="{{ Auth::user()->email }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="d-flex align-items-center text-primary mb-3">
                                    <i class="bi bi-telephone me-2"></i>Thông tin liên hệ
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded">
                                            <label class="small text-muted mb-1 d-block">Số điện thoại</label>
                                            <input type="tel" class="form-control border-0 bg-light p-0 fw-semibold"
                                                name="phone" value="{{ Auth::user()->phone }}"
                                                placeholder="Nhập số điện thoại">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded">
                                            <label class="small text-muted mb-1 d-block">Địa chỉ</label>
                                            <input type="text" class="form-control border-0 bg-light p-0 fw-semibold"
                                                name="address" value="{{ Auth::user()->address }}"
                                                placeholder="Nhập địa chỉ">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('clients.info') }}" class="btn btn-outline-secondary px-4">
                                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@endsection

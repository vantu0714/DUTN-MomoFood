@extends('admin.layouts.app')
@push('page-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .user-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
            transition: all 0.3s ease;
        }

        .user-card:hover {
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.12);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #405189 0%, #586bab 100%);
            padding: 1.5rem;
            border: none;
        }

        .header-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 0;
        }

        .avatar-upload-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 1.5rem;
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .avatar-image:hover {
            transform: scale(1.02);
        }

        .avatar-upload-button {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #405189 0%, #586bab 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.2);
            border: 2px solid #fff;
            transition: all 0.2s ease;
        }

        .avatar-upload-button:hover {
            transform: scale(1.1);
        }

        .user-profile-card {
            border-radius: 0.75rem;
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .user-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #495057;
        }

        .user-role {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .status-badge i {
            margin-right: 0.35rem;
            font-size: 0.9rem;
        }

        .form-section {
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .form-floating>label {
            padding-left: 1.75rem;
        }

        .form-floating>.form-control,
        .form-floating>.form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.5;
            padding: 1rem 0.75rem 0.5rem 1.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #405189;
            box-shadow: 0 0 0 0.15rem rgba(98, 89, 202, 0.25);
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 0.75rem;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
        }

        .form-floating-with-icon label {
            padding-left: 2rem;
        }

        .form-switch-lg {
            padding-left: 2.75em;
            min-height: 2rem;
        }

        .form-switch-lg .form-check-input {
            height: 1.5rem;
            width: 3rem;
            margin-top: 0.25rem;
        }

        .custom-alert {
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            border-left-color: #0ab39c;
            background-color: rgba(10, 179, 156, 0.1);
        }

        .alert-danger {
            border-left-color: #f06548;
            background-color: rgba(240, 101, 72, 0.1);
        }

        .btn {
            padding: 0.6rem 1.25rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #405189 0%, #586bab 100%);
            border: none;
        }

        .btn-light {
            background: #f5f7fa;
            border-color: #e9ecef;
        }

        .btn-soft-danger {
            background-color: rgba(240, 101, 72, 0.1);
            color: #f06548;
            border: none;
        }

        .btn-soft-danger:hover {
            background-color: rgba(240, 101, 72, 0.2);
        }

        .modal-content {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
        }

        .modal-header.bg-soft-danger {
            background-color: rgba(240, 101, 72, 0.1);
            color: #f06548;
        }

        @media (max-width: 992px) {
            .user-profile-card {
                margin-bottom: 1.5rem;
            }
        }

        .tooltip {
            font-size: 0.85rem;
        }
    </style>
@endpush
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-xl-12">
                <div class="card user-card " style="animation-delay: 0.3s">
                    <div class="card-header card-header-gradient">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-white text-primary rounded-circle fs-18">
                                        <i class="ri-user-settings-line"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="header-title">Thông tin người dùng: <span
                                        class="fw-bold text-white">{{ $user->name }}</span></h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('users.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data" class="row g-4">
                            @csrf
                            @method('PUT')

                            <div class="col-lg-4 " style="animation-delay: 0.4s">
                                <div class="user-profile-card">
                                    <div class="avatar-upload-wrapper">
                                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" id="avatarDisplay"
                                            class="avatar-image rounded-circle">
                                        <div id="triggerAvatarUpload" class="avatar-upload-button" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Thay đổi avatar">
                                            <i class="ri-camera-line text-white fs-16"></i>
                                        </div>
                                    </div>

                                    <h5 class="user-name text-center">{{ $user->name }}</h5>
                                    <p class="user-role text-center">
                                        @switch($user->role->name)
                                            @case('admin')
                                                Quản trị viên
                                            @break

                                            @case('user')
                                                Người dùng
                                            @break
                                        @break

                                        @default
                                            Người dùng
                                    @endswitch
                                </p>

                                <div class="d-flex flex-column gap-3 w-100 mt-3">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-light w-100">
                                        <i class="ri-eye-line me-1"></i> Xem thông tin
                                    </a>
                                </div>

                                <div class="mt-4 pt-2 border-top w-100">
                                    <h6 class="fw-semibold mb-3">Thông tin tài khoản</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Ngày tạo:</span>
                                        <span>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Cập nhật:</span>
                                        <span>{{ \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="form-section p-4">
                                <h5 class="mb-4 border-bottom pb-3">Chỉnh sửa thông tin cá nhân</h5>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating position-relative">
                                            <i class="ri-user-3-line input-icon"></i>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                id="fullname" placeholder="Nhập họ và tên" value="{{ $user->name }}"
                                                style="padding-left: 2.5rem;">
                                            <label for="fullname" style="padding-left: 2.5rem;">Họ và tên</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating position-relative">
                                            <i class="ri-mail-line input-icon"></i>
                                            <input readonly type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                id="inputEmail4" placeholder="Nhập email" value="{{ $user->email }}"
                                                style="padding-left: 2.5rem;">
                                            <label for="inputEmail4" style="padding-left: 2.5rem;">Email</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                                                        <div class="col-md-6">
                                        <div class="form-floating position-relative">
                                            <i class="ri-mail-line input-icon"></i>
                                            <input  type="text"
                                                class="form-control @error('address') is-invalid @enderror" name="address"
                                                placeholder="Nhập địa chỉ" id="inputAddress" value="{{ $user->address }}"
                                                style="padding-left: 2.5rem;">
                                            <label for="inputAddress" style="padding-left: 2.5rem;">Địa chỉ</label>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                                                        <div class="col-md-6">
                                        <div class="form-floating position-relative">
                                            <i class="ri-mail-line input-icon"></i>
                                            <input  type="text"
                                                class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                id="inputphone4" placeholder="Nhập số điện thoại" value="{{ $user->phone }}"
                                                style="padding-left: 2.5rem;">
                                            <label for="inputphone4" style="padding-left: 2.5rem;">Số điện thoại</label>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating position-relative">
                                            <i class="ri-shield-user-line input-icon"></i>
                                            <select name="role_id"
                                                class="form-select @error('role') is-invalid @enderror" id="userRole"
                                                style="padding-left: 2.5rem;">
                                                <option value="">Chọn vai trò</option>
                                                @foreach ($roles as $role)
                                                    <option @selected($user->role->name == $role->name) value="{{ $role->id }}">
                                                        {{ Str::ucfirst($role->name == 'admin' ? 'Quản trị viên' : 'Người dùng') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="userRole" style="padding-left: 2.5rem;">Vai trò người
                                                dùng</label>
                                            @error('role_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Hidden file input -->
                                    <input type="file" name="avatar" id="imageInput" accept="image/*"
                                        class="d-none">
                                    @error('avatar')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    <div class="col-12">
                                        <div class="hstack gap-2 justify-content-end mt-3">
                                            <a class="btn btn-light"
                                                href="{{ route('users.index') }}">
                                                <i class="ri-arrow-left-line align-bottom me-1"></i> Quay lại
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-line align-bottom me-1"></i> Cập nhật thông tin
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('page-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imageInput');
            const avatarDisplay = document.getElementById('avatarDisplay');
            const triggerAvatarUpload = document.getElementById('triggerAvatarUpload');

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            triggerAvatarUpload.addEventListener('click', () => {
                imageInput.click();
            });

            imageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        avatarDisplay.src = reader.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

        });
    </script>
@endpush

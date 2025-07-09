@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4 fw-bold text-primary">Thêm người dùng mới</h4>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="card shadow-sm p-3">
                        <label for="imageInput" class="d-block mb-2 fw-bold">Ảnh đại diện</label>
                        <img id="imagePreview" class="rounded-circle border mx-auto mb-3"
                            src="{{ asset('admin/img/default-avatar.png') }}"
                            style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;">
                        <input type="file" id="imageInput" name="avatar" class="form-control" accept="image/*">
                        @error('avatar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <hr>
                        <p class="text-muted mb-1"><strong>Vai trò:</strong>
                            <span id="selectedRole">Chưa chọn</span>
                        </p>
                        <p class="text-muted"><strong>Trạng thái:</strong> Mặc định kích hoạt</p>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" name="name" class="form-control">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control">
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control">
                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control">
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Vai trò người dùng</label>
                            <select name="role_id" id="roleSelect" class="form-control">
                                <option value="">-- Chọn vai trò --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="1" selected>Kích hoạt</option>
                                <option value="0">Khóa</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page-js')
    <script>
        document.getElementById('imageInput').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = () => {
                    const preview = document.getElementById('imagePreview');
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });


        document.getElementById('roleSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const display = document.getElementById('selectedRole');
            display.textContent = selectedOption.value ? selectedOption.text : 'Chưa chọn';
        });
    </script>
@endpush

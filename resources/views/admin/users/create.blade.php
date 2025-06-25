@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm người dùng mới</h2>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" name="name" class="form-control">
                @error('name')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
                @error('email')
                    {{ $message }}
                @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Avatar</label>
                <input type="file" name="avatar" id="imageInput" accept="image/*" class="form-control">
                <img class="mt-2" id="imagePreview" style="display: none; max-width: 100%; max-height: 300px;">
                @error('avatar')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" name="phone" class="form-control">
                @error('phone')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control">
                @error('address')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control">
                @error('password')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Role ID</label>
                <select name="role_id" class="form-control">
                    <option value="">-- Chọn vai trò --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1" selected>Kích hoạt</option>
                    <option value="0">Khóa</option>
                </select>
                @error('status')
                    {{ $message }}
                @enderror
            </div>


            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Huỷ</a>
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
    </script>
@endpush

@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm người dùng mới</h2>

        <form action="{{ route('users.store') }}" method="POST">
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

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Huỷ</a>
        </form>
    </div>
@endsection

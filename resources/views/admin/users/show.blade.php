@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Chi tiết người dùng: {{ $user->name }}</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" readonly >
                @error('name')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                @error('email')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" readonly>
               @error('phone')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="{{ $user->address }}" readonly>
                @error('address')
                    {{ $message }}
                @enderror
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Role ID</label>
                <select name="role_id" class="form-control" readonly >
                    <option value="">-- Chọn vai trò --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->name }} 
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    {{ $message }}
                @enderror
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection

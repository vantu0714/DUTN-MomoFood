@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách bình luận</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Mật khẩu</th>
                    <th>Role ID</th>
                    <th>Ngày tạo</th>
                    <th>Cập nhật</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($Comments as $Comment)
                <tr>
                    <td>{{ $Comment->id }}</td>
                    <td>{{ $Comment->name }}</td>
                    <td>{{ $Comment->email }}</td>
                    <td>{{ $Comment->phone }}</td>
                    <td>{{ $Comment->address }}</td>
                    <td>{{ $Comment->password }}</td>
                    <td>{{ $Comment->role_id }}</td>
                    <td>{{ $Comment->created_at }}</td>
                    <td>{{ $Comment->updated_at }}</td>
                    <td>
                        <a href="{{ route('Comments.edit', $Comment->id) }}" class="btn btn-sm btn-warning">Sửa</a>

                        <a href="{{ route('Comments.show', $Comment->id) }}" class="btn btn-sm btn-info">Xem</a>

                        <form action="{{ route('Comments.destroy', $Comment->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Xoá người dùng này?')">Xoá</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Không có người dùng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

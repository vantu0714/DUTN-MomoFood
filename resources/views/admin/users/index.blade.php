@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh sách người dùng</h2>

        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Thêm người dùng</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th>Cập nhật</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->name }}</td>
                            <td> {{ $user->created_at->format('d-m-Y') }}</td>
                            <td> {{ $user->updated_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn  btn-warning">Sửa</a>

                                <a href="{{ route('users.show', $user->id) }}" class="btn  btn-info">Xem</a>

                                <form id="delete-form-{{ $user->id }}"
                                    action="{{ route('users.destroy', $user->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn  btn-danger"
                                        onclick="confirmDelete({{ $user->id }})">
                                        Xóa
                                    </button>
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

<script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Bạn chắc chắn muốn xóa?',
            text: "Hành động này không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + userId).submit();
            }
        });
    }
    
</script>


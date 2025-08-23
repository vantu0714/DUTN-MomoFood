@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh sách người dùng</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Thêm người dùng</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Ảnh đại diện</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
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
                            <td><img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar" width="100"></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->name }}</td>
                            <td>
                                @if ($user->status == 1)
                                    <span class="badge bg-success">Kích hoạt</span>
                                @else
                                    <span class="badge bg-secondary">Khóa</span>
                                @endif
                            </td>
                            <td> {{ $user->created_at->format('d-m-Y') }}</td>
                            <td> {{ $user->updated_at->format('d-m-Y') }}</td>
                            <td style="white-space: nowrap;">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning"
                                    title="Sửa" style="display:inline-block;">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info"
                                    title="Xem" style="display:inline-block;">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <form id="toggle-status-form-{{ $user->id }}"
                                    action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST"
                                    style="display:inline-block; margin:0;">
                                    @csrf
                                    @method('PATCH')

                                    @if ($user->status == 1)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" title="Khóa"
                                            onclick="confirmToggle({{ $user->id }}, false)">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Kích hoạt"
                                            onclick="confirmToggle({{ $user->id }}, true)">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    @endif
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

<script>
    function confirmToggle(userId, isActivating) {
        const actionText = isActivating ? 'kích hoạt' : 'khóa';
        const buttonText = isActivating ? 'Kích hoạt' : 'Khóa';
        const buttonColor = isActivating ? '#198754' : '#6c757d';

        Swal.fire({
            title: `Bạn có chắc chắn muốn ${actionText} tài khoản này?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: buttonColor,
            cancelButtonColor: '#d33',
            confirmButtonText: buttonText,
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('toggle-status-form-' + userId).submit();
            }
        });
    }
</script>

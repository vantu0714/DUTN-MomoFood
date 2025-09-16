@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh sách người dùng</h2>

        <a href="{{ route('admin.users.create') }}" class="btn-add-user">
    <i class="fas fa-plus-circle"></i> Thêm người dùng mới
</a>

        <div class="table-responsive">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="th-content">
                                    <span class="th-text">ID</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-user"></i>
                                    <span class="th-text">Họ tên</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-image"></i>
                                    <span class="th-text">Ảnh đại diện</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-envelope"></i>
                                    <span class="th-text">Email</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-user-tag"></i>
                                    <span class="th-text">Vai trò</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-toggle-on"></i>
                                    <span class="th-text">Trạng thái</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span class="th-text">Ngày tạo</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-calendar-check"></i>
                                    <span class="th-text">Cập nhật</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <i class="fas fa-cogs"></i>
                                    <span class="th-text">Hành động</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="table-row">
                                <td>
                                    <div class="td-content">
                                        <span class="id-badge">#{{ $user->id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <span class="user-name">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <div class="avatar-wrapper">
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar"
                                                class="user-avatar"
                                                onerror="this.src='{{ asset('admins/assets/img/default-avatar.webp') }}'">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <span class="email-text">{{ $user->email }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <span class="role-badge">{{ $user->role->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        @if ($user->status == 1)
                                            <span class="status-badge active">
                                                <i class="fas fa-check-circle"></i>
                                                Kích hoạt
                                            </span>
                                        @else
                                            <span class="status-badge inactive">
                                                <i class="fas fa-times-circle"></i>
                                                Khóa
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <span class="date-text">
                                            <i class="far fa-calendar"></i>
                                            {{ $user->created_at->format('d-m-Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <span class="date-text">
                                            <i class="far fa-calendar"></i>
                                            {{ $user->updated_at->format('d-m-Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-content">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn edit-btn"
                                                title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                class="action-btn view-btn" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <form id="toggle-status-form-{{ $user->id }}"
                                                action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST"
                                                style="display:inline-block; margin:0;">
                                                @csrf
                                                @method('PATCH')

                                                @if ($user->status == 1)
                                                    <button type="button" class="action-btn lock-btn" title="Khóa"
                                                        onclick="confirmToggle({{ $user->id }}, false)">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="action-btn unlock-btn" title="Kích hoạt"
                                                        onclick="confirmToggle({{ $user->id }}, true)">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>Không có người dùng nào</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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

<style>

    .btn-add-user {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 35, 112, 0.2);
    border: none;
    margin-bottom: 24px;
}

.btn-add-user:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 35, 112, 0.35);
    background: linear-gradient(135deg, #0056b3, #004085);
    color: #fff;
}

.btn-add-user i {
    font-size: 16px;
}
    .table-responsive {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
    }

    .custom-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .custom-table thead tr {
        border-bottom: 2px solid #e9ecef;
    }

    .custom-table thead th {
        padding: 18px 16px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border: none;
        white-space: nowrap;
        position: relative;
    }

    .th-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .th-content i {
        color: #6c757d;
        font-size: 14px;
    }

    .th-text {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .custom-table tbody tr {
        border-bottom: 1px solid #f1f3f5;
        transition: all 0.3s ease;
    }

    .custom-table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }

    .custom-table tbody td {
        padding: 16px;
        vertical-align: middle;
        color: #495057;
        border: none;
    }

    .td-content {
        display: flex;
        align-items: center;
    }

    .id-badge {
        background: #e7f3ff;
        color: #0066cc;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    .user-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 14px;
    }

    .avatar-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #f1f3f5;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .user-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .email-text {
        color: #6c757d;
        font-size: 13px;
    }

    .role-badge {
        background: #fff3cd;
        color: #856404;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge i {
        font-size: 10px;
    }

    .date-text {
        color: #6c757d;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .date-text i {
        font-size: 12px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e9ecef;
        background: white;
        color: #6c757d;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .edit-btn:hover {
        background: #ffc107;
        color: white;
        border-color: #ffc107;
    }

    .view-btn:hover {
        background: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }

    .lock-btn:hover {
        background: #6c757d;
        color: white;
        border-color: #6c757d;
    }

    .unlock-btn:hover {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 48px;
        color: #dee2e6;
        margin-bottom: 16px;
    }

    .empty-state p {
        color: #6c757d;
        font-size: 16px;
        margin: 0;
    }

    @media (max-width: 768px) {
        .custom-table {
            font-size: 12px;
        }

        .custom-table thead th,
        .custom-table tbody td {
            padding: 12px 8px;
        }

        .th-content i {
            display: none;
        }

        .action-btn {
            width: 28px;
            height: 28px;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-row {
        animation: fadeIn 0.3s ease;
    }
</style>

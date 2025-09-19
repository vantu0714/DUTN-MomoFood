@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Quản lý danh mục</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Thêm danh mục
        </a>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-content">
                                <span class="th-text">ID</span>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <i class="fas fa-folder"></i>
                                <span class="th-text">Tên danh mục</span>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <i class="fas fa-align-left"></i>
                                <span class="th-text">Mô tả</span>
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
                    @foreach ($categories as $cat)
                        <tr class="table-row">
                            <td>
                                <div class="td-content">
                                    <span class="id-badge">#{{ $cat->id }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="td-content">
                                    <span class="category-name">{{ $cat->category_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="td-content">
                                    <span class="description-text">{{ $cat->description }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="td-content">
                                    @if ($cat->status)
                                        <span class="status-badge active">
                                            <i class="fas fa-check-circle"></i>
                                            Hiển thị
                                        </span>
                                    @else
                                        <span class="status-badge inactive">
                                            <i class="fas fa-eye-slash"></i>
                                            Ẩn
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="td-content">
                                    <span class="date-text">
                                        <i class="far fa-calendar"></i>
                                        {{ $cat->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="td-content">
                                    <span class="date-text">
                                        <i class="far fa-calendar"></i>
                                        {{ $cat->updated_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="td-content">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.categories.show', $cat->id) }}" class="action-btn view-btn"
                                            title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="action-btn edit-btn"
                                            title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="action-btn delete-btn" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

    <style>
            .table-container {
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
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                border-bottom: 2px solid #e9ecef;
            }

            .custom-table thead th {
                padding: 18px 16px;
                text-align: left;
                font-weight: 600;
                color: #495057;
                border: none;
                white-space: nowrap;
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
                color: #495057;
            }

            .custom-table tbody {
                background: white;
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
                background: #f0f4ff;
                color: #4e73df;
                padding: 4px 10px;
                border-radius: 6px;
                font-weight: 600;
                font-size: 12px;
            }

            .category-name {
                font-weight: 600;
                color: #2c3e50;
                font-size: 14px;
            }

            .description-text {
                color: #6c757d;
                font-size: 13px;
                max-width: 300px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
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
                border: 1px solid #c3e6cb;
            }

            .status-badge.inactive {
                background: #f8f9fa;
                color: #6c757d;
                border: 1px solid #dee2e6;
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
                color: #adb5bd;
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

            .view-btn:hover {
                background: #17a2b8;
                color: white;
                border-color: #17a2b8;
            }

            .edit-btn:hover {
                background: #ffc107;
                color: white;
                border-color: #ffc107;
            }

            .delete-btn:hover {
                background: #dc3545;
                color: white;
                border-color: #dc3545;
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
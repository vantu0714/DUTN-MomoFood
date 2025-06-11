@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-box-open me-2 text-primary"></i>
                    Quản lý sản phẩm
                </h1>
                <p class="text-muted mb-0">Quản lý toàn bộ sản phẩm trong hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download me-1"></i>
                    Xuất Excel
                </button>
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm shadow">
                    <i class="fas fa-plus me-1"></i>
                    Thêm sản phẩm
                </a>
            </div>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-gradient rounded-circle p-3">
                                    <i class="fas fa-boxes text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Tổng sản phẩm</div>
                                <div class="h5 mb-0">{{ $products->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-gradient rounded-circle p-3">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Còn hàng</div>
                                <div class="h5 mb-0">{{ $products->where('status', 'Còn hàng')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-danger bg-gradient rounded-circle p-3">
                                    <i class="fas fa-times-circle text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">Hết hàng</div>
                                <div class="h5 mb-0">{{ $products->where('status', 'Hết hàng')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-gradient rounded-circle p-3">
                                    <i class="fas fa-eye text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Tổng lượt xem</div>
                                <div class="h5 mb-0">{{ $products->sum('view') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tìm kiếm sản phẩm</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Nhập tên sản phẩm..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Danh mục</label>
                            <select class="form-select" name="category_id">
                                <option value="" style="font-weight: bold;">Tất cả danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Còn hàng" {{ request('status') == 'Còn hàng' ? 'selected' : '' }}>Còn hàng
                                </option>
                                <option value="Hết hàng" {{ request('status') == 'Hết hàng' ? 'selected' : '' }}>Hết hàng
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-table me-2 text-primary"></i>
                        Danh sách sản phẩm
                    </h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-cog me-1"></i>
                            Cột hiển thị
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 fw-semibold text-dark">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th class="border-0 fw-semibold text-dark">ID</th>
                                <th class="border-0 fw-semibold text-dark">Ảnh</th>
                                <th class="border-0 fw-semibold text-dark">Tên sản phẩm</th>
                                <th class="border-0 fw-semibold text-dark">Danh mục</th>
                                <th class="border-0 fw-semibold text-dark">Số lượng</th>
                                <th class="border-0 fw-semibold text-dark">Giá gốc</th>
                                <th class="border-0 fw-semibold text-dark">Giá khuyến mãi</th>
                                <th class="border-0 fw-semibold text-dark">Trạng thái</th>
                                <th class="border-0 fw-semibold text-dark">Lượt xem</th>
                                <th class="border-0 fw-semibold text-dark">Ngày tạo</th>
                                <th class="border-0 fw-semibold text-dark text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                                <tr class="align-middle">
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox"
                                            value="{{ $item->id }}">
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">#{{ $item->id }}</span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $item->image) }}"
                                                class="rounded shadow-sm" width="60" height="60"
                                                style="object-fit: cover;" alt="Product Image">
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $item->product_name }}</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ Str::limit($item->description, 60) }}
                                            </p>
                                            @if ($item->expiration_date)
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    HSD:
                                                    {{ \Carbon\Carbon::parse($item->expiration_date)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="category-badge fw-bold text-primary border border-primary rounded px-3 py-1">
                                            {{ $item->category->category_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="quantity-badge fw-bold 
                                                @if($item->quantity <= 10) text-danger border-danger
                                                @elseif($item->quantity <= 50) text-warning border-warning
                                                @else text-success border-success
                                                @endif border rounded px-3 py-1">
                                                <i class="fas fa-cubes me-1"></i>
                                                {{ number_format($item->quantity) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ number_format($item->original_price) }}đ</span>
                                    </td>
                                    <td>
                                        @if ($item->discounted_price && $item->discounted_price < $item->original_price)
                                            <span
                                                class="fw-semibold text-danger">{{ number_format($item->discounted_price) }}đ</span>
                                            <div class="small text-muted">
                                                <del>{{ number_format($item->original_price) }}đ</del>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'Còn hàng')
                                            <span
                                                class="status-badge status-available fw-bold text-success border border-success rounded px-3 py-1">
                                                <i class="fas fa-check me-1"></i>Còn hàng
                                            </span>
                                        @else
                                            <span
                                                class="status-badge status-out-of-stock fw-bold text-danger border border-danger rounded px-3 py-1">
                                                <i class="fas fa-times me-1"></i>Hết hàng
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-eye text-muted me-2"></i>
                                            <span class="fw-semibold">{{ number_format($item->view) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $item->created_at->format('d/m/Y') }}</span>
                                        <div class="small text-muted">{{ $item->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('products.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $item->id) }}"
                                                class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-product-id="{{ $item->id }}"
                                                data-product-name="{{ $item->product_name }}" data-bs-toggle="tooltip"
                                                title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Hiển thị {{ $products->count() }} sản phẩm
                    </div>
                    <nav>
                        {{-- {{ $products->links() }} --}}
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Delete Modal -->
    <!-- Modal xác nhận xoá dùng chung -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i> Xác nhận xóa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Bạn có chắc chắn muốn xóa sản phẩm <strong id="productName"></strong>?
                    </p>
                    <p class="text-muted small mb-0">Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-1"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .table tbody tr:hover {
            background-color: rgba(123, 180, 241, 0.05);
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .badge {
            font-weight: 500;
        }

        /* Cải thiện hiển thị danh mục */
        .category-badge {
            font-size: 0.875rem;
            font-weight: 700 !important;
            color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.1);
            border: 1.5px solid #0d6efd !important;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            white-space: nowrap;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        /* Cải thiện hiển thị số lượng */
        .quantity-badge {
            font-size: 0.875rem;
            font-weight: 700 !important;
            letter-spacing: 0.3px;
            white-space: nowrap;
            display: inline-block;
            min-width: 70px;
            text-align: center;
            border-width: 1.5px !important;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .quantity-badge.text-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .quantity-badge.text-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .quantity-badge.text-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        /* Cải thiện hiển thị trạng thái */
        .status-badge {
            font-size: 0.875rem;
            font-weight: 700 !important;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            white-space: nowrap;
            display: inline-block;
            min-width: 90px;
            text-align: center;
            border-width: 1.5px !important;
        }

        .status-available {
            color: #198754 !important;
            background-color: rgba(25, 135, 84, 0.1);
            border-color: #198754 !important;
        }

        .status-out-of-stock {
            color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545 !important;
        }

        /* Hover effects cho badges */
        .category-badge:hover,
        .quantity-badge:hover {
            transform: scale(1.02);
            transition: all 0.2s ease;
        }

        .category-badge:hover {
            background-color: rgba(13, 110, 253, 0.2);
        }

        .quantity-badge.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        .quantity-badge.text-warning:hover {
            background-color: rgba(255, 193, 7, 0.2);
        }

        .quantity-badge.text-success:hover {
            background-color: rgba(25, 135, 84, 0.2);
        }

        .status-badge:hover {
            transform: scale(1.02);
            transition: all 0.2s ease;
        }

        .status-available:hover {
            background-color: rgba(25, 135, 84, 0.2);
        }

        .status-out-of-stock:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .category-badge,
            .quantity-badge,
            .status-badge {
                font-size: 0.75rem;
                min-width: 60px;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Delete functionality
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const productNameSpan = document.getElementById('productName');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');

                    // Set product name in modal
                    productNameSpan.textContent = `"${productName}"`;

                    // Set form action
                    deleteForm.action = `{{ route('products.destroy', '') }}/${productId}`;

                    // Show modal
                    const modal = new bootstrap.Modal(deleteModal);
                    modal.show();
                });
            });
        });
    </script>
@endsection
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
            <a href="" class="btn btn-primary btn-sm shadow">
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
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tìm kiếm sản phẩm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Nhập tên sản phẩm...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Danh mục</label>
                    <select class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <!-- Add category options here -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Còn hàng">Còn hàng</option>
                        <option value="Hết hàng">Hết hàng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>
                            Lọc
                        </button>
                    </div>
                </div>
            </div>
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
                                <input type="checkbox" class="form-check-input">
                            </th>
                            <th class="border-0 fw-semibold text-dark">ID</th>
                            <th class="border-0 fw-semibold text-dark">Ảnh</th>
                            <th class="border-0 fw-semibold text-dark">Tên sản phẩm</th>
                            <th class="border-0 fw-semibold text-dark">Danh mục</th>
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
                                    <input type="checkbox" class="form-check-input" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $item->id }}</span>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <img src="{{ asset('storage') . '/' . $item->image }}" 
                                             class="rounded shadow-sm" 
                                             width="60" height="60" 
                                             style="object-fit: cover;" 
                                             alt="Product Image">
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $item->product_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            {{ Str::limit($item->description, 60) }}
                                        </p>
                                        @if($item->expiration_date)
                                            <small class="text-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                HSD: {{ \Carbon\Carbon::parse($item->expiration_date)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $item->category->category_name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ number_format($item->original_price) }}đ</span>
                                </td>
                                <td>
                                    @if($item->discounted_price && $item->discounted_price < $item->original_price)
                                        <span class="fw-semibold text-danger">{{ number_format($item->discounted_price) }}đ</span>
                                        <div class="small text-muted">
                                            <del>{{ number_format($item->original_price) }}đ</del>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'Còn hàng')
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-check me-1"></i>Còn hàng
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
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
                                        <a href="" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $item->id }}"
                                                data-bs-toggle="tooltip" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title text-danger">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Xác nhận xóa
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-0">Bạn có chắc chắn muốn xóa sản phẩm <strong>"{{ $item->product_name }}"</strong>?</p>
                                                    <p class="text-muted small mb-0">Hành động này không thể hoàn tác.</p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                                                    <form action="" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash me-1"></i>
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
                    <!-- Pagination links here -->
                    {{-- {{ $products->links() }} --}}
                </nav>
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
    background-color: rgba(0, 123, 255, 0.05);
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
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Select all checkbox functionality
    const selectAllCheckbox = document.querySelector('thead input[type="checkbox"]');
    const itemCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    
    selectAllCheckbox?.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});
</script>
@endsection
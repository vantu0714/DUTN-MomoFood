@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fas fa-layer-group text-primary me-2"></i>
                        Quản lý biến thể sản phẩm
                    </h2>
                    <p class="text-muted mb-0">Quản lý tất cả biến thể sản phẩm trong hệ thống</p>
                </div>
                <a href="{{ route('admin.product_variants.create') }}" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i>
                    Thêm biến thể mới
                </a>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-cubes text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Tổng biến thể</div>
                            <div class="fw-bold fs-5">{{ $variants->count() }}</div>
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
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-eye text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Đang hiển thị</div>
                            <div class="fw-bold fs-5">{{ $variants->where('status', 1)->count() }}</div>
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
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-eye-slash text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Đang ẩn</div>
                            <div class="fw-bold fs-5">{{ $variants->where('status', 0)->count() }}</div>
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
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-boxes text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Tổng tồn kho</div>
                            <div class="fw-bold fs-5">{{ $variants->sum('quantity_in_stock') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Danh sách biến thể
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Tìm kiếm biến thể..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="variantsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark">
                                <i class="fas fa-hashtag me-2 text-muted"></i>ID
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark">
                                <i class="fas fa-box me-2 text-muted"></i>Sản phẩm
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark">
                                <i class="fas fa-tag me-2 text-muted"></i>Tên biến thể
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark">
                                <i class="fas fa-dollar-sign me-2 text-muted"></i>Giá
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark">
                                <i class="fas fa-warehouse me-2 text-muted"></i>Tồn kho
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark">
                                <i class="fas fa-toggle-on me-2 text-muted"></i>Trạng thái
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-dark text-center">
                                <i class="fas fa-cog me-2 text-muted"></i>Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($variants as $variant)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark">{{ $variant->id }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-cube text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $variant->product->name }}</div>
                                            <small class="text-muted">Mã: #{{ $variant->product->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-medium text-dark">{{ $variant->name }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-bold text-success">{{ number_format($variant->price) }}đ</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($variant->quantity_in_stock > 10)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ $variant->quantity_in_stock }}
                                        </span>
                                    @elseif($variant->quantity_in_stock > 0)
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $variant->quantity_in_stock }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Hết hàng
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($variant->status)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="fas fa-eye me-1"></i>
                                            Hiển thị
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                            <i class="fas fa-eye-slash me-1"></i>
                                            Ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.product_variants.edit', $variant) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           data-bs-toggle="tooltip" 
                                           title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $variant->id }}"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $variant->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Xác nhận xóa
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-3">Bạn có chắc chắn muốn xóa biến thể này không?</p>
                                                    <div class="bg-light rounded p-3">
                                                        <strong>{{ $variant->name }}</strong><br>
                                                        <small class="text-muted">Thuộc sản phẩm: {{ $variant->product->name }}</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i>Hủy
                                                    </button>
                                                    <form action="{{ route('admin.product_variants.destroy', $variant) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block text-muted opacity-50"></i>
                                        <h5 class="text-muted">Chưa có biến thể nào</h5>
                                        <p class="mb-3">Hãy thêm biến thể đầu tiên cho sản phẩm của bạn</p>
                                        <a href="{{ route('admin.product_variants.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm biến thể mới
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
    }
    
    .btn-group .btn {
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        transform: scale(1.05);
    }
    
    .badge {
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    .text-success { color: #198754 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-secondary { color: #6c757d !important; }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('variantsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const text = row.textContent || row.innerText;
            
            if (text.toLowerCase().indexOf(filter) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>
@endpush
@endsection
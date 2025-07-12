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
              
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm shadow">
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
                                <div class="h5 mb-0">{{ $totalProducts }}</div>
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
                                <div class="h5 mb-0">{{ $availableProductsCount }}</div>
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
                                <div class="h5 mb-0">{{ $outOfStockProductsCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="col-xl-3 col-md-6 mb-3">
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
            </div> --}}
        </div>
        <!-- Filter & Search Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.products.index') }}">
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
                                <option value="" style="font-weight: bold;"
                                    {{ request('category_id') == '' ? 'selected' : '' }}>
                                    Tất cả danh mục
                                </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
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
                                {{-- <th class="border-0 fw-semibold text-dark">Lượt xem</th> --}}
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
                                            <img src="{{ asset('storage/' . $item->image) }}" class="rounded shadow-sm"
                                                width="60" height="60" style="object-fit: cover;"
                                                alt="Product Image">
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
                                            <span
                                                class="quantity-badge fw-bold 
                                                @if ($item->quantity_in_stock <= 10) text-danger border-danger
                                                @elseif($item->quantity_in_stock <= 50) text-warning border-warning
                                                @else text-success border-success @endif border rounded px-3 py-1">
                                                <i class="fas fa-cubes me-1"></i>
                                                {{ number_format($item->quantity_in_stock) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->product_type === 'variant' && $item->min_price && $item->max_price)
                                            @if ($item->min_price != $item->max_price)
                                                <span class="fw-semibold">{{ number_format($item->min_price) }}đ -
                                                    {{ number_format($item->max_price) }}đ</span>
                                            @else
                                                <span class="fw-semibold">{{ number_format($item->min_price) }}đ</span>
                                            @endif
                                        @else
                                            <span class="fw-semibold">{{ number_format($item->original_price) }}đ</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($item->discounted_price !== null && $item->discounted_price > 0 && $item->discounted_price < $item->original_price)
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
                                        @if ($item->quantity_in_stock > 0)
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

                                  {{--   <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-eye text-muted me-2"></i>
                                            <span class="fw-semibold">{{ number_format($item->view) }}</span>
                                        </div>
                                    </td> --}}
                                    <td>
                                        <span class="text-muted">{{ $item->created_at->format('d/m/Y') }}</span>
                                        <div class="small text-muted">{{ $item->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Nút xem -->
                                            <a href="{{ route('admin.products.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- Nút sửa -->
                                            <a href="{{ route('admin.products.edit', $item->id) }}"
                                                class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Nút mở modal -->
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteProductModal"
                                                data-product-id="{{ $item->id }}"
                                                data-product-name="{{ $item->product_name }}"
                                                data-has-variants="{{ $item->variants->count() > 0 ? 'true' : 'false' }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Khởi tạo tooltip
                                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                        return new bootstrap.Tooltip(tooltipTriggerEl);
                                    });

                                    console.log('Page loaded, tooltips initialized');

                                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                    if (csrfToken) {
                                        console.log('CSRF Token found:', csrfToken.getAttribute('content'));
                                    } else {
                                        console.error('CSRF Token not found!');
                                    }
                                });
                                const deleteProductModal = document.getElementById('deleteProductModal');
                                deleteProductModal.addEventListener('show.bs.modal', function(event) {
                                    const button = event.relatedTarget;
                                    const productId = button.getAttribute('data-product-id');
                                    const productName = button.getAttribute('data-product-name');
                                    const hasVariants = button.getAttribute('data-has-variants') === 'true';

                                    // Cập nhật form action
                                    const form = document.getElementById('deleteProductForm');
                                    form.action = `/admin/products/${productId}`;

                                    // Cập nhật nội dung cảnh báo
                                    const message = hasVariants ?
                                        `Sản phẩm <strong>${productName}</strong> có biến thể. Bạn muốn xóa toàn bộ hay chỉ xóa các biến thể?` :
                                        `Bạn có chắc chắn muốn xóa sản phẩm <strong>${productName}</strong>?`;

                                    document.getElementById('deleteProductMessage').innerHTML = message;

                                    // Ẩn nút "Chỉ xóa biến thể" nếu sản phẩm không có biến thể
                                    document.getElementById('variantOptions').querySelector('button.btn-warning').style.display =
                                        hasVariants ? 'inline-block' : 'none';
                                });
                                // Optional: test ajax delete
                                function testAjaxDelete(productId) {
                                    if (!confirm('Test AJAX delete?')) return;

                                    fetch(`/admin/products/${productId}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json'
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            console.log('Response:', data);
                                            if (data.success) {
                                                alert('Xóa thành công!');
                                                location.reload();
                                            } else {
                                                alert('Lỗi: ' + data.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Có lỗi xảy ra!');
                                        });
                                }
                            </script>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Hiển thị {{ $products->count() }} sản phẩm
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="deleteProductForm">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteProductMessage"></p>
                    <input type="hidden" name="action_type" id="deleteActionType" value="full">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <div id="variantOptions" class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"
                            onclick="document.getElementById('deleteActionType').value='variants'">Chỉ xóa biến
                            thể</button>
                        <button type="submit" class="btn btn-danger"
                            onclick="document.getElementById('deleteActionType').value='full'">Xóa toàn bộ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

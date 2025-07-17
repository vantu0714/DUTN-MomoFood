@extends('admin.layouts.app')

@section('content')
    <div class="product-variants-container">
        <div class="main-content fade-in">

            {{-- Enhanced Header Section --}}
            <div class="page-header-enhanced mb-5">
                <div class="header-content">
                    <div class="header-info">
                        <div class="title-section">
                            <div class="icon-wrapper">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <div>
                                <h1 class="page-title-enhanced">Quản lý sản phẩm biến thể</h1>
                                <p class="page-subtitle-enhanced">Danh sách tất cả các biến thể sản phẩm trong hệ thống</p>
                            </div>
                        </div>
                    </div>

                    {{-- Enhanced Search and Filter Section --}}
                    <div class="search-filter-section">
                        <form action="{{ route('admin.product_variants.index') }}" method="GET" class="search-form">
                            <div class="search-group">
                                <div class="search-input-wrapper">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" name="search" class="search-input" 
                                           placeholder="Tìm theo tên, mã sản phẩm hoặc SKU..." 
                                           value="{{ request('search') }}">
                                </div>
                                
                                <div class="filter-wrapper">
                                    <select name="stock_filter" class="filter-select">
                                        <option value="">Tất cả tồn kho</option>
                                        <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>
                                            <i class="fas fa-exclamation-triangle"></i> Tồn kho thấp (≤ 5)
                                        </option>
                                        <option value="medium" {{ request('stock_filter') == 'medium' ? 'selected' : '' }}>
                                            <i class="fas fa-minus-circle"></i> Trung bình (6 - 20)
                                        </option>
                                        <option value="high" {{ request('stock_filter') == 'high' ? 'selected' : '' }}>
                                            <i class="fas fa-check-circle"></i> Tồn kho cao (> 20)
                                        </option>
                                    </select>
                                </div>

                                <button class="search-btn" type="submit">
                                    <i class="fas fa-search"></i>
                                    <span>Tìm kiếm</span>
                                </button>

                                @if (request('search') || request('stock_filter'))
                                    <a href="{{ route('admin.product_variants.index') }}" class="clear-btn">
                                        <i class="fas fa-times"></i>
                                        <span>Xóa bộ lọc</span>
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="action-buttons">
                            <a href="{{ route('admin.product_variants.createMultiple') }}" class="add-btn">
                                <i class="fas fa-layer-group"></i>
                                <span>Thêm biến thể sản phẩm có sẳn </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enhanced Product Variants Display --}}
            @if ($groupedVariants && $groupedVariants->count() > 0)
                <div class="variants-grid">
                    @foreach ($groupedVariants as $productId => $variants)
                        @php $product = $variants->first()->product; @endphp

                        <div class="product-card">
                            <div class="product-header">
                                <div class="product-info">
                                    <div class="product-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-details">
                                        <h3 class="product-name">{{ $product->product_name }}</h3>
                                        <div class="product-meta">
                                            <span class="product-code">Mã SP: {{ $product->product_code }}</span>
                                            <span class="variant-count">{{ $variants->count() }} biến thể</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="expand-icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <div class="variants-table-container">
                                <div class="table-wrapper">
                                    <table class="variants-table">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-barcode"></i> SKU</th>
                                                <th><i class="fas fa-tag"></i> Giá</th>
                                                <th><i class="fas fa-warehouse"></i> Tồn kho</th>
                                                <th><i class="fas fa-image"></i> Ảnh</th>
                                                {{-- <th><i class="fas fa-cogs"></i> Hành động</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($variants as $variant)
                                                <tr class="variant-row">
                                                    <td class="sku-cell">
                                                        <code class="sku-code">{{ $variant->sku }}</code>
                                                    </td>
                                                    <td class="price-cell">
                                                        <span class="price">{{ number_format($variant->price, 0, ',', '.') }}₫</span>
                                                    </td>
                                                    <td class="stock-cell">
                                                        @php
                                                            $qty = $variant->quantity_in_stock;
                                                            $stockClass = $qty <= 5 ? 'stock-low' : ($qty <= 20 ? 'stock-medium' : 'stock-high');
                                                            $stockIcon = $qty <= 5 ? 'fas fa-exclamation-triangle' : ($qty <= 20 ? 'fas fa-minus-circle' : 'fas fa-check-circle');
                                                        @endphp
                                                        <div class="stock-badge {{ $stockClass }}">
                                                            <i class="{{ $stockIcon }}"></i>
                                                            <span>{{ $qty }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="image-cell">
                                                        <div class="image-wrapper">
                                                            @if ($variant->image && Storage::disk('public')->exists($variant->image))
                                                                <img src="{{ asset('storage/' . $variant->image) }}" 
                                                                     alt="Variant Image" class="variant-image">
                                                                <div class="image-overlay">
                                                                    <i class="fas fa-eye"></i>
                                                                </div>
                                                            @else
                                                                <div class="no-image">
                                                                    <i class="fas fa-image"></i>
                                                                    <small>Không có ảnh</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    {{-- <td class="actions-cell">
                                                        <div class="action-buttons-group">
                                                            <a href="{{ route('admin.product_variants.edit', $variant->id) }}" 
                                                               class="action-btn edit-btn" title="Chỉnh sửa">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('admin.product_variants.destroy', $variant->id) }}" 
                                                                  method="POST" class="delete-form">
                                                                @csrf @method('DELETE')
                                                                <button class="action-btn delete-btn" type="button" 
                                                                        onclick="confirmDelete(this)" title="Xóa">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <h3>Chưa có sản phẩm biến thể</h3>
                    <p>Hãy thêm biến thể đầu tiên cho sản phẩm của bạn</p>
                    <a href="{{ route('admin.product_variants.createMultiple') }}" class="empty-action-btn">
                        <i class="fas fa-plus"></i>
                        Thêm biến thể ngay
                    </a>
                </div>
            @endif

            {{-- Enhanced Pagination --}}
            @if ($variantsPaginated->hasPages())
                <div class="pagination-wrapper">
                    {{ $variantsPaginated->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('admins/assets/js/product-variants.js') }}"></script>
@endpush

                
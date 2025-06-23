@extends('admin.layouts.app')

@section('content')
    <style>
        .product-variants-container {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #20c997, #17a2b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .product-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #20c997, #17a2b8, #6f42c1);
        }

        .product-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
        }

        .product-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-code {
            background: linear-gradient(135deg, #20c997, #17a2b8);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(32, 201, 151, 0.3);
        }

        .variants-table {
            margin: 0;
            border: none;
        }

        .variants-table thead th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .variants-table tbody tr {
            border: none;
            transition: all 0.3s ease;
        }

        .variants-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            transform: scale(1.01);
        }

        .variants-table td {
            padding: 18px 15px;
            border: none;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .variant-badge {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 2px;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .price-display {
            font-weight: 600;
            color: #28a745;
            font-size: 1.1rem;
        }

        .stock-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .stock-high {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .stock-medium {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }

        .stock-low {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
            color: white;
        }

        .product-image {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.1);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-modern {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
            color: white;
        }

        .pagination-wrapper {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin: 10px;
                padding: 20px;
            }

            .page-title {
                font-size: 2rem;
            }

            .product-header {
                flex-direction: column;
                gap: 10px;
            }

            .variants-table {
                font-size: 0.9rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
    <div class="product-variants-container">
        <div class="main-content fade-in">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-cubes"></i>
                    Quản lý sản phẩm biến thể
                </h1>
                <p class="page-subtitle">Danh sách tất cả các biến thể sản phẩm trong hệ thống</p>
            </div>

            @if ($groupedVariants && $groupedVariants->count() > 0)
                @foreach ($groupedVariants as $productId => $variants)
                    @php
                        $product = $variants->first()->product;
                    @endphp

                    <div class="product-card fade-in">
                        <div class="product-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="product-name">
                                    <i class="fas fa-box"></i>
                                    {{ $product->product_name }}
                                </h3>
                                <span class="product-code">
                                    <i class="fas fa-barcode"></i>
                                    {{ $product->product_code }}
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table variants-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-tags"></i> Biến thể</th>
                                        <th><i class="fas fa-qrcode"></i> SKU</th>
                                        <th><i class="fas fa-dollar-sign"></i> Giá</th>
                                        <th><i class="fas fa-warehouse"></i> Kho</th>
                                        <th><i class="fas fa-image"></i> Ảnh</th>
                                        <th><i class="fas fa-cogs"></i> Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variants as $variant)
                                        <tr>
                                            <td>
                                                @if ($variant->attributeValues->count())
                                                    @foreach ($variant->attributeValues as $val)
                                                        <span class="variant-badge">
                                                            {{ $val->attribute->name }}: {{ $val->value }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <em class="text-muted">
                                                        <i class="fas fa-minus-circle"></i>
                                                        Không có biến thể
                                                    </em>
                                                @endif
                                            </td>
                                            <td>
                                                <code class="bg-light p-2 rounded text-dark">
                                                    {{ $variant->sku ?? 'N/A' }}
                                                </code>
                                            </td>
                                            <td>
                                                <span class="price-display">
                                                    {{ number_format($variant->price, 0, ',', '.') }}₫
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $stockClass = 'stock-high';
                                                    if ($variant->quantity_in_stock <= 5) {
                                                        $stockClass = 'stock-low';
                                                    } elseif ($variant->quantity_in_stock <= 20) {
                                                        $stockClass = 'stock-medium';
                                                    }
                                                @endphp
                                                <span class="stock-badge {{ $stockClass }}">
                                                    {{ $variant->quantity_in_stock }}
                                                    <i class="fas fa-boxes"></i>
                                                </span>
                                            </td>
                                            <td>
                                                @if ($variant->image)
                                                    <img src="{{ asset('storage/' . $variant->image) }}" alt="Ảnh sản phẩm"
                                                        class="product-image" width="60" height="60"
                                                        style="object-fit: cover;">
                                                @else
                                                    <div class="text-center text-muted">
                                                        <i class="fas fa-image fa-2x opacity-50"></i>
                                                        <br>
                                                        <small>Chưa có ảnh</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.product_variants.edit', $variant->id) }}"
                                                        class="btn-modern btn-edit" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                        Sửa
                                                    </a>
                                                    <form
                                                        action="{{ route('admin.product_variants.destroy', $variant->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('⚠️ Bạn có chắc chắn muốn xóa biến thể này không?\n\nHành động này không thể hoàn tác!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-modern btn-delete" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-data">
                    <i class="fas fa-box-open"></i>
                    <h4>Chưa có sản phẩm biến thể nào</h4>
                    <p>Hãy thêm sản phẩm biến thể đầu tiên để bắt đầu!</p>
                </div>
            @endif

            @if (isset($variantsPaginated) && $variantsPaginated->hasPages())
                <div class="pagination-wrapper">
                    <div class="d-flex justify-content-center">
                        {{ $variantsPaginated->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation for images
            const images = document.querySelectorAll('.product-image');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease';
            });

            // Add hover effects for table rows
            const tableRows = document.querySelectorAll('.variants-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Enhance delete confirmation
            const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Create custom modal or use SweetAlert if available
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Xác nhận xóa',
                            text: 'Bạn có chắc chắn muốn xóa biến thể này không?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Xóa',
                            cancelButtonText: 'Hủy'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm(
                                '⚠️ Bạn có chắc chắn muốn xóa biến thể này không?\n\nHành động này không thể hoàn tác!'
                                )) {
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection

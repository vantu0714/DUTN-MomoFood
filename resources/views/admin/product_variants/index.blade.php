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

            {{-- header + tìm kiếm + nút --}}
            <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-cubes"></i>
                        Quản lý sản phẩm biến thể
                    </h1>
                    <p class="page-subtitle">Danh sách tất cả các biến thể sản phẩm trong hệ thống</p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <form action="{{ route('admin.product_variants.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm theo tên, mã sản phẩm hoặc SKU..." value="{{ request('search') }}">
                        <button class="btn btn-primary ms-2" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('admin.product_variants.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                    <a href="{{ route('admin.product_variants.create') }}" class="btn btn-success btn-modern">
                        <i class="fas fa-plus-circle"></i> Thêm biến thể
                    </a>
                </div>
            </div>

            {{-- danh sách grouped --}}
            @if ($groupedVariants && $groupedVariants->count() > 0)
                @foreach ($groupedVariants as $productId => $variants)
                    @php $product = $variants->first()->product; @endphp

                    <div class="product-card fade-in">
                        <div class="product-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="product-name"><i class="fas fa-box"></i> {{ $product->product_name }}</h3>
                                <span class="product-code"><i class="fas fa-barcode"></i>
                                    {{ $product->product_code }}</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table variants-table">
                                <thead>
                                    <tr>
                                        <th>Biến thể</th>
                                        <th>SKU</th>
                                        <th>Giá</th>
                                        <th>Kho</th>
                                        <th>Ảnh</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variants as $variant)
                                        <tr>
                                            <td>
                                                @if ($variant->attributeValues->count())
                                                    @foreach ($variant->attributeValues as $val)
                                                        <span class="variant-badge">{{ $val->attribute->name }}:
                                                            {{ $val->value }}</span>
                                                    @endforeach
                                                @else
                                                    <em class="text-muted">Không có biến thể</em>
                                                @endif
                                            </td>
                                            <td><code class="bg-light p-2 rounded">{{ $variant->sku }}</code></td>
                                            <td><span
                                                    class="price-display">{{ number_format($variant->price, 0, ',', '.') }}₫</span>
                                            </td>
                                            <td>
                                                @php
                                                    $qty = $variant->quantity_in_stock;
                                                    $cls =
                                                        $qty <= 5
                                                            ? 'stock-low'
                                                            : ($qty <= 20
                                                                ? 'stock-medium'
                                                                : 'stock-high');
                                                @endphp
                                                <span class="stock-badge {{ $cls }}">{{ $qty }}</span>
                                            </td>
                                            <td>
                                                @if ($variant->image)
                                                    <img src="{{ asset('storage/' . $variant->image) }}" alt=""
                                                        width="60" height="60" class="product-image">
                                                @else
                                                    <div class="text-center text-muted"><i
                                                            class="fas fa-image fa-2x opacity-50"></i><br><small>Chưa có
                                                            ảnh</small></div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.product_variants.edit', $variant->id) }}"
                                                        class="btn-modern btn-edit"><i class="fas fa-edit"></i> Sửa</a>
                                                    <form
                                                        action="{{ route('admin.product_variants.destroy', $variant->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn-modern btn-delete"><i class="fas fa-trash"></i>
                                                            Xóa</button>
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
                    <p>Hãy tạo biến thể đầu tiên ngay!</p>
                </div>
            @endif

            {{-- phân trang --}}
            @if ($variantsPaginated->hasPages())
                <div class="pagination-wrapper">
                    <div class="d-flex justify-content-center">{{ $variantsPaginated->links() }}</div>
                </div>
            @endif

        </div>
    </div>

    {{-- thêm script Confirm delete đẹp --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[onsubmit]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('⚠️ Bạn có chắc chắn muốn xóa biến thể này không?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection

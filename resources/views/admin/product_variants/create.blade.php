@extends('admin.layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('admins/assets/css/variants.css') }}">
@endpush
@php
    $pendingProduct = session('pending_product');
@endphp
@section('content')
    <div class="product-variants-container container-fluid ">
        <div class="container-fluid px-4" style="max-width: 100vw;">
            <!-- Enhanced Header Section -->
            <div class="page-header-enhanced">
                <div class="header-content">
                    <div class="title-section">
                        <div class="icon-wrapper">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <h5 class="page-title-enhanced">Thêm biến thể sản phẩm</h5>
                            @if ($pendingProduct)
                                <p class="page-subtitle-enhanced">
                                    Sản phẩm: <strong>{{ $pendingProduct['product_name'] }}</strong>
                                </p>
                            @endif
                        </div>
                    </div>


                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Enhanced Preview Section -->
            <div class="fade-in">
                <div class="product-card">
                    <div class="card-header bg-gradient-success  ">
                        <h1 class="mb-0 fw-bold" style="font-size: 1.5rem; ">
                            <i class="fas fa-table me-3" style="font-size: 1rem;"></i>
                            XEM TRƯỚC BIẾN THỂ SẢN PHẨM
                        </h1>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0" id="preview-variants-table">
                                <thead class="table-success sticky-top">
                                    <tr>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-tag me-2"></i>Sản phẩm
                                        </th>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-seedling me-2"></i>Vị
                                        </th>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-expand-arrows-alt me-2"></i>khối lượng
                                        </th>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-image me-2"></i>Ảnh
                                        </th>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-money-bill-wave me-2"></i>Giá (VND)
                                        </th>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-cubes me-2"></i>Số lượng
                                        </th>
                                        <th class="py-4 fw-bold text-center" style="font-size: 1rem;">
                                            <i class="fas fa-qrcode me-2"></i>SKU
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="preview-variants-body" style="font-size: 1.4rem; font-weight: 600;"></tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Enhanced Form Section -->
            <div class="fade-in">
                <div class="product-card">
                    <div class="card-header text-white py-3" style="background-color: #ffffff;">
                        <h5 class="mb-0" style="font-size: 1.4rem;">
                            <i class="fas fa-cogs"></i>
                            Thông tin biến thể sản phẩm
                        </h5>
                    </div>

                    <div class="card-body p-5">
                        <form method="POST" action="{{ route('admin.product_variants.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                            <input type="hidden" name="product_code" value="{{ $product->product_code }}">
                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                            <input type="hidden" name="original_price" value="{{ $product->original_price }}">
                            <input type="hidden" name="discounted_price" value="{{ $product->discounted_price }}">
                            <input type="hidden" name="description" value="{{ $product->description }}">
                            <input type="hidden" name="image" value="{{ $product->image }}">
                            <input type="hidden" name="product_type" value="variant">
                            <input type="hidden" id="product_code" value="{{ $product->product_code }}">
                            <div id="variants-container">
                                <div class="variant-item mb-5">
                                    <div class="card border-0 shadow-lg">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3"
                                            style="border-left: 5px solid #f2f2f2;">
                                            <h5 class="mb-0 text-success fw-bold" style="font-size: 1.3rem;">
                                                <i class="fas fa-cube me-2"></i>
                                                Biến thể #1
                                            </h5>
                                            <button type="button" class="btn btn-danger btn-sm remove-variant"
                                                style="font-size: 1rem;">
                                                <i class="fas fa-trash-alt me-1"></i> Xóa biến thể
                                            </button>
                                        </div>
                                        <div class="card-body p-4">
                                            <!-- Main Attribute -->
                                            <div class="mb-4">
                                                <label class="form-label fw-bold text-dark mb-2"
                                                    style="font-size: 1.2rem;">
                                                    <i class="fas fa-palette me-2"></i>
                                                    Thuộc tính chính (Vị)
                                                </label>
                                                <div class="input-group" style="height: 50px;">
                                                    <span class="input-group-text bg-primary text-white px-3"
                                                        style="font-size: 1.1rem;">
                                                        <i class="fas fa-seedling"></i>
                                                    </span>
                                                    <input type="text" name="variants[0][main_attribute][name]"
                                                        value="Vị" class="form-control fw-bold" readonly
                                                        style="background: #f8f9fa; font-size: 1.1rem;">
                                                    <input type="text" name="variants[0][main_attribute][value]"
                                                        class="form-control" placeholder="Ví dụ: Cay, Ngọt, Mặn..."
                                                        style="padding: 0.75rem; font-size: 1.4rem; font-weight: 500;">
                                                </div>
                                            </div>

                                            <!-- Sub Attributes -->
                                            <div class="sub-attributes-group">
                                                <label class="form-label fw-bold text-dark" style="font-size: 1.2rem;">
                                                    <i class="fas fa-list-ul me-2"></i>
                                                    Các lựa chọn chi tiết
                                                </label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered"
                                                        style="table-layout: fixed; width: 100%;">
                                                        <thead class="table-primary">
                                                            <tr style="height: 10px;">
                                                                <th class="text-center"
                                                                    style="width: 15%; font-size: 1.1rem;">
                                                                    <i class="fas fa-expand-arrows-alt"></i> Khối lượng
                                                                </th>
                                                                <th class="text-center"
                                                                    style="width: 17%; font-size: 1.1rem;">
                                                                    <i class="fas fa-money-bill-wave me-1"></i> Giá (VND)
                                                                </th>
                                                                <th class="text-center"
                                                                    style="width: 13%; font-size: 1.1rem;">
                                                                    <i class="fas fa-boxes me-1"></i> Số lượng
                                                                </th>
                                                                <th class="text-center"
                                                                    style="width: 20%; font-size: 1.1rem;">
                                                                    <i class="fas fa-camera me-1"></i> Hình ảnh
                                                                </th>
                                                                <th class="text-center"
                                                                    style="width: 20%; font-size: 1.1rem;">
                                                                    <i class="fas fa-barcode me-1"></i> Mã SKU
                                                                </th>
                                                                <th class="text-center"
                                                                    style="width: 15%; font-size: 1rem;">
                                                                    <i class="fas fa-tools me-1"></i> Thao tác
                                                                </th>
                                                            </tr>
                                                        </thead>

                                                        <tbody class="sub-attributes-table">
                                                            <tr class="sub-attribute-row" style="height: 65px;">
                                                                <td class="py-3">
                                                                    <select
                                                                        name="variants[0][sub_attributes][0][attribute_value_id]"
                                                                        class="form-select weight-select"
                                                                        style="height: 50px; padding: 0.75rem; font-size: 1.2rem; font-weight: 400;">
                                                                        @foreach ($sizeValues as $size)
                                                                            <option value="{{ $size->id }}">
                                                                                {{ $size->value }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="py-3">

                                                                    <input type="number"
                                                                        name="variants[0][sub_attributes][0][price]"
                                                                        class="form-control text-end price-input"
                                                                        placeholder="0" min="0" step="1000"
                                                                        style="padding: 0.75rem; font-size: 1.4rem; font-weight: 500;">

                                                                </td>
                                                                <td class="py-3">
                                                                    <input type="number"
                                                                        name="variants[0][sub_attributes][0][quantity_in_stock]"
                                                                        class="form-control text-center" placeholder="0"
                                                                        min="0"
                                                                        style="height: 45px; padding: 0.75rem; font-size: 1.4rem; font-weight: 500;">
                                                                </td>
                                                                <td class="py-3">
                                                                    <input type="file"
                                                                        name="variants[0][sub_attributes][0][image]"
                                                                        class="form-control" accept="image/*"
                                                                        style="height: 45px; padding: 0.5rem; font-size: 1.2rem;">
                                                                </td>
                                                                <td class="py-3">
                                                                    <input type="text"
                                                                        name="variants[0][sub_attributes][0][sku]"
                                                                        class="form-control sku-input" readonly
                                                                        style="height: 45px; padding: 0.75rem; font-size: 1.3rem; font-weight: 500;">
                                                                </td>
                                                                <td class="text-center py-3">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-sub-attribute"
                                                                        style="font-size: 1rem;">
                                                                        <i class="fas fa-trash me-1"></i> Xóa
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="mt-3">
                                                    <button type="button"
                                                        class="btn btn-success btn-sm add-sub-attribute"
                                                        style="font-size: 1rem;">
                                                        <i class="fas fa-plus me-1"></i> Thêm khối lượng khác
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 mb-4">
                                <button type="button" class="btn btn-info btn-sm" id="add-variant"
                                    style="font-size: 1rem;">
                                    <i class="fas fa-plus-circle me-1"></i> Thêm biến thể mới
                                </button>
                            </div>

                            <hr class="my-4" style="height: 2px; background: linear-gradient(90deg, #667eea, #764ba2);">

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('admin.product_variants.cancel') }}"
                                    class="btn btn-secondary btn-sm py-2">
                                    <i class="fas fa-times me-1"></i> Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm px-4 py-2"
                                    style="font-size: 1.1rem;">
                                    <i class="fas fa-check me-1"></i> Lưu biến thể
                                </button>
                                <a href="{{ route('admin.product_variants.index') }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i> Quay lại
                                </a>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
<style>
    <style>.product-card {
        width: 100%;
        max-width: 100%;
    }
    .product-variants-container .sub-attributes-group .table td .form-control,
    .product-variants-container .sub-attributes-group .table td .form-select,
    .product-variants-container .sub-attributes-group .table td .input-group-text,
    .product-variants-container .sub-attributes-group .table td .btn {
        height: 38px !important;
        padding: 0.4rem 0.6rem !important;
        font-size: 1.1rem !important;
        min-width: 120px;
    }

    .product-variants-container .sub-attributes-group .table td input[type="number"],
    .product-variants-container .sub-attributes-group .table td input[type="text"],
    .product-variants-container .sub-attributes-group .table td input[type="file"] {
        width: 100% !important;
    }

    .product-variants-container .sub-attributes-group .table td .input-group .form-control {
        flex: 1 1 auto;
        width: 1%;
    }

    .product-variants-container .sub-attributes-group .table {
        border-collapse: collapse;
    }

    .product-variants-container .sub-attributes-group .table thead th {
        background: none !important;
        color: #333 !important;
        font-weight: 600;
        border: 1px solid #ccc !important;
        text-align: center;
        vertical-align: middle;
    }

    .product-variants-container .sub-attributes-group .table tbody td {
        border: 1px solid #ccc !important;
        background-color: #fff !important;
        vertical-align: middle;
    }

    .product-variants-container .sub-attributes-group .table .form-control,
    .product-variants-container .sub-attributes-group .table .input-group-text,
    .product-variants-container .sub-attributes-group .table .btn {
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    /* Bỏ khoảng trắng trái/phải của vùng nội dung */
    .product-variants-container .main-content,
    .product-variants-container .card,
    .product-variants-container .card-body {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Tùy chỉnh table responsive nếu cần full */
    .product-variants-container .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
</style>
@push('scripts')
    <script>
        window.productName = @json($product->product_name);
    </script>
    <script src="{{ asset('admins/assets/js/variants.js') }}"></script>
@endpush

@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0 text-gray-800">
                    <i class="fas fa-boxes text-primary me-2"></i>
                    Thêm biến thể sản phẩm
                </h1>
                <p class="text-muted mb-0 small">Sản phẩm: <strong class="text-primary">{{ $product->product_name }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.product_variants.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-gradient-primary text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Thông tin biến thể
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <form method="POST" action="{{ route('admin.product_variants.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" id="original_price" value="{{ $product->original_price }}">
                            <input type="hidden" id="product_code" value="{{ $product->product_code }}">

                            <div id="variants-container">
                                <div class="variant-item mb-3">
                                    <div class="card border-left-primary">
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                            <h6 class="mb-0 text-primary small">
                                                <i class="fas fa-cube me-1"></i>
                                                Biến thể #1
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-variant">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <div class="card-body p-3">
                                            <!-- Main Attribute -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark mb-2 small">
                                                    <i class="fas fa-palette me-1"></i>
                                                    Thuộc tính chính (Vị)
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-primary text-white">
                                                        <i class="fas fa-seedling"></i>
                                                    </span>
                                                    <input type="text" name="variants[0][main_attribute][name]"
                                                        value="Vị" class="form-control form-control-sm" readonly>
                                                    <input type="text" name="variants[0][main_attribute][value]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Ví dụ: Cay, Ngọt, Mặn...">
                                                </div>
                                            </div>

                                            <!-- Sub Attributes -->
                                            <div class="sub-attributes-group">
                                                <label class="form-label fw-bold text-dark mb-2 small">
                                                    <i class="fas fa-list-ul me-1"></i>
                                                    Các lựa chọn chi tiết
                                                </label>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th class="text-center py-2 small">
                                                                    <i class="fas fa-expand-arrows-alt me-1"></i>Size
                                                                </th>
                                                                <th class="text-center py-2 small">
                                                                    <i class="fas fa-money-bill-wave me-1"></i>Giá (VND)
                                                                </th>
                                                                <th class="text-center py-2 small">
                                                                    <i class="fas fa-boxes me-1"></i>SL
                                                                </th>
                                                                <th class="text-center py-2 small">
                                                                    <i class="fas fa-camera me-1"></i>Ảnh
                                                                </th>
                                                                <th class="text-center py-2 small">
                                                                    <i class="fas fa-barcode me-1"></i>SKU
                                                                </th>
                                                                <th class="text-center py-2 small">
                                                                    <i class="fas fa-tools me-1"></i>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="sub-attributes-table">
                                                            <tr class="sub-attribute-row">
                                                                <td class="py-2">
                                                                    <select
                                                                        name="variants[0][sub_attributes][0][attribute_value_id]"
                                                                        class="form-select form-select-sm size-select">
                                                                        @foreach ($sizeValues as $size)
                                                                            <option value="{{ $size->id }}">
                                                                                {{ $size->value }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="py-2">
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number"
                                                                            name="variants[0][sub_attributes][0][price]"
                                                                            class="form-control text-end price-input"
                                                                            placeholder="0" min="0" step="1000">
                                                                        <span class="input-group-text">VND</span>
                                                                    </div>
                                                                </td>
                                                                <td class="py-2">
                                                                    <input type="number"
                                                                        name="variants[0][sub_attributes][0][quantity_in_stock]"
                                                                        class="form-control form-control-sm text-center"
                                                                        placeholder="0" min="0">
                                                                </td>
                                                                <td class="py-2">
                                                                    <input type="file"
                                                                        name="variants[0][sub_attributes][0][image]"
                                                                        class="form-control form-control-sm"
                                                                        accept="image/*">
                                                                </td>
                                                                <td class="py-2">
                                                                    <input type="text"
                                                                        name="variants[0][sub_attributes][0][sku]"
                                                                        class="form-control form-control-sm sku-input"
                                                                        readonly>
                                                                </td>
                                                                <td class="text-center py-2">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger remove-sub-attribute">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success add-sub-attribute">
                                                    <i class="fas fa-plus me-1"></i> Thêm lựa chọn
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-info" id="add-variant">
                                    <i class="fas fa-plus-circle me-1"></i> Thêm biến thể mới
                                </button>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.product_variants.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times me-1"></i> Huỷ bỏ
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-check me-1"></i> Lưu biến thể
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-success text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-table me-2"></i>
                            Xem trước biến thể
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-sm mb-0" id="preview-variants-table">
                                <thead class="table-success sticky-top">
                                    <tr>
                                        <th class="py-2 small">
                                            <i class="fas fa-tag me-1"></i>SP
                                        </th>
                                        <th class="py-2 small">
                                            <i class="fas fa-seedling me-1"></i>Vị
                                        </th>
                                        <th class="py-2 small">
                                            <i class="fas fa-expand-arrows-alt me-1"></i>Size
                                        </th>
                                        <th class="py-2 small">
                                            <i class="fas fa-image me-1"></i>Ảnh
                                        </th>
                                        <th class="py-2 small">
                                            <i class="fas fa-money-bill-wave me-1"></i>Giá
                                        </th>
                                        <th class="py-2 small">
                                            <i class="fas fa-cubes me-1"></i>SL
                                        </th>
                                        <th class="py-2 small">
                                            <i class="fas fa-qrcode me-1"></i>SKU
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light border-top text-center text-muted small" id="preview-empty">
                            <i class="fas fa-clipboard-list me-1"></i>
                            Nhập thông tin để xem trước biến thể
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.productName = @json($product->product_name);
    </script>
    <script src="{{ asset('admins/assets/js/variants.js') }}"></script>
@endpush



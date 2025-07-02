@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus me-2 text-success"></i>
                    Thêm mã giảm giá
                </h1>
                <p class="text-muted mb-0">Tạo mã khuyến mãi mới cho khách hàng</p>
            </div>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <!-- Thông báo lỗi -->
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.promotions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên khuyến mãi</label>
                        <input type="text" name="promotion_name" class="form-control" required
                            value="{{ old('promotion_name') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Loại giảm giá</label>
                            <select name="discount_type" id="discount_type" class="form-select" required>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>
                                    Giảm theo số tiền
                                </option>
                                <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>
                                    Giảm theo phần trăm
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Giá trị giảm</label>
                            <input type="number" step="1" name="discount_value" id="discount_value" class="form-control"
                                required value="{{ old('discount_value') }}">
                        </div>
                    </div>

                    <div class="mb-3" id="max_discount_container">
                        <label class="form-label fw-semibold">Giảm tối đa (cho phần trăm)</label>
                        <input type="number" step="1" name="max_discount_value" class="form-control"
                            value="{{ old('max_discount_value') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tổng đơn hàng tối thiểu để áp dụng</label>
                        <input type="number" name="min_total_spent" class="form-control"
                            value="{{ old('min_total_spent') }}" placeholder="VD: 1000000">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="vip_only" id="vip_only" value="1"
                            {{ old('vip_only') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="vip_only">
                            Chỉ áp dụng cho khách hàng VIP
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giới hạn lượt sử dụng</label>
                        <input type="number" name="usage_limit" class="form-control"
                            value="{{ old('usage_limit') }}" placeholder="VD: 100 (bỏ trống nếu không giới hạn)">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="datetime-local" name="start_date" class="form-control" required
                                value="{{ old('start_date', \Carbon\Carbon::now()->addMinutes(1)->format('Y-m-d\TH:i')) }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="datetime-local" name="end_date" class="form-control" required
                                value="{{ old('end_date', \Carbon\Carbon::now()->addDays(1)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Lưu mã giảm giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const discountTypeSelect = document.getElementById('discount_type');
            const maxDiscountContainer = document.getElementById('max_discount_container');
            const discountValueInput = document.getElementById('discount_value');

            function toggleMaxDiscountField() {
                const selectedType = discountTypeSelect.value;

                if (selectedType === 'fixed') {
                    maxDiscountContainer.style.display = 'none';
                    discountValueInput.setAttribute('min', '1000');
                    discountValueInput.removeAttribute('max');
                    discountValueInput.setAttribute('placeholder', 'Tối thiểu 1000 VNĐ');
                } else {
                    maxDiscountContainer.style.display = 'block';
                    discountValueInput.setAttribute('min', '0');
                    discountValueInput.setAttribute('max', '100');
                    discountValueInput.setAttribute('placeholder', 'Tối đa 100%');
                }
            }

            toggleMaxDiscountField();
            discountTypeSelect.addEventListener('change', toggleMaxDiscountField);
        });
    </script>
@endsection

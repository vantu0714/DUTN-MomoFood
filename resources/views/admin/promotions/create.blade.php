@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
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

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.promotions.store') }}" method="POST">
                    @csrf

                    {{-- Tên chương trình --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên khuyến mãi</label>
                        <input type="text" name="promotion_name" class="form-control" required
                            value="{{ old('promotion_name') }}">
                    </div>

                    {{-- Mã giảm giá --}}
                    {{-- <div class="mb-3">
                        <label for="code" class="form-label">Mã giảm giá</label>
                        <input type="text" name="code" id="code"
                            class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    {{-- Loại & Giá trị giảm --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Loại giảm giá</label>
                            <select name="discount_type" id="discount_type" class="form-select" required>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm theo số
                                    tiền</option>
                                <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Giảm theo
                                    phần trăm</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Số tiền giảm(Số phần trăm 1-100)</label>
                            <input type="number" step="1" name="discount_value" id="discount_value"
                                class="form-control @error('discount_value') is-invalid @enderror" required
                                value="{{ old('discount_value') }}">
                            @error('discount_value')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                            <small class="text-danger d-block client-error" id="discount-error"
                                style="display: none;"></small>
                        </div>
                    </div>

                    {{-- Tổng chi tối thiểu --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tổng đơn hàng tối thiểu để áp dụng</label>
                        <input type="number" name="min_total_spent"
                            class="form-control @error('min_total_spent') is-invalid @enderror"
                            value="{{ old('min_total_spent') }}" placeholder="VD: 1000000">
                        <small class="text-danger client-error d-none"></small>
                        @error('min_total_spent')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giảm tối đa nếu là phần trăm --}}
                    <div class="mb-3" id="max_discount_container"
                        style="{{ old('discount_type') === 'percent' ? '' : 'display: none;' }}">
                        <label class="form-label fw-semibold">Số tiền giảm tối đa (Áp dụng cho phần trăm)</label>
                        <input type="number" step="1" name="max_discount_value" class="form-control"
                            value="{{ old('max_discount_value') }}">
                        <small class="text-danger d-block client-error" id="max-discount-error"
                            style="display: none;"></small>
                    </div>


                    {{-- Chỉ áp dụng cho VIP --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="vip_only" id="vip_only" value="1"
                            {{ old('vip_only') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="vip_only">Chỉ áp dụng cho khách hàng VIP</label>
                    </div>

                    {{-- Giới hạn lượt sử dụng --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giới hạn lượt sử dụng</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}"
                            placeholder="VD: 100 (bỏ trống nếu không giới hạn)">
                    </div>

                    {{-- Ngày bắt đầu & kết thúc --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="datetime-local" name="start_date" class="form-control" required
                                value="{{ old('start_date', now()->addMinutes(1)->format('Y-m-d\TH:i')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="datetime-local" name="end_date" class="form-control" required
                                value="{{ old('end_date', now()->addDays(1)->format('Y-m-d\TH:i')) }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Mô tả --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    {{-- Nút submit --}}
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

<script>
    function toggleMaxDiscountField() {
        const type = document.getElementById('discount_type')?.value;
        const maxDiscountContainer = document.getElementById('max_discount_container');
        if (maxDiscountContainer) {
            maxDiscountContainer.style.display = (type === 'percent') ? 'block' : 'none';
        }
    }

    function validateInput(input, errorEl) {
        const name = input.name;
        const value = input.value.trim();
        const discountType = document.getElementById('discount_type').value;
        const minTotalSpent = parseFloat(document.querySelector('[name="min_total_spent"]')?.value || 0);
        const discountValue = parseFloat(document.querySelector('[name="discount_value"]')?.value || 0);

        let message = '';

        if (!value) {
            if (name === 'promotion_name') message = 'Vui lòng nhập tên chương trình.';
            if (name === 'code') message = 'Vui lòng nhập mã giảm giá.';
            if (name === 'discount_value') message = 'Vui lòng nhập giá trị giảm.';
            if (name === 'min_total_spent') message = 'Vui lòng nhập tổng đơn hàng tối thiểu.';
        } else if (name === 'discount_value') {
            const num = parseFloat(value);
            if (discountType === 'percent') {
                if (num < 1 || num > 100) {
                    message = 'Phần trăm giảm phải từ 1 đến 100.';
                } else if (num > 50) {
                    message = 'Không được giảm quá 50% nếu là phần trăm.';
                }
            } else if (discountType === 'fixed') {
                if (num < 1000) {
                    message = 'Số tiền giảm tối thiểu là 1000đ.';
                } else if (!isNaN(minTotalSpent) && minTotalSpent > 0 && num > minTotalSpent * 0.5) {
                    message = 'Số tiền giảm không được vượt quá 50% tổng đơn hàng tối thiểu.';
                }
            }

        } else if (name === 'min_total_spent') {
            const num = parseFloat(value);
            if (isNaN(num) || num < 1000) {
                message = 'Tổng đơn hàng tối thiểu phải từ 1.000đ trở lên.';
            }
        }

        if (message) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
            input.classList.add('is-invalid');
        } else {
            errorEl.textContent = '';
            errorEl.style.display = 'none';
            input.classList.remove('is-invalid');
        }
    }

    function validateMaxDiscount() {
        const maxDiscountEl = document.querySelector('[name="max_discount_value"]');
        const discountValue = parseFloat(document.querySelector('[name="discount_value"]')?.value || 0);
        const minTotalSpent = parseFloat(document.querySelector('[name="min_total_spent"]')?.value || 0);
        const discountType = document.getElementById('discount_type')?.value;
        const errorEl = document.getElementById('max-discount-error');

        let message = '';
        if (discountType === 'percent') {
            const maxAllowed = (discountValue / 100) * minTotalSpent;
            const currentMax = parseFloat(maxDiscountEl.value || 0);
            if (!isNaN(currentMax) && currentMax > maxAllowed) {
                message =
                    `Số tiền giảm tối đa không được vượt quá ${maxAllowed.toLocaleString()}đ (tương ứng ${discountValue}% của tổng đơn tối thiểu).`;
            }
        }

        if (message) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
            maxDiscountEl.classList.add('is-invalid');
        } else {
            errorEl.textContent = '';
            errorEl.style.display = 'none';
            maxDiscountEl.classList.remove('is-invalid');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleMaxDiscountField();
        document.getElementById('discount_type')?.addEventListener('change', () => {
            toggleMaxDiscountField();
            validateMaxDiscount();
        });

        const fields = ['promotion_name', 'code', 'discount_value', 'min_total_spent'];

        fields.forEach(name => {
            const input = document.querySelector(`[name="${name}"]`);
            if (!input) return;

            let errorEl = input.parentElement.querySelector('.client-error');
            if (!errorEl) {
                errorEl = document.createElement('small');
                errorEl.classList.add('text-danger', 'd-block', 'client-error');
                errorEl.style.display = 'none';
                input.parentElement.appendChild(errorEl);
            }

            input.addEventListener('input', () => validateInput(input, errorEl));
            input.addEventListener('blur', () => validateInput(input, errorEl));
        });

        const minInput = document.querySelector('[name="min_total_spent"]');
        if (minInput) {
            minInput.addEventListener('input', () => {
                const discountInput = document.querySelector('[name="discount_value"]');
                const errorEl = discountInput?.parentNode.querySelector('.client-error');
                if (discountInput && errorEl) {
                    validateInput(discountInput, errorEl);
                }
                validateMaxDiscount();
            });
        }

        const maxDiscountInput = document.querySelector('[name="max_discount_value"]');
        if (maxDiscountInput) {
            maxDiscountInput.addEventListener('input', validateMaxDiscount);
        }

        document.querySelector('[name="discount_value"]')?.addEventListener('input', validateMaxDiscount);
    });
</script>

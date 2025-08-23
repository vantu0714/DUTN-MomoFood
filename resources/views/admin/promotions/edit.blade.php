@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    Chỉnh sửa mã giảm giá
                </h1>
                <p class="text-muted mb-0">Cập nhật thông tin mã giảm giá</p>
            </div>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tên khuyến mãi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên khuyến mãi</label>
                        <input type="text" name="promotion_name"
                            class="form-control @error('promotion_name') is-invalid @enderror"
                            value="{{ old('promotion_name', $promotion->promotion_name) }}" required>
                        @error('promotion_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mã giảm giá --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code', $promotion->code) }}" readonly>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Loại giảm giá --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loại giảm giá</label>
                        <select class="form-select" disabled>
                            <option value="fixed" {{ $promotion->discount_type == 'fixed' ? 'selected' : '' }}>
                                Giảm theo số tiền
                            </option>
                            <option value="percent" {{ $promotion->discount_type == 'percent' ? 'selected' : '' }}>
                                Giảm theo phần trăm
                            </option>
                        </select>

                        {{-- input hidden để giữ giá trị và cho JS đọc --}}
                        <input type="hidden" id="discount_type" name="discount_type"
                            value="{{ $promotion->discount_type }}">
                    </div>


                    {{-- Giá trị giảm --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá trị giảm</label>
                        <input type="number" step="0.01" name="discount_value"
                            class="form-control @error('discount_value') is-invalid @enderror"
                            value="{{ old('discount_value', $promotion->discount_value) }}" required>
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tổng giá trị đơn hàng tối thiểu</label>
                        <input type="number" step="1000" min="1000" name="min_total_spent"
                            class="form-control @error('min_total_spent') is-invalid @enderror"
                            value="{{ old('min_total_spent', $promotion->min_total_spent) }}">
                        @error('min_total_spent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giảm tối đa (chỉ áp dụng cho phần trăm) --}}
                    <div class="mb-3" id="max_discount_container"
                        style="{{ old('discount_type', $promotion->discount_type) === 'percent' ? '' : 'display: none;' }}">
                        <label class="form-label fw-semibold">Giảm tối đa (áp dụng cho %)</label>
                        <input type="number" step="1" name="max_discount_value"
                            class="form-control @error('max_discount_value') is-invalid @enderror"
                            value="{{ old('max_discount_value', $promotion->max_discount_value) }}">
                        {{-- <small class="text-danger client-error d-none"></small> --}}
                        @error('max_discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ngày bắt đầu và kết thúc --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="datetime-local" name="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="datetime-local" name="end_date"
                                class="form-control @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i')) }}"
                                min="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}"
                                required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $promotion->status) == 1 ? 'selected' : '' }}>Đang hoạt
                                động</option>
                            <option value="0" {{ old('status', $promotion->status) == 0 ? 'selected' : '' }}>Không
                                hoạt động</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giới hạn lượt sử dụng --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giới hạn lượt sử dụng</label>
                        <input type="number" name="usage_limit" min="0"
                            class="form-control @error('usage_limit') is-invalid @enderror"
                            value="{{ old('usage_limit', $promotion->usage_limit) }}">
                        @error('usage_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $promotion->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút submit --}}
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Script hiển thị giảm tối đa khi chọn loại phần trăm + validate client-side --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const MIN_MONEY = 1000;
        const discountTypeEl = document.getElementById('discount_type');
        const discountInput = document.querySelector('[name="discount_value"]');
        const minTotalInput = document.querySelector('[name="min_total_spent"]');
        const maxDiscountWrapper = document.getElementById('max_discount_wrapper'); // bọc input giảm tối đa
        const maxDiscountInput = document.querySelector('[name="max_discount_value"]'); // input giảm tối đa

        // Chỉ lấy số, bỏ hết dấu . , và ký tự khác
        // Chỉ lấy số, cho phép 1 dấu thập phân (.,)
        function parseMoney(v) {
            if (v == null) return NaN;
            let s = String(v).trim();
            if (s === '') return NaN;

            // Đổi dấu , thành .
            s = s.replace(',', '.');

            // Xóa ký tự không hợp lệ (giữ số, dấu . duy nhất)
            s = s.replace(/[^0-9.]/g, '');

            // Nếu có nhiều dấu ., chỉ giữ dấu đầu tiên
            const parts = s.split('.');
            if (parts.length > 2) {
                s = parts[0] + '.' + parts.slice(1).join('');
            }

            const num = parseFloat(s);
            return isNaN(num) ? NaN : num;
        }


        function ensureErrEl(input) {
            let el = input.parentElement.querySelector('.client-error');
            if (!el) {
                el = document.createElement('small');
                el.className = 'text-danger d-block client-error';
                input.parentElement.appendChild(el);
            }
            return el;
        }

        function setError(input, msg) {
            const el = ensureErrEl(input);
            if (msg) {
                el.textContent = msg;
                el.style.display = 'block';
                input.classList.add('is-invalid');
            } else {
                el.textContent = '';
                el.style.display = 'none';
                input.classList.remove('is-invalid');
            }
        }

        function validateMinTotal() {
            if (!minTotalInput) return true;
            const val = parseMoney(minTotalInput.value);
            let msg = '';
            if (isNaN(val)) msg = 'Vui lòng nhập số hợp lệ.';
            else if (val < MIN_MONEY) msg =
                `Tổng đơn hàng tối thiểu phải từ ${MIN_MONEY.toLocaleString('vi-VN')}đ trở lên.`;
            setError(minTotalInput, msg);
            return !msg;
        }

        function validateDiscount() {
            if (!discountInput) return true;
            const type = discountTypeEl?.value;
            const disc = parseMoney(discountInput.value);
            const minTotal = parseMoney(minTotalInput?.value || '');
            let msg = '';

            if (type === 'percent') {
                if (isNaN(disc)) msg = 'Vui lòng nhập số hợp lệ.';
                else if (disc < 1 || disc > 100) msg = 'Phần trăm giảm phải từ 1–100.';
                else if (disc > 50) msg = 'Không được giảm quá 50%.';
            } else { // fixed money
                if (isNaN(disc)) msg = 'Vui lòng nhập số hợp lệ.';
                else if (disc < MIN_MONEY) msg =
                    `Số tiền giảm tối thiểu là ${MIN_MONEY.toLocaleString('vi-VN')}đ.`;
                else if (!isNaN(minTotal) && minTotal >= MIN_MONEY && disc > minTotal * 0.5)
                    msg = 'Số tiền giảm không được vượt quá 50% tổng đơn hàng tối thiểu.';
            }

            setError(discountInput, msg);
            return !msg;
        }

        function validateMaxDiscount() {
            if (!maxDiscountInput || discountTypeEl?.value !== 'percent') return true;
            const val = parseMoney(maxDiscountInput.value);
            const minTotal = parseMoney(minTotalInput?.value || '');
            let msg = '';

            if (isNaN(val)) {
                msg = 'Vui lòng nhập số hợp lệ.';
            } else if (val < MIN_MONEY) {
                msg = `Giảm tối đa phải từ ${MIN_MONEY.toLocaleString('vi-VN')}đ trở lên.`;
            } else if (!isNaN(minTotal) && minTotal >= MIN_MONEY) {
                const disc = parseMoney(discountInput?.value || ''); // % giảm
                if (!isNaN(disc)) {
                    const maxAllowed = minTotal * (disc / 100); // lấy đúng theo % giảm
                    if (val > maxAllowed) {
                        msg =
                            `Giảm tối đa không được vượt quá ${disc}% của tổng đơn hàng tối thiểu (${maxAllowed.toLocaleString('vi-VN')}đ).`;
                    }
                }
            }

            setError(maxDiscountInput, msg);
            return !msg;
        }

        // Hiển thị/ẩn ô Giảm tối đa theo loại
        function toggleMaxDiscount() {
            if (!maxDiscountWrapper) return;
            if (discountTypeEl?.value === 'percent') {
                maxDiscountWrapper.style.display = '';
            } else {
                maxDiscountWrapper.style.display = 'none';
                if (maxDiscountInput) setError(maxDiscountInput, ''); // clear lỗi khi ẩn
            }
        }

        // Gắn sự kiện
        if (minTotalInput) {
            minTotalInput.addEventListener('input', () => {
                validateMinTotal();
                validateDiscount();
                validateMaxDiscount();
            });
            minTotalInput.addEventListener('blur', validateMinTotal);
            if (minTotalInput.type === 'number') minTotalInput.min = String(MIN_MONEY);
        }

        if (discountInput) {
            discountInput.addEventListener('input', () => {
                validateDiscount();
                validateMaxDiscount();
            });
            discountInput.addEventListener('blur', validateDiscount);
        }

        if (maxDiscountInput) {
            maxDiscountInput.addEventListener('input', validateMaxDiscount);
            maxDiscountInput.addEventListener('blur', validateMaxDiscount);
        }

        discountTypeEl?.addEventListener('change', () => {
            validateDiscount();
            toggleMaxDiscount();
            validateMaxDiscount();
        });

        // chạy lần đầu
        validateMinTotal();
        validateDiscount();
        toggleMaxDiscount();
        validateMaxDiscount();
    });
</script>

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
                            <option value="fixed" {{ $promotion->discount_type == 'fixed' ? 'selected' : '' }}>Giảm theo số
                                tiền</option>
                            <option value="percent" {{ $promotion->discount_type == 'percent' ? 'selected' : '' }}>Giảm theo
                                phần trăm</option>
                        </select>
                        <input type="hidden" name="discount_type" value="{{ $promotion->discount_type }}">
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

                    {{-- Giảm tối đa (chỉ áp dụng cho phần trăm) --}}
                    <div class="mb-3" id="max_discount_container"
                        style="{{ old('discount_type', $promotion->discount_type) === 'percent' ? '' : 'display: none;' }}">
                        <label class="form-label fw-semibold">Giảm tối đa (áp dụng cho %)</label>
                        <input type="number" step="1" name="max_discount_value"
                            class="form-control @error('max_discount_value') is-invalid @enderror"
                            value="{{ old('max_discount_value', $promotion->max_discount_value) }}">
                        <small class="text-danger client-error d-none"></small>
                        @error('max_discount_value')
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
    document.addEventListener('DOMContentLoaded', function() {
        const discountType = '{{ old('discount_type', $promotion->discount_type) }}';
        const maxDiscountContainer = document.getElementById('max_discount_container');
        const maxDiscountEl = document.querySelector('[name="max_discount_value"]');
        const errorEl = document.createElement('div');

        // Gắn class để hiện lỗi như Laravel
        errorEl.classList.add('invalid-feedback');
        errorEl.id = 'max-discount-error';

        // Gắn dưới ô input nếu chưa có
        if (maxDiscountEl && !document.getElementById('max-discount-error')) {
            maxDiscountEl.parentElement.appendChild(errorEl);
        }

        // Hiện/ẩn ô giảm tối đa
        if (discountType === 'percent') {
            maxDiscountContainer.style.display = '';
        } else {
            maxDiscountContainer.style.display = 'none';
        }

        // Hàm validate giới hạn giảm tối đa
        function validateMaxDiscount() {
            const discountValue = parseFloat(document.querySelector('[name="discount_value"]')?.value || 0);
            const minTotalSpent = parseFloat(document.querySelector('[name="min_total_spent"]')?.value || 0);
            const currentMax = parseFloat(maxDiscountEl?.value || 0);

            let message = '';

            if (discountType === 'percent' && !isNaN(discountValue) && !isNaN(minTotalSpent)) {
                const maxAllowed = (discountValue / 100) * minTotalSpent;

                if (!isNaN(currentMax) && currentMax > maxAllowed) {
                    message =
                        `Số tiền giảm tối đa không được vượt quá ${maxAllowed.toLocaleString('vi-VN')}đ (tương ứng ${discountValue}% của tổng đơn tối thiểu).`;
                    maxDiscountEl.classList.add('is-invalid');
                } else {
                    maxDiscountEl.classList.remove('is-invalid');
                }

                errorEl.textContent = message;
            }
        }

        // Gắn sự kiện khi người dùng thay đổi các input
        ['discount_value', 'min_total_spent', 'max_discount_value'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) {
                el.addEventListener('input', validateMaxDiscount);
            }
        });

        // Gọi lúc đầu để kiểm tra nếu đã có lỗi
        validateMaxDiscount();
    });
</script>

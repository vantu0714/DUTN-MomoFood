@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Thêm mã giảm giá</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.promotions.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="promotion_name" class="form-label">Tên khuyến mãi</label>
                <input type="text" name="promotion_name" class="form-control" required
                    value="{{ old('promotion_name') }}">
            </div>

            <div class="mb-3">
                <label for="discount_type" class="form-label">Loại giảm giá</label>
                <select name="discount_type" id="discount_type" class="form-select" required>
                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm theo số tiền
                    </option>
                    <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="discount_value" class="form-label">Giá trị giảm</label>
                <input type="number" step="1" name="discount_value" id="discount_value" class="form-control" required
                    value="{{ old('discount_value') }}">
            </div>

            <div class="mb-3" id="max_discount_container">
                <label for="max_discount_value" class="form-label">Giảm tối đa (áp dụng cho phần trăm)</label>
                <input type="number" step="1" name="max_discount_value" class="form-control"
                    value="{{ old('max_discount_value') }}">
            </div>

            <div class="mb-3">
                <label for="min_total_spent" class="form-label">Tổng đơn hàng tối thiểu để áp dụng (VNĐ)</label>
                <input type="number" name="min_total_spent" class="form-control"
                    value="{{ old('min_total_spent') }}" placeholder="VD: 1000000">
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="vip_only" id="vip_only"
                    value="1" {{ old('vip_only') ? 'checked' : '' }}>
                <label class="form-check-label" for="vip_only">
                    Chỉ áp dụng cho khách hàng VIP
                </label>
            </div>

            <div class="mb-3">
                <label for="usage_limit" class="form-label">Giới hạn lượt sử dụng</label>
                <input type="number" name="usage_limit" class="form-control"
                    value="{{ old('usage_limit') }}" placeholder="VD: 100 (để trống nếu không giới hạn)">
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date" class="form-control" required
                    value="{{ old('start_date', \Carbon\Carbon::now()->addMinutes(1)->format('Y-m-d')) }}"
                    min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" class="form-control" required
                    value="{{ old('end_date', \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')) }}">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-success">Lưu mã giảm giá</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            if (discountTypeSelect && discountValueInput && maxDiscountContainer) {
                toggleMaxDiscountField(); // Gọi lần đầu khi load
                discountTypeSelect.addEventListener('change', toggleMaxDiscountField);
            }
        });
    </script>
@endsection

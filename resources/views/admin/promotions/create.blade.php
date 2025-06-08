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

        <form action="{{ route('promotions.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="promotion_name" class="form-label">Tên khuyến mãi</label>
                <input type="text" name="promotion_name" class="form-control" required value="{{ old('promotion_name') }}">
            </div>

            <div class="mb-3">
                <label for="discount_type" class="form-label">Loại giảm giá</label>
                <select name="discount_type" class="form-select" required>
                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm theo số tiền</option>
                    <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="discount_value" class="form-label">Giá trị giảm</label>
                <input type="number" step="0.01" name="discount_value" class="form-control" required value="{{ old('discount_value') }}">
            </div>

            <div class="mb-3">
                <label for="max_discount_value" class="form-label">Giảm tối đa (áp dụng cho phần trăm)</label>
                <input type="number" step="0.01" name="max_discount_value" class="form-control" value="{{ old('max_discount_value') }}">
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date" class="form-control" required value="{{ old('start_date') }}">
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" class="form-control" required value="{{ old('end_date') }}">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('promotions.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-success">Lưu mã giảm giá</button>
            </div>
        </form>
    </div>
@endsection

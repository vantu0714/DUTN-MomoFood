@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Chỉnh sửa mã giảm giá</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="promotion_name" class="form-label">Tên khuyến mãi</label>
                <input type="text" name="promotion_name" class="form-control" required
                    value="{{ old('promotion_name', $promotion->promotion_name) }}">
            </div>

            <div class="mb-3">
                <label for="discount_type" class="form-label">Loại giảm giá</label>
                <select class="form-select" disabled>
                    <option value="fixed" {{ $promotion->discount_type == 'fixed' ? 'selected' : '' }}>Giảm theo số tiền</option>
                    <option value="percent" {{ $promotion->discount_type == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                </select>
                <input type="hidden" name="discount_type" value="{{ $promotion->discount_type }}">
            </div>

            <div class="mb-3">
                <label for="discount_value" class="form-label">Giá trị giảm</label>
                <input type="number" step="0.01" name="discount_value" class="form-control" required
                    value="{{ old('discount_value', $promotion->discount_value) }}">
            </div>

            <div class="mb-3">
                <label for="max_discount_value" class="form-label">Giảm tối đa (cho %)</label>
                <input type="number" step="0.01" name="max_discount_value" class="form-control"
                    value="{{ old('max_discount_value', $promotion->max_discount_value) }}">
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date" class="form-control" required
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                    value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" class="form-control" required
                    min="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}"
                    value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                @php
                    $selectedStatus = old('status', $promotion->status);
                @endphp
                <select name="status" class="form-select" required>
                    <option value="1" {{ $selectedStatus == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ $selectedStatus == 0 ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="usage_limit" class="form-label">Giới hạn lượt sử dụng</label>
                <input type="number" min="0" name="usage_limit" class="form-control"
                    value="{{ old('usage_limit', $promotion->usage_limit) }}">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $promotion->description) }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection

@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
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

        <!-- Alert error -->
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
                <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên khuyến mãi</label>
                        <input type="text" name="promotion_name" class="form-control"
                            value="{{ old('promotion_name', $promotion->promotion_name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loại giảm giá</label>
                        <select class="form-select" disabled>
                            <option value="fixed" {{ $promotion->discount_type == 'fixed' ? 'selected' : '' }}>Giảm theo số tiền</option>
                            <option value="percent" {{ $promotion->discount_type == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                        </select>
                        <input type="hidden" name="discount_type" value="{{ $promotion->discount_type }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá trị giảm</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control"
                            value="{{ old('discount_value', $promotion->discount_value) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giảm tối đa (áp dụng cho %)</label>
                        <input type="number" step="0.01" name="max_discount_value" class="form-control"
                            value="{{ old('max_discount_value', $promotion->max_discount_value) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="datetime-local" name="start_date" class="form-control"
                                value="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="datetime-local" name="end_date" class="form-control"
                                value="{{ old('end_date', \Carbon\Carbon::parse($promotion->end_date)->format('Y-m-d\TH:i')) }}"
                                min="{{ old('start_date', \Carbon\Carbon::parse($promotion->start_date)->format('Y-m-d\TH:i')) }}"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', $promotion->status) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="0" {{ old('status', $promotion->status) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giới hạn lượt sử dụng</label>
                        <input type="number" name="usage_limit" min="0" class="form-control"
                            value="{{ old('usage_limit', $promotion->usage_limit) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $promotion->description) }}</textarea>
                    </div>

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

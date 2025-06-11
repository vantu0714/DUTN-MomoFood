@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Chi tiết danh mục</h4>
        </div>

        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Tên danh mục:</label>
                <p class="form-control-plaintext">{{ $categories->category_name }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Trạng thái:</label>
                <p class="form-control-plaintext">
                    @if ($categories->status)
                        <span class="badge bg-success">Hiển thị</span>
                    @else
                        <span class="badge bg-secondary">Ẩn</span>
                    @endif
                </p>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mô tả:</label>
                <p class="form-control-plaintext">{{ $categories->description ?? 'Không có mô tả' }}</p>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection

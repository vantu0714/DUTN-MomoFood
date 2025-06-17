@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm sản phẩm mới</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="product_name" class="form-control" value="{{ old('product_name') }}">
                @error('product_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mã sản phẩm</label>
                <input type="text" name="product_code" class="form-control" value="{{ old('product_code') }}">
                @error('product_code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Ảnh sản phẩm</label>
                <input type="file" name="image" class="form-control">
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Giá gốc</label>
                <input type="number" step="0.01" name="original_price" class="form-control"
                    value="{{ old('original_price') }}">
                @error('original_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Giá khuyến mãi</label>
                <input type="number" step="0.01" name="discounted_price" class="form-control"
                    value="{{ old('discounted_price') }}">
                @error('discounted_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Số lượng</label>
                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" min="0">
                @error('quantity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Loại sản phẩm</label>
                <select name="product_type" class="form-control" required>
                    <option value="">-- Chọn loại sản phẩm --</option>
                    <option value="simple" {{ old('product_type') == 'simple' ? 'selected' : '' }}>Không có biến thể
                    </option>
                    <option value="variant" {{ old('product_type') == 'variant' ? 'selected' : '' }}>Có biến thể</option>
                </select>
                @error('product_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <div class="mb-3">
                <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Huỷ</a>
            </div>

        </form>
    </div>
@endsection

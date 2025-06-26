@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Chỉnh sửa danh mục</h2>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')


            <div class="mb-3">
                <label for="category_name" class="form-label">Tên danh mục</label>
                <input type="text" name="category_name" class="form-control"
                    value="{{ old('category_name', $category->category_name) }}">
                @error('category_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Ẩn</option>
                </select>
                @error('status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Huỷ</a>
        </form>
    </div>
@endsection

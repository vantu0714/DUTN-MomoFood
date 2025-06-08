@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm danh mục mới</h2>

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="category_name" class="form-label">Tên danh mục</label>
                
                <input type="text" name="category_name" class="form-control">
                @error('category_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
           
            <div class="mb-3">
                <label for="parent_id" class="form-label">Danh mục cha (nếu có)</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- Không có --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
                @error('status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Huỷ</a>
        </form>
    </div>
@endsection

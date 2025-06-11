@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Chỉnh sửa biến thể sản phẩm</h2>

        <form action="{{ route('admin.product_variants.update', $product_variant->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên biến thể</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product_variant->name) }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" step="0.01" name="price" class="form-control"
                    value="{{ old('price', $product_variant->price) }}">
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="quantity_in_stock" class="form-label">Số lượng tồn kho</label>
                <input type="number" name="quantity_in_stock" class="form-control"
                    value="{{ old('quantity_in_stock', $product_variant->quantity_in_stock) }}">
                @error('quantity_in_stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product_variant->sku) }}">
                @error('sku')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="product_id" class="form-label">Sản phẩm</label>
                <select name="product_id" class="form-control">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            {{ $product_variant->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $product_variant->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ $product_variant->status == 0 ? 'selected' : '' }}>Ẩn</option>
                </select>
                @error('status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $product_variant->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>
        </form>
    </div>
@endsection

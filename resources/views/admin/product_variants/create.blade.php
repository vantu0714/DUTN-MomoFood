@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm biến thể sản phẩm mới</h2>

        <form action="{{ route('admin.product_variants.store') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Tên biến thể</label>
                <input type="text" name="name" class="form-control">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" step="0.01" name="price" class="form-control">
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="quantity_in_stock" class="form-label">Số lượng tồn kho</label>
                <input type="number" name="quantity_in_stock" class="form-control">
                @error('quantity_in_stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control">
                @error('sku')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="product_id" class="form-label">Sản phẩm cha</label>
                <select name="product_id" class="form-control">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id')
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

            <button type="submit" class="btn btn-success">Lưu</button>
            <<a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>

        </form>
    </div>
@endsection

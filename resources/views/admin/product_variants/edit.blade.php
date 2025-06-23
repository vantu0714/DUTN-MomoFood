@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Chỉnh sửa biến thể sản phẩm</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.product_variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Sản phẩm --}}
        <div class="form-group">
            <label for="product_id">Sản phẩm</label>
            <select name="product_id" class="form-control" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" {{ $variant->product_id == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Giá --}}
        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" name="price" class="form-control" min="0" step="1000"
                   value="{{ old('price', $variant->price) }}" required>
        </div>

        {{-- Số lượng --}}
        <div class="form-group">
            <label for="quantity_in_stock">Số lượng</label>
            <input type="number" name="quantity_in_stock" class="form-control" min="0"
                   value="{{ old('quantity_in_stock', $variant->quantity_in_stock) }}" required>
        </div>

        {{-- SKU --}}
        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ old('sku', $variant->sku) }}">
        </div>

        {{-- Hình ảnh --}}
        <div class="form-group">
            <label for="image">Hình ảnh</label><br>
            @if ($variant->image)
                <img src="{{ asset('storage/' . $variant->image) }}" width="100" class="mb-2">
            @endif
            <input type="file" name="image" class="form-control-file">
        </div>

        {{-- Trạng thái --}}
        <div class="form-group">
            <label for="status">Trạng thái</label>
            <select name="status" class="form-control">
                <option value="1" {{ $variant->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ $variant->status == 0 ? 'selected' : '' }}>Ngừng</option>
            </select>
        </div>

        {{-- Thuộc tính chính: Vị --}}
        <div class="form-group">
            <label for="main_attribute_id">Vị</label>
            <select name="main_attribute_id" class="form-control" required>
                @foreach ($attributes->where('name', 'Vị')->first()?->values ?? [] as $value)
                    <option value="{{ $value->id }}"
                        {{ $variant->attributeValues->contains('id', $value->id) ? 'selected' : '' }}>
                        {{ $value->value }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Thuộc tính phụ: Size --}}
        <div class="form-group">
            <label for="sub_attribute_id">Size</label>
            <select name="sub_attribute_id" class="form-control" required>
                @foreach ($attributes->where('name', 'Size')->first()?->values ?? [] as $value)
                    <option value="{{ $value->id }}"
                        {{ $variant->attributeValues->contains('id', $value->id) ? 'selected' : '' }}>
                        {{ $value->value }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nút --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>
        </div>
    </form>
</div>
@endsection

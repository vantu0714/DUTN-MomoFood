@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2>Chỉnh sửa biến thể sản phẩm</h2>

        <form action="{{ route('admin.product_variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
            <div class="form-group">
                <label for="price">Giá</label>
                <input type="number" name="price" class="form-control" value="{{ $variant->price }}" required>
            </div>

            <div class="form-group">
                <label for="quantity_in_stock">Số lượng</label>
                <input type="number" name="quantity_in_stock" class="form-control"
                    value="{{ $variant->quantity_in_stock }}" required>
            </div>

            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ $variant->sku }}">
            </div>

            <div class="form-group">
                <label for="image">Hình ảnh</label>
                @if ($variant->image)
                    <br>
                    <img src="{{ asset('storage/' . $variant->image) }}" width="100">
                @endif
                <input type="file" name="image" class="form-control-file mt-2">
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $variant->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ $variant->status == 0 ? 'selected' : '' }}>Ngừng</option>
                </select>
            </div>

            <h4>Thuộc tính</h4>
            @foreach ($attributes as $attribute)
                <label>{{ $attribute->name }}</label>
                <select name="attribute_values[]" class="form-control">
                    @foreach ($attribute->values as $value)
                        <option value="{{ $value->id }}"
                            {{ $variant->attributeValues->contains('id', $value->id) ? 'selected' : '' }}>
                            {{ $value->value }}
                        </option>
                    @endforeach
                </select>
            @endforeach


            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>
        </form>
    </div>
@endsection

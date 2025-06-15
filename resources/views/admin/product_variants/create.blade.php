@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-2xl font-bold mb-4">Thêm biến thể sản phẩm</h2>

        <form action="{{ route('admin.product_variants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Sản phẩm --}}
            <div class="form-group mb-3">
                <label for="product_id">Sản phẩm</label>
                <select name="product_id" class="form-control" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tên biến thể --}}
            <div class="form-group mb-3">
                <label for="name">Tên biến thể</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            {{-- Giá --}}
            <div class="form-group mb-3">
                <label for="price">Giá</label>
                <input type="number" name="price" class="form-control" required>
            </div>

            {{-- Số lượng --}}
            <div class="form-group mb-3">
                <label for="quantity_in_stock">Số lượng</label>
                <input type="number" name="quantity_in_stock" class="form-control" required>
            </div>

            {{-- SKU --}}
            <div class="form-group mb-3">
                <label for="sku">SKU</label>
                <input type="text" name="sku" class="form-control">
            </div>

            {{-- Hình ảnh --}}
            <div class="form-group mb-3">
                <label for="image">Hình ảnh</label>
                <input type="file" name="image" class="form-control-file">
            </div>

            {{-- Trạng thái --}}
            <div class="form-group mb-3">
                <label for="status">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1">Hoạt động</option>
                    <option value="0">Ngừng</option>
                </select>
            </div>

            {{-- Thuộc tính --}}
            {{-- Thuộc tính --}}
            <h4 class="text-lg font-bold mt-4">Thuộc tính</h4>

            @foreach ($attributes as $attribute)
                <div class="form-group mb-3">
                    <label class="form-label d-block">{{ $attribute->name }}</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($attribute->values as $value)
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="attribute_values[{{ $attribute->id }}]"
                                    id="attr_{{ $attribute->id }}_{{ $value->id }}" value="{{ $value->id }}"
                                    {{ old("attribute_values.{$attribute->id}") == $value->id ? 'checked' : '' }} required>
                                <label class="form-check-label" for="attr_{{ $attribute->id }}_{{ $value->id }}">
                                    {{ $value->value }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach



            {{-- Nút submit --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
@endsection

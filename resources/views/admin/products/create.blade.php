@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm sản phẩm mới</h2>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="product_name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="product_name" class="form-control" value="{{ old('product_name') }}">
                @error('product_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="product_code" class="form-label">Mã sản phẩm</label>
                <input type="text" name="product_code" class="form-control" value="{{ old('product_code') }}">
                @error('product_code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>

                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh sản phẩm</label>
                <input type="file" name="image" class="form-control">
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="original_price" class="form-label">Giá gốc</label>
                <input type="number" step="0.01" name="original_price" class="form-control"
                    value="{{ old('original_price') }}">
                @error('original_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="discounted_price" class="form-label">Giá khuyến mãi</label>
                <input type="number" step="0.01" name="discounted_price" class="form-control"
                    value="{{ old('discounted_price') }}">
                @error('discounted_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng</label>
                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" min="0">
                @error('quantity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Huỷ</a>

            {{-- Biến thể sản phẩm --}}
            <hr>
            <h4>Biến thể sản phẩm</h4>
            <div id="variant-container">
                <div class="variant-item border p-3 mb-3">
                    <div class="mb-2">
                        <label>Tên biến thể</label>
                        <input type="text" name="variants[0][name]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Giá</label>
                        <input type="number" step="0.01" name="variants[0][price]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Số lượng</label>
                        <input type="number" name="variants[0][quantity_in_stock]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>SKU</label>
                        <input type="text" name="variants[0][sku]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Trạng thái</label>
                        <select name="variants[0][status]" class="form-control">
                            <option value="1">Hiển thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary mb-3" id="add-variant">+ Thêm biến thể</button>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Huỷ</a>

        </form>
    </div>

    {{-- Thêm JS để nhân bản block biến thể --}}
    <script>
        let variantIndex = 1;

        document.getElementById('add-variant').addEventListener('click', function() {
            const container = document.getElementById('variant-container');
            const template = `
            <div class="variant-item border p-3 mb-3">
                <div class="mb-2"><label>Tên biến thể</label>
                    <input type="text" name="variants[${variantIndex}][name]" class="form-control"></div>
                <div class="mb-2"><label>Giá</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control"></div>
                <div class="mb-2"><label>Số lượng</label>
                    <input type="number" name="variants[${variantIndex}][quantity_in_stock]" class="form-control"></div>
                <div class="mb-2"><label>SKU</label>
                    <input type="text" name="variants[${variantIndex}][sku]" class="form-control"></div>
                <div class="mb-2"><label>Trạng thái</label>
                    <select name="variants[${variantIndex}][status]" class="form-control">
                        <option value="1">Hiển thị</option>
                        <option value="0">Ẩn</option>
                    </select>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', template);
            variantIndex++;
        });
    </script>
    </form>
    </div>
@endsection

@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Thêm sản phẩm mới</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form id="product-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
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
                <input type="number" step="0.01" name="original_price" id="original_price" class="form-control"
                    value="{{ old('original_price') }}">
                @error('original_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">% Giảm giá</label>
                <input type="number" step="0.01" id="discount_percent" class="form-control"
                    placeholder="Nhập % giảm (VD: 20)">
            </div>

            <div class="mb-3">
                <label class="form-label">Giá khuyến mãi</label>
                <input type="number" step="0.1" name="discounted_price" id="discounted_price" class="form-control"
                    value="{{ old('discounted_price') }}" readonly>
                @error('discounted_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Số lượng</label>
                <input type="number" name="quantity_in_stock" class="form-control"
                    value="{{ old('quantity_in_stock', 0) }}" min="0">
                @error('quantity_in_stock')
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
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const originalInput = document.getElementById('original_price');
        const percentInput = document.getElementById('discount_percent');
        const discountInput = document.getElementById('discounted_price');

        function updateDiscountedPrice() {
            const original = parseFloat(originalInput.value);
            const percent = parseFloat(percentInput.value);

            if (!isNaN(original) && !isNaN(percent)) {
                const discounted = original * (1 - percent / 100);
                discountInput.value = discounted.toFixed(2);
            } else {
                discountInput.value = '';
            }
        }

        originalInput.addEventListener('input', updateDiscountedPrice);
        percentInput.addEventListener('input', updateDiscountedPrice);

        document.getElementById('product-form').addEventListener('submit', function(e) {
            const original = parseFloat(originalInput.value);
            const discount = parseFloat(discountInput.value);

            if (!isNaN(original) && !isNaN(discount)) {
                if (discount > original) {
                    e.preventDefault();
                    alert('Giá khuyến mãi không được lớn hơn giá gốc!');
                }
            }
        });
    </script>
@endpush

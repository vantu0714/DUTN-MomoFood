@extends('layouts.admin')

@section('content')
    <h1>Thêm thành phần Combo</h1>

    <form action="{{ route('admin.combo_items.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Combo</label>
            <select name="combo_id" class="form-control" required>
                <option value="">-- Chọn combo --</option>
                @foreach($combos as $combo)
                    <option value="{{ $combo->id }}">{{ $combo->product_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Loại thành phần</label>
            <select name="itemable_type" id="itemable_type" class="form-control" required>
                <option value="">-- Chọn loại --</option>
                <option value="product">Sản phẩm</option>
                <option value="variant">Biến thể</option>
            </select>
        </div>

        <div class="mb-3" id="product_select" style="display: none;">
            <label>Sản phẩm</label>
            <select name="itemable_id_product" class="form-control">
                <option value="">-- Chọn sản phẩm --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3" id="variant_select" style="display: none;">
            <label>Biến thể</label>
            <select name="itemable_id_variant" class="form-control">
                <option value="">-- Chọn biến thể --</option>
                @foreach($variants as $v)
                    <option value="{{ $v->id }}">{{ $v->variant_name ?? $v->id }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="itemable_id" id="itemable_id_hidden">

        <div class="mb-3">
            <label>Số lượng</label>
            <input type="number" name="quantity" class="form-control" required min="1">
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.combo_items.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('itemable_type').addEventListener('change', function () {
        let type = this.value;
        document.getElementById('product_select').style.display = type === 'product' ? 'block' : 'none';
        document.getElementById('variant_select').style.display = type === 'variant' ? 'block' : 'none';
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        let type = document.getElementById('itemable_type').value;
        let idField = type === 'product' 
            ? document.querySelector('[name="itemable_id_product"]') 
            : document.querySelector('[name="itemable_id_variant"]');
        document.getElementById('itemable_id_hidden').value = idField.value;
    });
</script>
@endpush

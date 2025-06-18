@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2>Thêm biến thể cho sản phẩm: <strong>{{ $product->product_name }}</strong></h2>

        <form method="POST" action="{{ route('admin.product_variants.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div id="variants-container">
                <div class="variant-item border p-3 mb-4">
                    <h5>Biến thể #1</h5>

                    <div class="attribute-group mb-3">
                        <label>Thuộc tính & Giá trị</label>
                        <div class="row attribute-row mb-2">
                            <div class="col-md-5">
                                <input type="text" name="variants[0][attributes][0][name]" class="form-control"
                                    placeholder="Tên thuộc tính ">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="variants[0][attributes][0][value]" class="form-control"
                                    placeholder="Giá trị ">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-attribute">Xoá</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary add-attribute">+ Thêm thuộc tính</button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Giá</label>
                            <input type="number" name="variants[0][price]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Số lượng</label>
                            <input type="number" name="variants[0][quantity_in_stock]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>SKU</label>
                            <input type="text" name="variants[0][sku]" class="form-control" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger remove-variant">Xoá biến thể</button>
                </div>
            </div>

            <button type="button" class="btn btn-info mb-3" id="add-variant">+ Thêm biến thể</button>
            <br>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Huỷ</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let variantIndex = 1;

        // Gán sự kiện sau khi DOM sẵn sàng
        document.addEventListener('DOMContentLoaded', function() {

            // Nút thêm biến thể
            document.getElementById('add-variant').addEventListener('click', function() {
                const container = document.getElementById('variants-container');
                const template = document.querySelector('.variant-item');
                const clone = template.cloneNode(true);

                // Tăng chỉ số biến thể
                clone.querySelector('h5').textContent = `Biến thể #${variantIndex + 1}`;

                // Reset input & cập nhật name
                const inputs = clone.querySelectorAll('input');
                inputs.forEach(input => {
                    input.value = '';

                    let name = input.getAttribute('name');
                    if (!name) return;

                    // Lấy tên trường cuối cùng: price, quantity_in_stock, sku, attributes
                    const fieldMatch = name.match(/\[(\w+)]$/);
                    const field = fieldMatch ? fieldMatch[1] : '';

                    // Nếu là attribute thì cần cả chỉ số attribute
                    if (name.includes('[attributes]')) {
                        name = `variants[${variantIndex}][attributes][0][${field}]`;
                    } else {
                        name = `variants[${variantIndex}][${field}]`;
                    }

                    input.setAttribute('name', name);
                });;


                // Xoá các attribute-row trừ dòng đầu tiên
                const attrRows = clone.querySelectorAll('.attribute-row');
                for (let i = 1; i < attrRows.length; i++) {
                    attrRows[i].remove();
                }

                container.appendChild(clone);
                variantIndex++;
            });

            // Sự kiện xoá biến thể
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-variant')) {
                    const variants = document.querySelectorAll('.variant-item');
                    if (variants.length > 1) {
                        e.target.closest('.variant-item').remove();
                    }
                }
            });

            // Thêm thuộc tính
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-attribute')) {
                    const variantItem = e.target.closest('.variant-item');
                    const group = variantItem.querySelector('.attribute-group');
                    const attrRows = group.querySelectorAll('.attribute-row');
                    const lastAttr = attrRows[attrRows.length - 1];
                    const newRow = lastAttr.cloneNode(true);

                    // Tìm index biến thể
                    const variantIdx = variantItem.querySelector('input[name^="variants["]').name.match(
                        /variants\[(\d+)]/)[1];
                    const attrIdx = attrRows.length;

                    // Cập nhật name cho inputs trong row mới
                    newRow.querySelectorAll('input').forEach((input, index) => {
                        input.value = '';
                        input.name =
                            `variants[${variantIdx}][attributes][${attrIdx}][${index === 0 ? 'name' : 'value'}]`;
                    });

                    group.insertBefore(newRow, e.target);
                }
            });

            // Xoá thuộc tính
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-attribute')) {
                    const attrGroup = e.target.closest('.attribute-group');
                    const rows = attrGroup.querySelectorAll('.attribute-row');
                    if (rows.length > 1) {
                        e.target.closest('.attribute-row').remove();
                    }
                }
            });
        });
    </script>
@endpush

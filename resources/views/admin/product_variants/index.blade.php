@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách biến thể sản phẩm</h2>

    <a href="{{ route('admin.product_variants.create') }}" class="btn btn-success mb-3">Thêm biến thể</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Tên biến thể</th>
                <th>Hình ảnh</th>
                <th>Giá</th>
                <th>SKU</th>
                <th>Số lượng</th>
                <th>Trạng thái</th>
                <th>Thuộc tính</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->id }}</td>
                <td>{{ $variant->product->product_name ?? 'Không có' }}</td>
                <td>{{ $variant->name }}</td>
                <td>
                    @if($variant->image)
                        <img src="{{ asset('storage/' . $variant->image) }}" alt="Ảnh" width="60">
                    @else
                        Không có ảnh
                    @endif
                </td>
                <td>{{ $variant->formatted_price }}</td>
                <td>{{ $variant->sku }}</td>
                <td>{{ $variant->quantity_in_stock }}</td>
                <td>
                    @if($variant->status)
                        <span class="badge badge-success">Hoạt động</span>
                    @else
                        <span class="badge badge-danger">Ngừng</span>
                    @endif
                </td>
                <td>
                    @if ($variant->attributeValues && count($variant->attributeValues))
                        @foreach($variant->attributeValues as $val)
                            <strong>{{ $val->attribute->name ?? 'Không rõ' }}:</strong> {{ $val->value }}<br>
                        @endforeach
                    @else
                        <em>Không có thuộc tính</em>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.product_variants.edit', $variant->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('admin.product_variants.destroy', $variant->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn chắc chắn muốn xoá?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $variants->links() }} {{-- Phân trang --}}
</div>
@endsection

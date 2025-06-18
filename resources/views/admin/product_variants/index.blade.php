@php $disableMapScript = true; @endphp
@extends('admin.layouts.app')
@section('content')
<div class="container">
    <h2>Danh sách biến thể sản phẩm</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Mã SP</th>
                <th>Sản phẩm</th>
                <th>Biến thể</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->sku }}</td>
                <td>{{ $variant->product->product_name ?? 'Không có' }}</td>
                <td>
                    @if ($variant->attributeValues && count($variant->attributeValues))
                        @foreach($variant->attributeValues as $val)
                            {{ $val->value }}{{ !$loop->last ? ' - ' : '' }}
                        @endforeach
                    @else
                        <em>Không có thuộc tính</em>
                    @endif
                </td>
                <td>{{ number_format($variant->price, 0, ',', '.') }} đ</td>
                <td>{{ $variant->quantity_in_stock }}</td>
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

    {{ $variants->links() }}
</div>
@endsection

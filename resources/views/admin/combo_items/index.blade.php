@php $disableMapScript = true; @endphp
@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách thành phần Combo</h2>
    <a href="{{ route('admin.combo_items.create') }}" class="btn btn-success mb-3">Thêm thành phần</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Combo</th>
                <th>Thành phần</th>
                <th>Loại</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comboItems as $item)
            <tr>
                <td>{{ $item->combo->product_name ?? 'Không rõ' }}</td>
                <td>
                    {{ $item->itemable->product_name ?? $item->itemable->variant_name ?? 'Không rõ' }}
                </td>
                <td>{{ class_basename($item->itemable_type) === 'Product' ? 'Sản phẩm' : 'Biến thể' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                    {{-- Nếu bạn muốn thêm chức năng sửa thì thêm route edit --}}
                    {{-- <a href="{{ route('admin.combo_items.edit', $item->id) }}" class="btn btn-sm btn-primary">Sửa</a> --}}
                    <form action="{{ route('admin.combo_items.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn chắc chắn muốn xoá?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $comboItems->links() }}
</div>
@endsection

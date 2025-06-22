@php $disableMapScript = true; @endphp
@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h2>Danh sách thành phần Combo</h2>
        <a href="{{ route('admin.combo_items.create') }}" class="btn btn-success mb-3">Thêm Combo</a>

        @php
            // Nhóm các thành phần theo combo_id
            $grouped = $comboItems->groupBy('combo_id');
        @endphp

        <table class="table table-bordered table-striped">
            <thead>
                <tr class="table-secondary">
                    <th>Combo</th>
                    <th>Thành phần</th>
                    <th>Loại</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grouped as $comboId => $items)
                    {{-- Dòng tiêu đề của Combo --}}
                    <tr class="table-primary">
                        <td colspan="4">
                            <strong>Combo:</strong> {{ $items->first()->combo->product_name ?? 'Không rõ' }}
                        </td>
                        <td>
                            <form action="{{ route('admin.combo_items.delete_combo', $comboId) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xoá toàn bộ combo này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xoá Combo</button>
                            </form>
                        </td>
                    </tr>


                    {{-- Các thành phần bên trong Combo --}}
                    @foreach ($items as $item)
                        <tr>
                            <td></td> {{-- Cột Combo để trống vì đã hiển thị phía trên --}}
                            <td>
                                @if ($item->itemable_type === \App\Models\Product::class)
                                    {{ $item->itemable->product_name ?? 'Không rõ' }}
                                @elseif ($item->itemable_type === \App\Models\ProductVariant::class)
                                    {{ $item->itemable->product->product_name ?? 'Không rõ' }} -
                                    @foreach ($item->itemable->attributeValues as $val)
                                        {{ $val->value }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                @else
                                    Không rõ
                                @endif
                            </td>
                            <td>
                                {{ class_basename($item->itemable_type) === 'Product' ? 'Sản phẩm' : 'Biến thể' }}
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                <form action="{{ route('admin.combo_items.destroy', $item->id) }}" method="POST"
                                    style="display:inline-block;" onsubmit="return confirm('Bạn chắc chắn muốn xoá?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5">Không có thành phần combo nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Hiển thị phân trang nếu có --}}
        <div class="mt-3">
            {{ $comboItems->links() }}
        </div>
    </div>
@endsection

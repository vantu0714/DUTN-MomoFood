@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Danh sách thành phần Combo</h2>
            <a href="{{ route('admin.combo_items.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm Combo
            </a>
        </div>

        @forelse ($grouped as $comboId => $items)
            @php
                $combo = $items->first()->combo;
            @endphp

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Combo:</strong> {{ $combo->product_name ?? 'Không rõ' }}
                    </div>
                    <div>
                        <a href="{{ route('admin.combo_items.create', ['combo_id' => $comboId]) }}"
                            class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form action="{{ route('admin.combo_items.delete_combo', $comboId) }}" method="POST"
                            class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá combo này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Xoá
                            </button>
                        </form>

                    </div>
                </div>

                <div class="card-body">
                    <p class="mb-2 text-muted">
                        <strong>Giá gốc:</strong>
                        <span class="text-decoration-line-through">
                            {{ number_format($combo->original_price ?? 0) }} đ
                        </span>
                        &nbsp;&nbsp;
                        <strong>Giá bán:</strong>
                        <span class="text-success fw-bold">
                            {{ number_format($combo->discounted_price ?? 0) }} đ
                        </span>
                    </p>

                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Thành phần</th>
                                <th>Loại</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach ($items as $index => $item)
                                @php
                                    $typeLabel =
                                        class_basename($item->itemable_type) === 'Product'
                                            ? 'Sản phẩm đơn'
                                            : 'Biến thể';
                                    $name = $item->itemable->product_name ?? ($item->itemable->name ?? 'Không rõ');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $name }}</td>
                                    <td>{{ $typeLabel }}</td>
                                    <td>{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Hiện chưa có thành phần nào trong combo.
            </div>
        @endforelse
    </div>
@endsection

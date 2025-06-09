@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Chi tiết mã giảm giá</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            {{ $promotion->promotion_name }}
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th>Loại giảm:</th>
                        <td>{{ ucfirst($promotion->discount_type) }}</td>
                    </tr>
                    <tr>
                        <th>Giá trị giảm:</th>
                        <td>
                            @if($promotion->discount_type === 'percent')
                                {{ $promotion->discount_value }}%
                            @else
                                {{ number_format($promotion->discount_value, 0, ',', '.') }}₫
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Giảm tối đa:</th>
                        <td>
                            @if($promotion->max_discount_value)
                                {{ number_format($promotion->max_discount_value, 0, ',', '.') }}₫
                            @else
                                Không giới hạn
                            @endif
                        </td>
                    <tr>
                        <th>Ngày bắt đầu:</th>
                        <td>{{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Ngày kết thúc:</th>
                        <td>{{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Mô tả:</th>
                        <td>{{ $promotion->description ?? 'Không có mô tả' }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if(\Carbon\Carbon::now()->between(\Carbon\Carbon::parse($promotion->start_date), \Carbon\Carbon::parse($promotion->end_date)))
                                <span class="badge bg-success">Đang hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Không hoạt động</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('promotions.index') }}" class="btn btn-secondary">Quay về danh sách</a>
            <a href="{{ route('promotions.edit', $promotion->id) }}" class="btn btn-warning">Sửa</a>
        </div>
    </div>
</div>
@endsection

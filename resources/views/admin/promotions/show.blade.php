@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Chi tiết mã giảm giá</h2>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                {{ $promotion->promotion_name }}
            </div>

            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>Loại giảm:</th>
                            <td>{{ $promotion->discount_type === 'percent' ? 'Giảm phần trăm' : 'Giảm số tiền' }}</td>
                        </tr>

                        <tr>
                            <th>Giá trị giảm:</th>
                            <td>
                                @if ($promotion->discount_type === 'percent')
                                    {{ $promotion->discount_value }}%
                                @else
                                    {{ number_format($promotion->discount_value, 0, ',', '.') }}₫
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Giảm tối đa:</th>
                            <td>
                                @if ($promotion->max_discount_value)
                                    {{ number_format($promotion->max_discount_value, 0, ',', '.') }}₫
                                @else
                                    Không giới hạn
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Ngày bắt đầu:</th>
                            <td>{{ $promotion->start_date->format('d/m/Y H:i') }}</td>
                        </tr>

                        <tr>
                            <th>Ngày kết thúc:</th>
                            <td>{{ $promotion->end_date->format('d/m/Y H:i') }}</td>
                        </tr>

                        <tr>
                            <th>Số lượt sử dụng:</th>
                            <td>{{ $promotion->used_count ?? 0 }}</td>
                        </tr>

                        <tr>
                            <th>Giới hạn lượt sử dụng:</th>
                            <td>
                                {{ $promotion->usage_limit ?? 'Không giới hạn' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Mô tả:</th>
                            <td>{{ $promotion->description ?? 'Không có mô tả' }}</td>
                        </tr>

                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                @php
                                    $now = now();
                                    $isValidTime = $now->between($promotion->start_date, $promotion->end_date);
                                    $isActive = $promotion->status == 1;
                                    $limitOK =
                                        is_null($promotion->usage_limit) ||
                                        $promotion->used_count < $promotion->usage_limit;
                                @endphp

                                @if ($isActive && $isValidTime && $limitOK)
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

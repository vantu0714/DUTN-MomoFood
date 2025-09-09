@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">
            <i class="fas fa-ticket-alt text-primary me-2"></i>
            Chi tiết mã giảm giá
        </h2>

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-tags me-2"></i> {{ $promotion->promotion_name }}</span>
                @php
                    $now = now();
                    $isValidTime = $now->between($promotion->start_date, $promotion->end_date);
                    $isActive = $promotion->status == 1;
                    $limitOK = is_null($promotion->usage_limit) || $promotion->used_count < $promotion->usage_limit;
                @endphp
                <span class="badge {{ $isActive && $isValidTime && $limitOK ? 'bg-success' : 'bg-secondary' }}">
                    {{ $isActive && $isValidTime && $limitOK ? 'Đang hoạt động' : 'Không hoạt động' }}
                </span>
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="w-25 text-nowrap">Tên mã giảm giá:</th>
                            <td>{{ $promotion->promotion_name }}</td>
                        </tr>

                        <tr>
                            <th>Mã code:</th>
                            <td>{{ $promotion->code }}</td>
                        </tr>

                        <tr>
                            <th>Loại giảm:</th>
                            <td>
                                {{ $promotion->discount_type === 'percent' ? 'Giảm phần trăm' : 'Giảm số tiền' }}
                            </td>
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
                                @if ($promotion->discount_type === 'percent' && $promotion->max_discount_value)
                                    {{ number_format($promotion->max_discount_value, 0, ',', '.') }}₫
                                @else
                                    Không có
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Tổng đơn hàng tối thiểu:</th>
                            <td>
                                @if ($promotion->min_total_spent)
                                    {{ number_format($promotion->min_total_spent, 0, ',', '.') }}₫
                                @else
                                    Không yêu cầu
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Số lượt đã sử dụng:</th>
                            <td>{{ $promotion->used_count ?? 0 }}</td>
                        </tr>

                        <tr>
                            <th>Số lượng:</th>
                            <td>{{ $promotion->usage_limit ?? 'Không giới hạn' }}</td>
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
                            <th>Mô tả:</th>
                            <td>{{ $promotion->description ?? 'Không có mô tả' }}</td>
                        </tr>

                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                @if ($promotion->status)
                                    <span class="badge bg-success">Đang bật</span>
                                @else
                                    <span class="badge bg-secondary">Đang tắt</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light d-flex justify-content-end gap-2">
                <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay về danh sách
                </a>
                <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Sửa
                </a>
            </div>
        </div>
    </div>
@endsection

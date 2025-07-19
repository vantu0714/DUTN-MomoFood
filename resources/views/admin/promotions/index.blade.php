@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-tags me-2 text-primary"></i>
                    Quản lý mã giảm giá
                </h1>
                <p class="text-muted mb-0">Danh sách mã giảm giá hiện có</p>
            </div>
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm shadow">
                <i class="fas fa-plus me-1"></i>
                Thêm mã giảm giá
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-gift me-2 text-primary"></i>
                    Danh sách mã giảm giá
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Tên mã</th>
                                <th class="border-0">Mã code</th>
                                <th class="border-0">Loại giảm</th>
                                <th class="border-0">Giá trị</th>
                                <th class="border-0">Ngày</th>
                                <th class="border-0">Trạng thái</th>
                                <th class="border-0 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($promotions as $promotion)
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $isActive =
                                        $promotion->status &&
                                        $now->between($promotion->start_date, $promotion->end_date);

                                    $typeText = [
                                        'fixed' => 'Giảm theo số tiền',
                                        'percent' => 'Giảm theo phần trăm',
                                    ];
                                @endphp
                                <tr class="align-middle">
                                    <td>#{{ $promotion->id }}</td>
                                    <td class="fw-semibold text-primary">{{ $promotion->promotion_name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase">{{ $promotion->code }}</span>
                                    </td>

                                    <td>
                                        <span class="badge bg-info text-dark text-uppercase">
                                            {{ $typeText[$promotion->discount_type] ?? $promotion->discount_type }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($promotion->discount_type === 'percent')
                                            {{ $promotion->discount_value }}%
                                        @else
                                            {{ number_format($promotion->discount_value) }}đ
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $promotion->start_date->format('d/m/Y') }} →
                                            {{ $promotion->end_date->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        @if ($isActive)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('admin.promotions.show', $promotion->id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.promotions.destroy', $promotion->id) }}"
                                                method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Chưa có mã giảm giá nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $promotions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

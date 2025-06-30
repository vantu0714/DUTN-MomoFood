@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh sách mã giảm giá</h2>

        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary mb-3">Thêm mã giảm giá</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tên</th>
                        <th>Loại giảm</th>
                        <th>Giá trị</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        <tr>
                            <td>{{ $promotion->promotion_name }}</td>
                            <td>
                                @if ($promotion->discount_type === 'fixed')
                                    Giảm theo số tiền
                                @elseif ($promotion->discount_type === 'percent')
                                    Giảm theo %
                                @else
                                    Không xác định
                                @endif
                            </td>
                            <td>{{ $promotion->discount_value }}</td>
                            <td>{{ $promotion->start_date }}</td>
                            <td>{{ $promotion->end_date }}</td>
                            <td>
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $isActive = $promotion->status && $now->between($promotion->start_date, $promotion->end_date);
                                @endphp
                
                                @if ($isActive)
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.promotions.show', $promotion->id) }}" class="btn btn-info btn-sm">Xem</a>
                                <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                                    class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Không có mã giảm giá nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            

            <div class="d-flex justify-content-center">
                {{ $promotions->links() }}
            </div>
        </div>
    </div>
@endsection



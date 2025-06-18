@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh sách mã giảm giá</h2>

        <a href="{{ route('promotions.create') }}" class="btn btn-primary mb-3">Thêm mã giảm giá</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tên</th>
                        <th>Loại giảm</th>
                        <th>Giá trị</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        <tr>
                            <td>{{ $promotion->promotion_name }}</td>
                            <td>{{ ucfirst($promotion->discount_type) }}</td>
                            <td>{{ $promotion->discount_value }}</td>
                            <td>
                                @if ($promotion->isActive())
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('promotions.show', $promotion->id) }}" class="btn btn-info btn-sm">Xem</a>
                                <a href="{{ route('promotions.edit', $promotion->id) }}"
                                    class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('promotions.destroy', $promotion->id) }}" method="POST"
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

@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Quản lý sản phẩm</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </a>

        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Mô tả</th>
                    <th>Ảnh</th>
                    <th>Thành phần</th>
                    <th>ngày hết hạn</th>
                    <th>giá gốc</th>
                    <th>Giảm giá</th>
                    <th>Trạng thái</th>
                    <th>Lượt xem</th>
                    <th>Hiển thị</th>
                    <th>Ngày tạo</th>
                    <th>Cập nhật</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item->category->category_name }}</td>
                        <td>{{ $item['description'] }}</td>
                        <td>
                            <img src="{{ asset('storage') . '/' . $item['image'] }}" width="90" alt="">

                        </td>
                        <td>{{ $item['ingredients'] }}</td>
                        <td>{{ $item['expiration_date'] }}</td>
                        <td>{{ $item['original_price'] }}</td>
                        <td>{{ $item['discounted_price'] }}</td>
                        <td>
                            <?= $item['status'] == 'Còn hàng' ? '<span style="color: green;">Còn hàng</span>' : '<span style="color: red;">Hết hàng</span>' ?>
                        </td>
                        <td>{{ $item['view'] }}</td>
                        <td>{{ $item['is_show_home'] }}</td>
                        <td>{{ $item['created_at'] }}</td>
                        <td>{{ $item['updated_at'] }}</td>
                        <td>
                             <a href="" class="btn btn-sm btn-info">Xem</a>
                        <a href="" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="" method="POST" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
@endsection

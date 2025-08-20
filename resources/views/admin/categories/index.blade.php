@extends('admin.layouts.app') 

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Quản lý danh mục</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Thêm danh mục
    </a>

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Cập nhật</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->category_name }}</td>
                    <td>{{ $cat->description }}</td>
                    <td>
                        <span class="badge bg-{{ $cat->status ? 'success' : 'secondary' }}">
                            {{ $cat->status ? 'Hiển thị' : 'Ẩn' }}
                        </span>
                    </td>
                    <td>{{ $cat->created_at->format('d/m/Y') }}</td>
                    <td>{{ $cat->updated_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.categories.show', $cat->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-warning"> <i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"> <i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>
@endsection

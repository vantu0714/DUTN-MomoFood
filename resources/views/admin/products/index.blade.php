@extends('admin.layouts.app') 

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Quản lý danh mục</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Thêm danh mục
    </a>

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Danh mục cha</th> {{-- thêm dòng này --}}
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Cập nhật</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        
        </tbody>

    </table>

</div>
@endsection

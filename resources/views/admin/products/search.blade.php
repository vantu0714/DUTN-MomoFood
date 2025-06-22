@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Kết quả cho từ khóa: "{{ $keyword }}"</h4>

    @if($products->count())
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->product_name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->product_name }}</h5>
                            <p class="text-danger fw-bold">{{ number_format($product->discounted_price) }} đ</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>Không tìm thấy sản phẩm nào phù hợp.</p>
    @endif
</div>
@endsection

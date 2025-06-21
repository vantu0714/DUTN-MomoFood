
@include('clients.layouts.header')
@include('clients.layouts.sidebar')


<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0">

        @yield('content')
        <div class="container py-4">
    <h4>Kết quả cho từ khóa: "{{ $keyword }}"</h4>

    @if($products->count())
        <div class="row">
                                       @foreach ($products as $product)
                                <div class="col-md-6 col-lg-4 d-flex">
                                    <div class="product-card w-100 d-flex flex-column position-relative">
                                        <div class="product-image">
                                            <a href="{{ route('product-detail.index', $product->id) }}">
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    onerror="this.onerror=null;this.src='{{ asset('clients/img/default.jpg') }}';"
                                                    class="img-fluid w-100 rounded-top"
                                                    alt="{{ $product->product_name }}">
                                                    
                                            </a>
                                        </div>

                                        <div class="badge bg-secondary text-white position-absolute px-3 py-1"
                                            style="top: 10px; left: 10px;">
                                            {{ $product->category?->category_name ?? 'Không có danh mục' }}
                                        </div>

                                        <div
                                            class="product-body p-3 border border-secondary border-top-0 rounded-bottom d-flex flex-column justify-content-between flex-grow-1">
                                            <div>
                                                <h5 class="product-title">{{ $product->product_name }}</h5>
                                                <p class="product-description">
                                                    {{ $product->description ?? 'No description available.' }}
                                                </p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <p class="text-dark fs-5 fw-bold mb-0">
                                                    {{ number_format($product->discounted_price, 0, ',', '.') }} VND
                                                </p>
                                                <form action="{{ route('carts.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id"
                                                        value="{{ $product->id }}">
                                                    <button type="submit"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i>Thêm vào giỏ
                                                        hàng
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
        </div>
    @else
        <p class="text-muted">Không tìm thấy sản phẩm nào phù hợp.</p>
    @endif
</div>

    </div>
</div>


@include('clients.layouts.footer')
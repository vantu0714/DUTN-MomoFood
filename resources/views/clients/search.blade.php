@include('clients.layouts.header')
@include('clients.layouts.sidebar')

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0">

        @yield('content')
        <div class="container py-4" style="margin-top: 150px">
            <h4 class="mb-4">Kết quả cho từ khóa: "{{ $keyword }}"</h4>

            @php
                $availableProducts = $products->where('quantity_in_stock', '>', 0);
            @endphp

            @if ($availableProducts->count())
                <div class="row g-3">
                    @foreach ($availableProducts as $product)
                        <div class="col-md-4 col-lg-3">
                            <div class="product-card h-100 d-flex flex-column position-relative shadow-sm">
                                <div class="product-image overflow-hidden" style="height: 200px;">
                                    <a href="{{ route('product-detail.show', $product->id) }}" class="h-100 d-block">
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            onerror="this.onerror=null;this.src='{{ asset('clients/img/default.jpg') }}';"
                                            class="img-fluid w-100 h-100 object-fit-cover rounded-top" <!-- Thêm
                                            object-fit-cover -->
                                        alt="{{ $product->product_name }}">
                                    </a>
                                </div>

                                <div class="badge bg-secondary text-white position-absolute px-2 py-1"
                                    style="top: 10px; left: 10px; font-size: 0.8rem;">
                                    {{ $product->category?->category_name ?? 'Không có danh mục' }}
                                </div>

                                <div
                                    class="product-body p-3 border border-secondary border-top-0 rounded-bottom d-flex flex-column flex-grow-1">
                                    <div class="mb-2">
                                        <h5 class="product-title fs-6 mb-1">{{ $product->product_name }}</h5>
                                        <p class="product-description small text-muted mb-2" style="font-size: 0.8rem;">
                                            {{ Str::limit($product->description ?? 'Không có mô tả', 60) }}
                                        </p>
                                    </div>
                                    <div class="mt-auto">
                                        <p class="text-dark fw-bold mb-2">
                                            {{ number_format($product->discounted_price, 0, ',', '.') }} ₫
                                        </p>
                                        <form action="{{ route('carts.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit"
                                                class="btn btn-sm border border-secondary rounded-pill px-3 text-primary w-100">
                                                <i class="fa fa-shopping-bag me-1 text-primary"></i>Thêm vào giỏ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">Không tìm thấy sản phẩm nào phù hợp hoặc sản phẩm đã hết hàng.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('clients.layouts.footer')

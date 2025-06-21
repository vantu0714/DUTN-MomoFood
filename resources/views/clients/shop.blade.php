@include('clients.layouts.header')
@include('clients.layouts.sidebar')
@vite('resources/css/shop.css')


<!-- Modal Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="input-group w-75 mx-auto d-flex">
                    <input type="search" class="form-control p-3" placeholder="keywords"
                        aria-describedby="search-icon-1">
                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Search End -->


<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cửa hàng</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="#">Trang</a></li>
        <li class="breadcrumb-item active text-white">Cửa hàng</li>
    </ol>
</div>
<!-- Single Page Header End -->


<!-- Fruits Shop Start-->
<div class="container-fluid fruite py-5">
    <div class="container py-5">
        <h1 class="mb-4">Cửa hàng đồ ăn vặt</h1>
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="row g-4">
                    <div class="col-xl-3">
                        <div class="input-group w-100 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="Tìm kiếm"
                                aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                    <div class="col-6"></div>
                    <div class="col-xl-3">
                        <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                            <label for="fruits">Sắp xếp mặc định:</label>
                            <select id="fruits" name="fruitlist" class="border-0 form-select-sm bg-light me-3"
                                form="fruitform">
                                <option value="volvo">Sản phẩm đang cập nhật</option>
                                <option value="saab">Được yêu thích</option>
                                <option value="opel">Đồ ăn phổ biến</option>
                                <option value="audi">Đồ uống siêu hot</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-3">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h4>Danh mục sản phẩm</h4>
                                    <ul class="list-unstyled fruite-categorie">
                                        @foreach ($categories as $category)
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="{{ route('shop.category', $category->id) }}">
                                                        <i
                                                            class="fas fa-apple-alt me-2"></i>{{ $category->category_name }}
                                                    </a>
                                                    <span>({{ $category->products_count ?? 0 }})</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h4 class="mb-2">Giá</h4>
                                    <input type="range" class="form-range w-100" id="rangeInput" name="rangeInput"
                                        min="0" max="500" value="0"
                                        oninput="amount.value=rangeInput.value">
                                    <output id="amount" name="amount" min-velue="0" max-value="500"
                                        for="rangeInput">0</output>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h4>Thêm vào</h4>
                                    <div class="mb-2">
                                        <input type="radio" class="me-2" id="Categories-1" name="category"
                                            value="Organic">
                                        <label for="Categories-1">Organic</label>
                                    </div>
                                    <div class="mb-2">
                                        <input type="radio" class="me-2" id="Categories-2" name="category"
                                            value="Fresh">
                                        <label for="Categories-2">Fresh</label>
                                    </div>
                                    <div class="mb-2">
                                        <input type="radio" class="me-2" id="Categories-3" name="category"
                                            value="Sales">
                                        <label for="Categories-3">Sales</label>
                                    </div>
                                    <div class="mb-2">
                                        <input type="radio" class="me-2" id="Categories-4" name="category"
                                            value="Discount">
                                        <label for="Categories-4">Discount</label>
                                    </div>
                                    <div class="mb-2">
                                        <input type="radio" class="me-2" id="Categories-5" name="category"
                                            value="Expired">
                                        <label for="Categories-5">Expired</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <h4 class="mb-3">SẢN PHẨM NỔI BẬT</h4>
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="rounded me-4" style="width: 100px; height: 100px;">
                                        <img src="{{ asset('storage/products/6bdnSqSQ20goGJtQqRsBDoYPY2538avaRiqRLmOO.jpg') }}"
                                            class="img-fluid rounded" alt="">
                                    </div>
                                    <div>
                                        <h6 class="mb-2">Cơm gà sốt tiêu</h6>
                                        <div class="d-flex mb-2">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <h5 class="fw-bold me-2"> 100.000vnđ</h5>
                                            <h5 class="text-danger text-decoration-line-through">150.000vnđ</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="rounded me-4" style="width: 100px; height: 100px;">
                                        <img src="{{ asset('storage/products/6bdnSqSQ20goGJtQqRsBDoYPY2538avaRiqRLmOO.jpg') }}"
                                            class="img-fluid rounded" alt="">
                                    </div>
                                    <div>
                                        <h6 class="mb-2">Combo gà nước</h6>
                                        <div class="d-flex mb-2">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <h5 class="fw-bold me-2">170.00vnđ</h5>
                                            <h5 class="text-danger text-decoration-line-through">220.000vnđ</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="rounded me-4" style="width: 100px; height: 100px;">
                                        <img src="{{ asset('storage/products/6bdnSqSQ20goGJtQqRsBDoYPY2538avaRiqRLmOO.jpg') }}"
                                            class="img-fluid rounded" alt="">
                                    </div>
                                    <div>
                                        <h6 class="mb-2">Bánh Mứt</h6>
                                        <div class="d-flex mb-2">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <h5 class="fw-bold me-2">98.000vnđ</h5>
                                            <h5 class="text-danger text-decoration-line-through">150.000vnđ</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center my-4">
                                    <a href="#"
                                        class="btn border border-secondary px-4 py-3 rounded-pill text-primary w-100">Xem
                                        thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row g-4">
                           @foreach ($products as $product)
    <div class="col-md-6 col-lg-4 d-flex">
        <div class="product-card w-100 d-flex flex-column position-relative">
            <div class="product-image">
                <a href="{{ route('product-detail.show', $product->id) }}">
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
                        {{ $product->description ?? 'Không có mô tả.' }}
                    </p>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <p class="text-dark fs-5 fw-bold mb-0">
                        {{ number_format($product->discounted_price, 0, ',', '.') }} VND
                    </p>

                    @if ($product->variants->isNotEmpty())
                        <form action="{{ route('carts.add') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
</form>

                    @else
                        <button class="btn btn-secondary" disabled>Không có biến thể</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

                        </div>
                        <div class="pagination-wrapper d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Fruits Shop End-->

@include('clients.layouts.footer')

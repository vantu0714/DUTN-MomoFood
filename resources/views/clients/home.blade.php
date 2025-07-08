@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
@endif

<!-- Spinner Start -->
<div id="spinner"
    class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" role="status"></div>
</div>
<!-- Spinner End -->

<!-- Modal Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- FORM SEARCH --}}
            <form action="{{ route('clients.search') }}" method="GET">
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" name="keyword" class="form-control p-3" placeholder="Tìm kiếm sản phẩm"
                            required>
                        <button type="submit" class="input-group-text p-3 bg-primary text-white border-0">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- Modal Search End -->


<!-- Hero Start -->
<!-- Hero Banner Fullscreen Start -->
<div class="hero-banner-full">
    <img src="https://ipos.vn/wp-content/uploads/2022/05/kinh-nghiem-mo-quan-an-vat.jpg" alt="Banner MomoFood">
</div>
<!-- Hero Banner Fullscreen End -->

<!-- Hero End -->


<!-- Featurs Section Start -->
<div class="container-fluid featurs py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-car-side fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Miễn phí vận chuyển</h5>
                        <p class="mb-0">Miễn phí cho đơn hàng từ 300</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-user-shield fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Thanh toán bảo mật</h5>
                        <p class="mb-0">Thanh toán bảo mật 100%</p>

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-exchange-alt fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Hỗ trợ trả hàng trong vòng 1 tuần</h5>
                        {{-- <p class="mb-0">Hoàn tiền trong vòng 30 ngày</p> --}}

                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">

                        <i class="fa fa-phone-alt fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">

                        <h5>Hỗ trợ 24/7</h5>
                        <p class="mb-0">Hỗ trợ mọi lúc nhanh chóngt</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Featurs Section End -->


<!-- Fruits Shop Start -->
<div class="container-fluid fruite py-5">
    <div class="container py-5">
        <div class="row">
            <!-- DANH MỤC BÊN TRÁI -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="bg-light p-3 rounded shadow-sm">
                    <h5 class="mb-3 text-primary"><i class="bi bi-list-ul me-2"></i>Danh mục sản phẩm</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item {{ request()->get('category_id') == '' ? 'active' : '' }}">
                            <a href="javascript:void(0);"
                                class="text-decoration-none text-dark category-tab"data-category="">Tất cả</a>
                        </li>
                        @foreach ($categories as $category)
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill category-tab"
                                    data-category="{{ $category->id }}" href="javascript:void(0);">
                                    <span class="text-dark"
                                        style="width: 130px;">{{ $category->category_name }}</span>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>

            <!-- DANH SÁCH SẢN PHẨM BÊN PHẢI -->
            <div class="col-lg-9">
                <div class="tab-class text-center">
                    <div class="row g-4">
                        <div class="col-12 text-start">
                            <h2 class="text-success">MÓN ĂN NỔI BẬT</h2>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade show active p-0">
                            <div class="row g-4">

                                @foreach ($products as $product)
                                    @php
                                        $firstVariant = null;
                                        $price = null;

                                        if ($product->product_type === 'variant') {
                                            $firstVariant = $product->variants->first();
                                            $price = $firstVariant?->discounted_price ?? $firstVariant?->price;
                                        } elseif ($product->product_type === 'simple') {
                                            $price = $product->discounted_price ?? $product->original_price;
                                        }
                                    @endphp
                                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                        <div class="rounded position-relative fruite-item h-100 d-flex flex-column">
                                            <a href="{{ route('product-detail.show', $product->id) }}">
                                                <div class="product-img-wrapper">
                                                    <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                                        class="img-fluid" alt="Product Image">
                                                </div>
                                            </a>
                                            <div class="text-white bg-secondary px-2 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px; font-size: 12px;">
                                                {{ $product->category?->category_name ?? 'Không có danh mục' }}
                                            </div>
                                            <div
                                                class="product-content p-3 border border-secondary border-top-0 rounded-bottom d-flex flex-column justify-content-between flex-grow-1">
                                                <h6 class="text-truncate" title="{{ $product->product_name }}">
                                                    {{ $product->product_name }}
                                                </h4>
                                                <p class="text-muted text-truncate">Mã sản phẩm:
                                                    {{ $product->product_code }}</p>

                                                @php
                                                    $originalPrice = $product->original_price;
                                                    $price = $product->discounted_price ?? $product->original_price;
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                                    <p class="text-dark fs-5 fw-bold mb-0">
                                                        @if ($price && $originalPrice && $price < $originalPrice)
                                                            <div class="product-price-sale">
                                                                {{ number_format($price, 0, ',', '.') }} <span
                                                                    class="currency">VND</span>
                                                            </div>
                                                            <div class="product-price-original">
                                                                {{ number_format($originalPrice, 0, ',', '.') }} VND
                                                            </div>
                                                        @elseif ($price)
                                                            <div class="product-price-sale">
                                                                {{ number_format($price, 0, ',', '.') }} <span
                                                                    class="currency">VND</span>
                                                            </div>
                                                        @else
                                                            <div class="text-muted">Liên hệ để biết giá</div>
                                                        @endif
                                                    </p>
                                                    <form class="add-to-cart-form">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $product->id }}">
                                                        @if ($product->product_type === 'variant' && $product->variants->first())
                                                            <input type="hidden" name="product_variant_id"
                                                                value="{{ $product->variants->first()->id }}">
                                                        @endif
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="btn btn-white"><i
                                                                class="bi bi-cart3 fa-2x text-danger"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div> <!-- End right column -->
        </div>
    </div>
</div>
<!-- Fruits Shop End -->




<!-- Featurs Start -->
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="promo-box">
                        <img src="https://file.hstatic.net/200000700229/article/ga-ran-vi-kfc-1_0c2450efe15d4b6f9e6bd2637b71d88d.jpg"
                            alt="Gà rán truyền thống">
                        <div class="promo-content bg-success text-white">
                            <h5>Gà rán truyền thống</h5>
                            <p class="mb-0">Giảm giá 20%</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="promo-box">
                        <img src="https://cdn.tgdd.vn/Files/2019/07/14/1179531/nuoc-ep-tao-co-tac-dung-gi-ma-ai-cung-thi-nhau-uong-201907142251530613.jpg"
                            alt="Nước cam tươi">
                        <div class="promo-content bg-dark text-white">
                            <h5>Nước ép táo</h5>
                            <p class="mb-0">Miễn phí vận chuyển</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="promo-box">
                        <img src="https://i.ytimg.com/vi/dZwJgX-IcH8/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLC38hYKpTlqHzCnJl-zQ7256hCeQQ"
                            alt="Pizza món chay">
                        <div class="promo-content bg-warning text-dark">
                            <h5>Bánh tráng trộn</h5>
                            <p class="mb-0">Giảm giá 10vnđ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Featurs End -->


<!-- Vesitable Shop Start-->
<div class="container-fluid vesitable py-5">
    <div class="container py-5">
        <h1 class="mb-4 fw-bold text-center text-primary">🔥 SẢN PHẨM BÁN CHẠY</h1>
        <div class="owl-carousel vegetable-carousel justify-content-center">
            @foreach ($bestSellingProducts as $product)
                @php
                    $firstVariant = null;
                    $price = null;
                    $original = null;

                    if ($product->product_type === 'variant') {
                        $firstVariant = $product->variants->firstWhere('quantity', '>', 0);
                        $price = $firstVariant?->discounted_price ?? $firstVariant?->price;
                        $original = $firstVariant?->price ?? 0;
                    } else {
                        $price = $product->discounted_price ?? $product->original_price;
                        $original = $product->original_price;
                    }
                @endphp

                <div class="card shadow-sm border border-warning rounded-4 overflow-hidden h-100">
                    <div class="position-relative">
                        <a href="{{ route('product-detail.show', $product->id) }}">
                            <div class="product-img-wrapper">
                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                    class="img-fluid w-100">
                            </div>
                        </a>

                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                            {{ $product->category->category_name ?? 'Sản phẩm' }}
                        </span>

                        {{-- App hỗ trợ --}}
                        <div class="position-absolute top-0 end-0 m-2 d-flex flex-column align-items-center">
                            <img src="{{ asset('clients/icons/excel.png') }}" width="24" class="mb-1">
                            <img src="{{ asset('clients/icons/word.png') }}" width="24" class="mb-1">
                            <img src="{{ asset('clients/icons/photoshop.png') }}" width="24" class="mb-1">
                            <img src="{{ asset('clients/icons/premiere.png') }}" width="24">
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold text-dark text-truncate">{{ $product->product_name }}</h6>
                        <p class="text-muted small mb-3">{{ Str::limit($product->description, 60) }}</p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-danger fw-bold fs-5">
                                    {{ number_format($price, 0, ',', '.') }} <small class="text-muted">VND</small>
                                </div>
                                @if ($price < $original)
                                    <div class="text-muted text-decoration-line-through small">
                                        {{ number_format($original, 0, ',', '.') }} VND
                                    </div>
                                @endif
                            </div>

                            @if ($price)
                                <form class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    @if ($product->product_type === 'variant' && $firstVariant)
                                        <input type="hidden" name="product_variant_id"
                                            value="{{ $firstVariant->id }}">
                                    @endif
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-white"><i
                                            class="bi bi-cart3 fa-2x text-danger"></i></button>
                                </form>
                            @else
                                <span class="text-danger">Hết hàng</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>



<!-- Vesitable Shop End -->


<!-- Banner Section Start-->
<div class="container-fluid banner bg-secondary my-5">
    <div class="container py-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="py-4">
                    <h1 class="display-3 text-white">Thế giới đồ ăn vặt MoMoFood</h1>
                    {{-- <p class="fw-normal display-3 text-dark mb-4">trong cửa hàng chúng tôi</p> --}}
                    <p class="mb-4 text-dark">Khám phá ngay các món ăn vặt bán chạy nhất tại MomoFood – hương vị khiến
                        bạn ghiền ngay từ lần đầu!
                        Từ bánh ngọt, snack giòn rụm đến các món ăn vặt độc đáo, chúng tôi mang đến cho bạn trải nghiệm
                        ẩm thực tuyệt vời. Mua ngay để thưởng thức những món ăn vặt ngon miệng và hấp dẫn nhất!</p>
                    </p>
                    <a href="#"
                        class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">MUA</a>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">

                    <img src="https://saigonchutla.vn/wp-content/uploads/2023/09/an-vat-kon-tum-3-800x445-1.jpg"
                        class="img-fluid w-100 rounded" alt="Thế giới đồ ăn vặt MoMoFood">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner Section End -->


<!-- Bestsaler Product Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h1 class="display-4">Bestseller Products</h1>
            <p>Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks
                reasonable.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">

                            <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}"
                                class="img-fluid rounded-circle w-100" alt="">

                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">

                            <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}"
                                class="img-fluid rounded-circle w-100" alt="">

                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">

                            <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}"
                                class="img-fluid rounded-circle w-100" alt="">

                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">

                            <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}"
                                class="img-fluid rounded-circle w-100" alt="">

                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">

                            <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}"
                                class="img-fluid rounded-circle w-100" alt="">

                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">

                            <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}"
                                class="img-fluid rounded-circle w-100" alt="">

                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="text-center">

                    <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}" class="img-fluid rounded"
                        alt="">

                    <div class="py-4">
                        <a href="#" class="h5">Organic Tomato</a>
                        <div class="d-flex my-3 justify-content-center">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <h4 class="mb-3">3.12 $</h4>
                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="text-center">
                    <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}" class="img-fluid rounded"
                        alt="">

                    <div class="py-4">
                        <a href="#" class="h5">Organic Tomato</a>
                        <div class="d-flex my-3 justify-content-center">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <h4 class="mb-3">3.12 $</h4>
                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="text-center">

                    <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}" class="img-fluid rounded"
                        alt="">

                    <div class="py-4">
                        <a href="#" class="h5">Organic Tomato</a>
                        <div class="d-flex my-3 justify-content-center">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <h4 class="mb-3">3.12 $</h4>
                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="text-center">

                    <img src="{{ asset('clients/img/vegetable-item-1.jpg') }}" class="img-fluid rounded"
                        alt="">
                    <div class="py-2">
                        <a href="#" class="h5">Organic Tomato</a>
                        <div class="d-flex my-3 justify-content-center">
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star text-primary"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <h4 class="mb-3">3.12 $</h4>
                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bestsaler Product End -->


<!-- Fact Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="bg-light p-5 rounded">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>satisfied customers</h4>
                        <h1>1963</h1>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>quality of service</h4>
                        <h1>99%</h1>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>quality certificates</h4>
                        <h1>33</h1>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>Available Products</h4>
                        <h1>789</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fact Start -->

<!-- Testimonial Start -->
<div class="container-fluid testimonial py-5">
    <div class="container py-5">
        <div class="testimonial-header text-center mb-5">
            <h4 class="text-primary">Đánh giá từ khách hàng</h4>
            <h1 class="display-5 text-dark">Khách hàng nói gì về chúng tôi</h1>
        </div>
        <div class="owl-carousel testimonial-carousel">

            @foreach ($product->comments as $comment)
                <div class="testimonial-item img-border-radius bg-light rounded p-4">
                    <div class="position-relative">
                        <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                            style="bottom: 30px; right: 0;"></i>

                        <div class="mb-4 pb-4 border-bottom border-secondary">
                            <p class="mb-0 text-dark">{{ $comment->content }}</p>
                        </div>

                        <div class="d-flex align-items-center flex-nowrap">
                            <div class="bg-secondary rounded">
                                <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('clients/img/avatar.jpg') }}"
                                    class="img-fluid rounded" style="width: 100px; height: 100px;" alt="Avatar">
                            </div>

                            <div class="ms-4 d-block">
                                <h5 class="text-dark mb-1">{{ $comment->user->name ?? 'Ẩn danh' }}</h5>
                                <p class="text-muted mb-2">{{ $comment->user->profession ?? 'Khách hàng' }}</p>

                                <div class="d-flex pe-5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star {{ $i <= $comment->rating ? 'text-primary' : 'text-secondary' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
  <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rangeInput = document.getElementById('rangeInput');
            const output = document.getElementById('amount');

            function formatCurrency(value) {
                return parseInt(value).toLocaleString('vi-VN') + ' đ';
            }

            rangeInput.addEventListener('input', function() {
                output.textContent = formatCurrency(this.value);
            });

            // Gọi lần đầu khi tải trang
            output.textContent = formatCurrency(rangeInput.value);


            document.addEventListener('DOMContentLoaded', function() {
                const radios = document.querySelectorAll('input[name="price_range"]');
                const customInputs = document.getElementById('customPriceInputs');

                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'custom') {
                            customInputs.style.display = '';
                        } else {
                            customInputs.style.display = 'none';
                            // Clear giá trị nếu không chọn tùy chỉnh
                            document.querySelector('input[name="min_price"]').value = '';
                            document.querySelector('input[name="max_price"]').value = '';
                        }
                    });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success') || session('error'))
                let message = "{{ session('success') ?? session('error') }}";
                let isError = {{ session('error') ? 'true' : 'false' }};

                const container = document.getElementById('toast-container');
                const messageEl = document.getElementById('toast-message');

                messageEl.textContent = message;
                container.classList.remove('d-none');
                if (isError) container.classList.add('toast-error');

                setTimeout(() => {
                    container.classList.add('d-none');
                    container.classList.remove('toast-error');
                }, 4000);
            @endif
        });
    </script>
@include('clients.layouts.footer')


<style>
    .hero-banner-full {
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        position: relative;
        margin-bottom: 3rem;
    }

    .hero-banner-full img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .list-group-item.active {
        background-color: #fff3cd;
        font-weight: bold;
        color: #d35400;
        border-left: 4px solid #ffc107;
    }
</style>

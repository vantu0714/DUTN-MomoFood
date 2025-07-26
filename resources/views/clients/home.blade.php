@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>



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


<!-- Hero Banner Fullscreen Start -->
<div class="hero-banner-full">
    <img src="https://ipos.vn/wp-content/uploads/2022/05/kinh-nghiem-mo-quan-an-vat.jpg" alt="Banner MomoFood">
</div>
<!-- Hero Banner Fullscreen End -->



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
        <!-- DANH MỤC NGANG -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-light p-3 rounded shadow-sm">
                    <h5 class="mb-3 text-primary"><i class="bi bi-list-ul me-2"></i>Danh mục sản phẩm</h5>
                    <ul class="nav nav-pills flex-wrap gap-2" id="category-list">
                        <li class="nav-item">
                            <a class="nav-link active category-tab" href="#" data-category="">Tất cả</a>
                        </li>
                        @foreach ($categories as $category)
                            <li class="nav-item">
                                <a class="nav-link category-tab" href="#" data-category="{{ $category->id }}">
                                    {{ $category->category_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- DANH SÁCH SẢN PHẨM -->
        <div class="row">
            <div class="col-12">
                <div class="tab-class text-center mt-4" id="best-selling-section">
                    <div class="row g-4">
                        <div class="col-12 text-start">
                            <h2 class="text-success">MÓN ĂN NỔI BẬT</h2>
                        </div>
                    </div>

                    <div id="filtered-products">
                        @include('clients.components.filtered-products')
                    </div>
                </div>
            </div>
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

        <div class="row g-4">
            @foreach ($bestSellingProducts as $product)
                @php
                    $firstVariant = null;
                    $price = 0;
                    $original = 0;
                    $variants = [];

                    if ($product->product_type === 'variant') {
                        $firstVariant = $product->variants->firstWhere('quantity', '>', 0);
                        if ($firstVariant) {
                            $price = $firstVariant->discounted_price ?? $firstVariant->price;
                            $original = $firstVariant->price;
                        }

                        $variants = $product->variants->map(function ($v) {
                            return [
                                'id' => $v->id,
                                'price' => $v->price,
                                'discounted_price' => $v->discounted_price,
                                'quantity' => $v->quantity,
                                'image' => $v->image,
                                'attribute_values' => $v->attributeValues->map(function ($attrValue) {
                                    return [
                                        'attribute_name' => $attrValue->attribute->name,
                                        'value' => $attrValue->value,
                                    ];
                                }),
                            ];
                        });
                    } else {
                        $price = $product->discounted_price ?? $product->original_price;
                        $original = $product->original_price;
                    }
                @endphp

                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold text-dark text-truncate">{{ $product->product_name }}</h6>
                            <p class="text-muted small mb-3">{{ Str::limit($product->description, 60) }}</p>

                            {{-- 👉 Nếu có biến thể, hiển thị flavor & weight --}}
                            @if ($product->product_type === 'variant')
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach ($product->variants as $variant)
                                        @php
                                            $flavor = $variant->attributeValues->firstWhere('attribute.name', 'Vị');
                                            $weight = $variant->attributeValues->firstWhere(
                                                'attribute.name',
                                                'Khối lượng',
                                            );
                                        @endphp
                                        <span class="badge bg-info text-white">
                                            {{ $flavor ? 'Vị: ' . $flavor->value : '' }}
                                        </span>
                                        <span class="badge bg-info text-white">
                                            {{ $weight ? 'Khối lượng: ' . $weight->value : '' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

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

                                <div class="d-flex justify-content-end mt-auto">
                                    <button type="button" class="btn btn-white open-cart-modal"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->product_name }}"
                                        data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                        data-product-category="{{ $product->category->category_name ?? 'Không rõ' }}"
                                        data-product-price="{{ $price ?? 0 }}"
                                        data-product-original-price="{{ $original ?? 0 }}"
                                        data-product-description="{{ $product->description }}"
                                        data-variants='@json($variants)' data-bs-toggle="modal"
                                        data-bs-target="#cartModal">
                                        <i class="bi bi-cart3 fa-2x text-danger"></i>
                                    </button>
                                </div>
                            </div>
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
<!-- Bestsaler Product Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h1 class="display-4"
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 700;">
                Siêu Phẩm Ăn Vặt 5 ⭐
            </h1>
            <p
                style="font-size: 18px; line-height: 1.6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                Đồ ăn vặt được đánh giá cao – vị ngon khó tìm thấy. Mỗi món đều được tuyển chọn kỹ lưỡng từ nguyên liệu
                chất lượng, chế biến hợp vệ sinh và đóng gói cẩn thận. Được yêu thích bởi hàng ngàn khách hàng trên khắp
                cả nước.

            </p>
        </div>


        <div class="row g-4">
            @foreach ($highRatedProducts as $product)
                @php
                    $variantsArray = collect($product->variants ?? [])->map(function ($variant) {
                        $flavor = optional($variant->attributeValues->firstWhere('attribute.name', 'Vị'))->value ?? '';
                        $weight =
                            optional($variant->attributeValues->firstWhere('attribute.name', 'Khối lượng'))->value ??
                            (optional($variant->attributeValues->firstWhere('attribute.name', 'Size'))->value ?? '');
                        return [
                            'id' => $variant->id,
                            'price' => $variant->price,
                            'discounted_price' => $variant->discounted_price,
                            'image' => $variant->image
                                ? asset('storage/' . $variant->image)
                                : asset('images/no-image.png'),
                            'flavor' => $flavor ?: 'Không rõ',
                            'weight' => $weight ?: 'Không rõ',
                            'quantity_in_stock' => $variant->quantity_in_stock ?? ($variant->quantity ?? 0),
                        ];
                    });
                @endphp
                <div class="col-lg-6 col-xl-4">
                    <div class="p-4 rounded bg-light h-100">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                    class="img-fluid rounded-circle w-100" alt="{{ $product->product_name }}">
                            </div>
                            <div class="col-6">
                                <a href="{{ route('product-detail.show', $product->id) }}" class="h5 d-block mb-2">
                                    {{ $product->product_name }}
                                </a>

                                @php
                                    $avgRating = round($product->comments->avg('rating') ?? 0);
                                @endphp
                                <div class="d-flex align-items-center mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star{{ $i <= $avgRating ? ' text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                    <small class="ms-2 text-muted">({{ number_format($avgRating, 1) }}/5)</small>
                                </div>

                                <h4 class="mb-3 text-danger fw-bold">
                                    {{ number_format($product->display_price ?? 0, 0, ',', '.') }} đ
                                </h4>

                                <button type="button"
                                    class="btn border border-secondary rounded-pill px-3 text-primary open-cart-modal d-flex align-items-center"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->product_name }}"
                                    data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    data-product-category="{{ $product->category->category_name ?? 'Không rõ' }}"
                                    data-product-price="{{ $product->display_price ?? 0 }}"
                                    data-product-original-price="{{ $product->original_price ?? 0 }}"
                                    data-product-description="{{ $product->description }}"
                                    data-variants='@json($variantsArray, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)' data-bs-target="#cartModal">
                                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
<!-- Fact Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="bg-light p-5 rounded">
            <h1 class="display-4"
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 700; text-align: center">
                Tin tức 📰
            </h1>
            <br>
            <div class="news-grid">
                <!-- Card 4 -->
                <div class="news-card">
                    <img src="{{ asset('clients/img/anhtintuc4.png') }}" alt="Tin 4">
                    <div class="news-card-body">
                        <h2 class="news-card-title">Deal Sốc "Nửa Giá": Thưởng Thức Mì Ý Ngon Mê Ly</h2>
                        <p class="news-card-desc">Bạn là fan của mì Ý và luôn tìm kiếm những ưu đãi hấp dẫn? Vậy thì
                            đây chính là tin vui dành cho bạn! ...</p>
                        <div class="news-meta">
                            <span><i class="bi bi-calendar3"></i> 10/06/2025</span>
                            <a href="{{ route('news.detail', ['id' => 4]) }}" class="btn-read-more">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="news-card">
                    <img src="{{ asset('clients/img/anhtintuc5.png') }}" alt="Tin 5">
                    <div class="news-card-body">
                        <h2 class="news-card-title">Cuối Tuần "Cháy Phố": Khuyến Mãi Combo Gia Đình Cực Hấp Dẫn</h2>
                        <p class="news-card-desc">Cuối tuần là thời điểm tuyệt vời để cùng gia đình quây quần bên nhau,
                            tận hưởng những khoảnh khắc thư giãn và thưởng...</p>
                        <div class="news-meta">
                            <span><i class="bi bi-calendar3"></i> 08/06/2025</span>
                            <a href="{{ route('news.detail', parameters: ['id' => 5]) }}" class="btn-read-more">Xem
                                chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="news-card">
                    <img src="{{ asset('clients/img/anhtintuc6.png') }}" alt="Tin 6">
                    <div class="news-card-body">
                        <h2 class="news-card-title">Thứ 4 'vàng': Ưu đãi đặc biệt cho tín đồ gà rán</h2>
                        <p class="news-card-desc">Hội những người mê gà rán đâu rồi? Thứ 4 này đừng bỏ lỡ cơ hội tận
                            hưởng ưu đãi siêu hấp dẫn dành riêng...</p>
                        <div class="news-meta">
                            <span><i class="bi bi-calendar3"></i> 05/06/2025</span>
                            <a href="{{ route('news.detail', parameters: ['id' => 6]) }}" class="btn-read-more">Xem
                                chi tiết</a>
                        </div>
                    </div>
                </div>
                <a href=""></a>
                <a class="xemtatca" href="{{ route('news.index') }}" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 400; ">
                Xem tất cả > 
                </a>
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
            @foreach ($comments as $comment)
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

<!-- Modal chi tiết sản phẩm -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('carts.add') }}" id="modal-add-to-cart-form" class="modal-content">
            @csrf
            <input type="hidden" name="product_id" id="modal-product-id">
            <input type="hidden" name="product_variant_id" id="modal-variant-id">

            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary" id="cartModalLabel">Chọn sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- Hình ảnh -->
                    <div class="col-md-6 text-center">
                        <img id="modal-product-image" src="" alt="Hình sản phẩm"
                            class="img-fluid rounded shadow-sm"
                            style="max-height: 500px; object-fit: cover; width: 100%;">
                    </div>

                    <!-- Thông tin sản phẩm -->
                    <div class="col-md-6">
                        <h4 id="modal-product-name" class="fw-bold mb-2 text-dark"></h4>
                        <p class="text-muted mb-2">
                            Danh mục: <span id="modal-product-category" class="fw-medium text-dark"></span>
                        </p>

                        <p class="h5 text-danger fw-bold mb-3 tabular-numbers">
                            <span id="modal-product-price">0</span>
                            <span class="text-muted fs-6">VND</span>
                            <del class="text-secondary fs-6 ms-2" id="modal-product-original-price"></del>
                        </p>

                        <div class="mb-3" id="modal-rating">
                            <!-- Đánh giá (nếu cần) -->
                        </div>

                        <p id="modal-product-description" class="text-muted mb-3" style="min-height: 60px;"></p>


                        <!-- Biến thể -->
                        <div class="mb-3" id="variant-section">
                            <label class="form-label fw-semibold">🍃 Chọn biến thể:</label>
                            <div id="variant-options" class="d-flex flex-wrap gap-2">
                                <!-- JS sẽ render radio button biến thể -->
                            </div>
                        </div>


                        <!-- Số lượng -->
                        <div class="mb-3">
                            <label for="modal-quantity" class="form-label fw-semibold">🔁 Số lượng:</label>
                            <div class="input-group" style="width: 160px;">
                                <button type="button" class="btn btn-outline-secondary" id="decrease-qty">-</button>
                                <input type="number" class="form-control text-center" id="modal-quantity"
                                    name="quantity" value="1" min="1">
                                <button type="button" class="btn btn-outline-secondary" id="increase-qty">+</button>
                                <br>

                            </div>
                            <small id="stock-info" class="text-muted mt-1 d-block">Kho: --</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                    <i class="bi bi-bag-plus-fill me-1"></i> Thêm vào giỏ hàng
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = new bootstrap.Modal(document.getElementById('cartModal'));

        const productNameEl = document.getElementById('modal-product-name');
        const productImageEl = document.getElementById('modal-product-image');
        const productCategoryEl = document.getElementById('modal-product-category');
        const productPriceEl = document.getElementById('modal-product-price');
        const productOriginalPriceEl = document.getElementById('modal-product-original-price');
        const productDescEl = document.getElementById('modal-product-description');
        const variantOptionsEl = document.getElementById('variant-options');
        const productIdInput = document.getElementById('modal-product-id');
        const productVariantIdInput = document.getElementById('modal-variant-id');
        const quantityInput = document.getElementById('modal-quantity');
        const stockInfoEl = document.getElementById('stock-info');

        const weightGroup = document.getElementById('modal-weight-group');
        if (weightGroup) weightGroup.style.display = 'none';

        // Nút +/-
        document.getElementById('increase-qty').addEventListener('click', () => {
            const max = parseInt(quantityInput.max) || 9999;
            let current = parseInt(quantityInput.value);
            if (current < max) quantityInput.value = current + 1;
        });

        document.getElementById('decrease-qty').addEventListener('click', () => {
            if (quantityInput.value > 1) quantityInput.stepDown();
        });

        // Mở modal
        document.querySelectorAll('.open-cart-modal').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productImage = this.dataset.productImage;
                const productCategory = this.dataset.productCategory;
                const productPrice = parseInt(this.dataset.productPrice || 0);
                const productOriginalPrice = parseInt(this.dataset.productOriginalPrice || 0);
                const productDescription = this.dataset.productDescription || '';
                const variants = JSON.parse(this.dataset.variants || '[]');

                // Reset modal
                productIdInput.value = productId;
                productNameEl.textContent = productName;
                productImageEl.src = productImage;
                productCategoryEl.textContent = productCategory;
                productDescEl.textContent = productDescription;
                quantityInput.value = 1;
                quantityInput.removeAttribute('max');
                variantOptionsEl.innerHTML = '';
                productVariantIdInput.value = '';
                productPriceEl.textContent = productPrice.toLocaleString();
                productOriginalPriceEl.textContent = (productOriginalPrice > productPrice) ?
                    productOriginalPrice.toLocaleString() + ' VND' : '';
                productOriginalPriceEl.style.display = (productOriginalPrice > productPrice) ?
                    'inline' : 'none';
                if (stockInfoEl) stockInfoEl.textContent = 'Kho: --';

                if (weightGroup) weightGroup.style.display = 'none';

                // Hiển thị biến thể
                if (variants.length > 0) {
                    variants.forEach(variant => {
                        console.log(variant);
                        const imageUrl = variant.image || productImage;
                        const flavorText = variant.flavor || '';
                        const weightText = variant.weight || variant.mass || variant
                            .size || '';
                        const stock = variant.quantity_in_stock ?? variant.quantity ??
                            variant.stock ?? 0;

                        const html = `
                        <div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center"
                            style="cursor: pointer; transition: 0.3s;"
                            data-variant-id="${variant.id}"
                            data-variant-price="${variant.discounted_price || variant.price}"
                            data-variant-original="${variant.price}"
                            data-variant-weight="${weightText}"
                            data-variant-stock="${stock}"
                            data-variant-image="${imageUrl}">
                            <img src="${imageUrl}" alt="variant-image"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <div class="fw-semibold text-dark">${flavorText} - ${weightText}</div>
                            </div>
                        </div>`;
                        variantOptionsEl.insertAdjacentHTML('beforeend', html);
                    });

                    // Gán sự kiện click biến thể
                    variantOptionsEl.querySelectorAll('.variant-card').forEach(card => {
                        card.addEventListener('click', () => {
                            variantOptionsEl.querySelectorAll('.variant-card')
                                .forEach(c => {
                                    c.classList.remove('border-primary',
                                        'shadow');
                                });
                            card.classList.add('border-primary', 'shadow');

                            const id = card.dataset.variantId;
                            const price = parseInt(card.dataset.variantPrice);
                            const original = parseInt(card.dataset
                                .variantOriginal);
                            const imageUrl = card.dataset.variantImage;
                            const stock = parseInt(card.dataset.variantStock ||
                                0);

                            productVariantIdInput.value = id;
                            productPriceEl.textContent = price.toLocaleString();
                            productOriginalPriceEl.textContent = (original >
                                    price) ? original.toLocaleString() +
                                ' VND' : '';
                            productOriginalPriceEl.style.display = (original >
                                price) ? 'inline' : 'none';
                            productImageEl.src = imageUrl;

                            // Hiển thị kho + giới hạn số lượng
                            if (stockInfoEl) {
                                stockInfoEl.textContent =
                                    `Kho: ${stock} sản phẩm`;
                            }
                            quantityInput.max = stock;
                            if (parseInt(quantityInput.value) > stock) {
                                quantityInput.value = stock;
                            }
                        });
                    });
                }

                modal.show();
            });
        });

        // Validate chọn biến thể trước khi thêm giỏ hàng
        document.getElementById('modal-add-to-cart-form').addEventListener('submit', function(e) {
            if (variantOptionsEl.innerHTML.trim() !== '' && !productVariantIdInput.value) {
                e.preventDefault();
                alert('⚠️ Vui lòng chọn biến thể trước khi thêm vào giỏ hàng.');
            }
        });
    });
</script>


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
    .owl-carousel .owl-item {
        margin-right: 1px !important;
        margin-left: 10px !important;
    }

    .tabular-numbers,
    .tabular-numbers span,
    .tabular-numbers del {
        font-family: 'Roboto', sans-serif !important;
        font-variant-numeric: tabular-nums !important;
        font-size: 1.5rem !important;
        line-height: 1.2 !important;
        vertical-align: middle !important;
        display: inline-block !important;
    }


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

    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            /* Căn chỉnh theo chiều cao header của bạn */
            z-index: 2;
        }
    }
</style>

<style>
    .product-img-wrapper {
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-img-wrapper img {
        max-height: 100%;
        object-fit: contain;
    }

    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: scale(1.03);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
    }

    .btn-white {
        background-color: white;
        border: 1px solid #ffc107;
        border-radius: 10px;
    }

    .btn-white:hover {
        background-color: #ffc107;
    }


    /* // form */
    .category-tab.active {
        background-color: #dc6d5c !important;
        color: white;
    }

    .category-tab.active span {
        color: white !important;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryTabs = document.querySelectorAll('.category-tab');
        const filteredProducts = document.querySelector('#filtered-products');

        // Bắt sự kiện click vào danh mục
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn nhảy trang (nếu là thẻ <a>)
                categoryTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const categoryId = this.dataset.category;

                fetch(`/filter-category?category=${categoryId}`)
                    .then(res => res.text())
                    .then(data => {
                        filteredProducts.innerHTML = data;
                        rebindOpenCartModal();
                        // Đã loại bỏ scroll nhảy lên
                    })
                    .catch(err => console.error('Lỗi lọc danh mục:', err));
            });
        });

        // Bắt sự kiện phân trang (AJAX)
        document.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();

                fetch(link.href)
                    .then(res => res.text())
                    .then(data => {
                        filteredProducts.innerHTML = data;
                        rebindOpenCartModal();
                        // Không scroll, giữ nguyên vị trí
                    })
                    .catch(err => console.error('Lỗi phân trang:', err));
            }
        });
    });
</script>

<style>
    .sticky-sidebar {
        position: sticky;
        top: 100px;
        max-height: 450px;
        overflow-y: auto;
        padding-right: 5px;
        z-index: 100;
    }

    #category-list .nav-link {
        background-color: #fff;
        border: 1px solid #ccc;
        color: #333;
        border-radius: 30px;
        padding: 6px 15px;
        transition: all 0.2s;
    }

    #category-list .nav-link.active,
    #category-list .nav-link:hover {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
</style>


<style>
    #variant-options {
        gap: 10px;
    }

    .variant-card {
        flex: 0 0 auto;
        width: 180px;
        transition: all 0.3s ease;
    }

    .variant-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .variant-card.border-primary {
        border-width: 2px;
        background-color: #eaf4ff;
    }

    .variant-card img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
</style>


<script>
    function rebindOpenCartModal() {
        const modal = new bootstrap.Modal(document.getElementById('cartModal'));
        const variantSection = document.getElementById('variant-section');
        const productNameEl = document.getElementById('modal-product-name');
        const productImageEl = document.getElementById('modal-product-image');
        const productCategoryEl = document.getElementById('modal-product-category');
        const productPriceEl = document.getElementById('modal-product-price');
        const productOriginalPriceEl = document.getElementById('modal-product-original-price');
        const productDescEl = document.getElementById('modal-product-description');
        const variantOptionsEl = document.getElementById('variant-options');
        const productIdInput = document.getElementById('modal-product-id');
        const productVariantIdInput = document.getElementById('modal-variant-id');
        const quantityInput = document.getElementById('modal-quantity');

        document.querySelectorAll('.open-cart-modal').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productImage = this.dataset.productImage;
                const productCategory = this.dataset.productCategory;
                const productPrice = parseInt(this.dataset.productPrice || 0);
                const productOriginalPrice = parseInt(this.dataset.productOriginalPrice || 0);
                const productDescription = this.dataset.productDescription || '';
                const variants = JSON.parse(this.dataset.variants || '[]');

                // Reset
                productIdInput.value = productId;
                productNameEl.textContent = productName;
                productImageEl.src = productImage;
                productCategoryEl.textContent = productCategory;
                productDescEl.textContent = productDescription;
                quantityInput.value = 1;
                variantOptionsEl.innerHTML = '';
                productVariantIdInput.value = '';
                productPriceEl.textContent = productPrice.toLocaleString();
                productOriginalPriceEl.textContent = (productOriginalPrice > productPrice) ?
                    productOriginalPrice.toLocaleString() + ' VND' : '';
                productOriginalPriceEl.style.display = (productOriginalPrice > productPrice) ?
                    'inline' : 'none';

                if (variants.length > 0) {
                    variantSection.style.display = 'block';
                    variants.forEach(variant => {
                        const imageUrl = variant.image || productImage;
                        const flavorText = variant.flavor || '';
                        const weightText = variant.weight || variant.mass || variant.size || '';

                        const html = `
                        <div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center"
                            style="cursor: pointer; transition: 0.3s;"
                            data-variant-id="${variant.id}"
                            data-variant-price="${variant.discounted_price || variant.price}"
                            data-variant-original="${variant.price}"
                            data-variant-weight="${weightText}"
                            data-variant-image="${imageUrl}">
                            <img src="${imageUrl}" alt="variant-image"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <div class="fw-semibold text-dark">${flavorText} - ${weightText}</div>
                            </div>
                        </div>`;
                        variantOptionsEl.insertAdjacentHTML('beforeend', html);
                    });

                    // Gán click biến thể
                    variantOptionsEl.querySelectorAll('.variant-card').forEach(card => {
                        card.addEventListener('click', () => {
                            variantOptionsEl.querySelectorAll('.variant-card').forEach(
                                c => c.classList.remove('border-primary', 'shadow'));
                            card.classList.add('border-primary', 'shadow');

                            const id = card.dataset.variantId;
                            const price = parseInt(card.dataset.variantPrice);
                            const original = parseInt(card.dataset.variantOriginal);
                            const imageUrl = card.dataset.variantImage;

                            productVariantIdInput.value = id;
                            productPriceEl.textContent = price.toLocaleString();
                            productOriginalPriceEl.textContent = (original > price) ?
                                original.toLocaleString() + ' VND' : '';
                            productOriginalPriceEl.style.display = (original > price) ?
                                'inline' : 'none';
                            productImageEl.src = imageUrl;
                        });
                    });
                } else {
                    variantSection.style.display = 'none';
                    variantOptionsEl.innerHTML = '';
                    productVariantIdInput.value = '';
                }

                modal.show();
            });
        });
    }
</script>


<style>
    .xemtatca {
        text-align: center;
        margin-top: 20px;
        color: #9ca3af;
        font-size: 24px;
    }
    .news-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .section-title {
        text-align: center;
        font-size: 32px;
        font-weight: bold;
        color: #ef4444;
        margin-bottom: 40px;
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
    }

    .news-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .news-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .news-card-title {
        font-size: 18px;
        font-weight: bold;
        color: #f97316;
        margin-bottom: 10px;
    }

    .news-card-desc {
        color: #4b5563;
        font-size: 14px;
        flex: 1;
    }

    .news-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-top: 15px;
        color: #9ca3af;
    }

    .btn-read-more {
        padding: 6px 12px;
        background-color: transparent;
        border: 1px solid #f97316;
        color: #f97316;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-read-more:hover {
        background-color: #f97316;
        color: white;
    }
</style>

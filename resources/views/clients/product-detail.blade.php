@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">
<link rel="stylesheet" href="{{ asset('clients/css/shop-detail.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<!-- Single Page Header start -->
{{-- <div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Chi Tiết Sản Phẩm</h1>
</div> --}}
<div class="page-header "></div>
<!-- Single Product Start -->
<div class="container-fluid">
    <div class="container py-1">
        <div class="row g-4 mb-5 justify-content-center">
            <div class="col-lg-8 col-xl-9">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Cửa hàng</a></li>
                        @if ($product->category)
                            @if ($product->category->parent)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('shop.category', $product->category->parent->id) }}">
                                        {{ $product->category->parent->category_name }}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item">
                                <a href="{{ route('shop.category', $product->category->id) }}">
                                    {{ $product->category->category_name }}
                                </a>
                            </li>
                        @else
                            <li class="breadcrumb-item">Không có danh mục</li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
                    </ol>
                </nav>

                <div class="row g-4">
                    <div class="col-lg-6">
                        {{-- Ảnh chính --}}
                        <div class="product-image-container mb-3">
                            <img id="mainProductImage"
                                src="{{ $product->image ? asset('storage/' . $product->image) : $product->variants->first()?->image_url ?? '' }}"
                                data-original-src="{{ $product->image ? asset('storage/' . $product->image) : $product->variants->first()?->image_url ?? '' }}"
                                class="product-image img-fluid rounded shadow-sm" alt="{{ $product->product_name }}"
                                style="width: 100%; max-height: 450px; object-fit: cover;">
                            <div class="image-overlay"></div>
                            <div class="zoom-icon">
                                <i class="fas fa-search-plus text-success"></i>
                            </div>
                        </div>
                        {{-- Ảnh biến thể hiển thị bên dưới --}}
                        <div class="variant-thumbnails d-flex gap-2 flex-wrap">
                            {{-- Ảnh chính cũng là thumbnail đầu tiên --}}
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $product->product_name }}"
                                    class="variant-thumbnail img-thumbnail border border-primary"
                                    data-full-image="{{ asset('storage/' . $product->image) }}"
                                    style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                            @endif
                            {{-- Ảnh từ biến thể --}}
                            @foreach ($product->variants as $variant)
                                @if ($variant->image_url)
                                    <img src="{{ $variant->image_url }}" alt="{{ $variant->full_name }}"
                                        class="variant-thumbnail img-thumbnail"
                                        data-full-image="{{ $variant->image_url }}"
                                        style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                                @endif
                            @endforeach
                        </div>
                        {{-- Chia sẻ mạng xã hội --}}
                        <strong>Chia sẻ:</strong>
                        <div class="d-flex gap-2 mt-2">
                            {{-- Messenger --}}
                            <a href="https://www.facebook.com/dialog/send?link={{ urlencode(Request::url()) }}&app_id=1507345650254377&redirect_uri={{ urlencode(Request::url()) }}"
                                target="_blank" class="btn btn-sm rounded-circle shadow-sm"
                                style="background-color: #0084FF; color: white; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-facebook-messenger"></i>
                            </a>

                            {{-- Facebook --}}
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}"
                                target="_blank" class="btn btn-sm rounded-circle shadow-sm"
                                style="background-color: #3b5998; color: white; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-facebook-f"></i>
                            </a>

                            {{-- Instagram (chỉ mở Instagram trang chủ, không có API chia sẻ trực tiếp) --}}
                            <a href="https://www.instagram.com/" target="_blank"
                                class="btn btn-sm rounded-circle shadow-sm"
                                style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
                                  color: white; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-instagram"></i>
                            </a>


                            {{-- Twitter --}}
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}"
                                target="_blank" class="btn btn-sm rounded-circle shadow-sm"
                                style="background-color: #1DA1F2; color: white; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                        <div class="mt-5">
                            <h4 class="fw-bold mb-3 text-uppercase section-title">Chi tiết sản phẩm</h4>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="text-muted" style="width: 150px;">Danh mục</th>
                                        <td>
                                            @php
                                                $categories = [];
                                                $category = $product->category;

                                                while ($category) {
                                                    $categories[] = $category;
                                                    $category = $category->parent;
                                                }

                                                $categories = array_reverse($categories);
                                            @endphp

                                            @foreach ($categories as $index => $cat)
                                                <a href="{{ route('shop.category', $cat->id) }}"
                                                    style="color: #d67054; text-decoration: none;">
                                                    {{ $cat->category_name }}
                                                </a>
                                                @if ($index < count($categories) - 1)
                                                    &nbsp;&gt;&nbsp;
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="text-muted">Kho</th>
                                        <td>{{ $product->quantity_in_stock ?? 'Không rõ' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Xuất xứ</th>
                                        <td>{{ $product->origin ? $product->origin->name : 'Đang cập nhật' }}</td>
                                    </tr>

                                    <tr>
                                        <th class="text-muted">Hạn sử dụng</th>
                                        <td>
                                            @if ($product->expiration_date)
                                                {{ \Carbon\Carbon::parse($product->expiration_date)->format('d/m/Y') }}
                                                (còn
                                                {{ \Carbon\Carbon::now()->diffInDays($product->expiration_date, false) }}
                                                ngày)
                                            @else
                                                Không rõ
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6 product-info">
                        <h3 class="fw-bold text-dark mb-3 section-title">{{ $product->product_name }}</h3>
                        <!-- START: Đánh giá tổng quan -->
                        <div class="product-summary-stats d-flex align-items-center gap-4 mb-3">
                            <div class="rating-summary d-flex align-items-center border-end pe-4">
                                <span class="fw-bold me-1 text-dark" style="font-size: 1.2rem;">
                                    {{ number_format($averageRating, 1) }}
                                </span>
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fa fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <div class="review-count border-end px-4">
                                <span class="fw-bold text-dark">{{ number_format($product->comments->count()) }}</span>
                                <span class="text-muted">Đánh Giá</span>
                            </div>
                            <div class="sold-count ps-4">
                                <span class="text-muted">Đã Bán</span>
                                <span class="fw-bold text-dark ms-1">
                                    {{ $product->sold_count >= 1000 ? number_format($product->sold_count / 1000, 1) . 'k' : $product->sold_count }}
                                </span>
                            </div>
                        </div>
                        <!-- END: Đánh giá tổng quan -->
                        <p class="text-muted mb-3">
                            <i class="fas fa-tag me-2"></i>
                            <strong>Danh mục:</strong> {{ $product->category?->category_name ?? 'Không có danh mục' }}
                        </p>
                        @php
                            $prices = $product->variants->pluck('price')->filter(); // loại bỏ null
                            $minPrice = $prices->min();
                            $maxPrice = $prices->max();
                        @endphp

                        <h3 class="product-price mb-4 d-flex align-items-center gap-2" id="productPriceDisplay">
                            @if ($product->variants && $product->variants->count())
                                @php $isDiscounted = false; @endphp

                                {{-- Giá chính --}}
                                <span id="variantPrice" class="price-amount fw-bold text-danger"
                                    style="font-size: 2rem; line-height: 1;">
                                    đ{{ number_format($minPrice, 0, ',', '.') }}
                                    @if ($minPrice != $maxPrice)
                                        - đ{{ number_format($maxPrice, 0, ',', '.') }}
                                    @endif
                                </span>

                                {{-- Giá gạch ngang và giảm giá ẩn đi nhưng vẫn giữ không gian --}}
                                <span class="original-price text-muted text-decoration-line-through"
                                    style="font-size: 1.5rem; line-height: 1; visibility: hidden;">
                                    đ0
                                </span>
                                <span class="discount-percent badge bg-danger-subtle text-danger fw-semibold"
                                    style="font-size: 1.5rem; line-height: 1; visibility: hidden;">
                                    -0%
                                </span>

                                <input type="hidden" id="minPrice" value="{{ $minPrice }}">
                                <input type="hidden" id="maxPrice" value="{{ $maxPrice }}">
                            @else
                                @php
                                    $isDiscounted =
                                        $product->discounted_price &&
                                        $product->discounted_price < $product->original_price;
                                    $discountPercent = $isDiscounted
                                        ? round((1 - $product->discounted_price / $product->original_price) * 100)
                                        : 0;
                                @endphp

                                {{-- Giá chính --}}
                                <span id="variantPrice"
                                    class="price-amount fw-bold {{ $isDiscounted ? 'text-danger' : 'text-dark' }}"
                                    style="font-size: 2rem; line-height: 1;">
                                    đ{{ number_format($isDiscounted ? $product->discounted_price : $product->original_price, 0, ',', '.') }}
                                </span>

                                {{-- Giá gạch ngang nếu có --}}
                                <span class="original-price text-muted text-decoration-line-through"
                                    id="originalPrice"
                                    style="font-size: 1.5rem; line-height: 1; {{ !$isDiscounted ? 'visibility: hidden;' : '' }}">
                                    đ{{ number_format($product->original_price, 0, ',', '.') }}
                                </span>

                                {{-- Badge giảm giá nếu có --}}
                                <span class="discount-percent badge bg-danger-subtle text-danger fw-semibold"
                                    id="discountPercent"
                                    style="font-size: 1.5rem; line-height: 1; {{ !$isDiscounted ? 'visibility: hidden;' : '' }}">
                                    -{{ $discountPercent }}%
                                </span>
                            @endif
                        </h3>
                        <p class="text-muted mb-4" style="line-height: 1.8; font-size: 15px;">
                            {{ $product->description ?? 'Không có mô tả.' }}</p>
                        <form id="addToCartForm" action="{{ route('carts.add') }}" method="POST"
                            class="d-flex flex-column gap-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <!-- Updated Variant Section in your Blade template -->
                            @if ($product->variants && $product->variants->count())
                                <div class="variant-section">
                                    <label class="form-label fw-bold mb-3" style="font-size: 16px;">
                                        <i class="fas fa-layer-group me-2 text-success"></i>Chọn loại:
                                    </label>
                                    <div class="row g-3" id="variantOptions">
                                        @foreach ($product->variants as $variant)
                                            <div class="col-md-6">
                                                <div class="variant-option" data-variant-id="{{ $variant->id }}"
                                                    data-variant-name="{{ trim(str_replace(['(', ')'], '', $variant->full_name)) }}"
                                                    data-variant-price="{{ $variant->discounted_price ?? $variant->price }}"
                                                    data-variant-original="{{ $variant->price }}"
                                                    data-variant-stock="{{ $variant->quantity_in_stock }}"
                                                    data-variant-image="{{ $variant->image_url }}"
                                                    style="cursor: pointer;">
                                                    <div class="variant-content d-flex align-items-center gap-2">
                                                        @if ($variant->image_url)
                                                            <img src="{{ $variant->image_url }}"
                                                                class="variant-image"
                                                                alt="{{ trim(str_replace(['(', ')'], '', $variant->full_name)) }}"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div class="flex-grow-1">
                                                            @php
                                                                $cleaned = str_replace(
                                                                    ['(', ')'],
                                                                    '',
                                                                    $variant->full_name,
                                                                );
                                                                $parts = explode(',', $cleaned);
                                                            @endphp
                                                            <div class="variant-name">
                                                                @foreach ($parts as $part)
                                                                    <div>{{ trim($part) }}</div>
                                                                @endforeach
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="product_variant_id" id="selectedVariantId"
                                        value="">
                                    <div class="text-end mt-2">
                                        <button type="button" id="cancelVariantSelection"
                                            class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times-circle me-1"></i> Hủy chọn
                                        </button>
                                    </div>
                                </div>

                            @endif

                            {{-- CHỌN SỐ LƯỢNG --}}
                            <div class="d-flex align-items-center gap-4">
                                <label class="form-label fw-bold mb-0" style="font-size: 16px;">
                                    <i class="fas fa-sort-numeric-up me-2 text-success"></i>Số lượng:
                                </label>
                                <div class="quantity-control d-flex align-items-center">
                                    <button type="button" class="quantity-btn btn-minus">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity" class="quantity-input"
                                        value="1" min="1">
                                    <button type="button" class="quantity-btn btn-plus">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                @php
                                    $hasVariants = $product->variants->count() > 0;
                                    $totalStock = $hasVariants
                                        ? $product->variants->sum('quantity_in_stock')
                                        : $product->quantity_in_stock;
                                @endphp

                                <div class="available-stock text-muted ms-3" id="availableStock">
                                    {{ $totalStock }} sản phẩm có sẵn
                                </div>

                            </div>
                            <button type="submit" class="add-to-cart-btn w-100">
                                <i class="fa fa-shopping-bag me-2"></i>
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                    {{-- PHẦN MÔ TẢ VÀ ĐÁNH GIÁ --}}
                    @php
                        $hasRated = $product->comments->contains('user_id', Auth::id());
                    @endphp
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs mb-3">
                                <button class="nav-link active border-white border-bottom-0" type="button"
                                    role="tab" id="nav-about-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-about" aria-controls="nav-about" aria-selected="true">
                                    <i class="fas fa-info-circle me-2"></i>Mô tả
                                </button>
                                <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                    id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                    aria-controls="nav-mission" aria-selected="false">
                                    <i class="fas fa-star me-2"></i>Đánh giá
                                </button>
                            </div>
                        </nav>
                        <div class="tab-content mb-5">
                            <div class="tab-pane active" id="nav-about" role="tabpanel"
                                aria-labelledby="nav-about-tab">
                                <div class="p-4 bg-light rounded">
                                    <p style="line-height: 1.8;">{!! nl2br(e($product->description ?? 'Không có mô tả.')) !!}</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-mission" role="tabpanel"
                                aria-labelledby="nav-mission-tab">
                                <h4 class="mb-4 fw-bold text-uppercase section-title" style="color: #1a202c;">
                                    Đánh giá của người dùng
                                </h4>
                                @forelse($product->comments->where('status', 1) as $comment)
                                    <div class="d-flex mb-4 border rounded shadow-sm p-4 bg-white">
                                        <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('clients/img/avatar.jpg') }}"
                                            class="img-fluid rounded-circle me-3" style="width: 60px; height: 60px;"
                                            alt="Avatar">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 fw-bold">{{ $comment->user->name ?? 'Ẩn danh' }}</h6>
                                                <small
                                                    class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                            @php
                                                $rating = is_numeric($comment->rating) ? (int) $comment->rating : 0;
                                            @endphp
                                            <div class="mb-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star"
                                                        style="color: {{ $i <= $rating ? '#ffc107' : '#ccc' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="mb-0 text-dark">{{ $comment->content }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Chưa có bình luận nào cho sản phẩm này.</p>
                                    </div>
                                @endforelse

                            </div>
                        </div>
                    </div>
                    {{-- FORM BÌNH LUẬN --}}

                    @if (Auth::check() && $hasPurchased && !$hasReviewed)
                        <form action="{{ route('comments.store') }}" method="POST"
                            class="bg-light p-4 p-md-5 rounded shadow-sm">
                            @csrf
                            <h4 class="mb-4 fw-bold text-uppercase text-primary section-title"
                                style="color: #1a202c !important;">Để lại đánh giá</h4>

                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="mb-4">
                                <label for="content" class="form-label fw-semibold">Đánh giá của bạn *</label>
                                <textarea id="content" name="content" class="form-control rounded-3" rows="6"
                                    placeholder="Hãy chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold d-block">Chọn số sao:</label>
                                <div class="rating d-flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star fa-lg text-muted star" data-rating="{{ $i }}"
                                            style="cursor: pointer; transition: all 0.3s ease;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-value" value="0">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn px-5 py-2 rounded-pill"
                                    style="background-color: #e0806d; border-color: #e0806d; color: white;">
                                    <i class="fa fa-paper-plane me-2"></i> Gửi Đánh Giá
                                </button>

                            </div>
                        </form>
                    @elseif (Auth::check() && $hasPurchased && $hasReviewed)
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            Bạn đã đánh giá sản phẩm này. Cảm ơn bạn!
                        </div>
                    @elseif(Auth::check())
                        <div class="alert alert-warning text-center py-4">
                            <i class="fas fa-exclamation-circle me-2 text-warning"></i>
                            Bạn cần mua sản phẩm này trước khi có thể đánh giá.
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="mb-3">Bạn cần đăng nhập để có thể đánh giá sản phẩm</p>
                            <a href="{{ route('login') }}" class="btn rounded-pill px-4"
                                style="background-color: #d67054; border: none; color: #fff;">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- <pre>{{ dd($item->variants) }}</pre> --}}
        {{-- SẢN PHẨM LIÊN QUAN --}}
        <h2 class="fw-bold mb-4 section-title">SẢN PHẨM LIÊN QUAN</h2>
        <div class="related-products-carousel"><button class="carousel-nav prev" id="prevBtn"
                onclick="moveCarousel(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-nav next" id="nextBtn" onclick="moveCarousel(1)">
                <i class="fas fa-chevron-right"></i>
            </button>

            <div class="carousel-container">
                {{-- Nút điều hướng --}}

                {{-- Container carousel --}}
                <div class="carousel-wrapper" id="carouselWrapper">
                    @forelse ($relatedProducts as $item)
                        @php
                            $hasVariants = $item->variants->isNotEmpty();
                            $firstVariant = $item->variants->first();

                            if ($hasVariants) {
                                $prices = $item->variants->pluck('price')->filter();
                                $minPrice = $prices->min() ?? 0;
                                $maxPrice = $prices->max() ?? 0;
                            } else {
                                $price = $item->discounted_price ?? 0;
                                $original = $item->original_price ?? 0;
                                $hasDiscount = $original > $price;
                                $discountPercent = $hasDiscount ? round((($original - $price) / $original) * 100) : 0;
                            }
                        @endphp

                        <div class="product-item">
                            <div class="border rounded shadow-sm h-100 p-3 position-relative">
                                {{-- Ảnh sản phẩm có thể click --}}
                                <div class="position-relative mb-2">
                                    <a href="{{ route('product-detail.show', $item->id) }}" class="d-block">
                                        <img src="{{ asset('storage/' . ($firstVariant?->image ?? $item->image)) }}"
                                            class="img-fluid rounded w-100" alt="{{ $item->product_name }}"
                                            style="height: 200px; object-fit: cover; transition: transform 0.3s ease; cursor: pointer;">

                                    </a>

                                    {{-- Badge --}}
                                    @if (!empty($item->badge))
                                        <span
                                            class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 rounded-end small">
                                            {{ $item->badge }}
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-muted mb-1 small">
                                        {{ strtoupper($item->category?->category_name ?? 'SẢN PHẨM') }}
                                    </p>
                                    <h6 class="text-dark fw-bold mb-2">{{ $item->product_name }}</h6>

                                    {{-- Phần giá cải tiến --}}
                                    <div class="mb-2">
                                        @if ($hasVariants)
                                            <span class="text-danger fw-bold">
                                                {{ number_format($minPrice, 0, ',', '.') }}đ
                                                @if ($minPrice !== $maxPrice)
                                                    - {{ number_format($maxPrice, 0, ',', '.') }}đ
                                                @endif
                                            </span>
                                        @else
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-danger fw-bold fs-6">{{ number_format($price, 0, ',', '.') }}đ</span>
                                                @if ($hasDiscount)
                                                    <div class="d-flex align-items-center gap-1">
                                                        <del
                                                            class="text-muted small">{{ number_format($original, 0, ',', '.') }}đ</del>
                                                        <span
                                                            class="bg-danger text-white px-2 py-1 rounded small fw-bold"
                                                            style="font-size: 0.75rem;">
                                                            -{{ $discountPercent }}%
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Ảnh các biến thể ở dưới giá --}}
                                    @if ($hasVariants && $item->variants->count() > 1)
                                        <div class="d-flex flex-wrap gap-1 justify-content-start">
                                            @foreach ($item->variants->take(5) as $variant)
                                                <img src="{{ asset('storage/' . ($variant->image ?? $item->image)) }}"
                                                    alt="variant" class="rounded border"
                                                    style="width: 30px; height: 30px; object-fit: cover; cursor: pointer;"
                                                    title="Variant {{ $loop->iteration }}">
                                            @endforeach
                                            @if ($item->variants->count() > 5)
                                                <div class="d-flex align-items-center justify-content-center rounded border bg-light"
                                                    style="width: 30px; height: 30px; font-size: 0.7rem; color: #666;">
                                                    +{{ $item->variants->count() - 5 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">Không có sản phẩm liên quan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');

        if (!stars.length || !ratingInput) return;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;

                stars.forEach(s => {
                    const sRating = s.getAttribute('data-rating');
                    if (parseInt(sRating) <= rating) {
                        s.classList.remove('text-muted');
                        s.classList.add('text-warning');
                    } else {
                        s.classList.remove('text-warning');
                        s.classList.add('text-muted');
                    }
                });
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const variantOptions = document.querySelectorAll('.variant-option');
        const mainImage = document.getElementById('mainProductImage');
        const priceElement = document.getElementById('variantPrice');
        const originalPriceElement = document.getElementById('originalPrice');
        const discountPercentElement = document.getElementById('discountPercent');
        const stockElement = document.getElementById('availableStock');
        const quantityInput = document.getElementById('quantity');
        const cancelButton = document.getElementById('cancelVariantSelection');
        const selectedVariantIdInput = document.getElementById('selectedVariantId');

        let selectedVariant = null;

        function formatCurrency(number) {
            return number.toLocaleString('vi-VN');
        }

        function updatePriceDisplay(price, original) {
            if (priceElement) priceElement.textContent = 'đ' + formatCurrency(price);

            if (originalPriceElement && discountPercentElement) {
                if (original > price) {
                    originalPriceElement.textContent = 'đ' + formatCurrency(original);
                    originalPriceElement.style.display = 'inline';
                    const percent = Math.round((1 - price / original) * 100);
                    discountPercentElement.textContent = `-${percent}%`;
                    discountPercentElement.style.display = 'inline-block';
                } else {
                    originalPriceElement.style.display = 'none';
                    discountPercentElement.style.display = 'none';
                }
            }
        }

        function updateMainImage(imageUrl) {
            if (!mainImage || !imageUrl) return;
            mainImage.src = imageUrl;
        }

        function resetToDefault() {
            selectedVariant = null;
            selectedVariantIdInput.value = '';

            variantOptions.forEach(opt => opt.classList.remove('selected'));

            const min = parseInt(document.getElementById('minPrice')?.value || 0);
            const max = parseInt(document.getElementById('maxPrice')?.value || 0);
            if (priceElement) {
                if (min && max && min !== max) {
                    priceElement.textContent = `đ${formatCurrency(min)} - đ${formatCurrency(max)}`;
                } else {
                    priceElement.textContent = `đ${formatCurrency(min)}`;
                }
            }

            if (originalPriceElement) originalPriceElement.style.display = 'none';
            if (discountPercentElement) discountPercentElement.style.display = 'none';

            if (mainImage?.dataset.originalSrc) {
                mainImage.src = mainImage.dataset.originalSrc;
            }

            if (stockElement) {
                const totalStock = Array.from(variantOptions).reduce((sum, opt) => {
                    return sum + (parseInt(opt.dataset.variantStock || '0'));
                }, 0);
                stockElement.textContent = `${totalStock} sản phẩm có sẵn`;
            }


            if (quantityInput) quantityInput.value = 1;
        }

        variantOptions.forEach(option => {
            option.addEventListener('click', () => {
                variantOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');

                const price = parseFloat(option.dataset.variantPrice);
                const original = parseFloat(option.dataset.variantOriginal || option.dataset
                    .variantPrice);
                const image = option.dataset.variantImage;
                const stock = parseInt(option.dataset.variantStock || '0');
                const id = option.dataset.variantId;

                selectedVariant = {
                    id,
                    price,
                    original,
                    stock,
                    image
                };
                selectedVariantIdInput.value = id;

                updatePriceDisplay(price, original);
                updateMainImage(image);

                if (stockElement) stockElement.textContent = `${stock} sản phẩm có sẵn`;

                if (quantityInput && quantityInput.value > stock) {
                    quantityInput.value = stock;
                }
            });
        });

        if (cancelButton) {
            cancelButton.addEventListener('click', resetToDefault);
        }

        // Quantity buttons
        document.querySelector('.btn-minus')?.addEventListener('click', () => {
            let value = parseInt(quantityInput.value) || 1;
            if (value > 1) quantityInput.value = value - 1;
        });

        document.querySelector('.btn-plus')?.addEventListener('click', () => {
            let value = parseInt(quantityInput.value) || 1;
            const max = selectedVariant?.stock || 9999;
            if (value < max) quantityInput.value = value + 1;
        });

        // Init
        resetToDefault();
        const addToCartBtn = document.querySelector('.add-to-cart-btn');
        const productType = "{{ $product->product_type }}"; // Laravel blade

        addToCartBtn?.addEventListener('click', function(e) {
            if (productType === 'variant') {
                if (!selectedVariant || !selectedVariantIdInput.value) {
                    e.preventDefault();
                    alert('Vui lòng chọn biến thể trước khi thêm vào giỏ hàng.');
                }
            }
        });
    });
</script>
{{-- JavaScript cho carousel liên quan --}}
<script>
    let currentIndex = 0;
    const carousel = document.getElementById('carouselWrapper');
    const items = carousel.children;
    const totalItems = items.length;

    // Tính số items hiển thị theo màn hình
    function getItemsPerView() {
        const width = window.innerWidth;
        if (width <= 576) return 1;
        if (width <= 768) return 2;
        if (width <= 992) return 3;
        return 4;
    }

    function updateCarousel() {
        const itemsPerView = getItemsPerView();
        const maxIndex = Math.max(0, totalItems - itemsPerView);

        // Giới hạn currentIndex
        if (currentIndex > maxIndex) {
            currentIndex = maxIndex;
        }

        // Tính toán offset
        const itemWidth = items[0] ? items[0].offsetWidth : 0;
        const gap = 15; // gap giữa các items
        const offset = currentIndex * (itemWidth + gap);

        carousel.style.transform = `translateX(-${offset}px)`;

        // Cập nhật trạng thái nút
        document.getElementById('prevBtn').disabled = currentIndex === 0;
        document.getElementById('nextBtn').disabled = currentIndex >= maxIndex;
    }

    function moveCarousel(direction) {
        const itemsPerView = getItemsPerView();
        const maxIndex = Math.max(0, totalItems - itemsPerView);

        currentIndex += direction;

        if (currentIndex < 0) {
            currentIndex = 0;
        } else if (currentIndex > maxIndex) {
            currentIndex = maxIndex;
        }

        updateCarousel();
    }

    // Khởi tạo carousel
    document.addEventListener('DOMContentLoaded', function() {
        updateCarousel();
    });

    // Cập nhật khi resize window
    window.addEventListener('resize', function() {
        updateCarousel();
    });
</script>

{{-- CSS cho  carousel liên quan --}}
<style>
    .related-products-carousel {
        position: relative;
        margin: 20px 0;
        padding: 0px;
        /* Thêm padding để có chỗ cho nút */
    }

    .carousel-container {
        position: relative;
        overflow: hidden;
    }

    .carousel-wrapper {
        display: flex;
        transition: transform 0.3s ease;
        gap: 15px;
    }

    .product-item {
        flex: 0 0 auto;
        width: calc(25% - 11.25px);
        /* 4 items per view */
    }

    @media (max-width: 992px) {
        .product-item {
            width: calc(33.333% - 10px);
            /* 3 items per view */
        }
    }

    @media (max-width: 768px) {
        .product-item {
            width: calc(50% - 7.5px);
            /* 2 items per view */
        }

        .related-products-carousel {
            padding: 0 50px;
            /* Giảm padding trên mobile */
        }

        .carousel-nav.prev {
            left: -40px;
        }

        .carousel-nav.next {
            right: -40px;
        }
    }

    @media (max-width: 576px) {
        .product-item {
            width: calc(100% - 0px);
            /* 1 item per view */
        }

        .related-products-carousel {
            padding: 0 30px;
            /* Padding nhỏ hơn trên mobile nhỏ */
        }

        .carousel-nav {
            width: 35px;
            height: 35px;
        }

        .carousel-nav.prev {
            left: -25px;
        }

        .carousel-nav.next {
            right: -25px;
        }
    }

    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: 1px solid #ddd;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .carousel-nav:hover {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .carousel-nav.prev {
        left: -50px;
    }

    .carousel-nav.next {
        right: -50px;
    }

    .carousel-nav:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8f9fa;
    }

    .carousel-nav:disabled:hover {
        background: #f8f9fa;
        color: #6c757d;
    }

    .related-product-carousel .card img:hover,
    a img:hover {
        transform: scale(1.05);
    }

    .product-badge {
        font-size: 0.75rem;
        background: red;
        color: white;
        padding: 2px 6px;
        border-radius: 0 8px 8px 0;
    }
</style>
@include('clients.layouts.footer')
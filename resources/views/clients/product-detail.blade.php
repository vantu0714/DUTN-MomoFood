@include('clients.layouts.header')
@include('clients.layouts.sidebar')
{{-- @vite('resources/css/shop.css') --}}
<link rel="stylesheet" href="{{ asset('clients/css/shop-detail.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Chi Tiết Sản Phẩm</h1>
</div>

<!-- Single Product Start -->
<div class="container-fluid  ">
    <div class="container py-3">
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

                        <h3 class="product-price mb-4 d-flex align-items-center" id="productPriceDisplay">
                            @if ($product->variants && $product->variants->count())
                                <span id="variantPrice" class="fw-bold text-danger" style="font-size: 2rem;">
                                    đ{{ number_format($minPrice, 0, ',', '.') }}
                                    @if ($minPrice != $maxPrice)
                                        - đ{{ number_format($maxPrice, 0, ',', '.') }}
                                    @endif
                                </span>

                                {{-- Các giá trị mặc định để JS reset --}}
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
                                {{-- Với sản phẩm đơn --}}
                                <span
                                    class="price-amount fw-bold me-2 {{ $isDiscounted ? 'text-danger' : 'text-dark' }}"
                                    style="font-size: 2rem;" id="variantPrice">
                                    đ{{ number_format($isDiscounted ? $product->discounted_price : $product->original_price, 0, ',', '.') }}
                                </span>

                                <span class="original-price text-muted text-decoration-line-through me-2"
                                    id="originalPrice"
                                    style="font-size: 1.5rem; {{ !$isDiscounted ? 'display: none;' : '' }}">
                                    đ{{ number_format($product->original_price, 0, ',', '.') }}
                                </span>

                                <span class="discount-percent badge bg-danger-subtle text-danger fw-semibold"
                                    id="discountPercent"
                                    style="font-size: 1.5rem; {{ !$isDiscounted ? 'display: none;' : '' }}">
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
                                <div class="available-stock text-muted ms-3" id="availableStock">
                                    {{ $product->variants->first()?->quantity_in_stock ?? $product->quantity_in_stock }}
                                    sản phẩm có sẵn
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
                                <h4 class="mb-4 fw-bold text-uppercase text-primary section-title">Đánh giá của người
                                    dùng</h4>
                                @forelse($product->comments as $comment)
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
                            <h4 class="mb-4 fw-bold text-uppercase text-primary section-title">Để lại đánh giá</h4>
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
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill">
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
                            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- SẢN PHẨM LIÊN QUAN --}}
        <h2 class="fw-bold mb-4 section-title">SẢN PHẨM LIÊN QUAN</h2>
        <div class="vesitable">
            <div class="owl-carousel vegetable-carousel justify-content-center">
                @foreach ($relatedProducts as $item)
                    <div class="border border-success rounded vesitable-item h-100 d-flex flex-column">
                        <div class="vesitable-img">
                            <img src="{{ asset('storage/' . $item->image) }}" class="img-fluid w-100 rounded-top"
                                alt="">
                        </div>
                        <div class="text-white bg-success px-3 py-1 rounded position-absolute"
                            style="top: 10px; right: 10px;">
                            {{ $item->category?->category_name ?? 'Sản phẩm' }}
                        </div>
                        <div class="p-4 pb-0 rounded-bottom d-flex flex-column flex-grow-1">
                            <h5>{{ $item->product_name }}</h5>
                            <p class="flex-grow-1 text-muted">{{ Str::limit($item->description, 60) }}</p>
                            <div class="d-flex justify-content-between flex-wrap align-items-end">
                                <p class="text-dark fs-6 fw-bold mb-0">
                                    {{ number_format($item->discounted_price ?? 0, 0, ',', '.') }} VND
                                </p>
                                <a href="{{ route('product-detail.show', $item->id) }}"
                                    class="btn btn-outline-success rounded-pill btn-sm mt-2">
                                    <i class="fa fa-shopping-bag me-1 text-success"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- Single Product End -->
{{-- SCRIPT CHỌN SAO --}}



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
                const defaultStock = parseInt(document.getElementById('originalStock')?.value || 0);
                stockElement.textContent = `${defaultStock} sản phẩm có sẵn`;
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
    });
</script>
<!-- Footer Start -->
@include('clients.layouts.footer')

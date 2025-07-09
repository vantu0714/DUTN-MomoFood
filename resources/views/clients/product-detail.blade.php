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
<div class="container-fluid py-5 mt-5">
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
                        <h3 class="product-price mb-4 d-flex align-items-center">
                            @php
                                $isDiscounted =
                                    $product->discounted_price && $product->discounted_price < $product->original_price;
                                $discountPercent = $isDiscounted
                                    ? round((1 - $product->discounted_price / $product->original_price) * 100)
                                    : 0;
                            @endphp
                            {{-- Giá sau giảm (nếu có) --}}
                            <span class="price-amount fw-bold me-2 {{ $isDiscounted ? 'text-danger' : 'text-dark' }}"
                                style="font-size: 2rem;" data-original-price="{{ $product->discounted_price }}"
                                data-original-original="{{ $product->original_price }}">
                                <span
                                    class="text-decoration-none">đ</span>{{ number_format($isDiscounted ? $product->discounted_price : $product->original_price, 0, ',', '.') }}
                            </span>
                            {{-- Giá gốc --}}
                            <span class="original-price text-muted text-decoration-line-through me-2"
                                style="font-size: 1.5rem; {{ !$isDiscounted ? 'display: none;' : '' }}">
                                đ{{ number_format($product->original_price, 0, ',', '.') }}
                            </span>
                            {{-- Giảm giá phần trăm --}}
                            <span class="discount-percent badge bg-danger-subtle text-danger fw-semibold"
                                style="font-size: 1.5rem; {{ !$isDiscounted ? 'display: none;' : '' }}">
                                -{{ $discountPercent }}%
                            </span>
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
                                                    data-variant-price="{{ $variant->formatted_final_price }}"
                                                    data-variant-image="{{ $variant->image_url }}"
                                                    data-variant-stock="{{ $variant->quantity_in_stock }}"
                                                    style="cursor: pointer;">
                                                    <div class="variant-content">
                                                        @if ($variant->image_url)
                                                            <img src="{{ $variant->image_url }}"
                                                                class="variant-image"
                                                                alt="{{ trim(str_replace(['(', ')'], '', $variant->full_name)) }}">
                                                        @endif
                                                        <div class="flex-grow-1">
                                                            <div class="variant-name">
                                                                {{ trim(str_replace(['(', ')'], '', $variant->full_name)) }}
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
                    @if (Auth::check() && $hasPurchased)
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


                    {{-- SCRIPT CHỌN SAO --}}
                    @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const stars = document.querySelectorAll('.star');
                                const ratingInput = document.getElementById('rating-value');

                                stars.forEach(star => {
                                    star.addEventListener('click', function() {
                                        const rating = this.getAttribute('data-rating');
                                        ratingInput.value = rating;

                                        // Highlight sao đã chọn
                                        stars.forEach(s => {
                                            if (s.getAttribute('data-rating') <= rating) {
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
                        </script>
                    @endpush

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedVariant = null;

        const variantOptions = document.querySelectorAll('[data-variant-id]');
        const thumbnails = document.querySelectorAll('.variant-thumbnail');
        const priceElement = document.querySelector('.price-amount');
        const originalPriceElement = document.querySelector('.original-price');
        const discountPercentElement = document.querySelector('.discount-percent');
        const mainImage = document.getElementById('mainProductImage');
        const quantityInput = document.querySelector('#quantity');
        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const stockElement = document.getElementById('availableStock');
        const form = document.getElementById('addToCartForm');
        const cancelButton = document.getElementById('cancelVariantSelection');

        function updateMainImage(newSrc) {
            if (!mainImage || !newSrc) return;
            mainImage.style.opacity = '0.7';
            mainImage.style.transform = 'scale(0.95)';
            setTimeout(() => {
                mainImage.src = newSrc;
                mainImage.style.opacity = '1';
                mainImage.style.transform = 'scale(1)';
            }, 200);
        }

        function highlightThumbnail(imageSrc) {
            thumbnails.forEach(thumb => {
                const thumbImage = thumb.getAttribute('data-full-image');
                if (thumbImage === imageSrc) {
                    thumb.classList.add('border-success');
                } else {
                    thumb.classList.remove('border-success');
                }
            });
        }

        function formatCurrency(number) {
            return number.toLocaleString('vi-VN');
        }

        function updatePriceDisplay(discounted, original) {
            if (!priceElement) return;
            priceElement.textContent = 'đ' + formatCurrency(discounted);

            if (original > discounted) {
                if (originalPriceElement) {
                    originalPriceElement.textContent = 'đ' + formatCurrency(original);
                    originalPriceElement.style.display = 'inline';
                }
                if (discountPercentElement) {
                    const percent = Math.round((1 - discounted / original) * 100);
                    discountPercentElement.textContent = `-${percent}%`;
                    discountPercentElement.style.display = 'inline-block';
                }
            } else {
                if (originalPriceElement) {
                    originalPriceElement.textContent = '';
                    originalPriceElement.style.display = 'none';
                }
                if (discountPercentElement) {
                    discountPercentElement.textContent = '';
                    discountPercentElement.style.display = 'none';
                }
            }
        }

        variantOptions.forEach(option => {
            option.addEventListener('click', function() {
                variantOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');

                selectedVariant = {
                    id: this.dataset.variantId || '',
                    name: this.dataset.variantName || '',
                    price: this.dataset.variantPrice || '',
                    original: this.dataset.variantOriginal || this.dataset.variantPrice,
                    image: this.dataset.variantImage || '',
                    stock: parseInt(this.dataset.variantStock || '0')
                };

                document.getElementById('selectedVariantId').value = selectedVariant.id;

                if (selectedVariant.image) {
                    updateMainImage(selectedVariant.image);
                    highlightThumbnail(selectedVariant.image);
                }

                const discounted = parseInt(selectedVariant.price.replace(/[^\d]/g, '')) || 0;
                const original = parseInt(selectedVariant.original.replace(/[^\d]/g, '')) ||
                    discounted;

                updatePriceDisplay(discounted, original);

                if (stockElement) {
                    stockElement.textContent = `${selectedVariant.stock} sản phẩm có sẵn`;
                }

                if (quantityInput && selectedVariant.stock > 0) {
                    if (parseInt(quantityInput.value) > selectedVariant.stock) {
                        quantityInput.value = selectedVariant.stock;
                    }
                }
            });
        });

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const fullImage = this.getAttribute('data-full-image');
                if (fullImage) {
                    updateMainImage(fullImage);
                    highlightThumbnail(fullImage);
                }
            });
        });

        // Star rating
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');

        function highlightStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-muted');
                    star.classList.add('text-warning');
                } else {
                    star.classList.remove('text-warning');
                    star.classList.add('text-muted');
                }
            });
        }

        if (stars.length > 0 && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('mouseenter', function() {
                    highlightStars(parseInt(this.dataset.rating));
                });
                star.addEventListener('mouseleave', function() {
                    highlightStars(parseInt(ratingInput.value));
                });
                star.addEventListener('click', function() {
                    ratingInput.value = this.dataset.rating;
                    highlightStars(parseInt(this.dataset.rating));
                });
            });
        }

        // Quantity control
        if (minusBtn) {
            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let value = parseInt(quantityInput.value) || 1;
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });
        }

        if (plusBtn) {
            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let value = parseInt(quantityInput.value) || 1;
                let max = selectedVariant?.stock || 9999;
                if (value < max) {
                    quantityInput.value = value + 1;
                }
            });
        }

        // Validate form before submit
        if (form) {
            form.addEventListener('submit', function(e) {
                const hasVariants = variantOptions.length > 0;
                if (hasVariants && !selectedVariant) {
                    e.preventDefault();
                    alert('Vui lòng chọn loại sản phẩm!');
                }
            });
        }

        // ❌ Cancel variant selection
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                selectedVariant = null;
                document.getElementById('selectedVariantId').value = '';

                // Bỏ chọn tất cả biến thể
                variantOptions.forEach(opt => opt.classList.remove('selected'));

                // Reset ảnh sản phẩm
                if (mainImage && mainImage.dataset.originalSrc) {
                    updateMainImage(mainImage.dataset.originalSrc);
                }

                // Reset giá
                const defaultPrice = parseInt(document.getElementById('originalPrice').value || 0);
                const defaultOriginalPrice = parseInt(document.getElementById('originalOriginalPrice')
                    .value || 0);
                updatePriceDisplay(defaultPrice, defaultOriginalPrice);

                // Reset tồn kho
                const defaultStock = parseInt(document.getElementById('originalStock').value || 0);
                if (stockElement) {
                    stockElement.textContent = `${defaultStock} sản phẩm có sẵn`;
                }

                // Reset số lượng
                if (quantityInput) {
                    quantityInput.value = 1;
                }

                // Bỏ chọn hình nhỏ
                highlightThumbnail(null);
            });
        }

    });
</script>



<!-- Footer Start -->
@include('clients.layouts.footer')

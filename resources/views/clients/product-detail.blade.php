@include('clients.layouts.header')
@include('clients.layouts.sidebar')
{{-- @vite('resources/css/shop.css') --}}
<link rel="stylesheet" href="{{ asset('clients/css/shop-detail.css') }}">
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Chi Tiết Sản Phẩm</h1>
</div>
<!-- Single Product Start -->
<div class="container-fluid py-5 mt-5">
    <div class="container py-3">
        <div class="row g-4 mb-5 justify-content-center">
            <div class="col-lg-8 col-xl-9">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="product-image-container">
                            <img id="mainProductImage" src="{{ asset('storage/' . $product->image) }}"
                                class="product-image" alt="{{ $product->product_name }}">
                            <div class="image-overlay"></div>
                            <div class="zoom-icon">
                                <i class="fas fa-search-plus text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 product-info">
                        <h2 class="fw-bold text-dark mb-3 section-title">{{ $product->product_name }}</h2>

                        <p class="text-muted mb-3">
                            <i class="fas fa-tag me-2"></i>
                            <strong>Danh mục:</strong> {{ $product->category?->category_name ?? 'Không có danh mục' }}
                        </p>

                        <h3 class="product-price mb-4 d-flex align-items-baseline">
                            @if ($product->discounted_price && $product->discounted_price < $product->original_price)
                                <span class="price-amount fw-bold text-danger me-3" style="font-size: 2.2rem;">
                                    {{ number_format($product->discounted_price, 0, ',', '.') }}
                                </span>
                                <span class="original-price text-muted text-decoration-line-through"
                                    style="font-size: 1.5rem;">
                                    {{ number_format($product->original_price, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="price-amount fw-bold" style="font-size: 2.2rem;">
                                    {{ number_format($product->original_price, 0, ',', '.') }}
                                </span>
                            @endif
                            <span class="currency ms-2 text-muted">VND</span>
                        </h3>


                        <div class="rating-stars mb-4">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= 4 ? 'text-warning' : 'text-muted' }}"
                                    style="font-size: 1.2rem;"></i>
                            @endfor
                            <span class="ms-2 text-muted fw-semibold">(4.0 / 5)</span>
                        </div>

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
                                                    style="cursor: pointer;">
                                                    <div class="variant-content">
                                                        @if ($variant->image_url)
                                                            <img src="{{ $variant->image_url }}" class="variant-image"
                                                                alt="{{ trim(str_replace(['(', ')'], '', $variant->full_name)) }}">
                                                        @endif
                                                        <div class="flex-grow-1">
                                                            <!-- Option 1: Single line with all info -->
                                                            <div class="variant-name">
                                                                {{ trim(str_replace(['(', ')'], '', $variant->full_name)) }}
                                                            </div>

                                                            <!-- Option 2: Separate Vị and Size on same line (uncomment to use) -->
                                                            <!--
                                <div class="variant-name-inline">
                                    <span class="variant-flavor">Vị: {{ $variant->flavor ?? 'Cay' }}</span>
                                  
                                    <span class="variant-size">Size: {{ $variant->size ?? 'M' }}</span>
                                </div>
                                -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="product_variant_id" id="selectedVariantId"
                                        value="">
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
                            </div>

                            <button type="submit" class="add-to-cart-btn w-100">
                                <i class="fa fa-shopping-bag me-2"></i>
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                    {{-- PHẦN MÔ TẢ VÀ ĐÁNH GIÁ --}}
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs mb-3">
                                <button class="nav-link active border-white border-bottom-0" type="button"
                                    role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                    aria-controls="nav-about" aria-selected="true">
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
                                            <div class="d-flex mb-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i <= $comment->rating ? 'text-warning' : 'text-secondary' }}"></i>
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
                    @if (Auth::check())
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedVariant = null;
        // Xử lý chọn biến thể
        const variantOptions = document.querySelectorAll('[data-variant-id]');
        variantOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Bỏ chọn variant hiện tại
                variantOptions.forEach(opt => opt.classList.remove('selected'));

                // Chọn variant mới
                this.classList.add('selected');
                selectedVariant = {
                    id: this.dataset.variantId,
                    name: this.dataset.variantName,
                    price: this.dataset.variantPrice,
                    image: this.dataset.variantImage
                };
                document.getElementById('selectedVariantId').value = selectedVariant.id;
                // Cập nhật ảnh chính với hiệu ứng smooth
                if (selectedVariant.image) {
                    const mainImage = document.getElementById('mainProductImage');
                    mainImage.style.opacity = '0.7';
                    mainImage.style.transform = 'scale(0.95)';

                    setTimeout(() => {
                        mainImage.src = selectedVariant.image;
                        mainImage.style.opacity = '1';
                        mainImage.style.transform = 'scale(1)';
                    }, 200);
                }

                // Cập nhật giá
                const priceElement = document.querySelector('.price-amount');
                if (priceElement && selectedVariant.price) {
                    // Lấy số từ chuỗi price (ví dụ: "50,000 VND" -> 50000)
                    const numericPrice = selectedVariant.price.replace(/[^\d]/g, '');
                    if (numericPrice) {
                        priceElement.textContent = parseInt(numericPrice).toLocaleString(
                            'vi-VN');
                    }
                }
            });
        });
        // Xử lý rating (chọn sao)
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');

        if (stars.length > 0 && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(rating);
                });

                star.addEventListener('mouseleave', function() {
                    const currentRating = parseInt(ratingInput.value);
                    highlightStars(currentRating);
                });

                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    ratingInput.value = rating;
                    highlightStars(rating);
                });
            });
        }

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
        // Xử lý tăng giảm số lượng
        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityInput = document.querySelector('#quantity');

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
                quantityInput.value = value + 1;
            });
        }

        // Xử lý submit form
        const form = document.getElementById('addToCartForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Kiểm tra xem đã chọn biến thể chưa (nếu có)
                const hasVariants = variantOptions.length > 0;

                if (hasVariants && !selectedVariant) {
                    e.preventDefault();
                    alert('Vui lòng chọn loại sản phẩm!');
                    return;
                }
            });
        }
    });
</script>
<!-- Footer Start -->
@include('clients.layouts.footer')

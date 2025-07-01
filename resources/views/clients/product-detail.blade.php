@include('clients.layouts.header')
@include('clients.layouts.sidebar')
{{-- @vite('resources/css/shop.css') --}}
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">

<style>
    .product-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        background: linear-gradient(145deg, #f8f9fa, #ffffff);
        padding: 20px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .product-image {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 15px;
        width: 100%;
        height: 500px;
        object-fit: cover;
        filter: brightness(1.02) contrast(1.05);
    }

    .product-image:hover {
        transform: scale(1.03) rotateY(2deg);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg,
                rgba(40, 167, 69, 0.1) 0%,
                rgba(32, 201, 151, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 20px;
        pointer-events: none;
    }

    .product-image-container:hover .image-overlay {
        opacity: 1;
    }

    .zoom-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(40, 167, 69, 0.3);
    }

    .product-image-container:hover .zoom-icon {
        transform: translate(-50%, -50%) scale(1);
    }

    .variant-option {
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        text-align: center;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .variant-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(40, 167, 69, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .variant-option:hover::before {
        left: 100%;
    }

    .variant-option:hover {
        border-color: #28a745;
        background-color: #f8fff9;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
    }

    .variant-option.selected {
        border-color: #28a745;
        background: linear-gradient(145deg, #28a745, #20c997);
        color: white;
        box-shadow: 0 8px 30px rgba(40, 167, 69, 0.4);
        transform: translateY(-2px);
    }

    .variant-option.selected:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.5);
    }

    .variant-image {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .variant-option.selected .variant-image {
        border-color: rgba(255, 255, 255, 0.8);
        transform: scale(1.05);
    }

    .size-option {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }

    .color-option {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        position: relative;
    }

    .color-option::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .color-option.selected::after {
        opacity: 1;
        background: white;
        box-shadow: 0 0 0 2px #28a745;
    }

    .quantity-control {
        border: 2px solid #e9ecef;
        border-radius: 50px;
        overflow: hidden;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .quantity-btn {
        width: 45px;
        height: 45px;
        border: none;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .quantity-btn:hover {
        background: #28a745;
        color: white;
        transform: scale(1.1);
    }

    .quantity-input {
        width: 70px;
        border: none;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        background: transparent;
    }

    .add-to-cart-btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        padding: 15px 35px;
        border-radius: 50px;
        color: white;
        font-weight: bold;
        font-size: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        position: relative;
        overflow: hidden;
    }

    .add-to-cart-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .add-to-cart-btn:hover::before {
        left: 100%;
    }

    .add-to-cart-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
    }

    .add-to-cart-btn:active {
        transform: translateY(-1px);
    }

    .section-title {
        position: relative;
        display: inline-block;
        margin-bottom: 30px;
        font-size: 1.8rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 2px;
    }

    .product-info {
        padding: 20px;
    }

    .product-price {
        background: linear-gradient(135deg, #28a745, #20c997);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 2px 4px rgba(40, 167, 69, 0.2));
    }

    .rating-stars {
        background: rgba(255, 193, 7, 0.1);
        padding: 10px 15px;
        border-radius: 25px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .variant-content {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
    }

    .variant-name {
        font-weight: 600;
        font-size: 15px;
    }
</style>

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Chi Tiết Sản Phẩm</h1>
</div>
<!-- Single Page Header End -->

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
                            <span class="price-amount fw-bold" style="font-size: 2.2rem;">
                                {{ number_format($product->discounted_price, 0, ',', '.') }}
                            </span>
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

                        <form action="{{ route('carts.add') }}" method="POST" class="d-flex flex-column gap-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- CHỌN BIẾN THỂ - ĐÃ XÓA GIÁ --}}
                            @if ($product->variants && $product->variants->count())
                                <div class="variant-section">
                                    <label class="form-label fw-bold mb-3" style="font-size: 16px;">
                                        <i class="fas fa-tshirt me-2 text-success"></i>Chọn biến thể:
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
        const form = document.querySelector('form[action="{{ route('carts.add') }}"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Kiểm tra xem đã chọn biến thể chưa (nếu có)
                const hasVariants = variantOptions.length > 0;

                if (hasVariants && !selectedVariant) {
                    e.preventDefault();
                    alert('Vui lòng chọn biến thể sản phẩm!');
                    return;
                }
            });
        }
    });
</script>

<!-- Footer Start -->
@include('clients.layouts.footer')

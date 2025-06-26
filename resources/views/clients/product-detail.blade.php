@include('clients.layouts.header')
@include('clients.layouts.sidebar')
@vite('resources/css/shop.css')

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
                        <div class="border rounded">
                            <a href="#">
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid w-100 rounded-top"
                                    alt="">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 product-info">
                        <h4 class="fw-bold text-dark mb-2">{{ $product->product_name }}</h4>

                        <p class="text-muted mb-2">
                            <strong>Danh mục:</strong> {{ $product->category?->category_name ?? 'Không có danh mục' }}
                        </p>

                        <h5 class="product-price mb-3 d-flex align-items-baseline">
                            <span
                                class="price-amount">{{ number_format($product->discounted_price, 0, ',', '.') }}</span>
                            <span class="currency ms-1">VND</span>
                        </h5>

                        <div class="rating-stars mb-3">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= 4 ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </div>

                        <p class="text-muted mb-4">{{ $product->description ?? 'Không có mô tả.' }}</p>

                        <form action="{{ route('carts.add') }}" method="POST"
                            class="d-flex flex-column align-items-start gap-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">


                            <div class="input-group quantity" style="width: 140px;">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-minus">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity"
                                    class="form-control text-center border-0 fw-bold" value="1" min="1">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-plus">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>


                            <button type="submit" class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold">
                                <i class="fa fa-shopping-bag me-2"></i> Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs mb-3">
                                <button class="nav-link active border-white border-bottom-0" type="button"
                                    role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                    aria-controls="nav-about" aria-selected="true">Mô tả</button>
                                <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                    id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                    aria-controls="nav-mission" aria-selected="false">Đánh giá</button>
                            </div>
                        </nav>
                        <div class="tab-content mb-5">
                            <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                <p>{!! nl2br(e($product->description ?? 'Không có mô tả.')) !!}</p>
                            </div>

                            <div class="tab-pane fade" id="nav-mission" role="tabpanel"
                                aria-labelledby="nav-mission-tab">
                                <h4 class="mb-4 fw-bold text-uppercase text-primary">Đánh giá của người dùng</h4>

                                @forelse($product->comments as $comment)
                                    <div class="d-flex mb-4 border rounded shadow-sm p-3 bg-white">
                                        <!-- Avatar người dùng -->
                                        <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('clients/img/avatar.jpg') }}"
                                            class="img-fluid rounded-circle me-3" style="width: 100px; height: 100px;"
                                            alt="Avatar">
                                        <!-- Nội dung bình luận -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">{{ $comment->user->name ?? 'Ẩn danh' }}</h5>
                                                <small
                                                    class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                           <div class="d-flex pe-5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $comment->rating ? 'text-primary' : 'text-secondary' }}"></i>
                                    @endfor
                                </div>
                                            <!-- Nội dung -->
                                            <p class="mb-0 text-dark">{{ $comment->content }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">Chưa có bình luận nào cho sản phẩm này.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @if (Auth::check())
                        <form action="{{ route('comments.store') }}" method="POST"
                            class="bg-light p-4 p-md-5 rounded shadow-sm">
                            @csrf

                            <h4 class="mb-4 fw-bold text-uppercase text-primary">Bình luận</h4>

                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="mb-4">
                                <label for="content" class="form-label fw-semibold">Đánh giá của bạn *</label>
                                <textarea id="content" name="content" class="form-control rounded-3" rows="6" placeholder="Hãy viết gì đó..."
                                    required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold d-block">Chọn số sao:</label>
                                <div class="rating d-flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star fa-lg text-muted star"
                                            data-rating="{{ $i }}"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-value" value="0">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill">
                                    <i class="fa fa-paper-plane me-2"></i> Gửi Bình Luận
                                </button>
                            </div>
                        </form>
                    @else
                        <p><a href="{{ route('login') }}">Đăng nhập</a> để gửi bình luận.</p>
                    @endif
                </div>
            </div>
        </div>
        <h1 class="fw-bold mb-0">SẢN PHẨM LIÊN QUAN</h1>
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
        // Xử lý rating (chọn sao)
        const stars = document.querySelectorAll('.star');
        let selectedRating = 0;

        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                stars.forEach((s, i) => {
                    s.classList.toggle('hovered', i <= index);
                });
            });

            star.addEventListener('mouseleave', () => {
                stars.forEach((s, i) => {
                    s.classList.toggle('hovered', i < selectedRating);
                });
            });

            star.addEventListener('click', () => {
                selectedRating = index + 1;
                stars.forEach((s, i) => {
                    s.classList.toggle('hovered', i < selectedRating);
                });
                console.log('Đã chọn sao:', selectedRating);
            });
        });

        // Xử lý tăng giảm số lượng
        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityInput = document.querySelector('#quantity');

        if (quantityInput) {
            quantityInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }

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
    });

    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;

                // Reset màu toàn bộ
                stars.forEach(s => s.classList.remove('text-warning'));
                stars.forEach(s => s.classList.add('text-muted'));

                // Đổi màu từ 1 đến ngôi sao được chọn
                for (let i = 0; i < rating; i++) {
                    stars[i].classList.remove('text-muted');
                    stars[i].classList.add('text-warning');
                }
            });
        });
    });
</script>
<!-- Footer Start -->
@include('clients.layouts.footer')

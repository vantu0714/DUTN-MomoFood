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
                    <div class="col-lg-6">
                        <h4 class="fw-bold mb-3">{{ $product->product_name }}</h4>
                        <p class="mb-3">Danh mục sản phẩm:
                            {{ $product->category?->category_name ?? 'Không có danh mục' }}</p>
                        <h5 class="fw-bold mb-3">{{ number_format($product->discounted_price, 0, ',', '.') }} VND</h5>

                        <div class="d-flex mb-4">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= 4 ? 'text-secondary' : '' }}"></i>
                            @endfor
                        </div>

                        <p class="mb-4">{{ $product->description ?? 'Không có mô tả.' }}</p>

                        <form action="{{ route('carts.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="input-group quantity mb-3" style="width: 120px;">
                                <button type="button" class="btn btn-sm btn-minus rounded-circle bg-light border">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity"
                                    class="form-control form-control-sm text-center border-0" value="1"
                                    min="1">
                                <button type="button" class="btn btn-sm btn-plus rounded-circle bg-light border">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <button type="submit"
                                class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary">
                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs mb-3">
                                <button class="nav-link active border-white border-bottom-0" type="button"
                                    role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                    aria-controls="nav-about" aria-selected="true">Description</button>
                                <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                    id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                    aria-controls="nav-mission" aria-selected="false">Reviews</button>
                            </div>
                        </nav>
                        <div class="tab-content mb-5">
                            <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                <p>The generated Lorem Ipsum is therefore always free from repetition injected humour,
                                    or non-characteristic words etc.
                                    Susp endisse ultricies nisi vel quam suscipit </p>
                                <p>Sabertooth peacock flounder; chain pickerel hatchetfish, pencilfish snailfish
                                    filefish Antarctic
                                    icefish goldeye aholehole trumpetfish pilot fish airbreathing catfish, electric ray
                                    sweeper.</p>
                                <div class="px-2">
                                    <div class="row g-4">
                                        <div class="col-6">
                                            <div
                                                class="row bg-light align-items-center text-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Weight</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">1 kg</p>
                                                </div>
                                            </div>
                                            <div class="row text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Country of Origin</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">Agro Farm</p>
                                                </div>
                                            </div>
                                            <div
                                                class="row bg-light text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Quality</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">Organic</p>
                                                </div>
                                            </div>
                                            <div class="row text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Сheck</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">Healthy</p>
                                                </div>
                                            </div>
                                            <div
                                                class="row bg-light text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Min Weight</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">250 Kg</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-mission" role="tabpanel"
                                aria-labelledby="nav-mission-tab">
                                <h4 class="mb-4 fw-bold text-uppercase text-primary">Đánh giá của người dùng</h4>

                                @forelse($product->comments as $comment)
                                    <div class="d-flex mb-4 border rounded shadow-sm p-3 bg-white">
                                        <!-- Avatar người dùng -->
                                        <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('img/avatar.jpg') }}"
                                            class="img-fluid rounded-circle me-3" style="width: 80px; height: 80px;"
                                            alt="Avatar người dùng">

                                        <!-- Nội dung bình luận -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">{{ $comment->user->name ?? 'Ẩn danh' }}</h5>
                                                <small
                                                    class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                            </div>

                                            <div class="d-flex mb-2">
                                                @php
                                                    $rate = is_numeric($comment->rating) ? (int) $comment->rating : 0;
                                                @endphp

                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fa fa-star {{ $i <= $rate ? 'text-warning' : 'text-secondary' }}"></i>
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
                            <div class="tab-pane" id="nav-vision" role="tabpanel">
                                <p class="text-dark">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et tempor
                                    sit. Aliqu diam
                                    amet diam et eos labore. 3</p>
                                <p class="mb-0">Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos
                                    labore.
                                    Clita erat ipsum et lorem et sit</p>
                            </div>
                        </div>
                    </div>
                    @if (Auth::check())
                        <form action="{{ route('comments.store') }}" method="POST"
                            class="bg-light p-4 p-md-5 rounded shadow-sm">
                            @csrf

                            <h4 class="mb-4 fw-bold text-uppercase text-primary">Bình luận</h4>

                            <!-- Hidden: ID sản phẩm -->
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- Nội dung bình luận -->
                            <div class="mb-4">
                                <label for="content" class="form-label fw-semibold">Đánh giá của bạn *</label>
                                <textarea id="content" name="content" class="form-control rounded-3" rows="6"
                                    placeholder="Hãy viết gì đó..." required></textarea>
                            </div>

                            <!-- Số sao đánh giá -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold d-block">Chọn số sao:</label>
                                <div class="rating d-flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star fa-lg text-muted star"
                                            data-rating="{{ $i }}"></i>
                                    @endfor
                                </div>
                                <!-- Input thật để lưu số sao -->
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
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Parsely</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-1.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Parsely</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$4.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-3.png" class="img-fluid w-100 rounded-top bg-light"
                            alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Banana</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-4.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Bell Papper</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-5.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Potatoes</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Parsely</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-5.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Potatoes</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="img/vegetable-item-6.jpg" class="img-fluid w-100 rounded-top" alt="">
                    </div>
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">Vegetable</div>
                    <div class="p-4 pb-0 rounded-bottom">
                        <h4>Parsely</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold">$7.99 / kg</p>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
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
</script>
<script>
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

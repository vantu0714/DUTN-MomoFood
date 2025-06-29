@include('clients.layouts.header')
@include('clients.layouts.sidebar')
{{-- @vite('resources/css/shop.css') --}}
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">



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
                        <form action="{{ route('clients.search') }}" method="GET"
                            class="input-group w-100 mx-auto d-flex">
                            <input type="search" class="form-control border-secondary" name="keyword"
                                placeholder="Tìm kiếm sản phẩm" aria-describedby="search-icon-1">
                            <button type="submit" id="search-icon-1" class="btn btn-outline-primary p-3"
                                type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
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
                                        @foreach ($categories as $categoryItem)
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a
                                                        href="{{ route('shop.category', ['id' => $categoryItem->id]) }}
                                                        {{ request()->has('min_price') || request()->has('max_price') ? '?min_price=' . request('min_price') . '&max_price=' . request('max_price') : '' }}">
                                                        <i
                                                            class="fas fa-apple-alt me-2"></i>{{ $categoryItem->category_name }}
                                                    </a>
                                                    <span>({{ $categoryItem->products_count ?? 0 }})</span>
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <form
                                    action="{{ isset($category) ? route('shop.category', ['id' => $category->id]) : route('shop.index') }}"
                                    method="GET" class="p-3 bg-light rounded shadow-sm">
                                    @if (isset($category))
                                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                                    @endif

                                    <h5 class="mb-3 fw-bold">Lọc theo giá</h5>

                                    {{-- Radio lọc nhanh --}}
                                    <div class="mb-3">
                                        @php
                                            $ranges = [
                                                '0-50000' => 'Dưới 50k',
                                                '50000-200000' => '50k - 200k',
                                                '200000-500000' => '200k - 500k',
                                                '500000-1000000' => '500k - 1 triệu',
                                            ];
                                        @endphp

                                        @foreach ($ranges as $value => $label)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="price_range"
                                                    value="{{ $value }}" id="range{{ $loop->index }}"
                                                    {{ request('price_range') == $value ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="range{{ $loop->index }}">{{ $label }}</label>
                                            </div>
                                        @endforeach

                                        {{-- Lựa chọn tùy chỉnh --}}
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="price_range"
                                                value="custom" id="rangeCustom"
                                                {{ request('price_range') == 'custom' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rangeCustom">Tùy chọn</label>
                                        </div>
                                    </div>

                                    {{-- Nếu chọn tùy chỉnh thì cho nhập min-max --}}
                                    <div class="row g-2 mb-3" id="customPriceInputs"
                                        style="{{ request('price_range') == 'custom' ? '' : 'display:none;' }}">
                                        <div class="col-6">
                                            <input type="number" name="min_price"
                                                class="form-control form-control-sm" placeholder="Giá từ"
                                                value="{{ request('min_price') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="max_price"
                                                class="form-control form-control-sm" placeholder="Giá đến"
                                                value="{{ request('max_price') }}">
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill">
                                            <i class="fas fa-filter me-1"></i> Lọc
                                        </button>
                                    </div>
                                </form>

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
                                @php
                                    $isVariant = $product->product_type === 'variant';
                                    $variant = null;

                                    if ($isVariant) {
                                        // Lấy biến thể đầu tiên còn hàng
                                        $variant = $product->variants->firstWhere('quantity_in_stock', '>', 0);
                                    }

                                    $price = $isVariant
                                        ? $variant->discounted_price ?? ($variant->price ?? null)
                                        : $product->discounted_price ?? $product->original_price;

                                    $originalPrice = $isVariant ? $variant->price ?? null : $product->original_price;
                                @endphp

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
                                                    {{ $product->description ?? 'No description available.' }}
                                                </p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                @if ($price && $originalPrice && $price < $originalPrice)
                                                    <div>
                                                        <h5 class="fw-bold mb-0 text-dark">
                                                            {{ number_format($price, 0, ',', '.') }} VND
                                                        </h5>
                                                        <h6 class="text-danger text-decoration-line-through mb-0">
                                                            {{ number_format($originalPrice, 0, ',', '.') }} VND
                                                        </h6>
                                                    </div>
                                                @elseif ($price)
                                                    <h5 class="fw-bold mb-0 text-dark">
                                                        {{ number_format($price, 0, ',', '.') }} VND
                                                    </h5>
                                                @else
                                                    <h6 class="text-muted mb-0">Liên hệ để biết giá</h6>
                                                @endif

                                                <form action="{{ route('carts.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id"
                                                        value="{{ $product->id }}">
                                                    <button type="submit"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i>Thêm vào
                                                        giỏ hàng
                                                    </button>
                                                </form>
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

    @include('clients.layouts.footer')

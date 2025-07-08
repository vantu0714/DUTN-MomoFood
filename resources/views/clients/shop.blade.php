@include('clients.layouts.header')
@include('clients.layouts.sidebar')
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
<div class="container-fluid page-header mb-5"
    style="
    background: url('https://inan2h.vn/wp-content/uploads/2022/12/in-banner-quang-cao-do-an-7-1.jpg') center center / cover no-repeat;
    position: relative;
    height: 400px; /* Tăng chiều cao cho ảnh to hơn */">
    <div class="overlay"
        style="
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.2); /* mờ nhẹ cho chữ dễ đọc */
        ">
    </div>

    <!-- Nội dung căn giữa -->
    <div class="container h-100 position-relative" style="z-index: 2;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <h1 class="text-white display-3 fw-bold" style="text-shadow: 2px 2px 8px rgba(0,0,0,0.8);">
                Cửa hàng
            </h1>
        </div>
    </div>
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
                                                    @php
                                                        $query = http_build_query([
                                                            'min_price' => request('min_price'),
                                                            'max_price' => request('max_price'),
                                                        ]);
                                                    @endphp
                                                    <a
                                                        href="{{ route('shop.category', ['id' => $categoryItem->id]) }}{{ $query ? '?' . $query : '' }}">
                                                        <i
                                                            class="fas fa-apple-alt me-2"></i>{{ $categoryItem->category_name }}
                                                    </a>
                                                    <span>({{ $categoryItem->available_products_count ?? 0 }})</span>
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
                                <h4 class="mb-4 text-primary"><i class="bi bi-star-fill me-2 text-warning"></i>SẢN
                                    PHẨM NỔI BẬT</h4>

                                @foreach ($featuredProducts->random(3) as $product)
                                    @php
                                        $price = $product->discounted_price ?? $product->original_price;
                                    @endphp
                                    <div class="d-flex align-items-center mb-4">
                                        <!-- Hình ảnh -->
                                        <div class="rounded me-3"
                                            style="width: 100px; height: 100px; overflow: hidden;">
                                            <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                class="img-fluid rounded h-100 w-100 object-fit-cover"
                                                alt="{{ $product->product_name }}">
                                        </div>

                                        <!-- Thông tin -->
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-dark text-truncate"
                                                title="{{ $product->product_name }}">
                                                {{ $product->product_name }}
                                            </h6>

                                            <!-- Đánh giá giả lập -->
                                            <div class="d-flex mb-1">
                                                @for ($i = 0; $i < 4; $i++)
                                                    <i class="fa fa-star text-warning me-1"></i>
                                                @endfor
                                                <i class="fa fa-star text-secondary"></i>
                                            </div>

                                            <!-- Giá -->
                                            <div class="d-flex align-items-center">
                                                <h6 class="text-danger fw-bold mb-0 me-2">
                                                    {{ number_format($price, 0, ',', '.') }}đ
                                                </h6>
                                                @if ($product->original_price && $product->discounted_price)
                                                    <small class="text-muted text-decoration-line-through">
                                                        {{ number_format($product->original_price, 0, ',', '.') }}đ
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-center mt-3">
                                    <a href="{{ route('shop.index') }}"
                                        class="btn btn-outline-secondary px-4 py-2 rounded-pill text-primary">
                                        Xem thêm
                                    </a>
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
                                                <div class="product-price-wrapper">
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
                                                </div>
                                                <form class="add-to-cart-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id"
                                                        value="{{ $product->id }}">

                                                    @php
                                                        $firstAvailableVariant =
                                                            $product->product_type === 'variant'
                                                                ? $product->variants->firstWhere(
                                                                    'quantity_in_stock',
                                                                    '>',
                                                                    0,
                                                                )
                                                                : null;
                                                    @endphp

                                                    @if ($firstAvailableVariant)
                                                        <input type="hidden" name="product_variant_id"
                                                            value="{{ $firstAvailableVariant->id }}">
                                                    @endif

                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-white">
                                                        <i class="bi bi-cart3 fa-2x text-danger"></i>
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
    <!-- Fruits Shop End-->

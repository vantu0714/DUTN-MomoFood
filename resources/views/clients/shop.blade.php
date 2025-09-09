@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">
{{-- @include('clients.components.cart-modal') --}}



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
    background: url('https://ipos.vn/wp-content/uploads/2022/02/kinh-doanh-do-an-vat-online.jpg') center center / cover no-repeat;
    position: relative;
    height: 500px;">
    <div class="overlay"
        style="
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.2);
        ">
    </div>
</div>

<!-- Single Page Header End -->

<!-- Fruits Shop Start-->
<div class="container-fluid fruite py-5">
    <div class="container py-0">
        <div class="section-title text-center">
            <span class="title-text">CỬA HÀNG ĐỒ ĂN VẶT</span>
        </div>
        <div class="row g-4">
            <div class="col-lg-12">
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
                                            <input type="number" name="min_price" class="form-control form-control-sm"
                                                placeholder="Giá từ" value="{{ request('min_price') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="max_price" class="form-control form-control-sm"
                                                placeholder="Giá đến" value="{{ request('max_price') }}">
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
                                <h3 class="display-4"
                                    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:1.5rem;">
                                    SIÊU PHẨM ĂN VẶT 5 ⭐
                                </h3>

                                @foreach ($highRatedProducts->take(3) as $product)
                                    @php
                                        $isVariant = $product->product_type === 'variant';

                                        if ($isVariant && $product->variants->count() > 0) {
                                            // Lấy giá min-max của tất cả biến thể
                                            $prices = $product->variants->map(function ($v) {
                                                return $v->discounted_price ?? $v->price;
                                            });
                                            $minPrice = $prices->min();
                                            $maxPrice = $prices->max();
                                        } else {
                                            // Sản phẩm đơn
                                            $minPrice = $product->discounted_price ?? $product->original_price;
                                            $maxPrice = $product->original_price;
                                        }

                                        $avgRating = round($product->comments->avg('rating') ?? 0);
                                    @endphp

                                    <div class="d-flex align-items-center mb-4">
                                        <!-- Hình ảnh -->
                                        <div class="rounded me-3"
                                            style="width: 100px; height: 100px; overflow: hidden;">
                                            <a href="{{ route('product-detail.show', $product->id) }}">
                                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                    class="img-fluid rounded h-100 w-100 object-fit-cover"
                                                    alt="{{ $product->product_name }}">
                                            </a>
                                        </div>

                                        <!-- Thông tin -->
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-dark text-truncate"
                                                title="{{ $product->product_name }}">
                                                <a href="{{ route('product-detail.show', $product->id) }}"
                                                    class="text-dark">
                                                    {{ $product->product_name }}
                                                </a>
                                            </h6>

                                            <!-- Đánh giá -->
                                            <div class="d-flex mb-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star{{ $i <= $avgRating ? ' text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                                <small
                                                    class="ms-2 text-muted">({{ number_format($avgRating, 1) }}/5)</small>
                                            </div>

                                            <!-- Giá -->
                                            <div class="d-flex align-items-baseline price-block">
                                                @if ($isVariant)
                                                    <span class="fw-bold text-danger">
                                                        {{ number_format($minPrice, 0, ',', '.') }} -
                                                        {{ number_format($maxPrice, 0, ',', '.') }} VND
                                                    </span>
                                                @else
                                                    <span class="fw-bold text-danger">
                                                        {{ number_format($minPrice, 0, ',', '.') }} VND
                                                    </span>
                                                    @if ($minPrice < $maxPrice)
                                                        <small class="text-muted text-decoration-line-through ms-2">
                                                            {{ number_format($maxPrice, 0, ',', '.') }} VND
                                                        </small>
                                                    @endif
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
                                    $firstVariant = null;
                                    $price = null;
                                    $original = null;

                                    if ($product->product_type === 'variant') {
                                        $firstVariant = $product->variants->firstWhere('quantity_in_stock', '>', 0);
                                        $price = $firstVariant?->discounted_price ?? $firstVariant?->price;
                                        $original = $firstVariant?->price ?? 0;
                                    } else {
                                        $price = $product->discounted_price ?? $product->original_price;
                                        $original = $product->original_price;
                                    }

                                    $variants =
                                        $product->product_type === 'variant'
                                            ? $product->variants->map(function ($v) {
                                                $flavor = $v->attributeValues->firstWhere('attribute.name', 'Vị')
                                                    ?->value;
                                                $weight = $v->attributeValues->firstWhere('attribute.name')?->value;

                                                return [
                                                    'id' => $v->id,
                                                    'flavor' => $flavor,
                                                    'weight' => $weight,
                                                    'price' => $v->price,
                                                    'discounted_price' => $v->discounted_price,
                                                    'quantity' => $v->quantity_in_stock,
                                                    'image' => $v->image
                                                        ? asset('storage/' . $v->image)
                                                        : asset('clients/img/default.jpg'),
                                                ];
                                            })
                                            : [];
                                @endphp

                                <div class="col-12 col-md-6 col-lg-4">
                                    <div
                                        class="card h-100 shadow-sm border border-secondary rounded-4 d-flex flex-column">
                                        <a href="{{ route('product-detail.show', $product->id) }}">
                                            <div class="product-img-wrapper">
                                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                    alt="{{ $product->product_name }}"
                                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                                    class="img-fluid w-100">
                                            </div>
                                        </a>

                                        <div
                                            class="badge bg-secondary text-white position-absolute top-0 start-0 m-2 rounded-pill px-3 py-1">
                                            {{ $product->category?->category_name ?? 'Không rõ' }}
                                        </div>

                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="fw-bold text-dark text-truncate"
                                                    title="{{ $product->product_name }}">
                                                    {{ $product->product_name }}
                                                </h6>
                                                <p class="text-muted small mb-2 product-description">
                                                    {{ $product->description }}
                                                </p>
                                            </div>

                                            @php
                                                // Nếu có nhiều biến thể -> lấy min/max price
                                                if (!empty($variants) && count($variants) > 0) {
                                                    $prices = collect($variants)->pluck('price')->filter()->all();
                                                    $minPrice = $prices ? min($prices) : null;
                                                    $maxPrice = $prices ? max($prices) : null;
                                                } else {
                                                    // Sản phẩm đơn giản
                                                    $minPrice = $price ?? null;
                                                    $maxPrice = $price ?? null;
                                                }
                                            @endphp

                                            <div
                                                class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                                <div>
                                                    @if ($minPrice && $maxPrice && $minPrice != $maxPrice)
                                                        {{-- Hiển thị khoảng giá --}}
                                                        <div class="text-danger fw-bold fs-5 mb-0">
                                                            {{ number_format($minPrice, 0, ',', '.') }} -
                                                            {{ number_format($maxPrice, 0, ',', '.') }}
                                                            <small>VND</small>
                                                        </div>
                                                    @elseif ($minPrice)
                                                        {{-- Hiển thị 1 giá duy nhất --}}
                                                        <div class="text-danger fw-bold fs-5 mb-0">
                                                            {{ number_format($minPrice, 0, ',', '.') }}
                                                            <small>VND</small>
                                                        </div>
                                                    @else
                                                        {{-- Không có giá --}}
                                                        <div class="text-muted">Liên hệ để biết giá</div>
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-white open-cart-modal"
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->product_name }}"
                                                    data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                    data-product-category="{{ $product->category->category_name ?? 'Không rõ' }}"
                                                    data-product-price="{{ $minPrice ?? 0 }}"
                                                    data-product-original-price="{{ $maxPrice ?? 0 }}"
                                                    data-product-description="{{ $product->description }}"
                                                    data-variants='@json($variants)'
                                                    data-total-stock="{{ $product->product_type === 'simple' ? $product->quantity_in_stock : $firstVariant?->quantity_in_stock ?? 0 }}"
                                                    data-bs-toggle="modal" data-bs-target="#cartModal">
                                                    <i class="bi bi-cart3 fa-2x text-danger"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if ($products->isEmpty())
                                <div class="col-12 text-center">
                                    <p class="text-muted">Không có sản phẩm nào trong danh mục này.</p>
                                </div>
                            @endif
                        </div>


                        <div class="pagination-wrapper d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- Fruits Shop End-->
<!-- Modal chi tiết sản phẩm -->
@foreach ($products as $product)
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('carts.add') }}" id="modal-add-to-cart-form"
                class="modal-content">
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
                                <label class="form-label fw-semibold">🍃 Chọn loại:</label>
                                <div id="variant-options" class="d-flex flex-wrap gap-2">
                                    @foreach ($product->variants as $variant)
                                        @php
                                            $disabled = $variant->status == 0 || $variant->quantity_in_stock <= 0;
                                        @endphp
                                        <label
                                            class="variant-option btn btn-outline-primary {{ $disabled ? 'disabled-variant' : '' }}">
                                            <input type="radio" name="product_variant_id"
                                                value="{{ $variant->id }}" class="d-none"
                                                {{ $disabled ? 'disabled' : '' }}>
                                            {{ $variant->flavor ?? '' }}
                                            {{ $variant->size ?? '' }}
                                            - {{ number_format($variant->price, 0, ',', '.') }} VND
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Số lượng -->
                            @php
                                $hasVariants = $product->variants->count() > 0;
                                $totalStock = $hasVariants
                                    ? $product->variants->sum('quantity_in_stock')
                                    : $product->quantity_in_stock;
                            @endphp
                            <div class="mb-3">
                                <label for="modal-quantity" class="form-label fw-semibold">🔁 Số lượng:</label>
                                <div class="input-group" style="width: 160px;">
                                    <button type="button" class="btn btn-outline-secondary"
                                        id="decrease-qty">-</button>
                                    <input type="number" class="form-control text-center" id="modal-quantity"
                                        name="quantity" value="1" min="1">
                                    <button type="button" class="btn btn-outline-secondary"
                                        id="increase-qty">+</button>
                                    <br>
                                </div>
                                <div class="available-stock text-muted ms-3" id="availableStock">
                                    sản phẩm có sẵn {{ $totalStock }}
                                </div>
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
@endforeach
<!--js modal-->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = new bootstrap.Modal(document.getElementById('cartModal'));

        // DOM element
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
        const stockInfoEl = document.getElementById('availableStock');
        const totalStockQuantity = "{{ $totalStock }}";

        const weightGroup = document.getElementById('modal-weight-group');
        if (weightGroup) weightGroup.style.display = 'none';

        // tăng giảm số lượng
        document.getElementById('increase-qty').addEventListener('click', () => {
            const max = parseInt(quantityInput.max) || totalStockQuantity;
            let current = parseInt(quantityInput.value);
            if (current < max) {
                quantityInput.value = current + 1;
            } else {
                Toastify({
                    text: "Bạn đã vượt quá số lượng cho phép!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#f44336",
                    stopOnFocus: true
                }).showToast();
            }
        });

        document.getElementById('decrease-qty').addEventListener('click', () => {
            if (quantityInput.value > 1) quantityInput.stepDown();
        });

        // mở modal
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
                const totalStock = parseInt(this.dataset.totalStock || 0);

                // reset modal
                productIdInput.value = productId;
                productNameEl.textContent = productName;
                productImageEl.src = productImage;
                productCategoryEl.textContent = productCategory;
                productDescEl.textContent = productDescription;
                quantityInput.value = 1;
                quantityInput.removeAttribute('max');
                variantOptionsEl.innerHTML = '';
                productVariantIdInput.value = '';
                productOriginalPriceEl.style.display = 'none';
                productOriginalPriceEl.textContent = '';

                // nếu không có biến thể
                if (variants.length === 0) {
                    productPriceEl.textContent = productPrice.toLocaleString();
                    if (productOriginalPrice > productPrice) {
                        productOriginalPriceEl.textContent = productOriginalPrice
                            .toLocaleString() + ' VND';
                        productOriginalPriceEl.style.display = 'inline';
                    }
                    if (stockInfoEl) {
                        stockInfoEl.textContent = `Sản phẩm có sẵn: ${totalStock}`;
                        quantityInput.max = totalStock;
                    }
                } else {
                    // có biến thể → hiển thị khoảng giá
                    const prices = variants.map(v => parseInt(v.discounted_price || v.price ||
                        0)).filter(p => p > 0);
                    if (prices.length > 0) {
                        const minPrice = Math.min(...prices);
                        const maxPrice = Math.max(...prices);
                        productPriceEl.textContent = (minPrice === maxPrice) ?
                            minPrice.toLocaleString() :
                            `${minPrice.toLocaleString()} – ${maxPrice.toLocaleString()}`;
                    }
                    if (stockInfoEl) stockInfoEl.textContent = 'Vui lòng chọn loại sản phẩm';
                }

                // render biến thể
                const variantSectionEl = document.getElementById('variant-section');
                if (variants.length > 0) {
                    variantSectionEl.style.display = 'block';

                    variants.forEach(variant => {
                        const imageUrl = variant.image || productImage;
                        const flavorText = variant.flavor || '';
                        const weightText = variant.weight || '';
                        const stock = parseInt(variant.quantity ?? 0);
                        const disabled = (stock <= 0);

                        const html = `
                            <div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center ${disabled ? 'disabled-variant' : ''}"
                                 style="cursor: ${disabled ? 'not-allowed' : 'pointer'}; transition: 0.3s;"
                                 data-variant-id="${variant.id}"
                                 data-variant-price="${variant.discounted_price || variant.price}"
                                 data-variant-original="${variant.price}"
                                 data-variant-weight="${weightText}"
                                 data-variant-stock="${stock}"
                                 data-variant-image="${imageUrl}"
                                 ${disabled ? 'data-disabled="true"' : ''}>
                                <img src="${imageUrl}" alt="variant-image"
                                    class="rounded me-3"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <div class="fw-semibold text-dark">${flavorText} - ${weightText}</div>
                                    ${disabled ? '<small class="text-danger">Hết hàng</small>' : ''}
                                </div>
                            </div>`;
                        variantOptionsEl.insertAdjacentHTML('beforeend', html);
                    });

                    // sự kiện chọn biến thể
                    variantOptionsEl.querySelectorAll('.variant-card').forEach(card => {
                        if (card.dataset.disabled) return;
                        card.addEventListener('click', () => {
                            variantOptionsEl.querySelectorAll('.variant-card')
                                .forEach(c => c.classList.remove(
                                    'border-primary', 'shadow'));
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

                            if (stockInfoEl) stockInfoEl.textContent =
                                `Sản phẩm có sẵn: ${stock}`;
                            quantityInput.max = stock;
                            if (parseInt(quantityInput.value) > stock)
                                quantityInput.value = stock;
                        });
                    });
                } else {
                    variantSectionEl.style.display = 'none';
                }

                // validate nhập số lượng
                if (quantityInput) {
                    quantityInput.addEventListener('input', function() {
                        const max = parseInt(quantityInput.max) || totalStockQuantity;
                        let value = parseInt(quantityInput.value) || 1;
                        if (value > max) {
                            quantityInput.value = max;
                            Toastify({
                                text: "Bạn đã vượt quá số lượng cho phép!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#f44336",
                                stopOnFocus: true
                            }).showToast();
                        }
                    });
                }

                modal.show();
            });
        });

        // validate chọn biến thể
        document.getElementById('modal-add-to-cart-form').addEventListener('submit', function(e) {
            if (variantOptionsEl.innerHTML.trim() !== '' && !productVariantIdInput.value) {
                e.preventDefault();
                alert('⚠️ Vui lòng chọn sản phẩm trước khi thêm vào giỏ hàng.');
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
    $(document).ready(function() {

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
    });
</script>

<style>
    .variant-option.disabled-variant,
    .variant-card.disabled-variant {
        opacity: 0.5 !important;
        pointer-events: none !important;
        cursor: not-allowed !important;
    }

    .variant-option.disabled-variant {
        background-color: #f8f9fa !important;
        border-color: #ccc !important;
        color: #6c757d !important;
    }

    .variant-card.disabled-variant {
        background-color: #f8f9fa !important;
        border: 1px solid #ccc !important;
    }

    .variant-option.disabled-variant,
    .variant-option.disabled-variant input {
        opacity: 0.5 !important;
        pointer-events: none !important;
        cursor: not-allowed !important;
    }

    .variant-card.disabled-variant {
        opacity: 0.5 !important;
        pointer-events: none !important;
        cursor: not-allowed !important;
    }

    .comment-avatar {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 2px solid #fff;
        flex-shrink: 0;
        display: block;
    }

    a.h5.d-block.mb-2:hover {
        color: #d67054 !important;
    }

    .product-card {
        padding: 1rem;
        background-color: #f8f9fa;

        border-radius: 10px;
        min-height: 100%;

    }

    .product-price {
        text-align: left;
        padding-left: 0.5rem;
        /* hoặc giá trị tương ứng với tên sản phẩm */
        margin-left: 0;
        /* đảm bảo không bị lệch */
    }

    .image-wrapper {
        width: 150px !important;
        height: 150px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
        border: 4px solid #f1f1f1 !important;
        /* Tuỳ chỉnh màu viền nếu muốn */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 auto !important;
    }

    /* .image-wrapper img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        display: block !important;
    } */

    .image-wrapper img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        border-radius: 50% !important;
        display: block !important;
        background-color: white;

    }

    .comment-content {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Số dòng hiển thị */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-height: 3.2em;
        /* Tùy thuộc vào font-size */
    }

    .testimonial-container {
        position: relative;
    }

    /* Nút điều hướng */
    .testimonial-nav {
        position: absolute;
        top: 240px;
        /* Căn giữa theo chiều cao avatar */
        left: 0;
        right: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 10px;
        pointer-events: none;
        /* Cho phép click vào nút mà không cản phần khác */
        opacity: 0;
        /* Mặc định ẩn */
        transition: opacity 0.3s ease;
        z-index: 10;
    }

    /* Khi hover vào container thì hiện nút */
    .testimonial-container:hover .testimonial-nav {
        opacity: 1;
        pointer-events: auto;
    }

    /* Nút */
    .testimonial-nav button {
        width: 48px;
        height: 48px;
        background-color: #b3b3b3;
        color: #fff;
        border: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
        pointer-events: auto;
        opacity: 0.6;
        /* Làm mờ nhẹ lúc không hover nút */
    }

    /* Khi hover vào nút thì sáng lên */
    .testimonial-nav button:hover {
        background-color: #c82333;
        opacity: 1;
    }


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

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="price_range"]');
            const customInputs = document.getElementById('customPriceInputs');
            const minInput = document.querySelector('input[name="min_price"]');
            const maxInput = document.querySelector('input[name="max_price"]');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customInputs.style.display = 'flex'; // hiển thị dạng flex
                    } else {
                        customInputs.style.display = 'none';
                        minInput.value = '';
                        maxInput.value = '';
                    }
                });
            });
        });
    </script>

    <style>
        .section-title {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            /* khoảng cách giữa icon và chữ */
            margin-bottom: 40px;
            /* khoảng cách dưới */
        }

        .section-title .title-text {
            color: #e86c4d;
            /* màu cam giống hình */
            font-weight: 700;
            /* đậm */
            font-size: 48px;
            font-family: 'Arial', sans-serif;
            text-transform: uppercase;
        }
    </style>

    @include('clients.layouts.footer')

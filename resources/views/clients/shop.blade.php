@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">
@include('clients.components.cart-modal')



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
                                                $weight = $v->attributeValues->firstWhere(
                                                    'attribute.name',
                                                    'Khối lượng',
                                                )?->value;

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
    <!-- Fruits Shop End-->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('cartModal'));

            const productNameEl = document.getElementById('modal-product-name');
            const productImageEl = document.getElementById('modal-product-image');
            const productCategoryEl = document.getElementById('modal-product-category');
            const productPriceEl = document.getElementById('modal-product-price');
            const productOriginalPriceEl = document.getElementById('modal-product-original-price');
            const productDescEl = document.getElementById('modal-product-description');
            const variantOptionsEl = document.getElementById('variant-options');
            const variantSection = document.getElementById('variant-section');
            const productIdInput = document.getElementById('modal-product-id');
            const productVariantIdInput = document.getElementById('modal-variant-id');
            const quantityInput = document.getElementById('modal-quantity');
            const stockInfoEl = document.getElementById('stock-info');

            const weightGroup = document.getElementById('modal-weight-group');
            if (weightGroup) weightGroup.style.display = 'none';

            document.getElementById('increase-qty').addEventListener('click', () => quantityInput.stepUp());
            document.getElementById('decrease-qty').addEventListener('click', () => {
                if (quantityInput.value > 1) quantityInput.stepDown();
            });

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

                    // Reset modal
                    productIdInput.value = productId;
                    productNameEl.textContent = productName;
                    productImageEl.src = productImage;
                    productCategoryEl.textContent = productCategory;
                    productDescEl.textContent = productDescription;
                    quantityInput.value = 1;
                    productPriceEl.textContent = productPrice.toLocaleString();
                    productOriginalPriceEl.textContent = (productOriginalPrice > productPrice) ?
                        productOriginalPrice.toLocaleString() + ' VND' :
                        '';
                    productOriginalPriceEl.style.display = (productOriginalPrice > productPrice) ?
                        'inline' : 'none';
                    variantOptionsEl.innerHTML = '';
                    productVariantIdInput.value = '';

                    // Ẩn khối lượng riêng
                    if (weightGroup) weightGroup.style.display = 'none';

                    // Xử lý biến thể
                    if (variants.length > 0) {
                        variantSection.style.display = 'block';
                        variants.forEach((variant) => {
                            const imageUrl = variant.image || productImage;
                            const flavorText = variant.flavor || '';
                            const weightText = variant.weight || variant.mass || variant
                                .size || '';
                            const html = `
                            <div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center"
                                style="cursor: pointer; transition: 0.3s;"
                                data-variant-id="${variant.id}"
                                data-variant-price="${variant.discounted_price || variant.price}"
                                data-variant-original="${variant.price}"
                                data-variant-weight="${weightText}"
                                data-variant-image="${imageUrl}">
                                <img src="${imageUrl}" alt="variant-image"
                                    class="rounded me-3"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <div class="fw-semibold text-dark">${flavorText} - ${weightText}</div>
                                </div>
                            </div>`;
                            variantOptionsEl.insertAdjacentHTML('beforeend', html);
                        });

                        // Click chọn biến thể
                        variantOptionsEl.querySelectorAll('.variant-card').forEach(card => {
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

                                productVariantIdInput.value = id;
                                productPriceEl.textContent = price.toLocaleString();
                                productOriginalPriceEl.textContent = (original >
                                        price) ?
                                    original.toLocaleString() + ' VND' :
                                    '';
                                productOriginalPriceEl.style.display = (original >
                                    price) ? 'inline' : 'none';
                                productImageEl.src = imageUrl;
                            });
                        });
                    } else {
                        // Không có biến thể → ẩn section
                        variantSection.style.display = 'none';
                        variantOptionsEl.innerHTML = '';
                        productVariantIdInput.value = '';
                    }

                    modal.show();
                });
            });

            // Validate biến thể
            document.getElementById('modal-add-to-cart-form').addEventListener('submit', function(e) {
                if (variantOptionsEl.innerHTML.trim() !== '' && !productVariantIdInput.value) {
                    e.preventDefault();
                    alert('⚠️ Vui lòng chọn biến thể trước khi thêm vào giỏ hàng.');
                }
            });
        });

        function rebindOpenCartModal() {
            const modal = new bootstrap.Modal(document.getElementById('cartModal'));
            const variantSection = document.getElementById('variant-section');
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

                    // Reset
                    productIdInput.value = productId;
                    productNameEl.textContent = productName;
                    productImageEl.src = productImage;
                    productCategoryEl.textContent = productCategory;
                    productDescEl.textContent = productDescription;
                    quantityInput.value = 1;
                    variantOptionsEl.innerHTML = '';
                    productVariantIdInput.value = '';
                    productPriceEl.textContent = productPrice.toLocaleString();
                    productOriginalPriceEl.textContent = (productOriginalPrice > productPrice) ?
                        productOriginalPrice.toLocaleString() + ' VND' : '';
                    productOriginalPriceEl.style.display = (productOriginalPrice > productPrice) ?
                        'inline' : 'none';

                    if (variants.length > 0) {
                        variantSection.style.display = 'block';
                        variants.forEach(variant => {
                            const imageUrl = variant.image || productImage;
                            const flavorText = variant.flavor || '';
                            const weightText = variant.weight || variant.mass || variant.size || '';

                            const html = `
                        <div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center"
                            style="cursor: pointer; transition: 0.3s;"
                            data-variant-id="${variant.id}"
                            data-variant-price="${variant.discounted_price || variant.price}"
                            data-variant-original="${variant.price}"
                            data-variant-weight="${weightText}"
                            data-variant-image="${imageUrl}">
                            <img src="${imageUrl}" alt="variant-image"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <div class="fw-semibold text-dark">${flavorText} - ${weightText}</div>
                            </div>
                        </div>`;
                            variantOptionsEl.insertAdjacentHTML('beforeend', html);
                        });

                        // Gán click biến thể
                        variantOptionsEl.querySelectorAll('.variant-card').forEach(card => {
                            card.addEventListener('click', () => {
                                variantOptionsEl.querySelectorAll('.variant-card').forEach(
                                    c => c.classList.remove('border-primary', 'shadow'));
                                card.classList.add('border-primary', 'shadow');

                                const id = card.dataset.variantId;
                                const price = parseInt(card.dataset.variantPrice);
                                const original = parseInt(card.dataset.variantOriginal);
                                const imageUrl = card.dataset.variantImage;

                                productVariantIdInput.value = id;
                                productPriceEl.textContent = price.toLocaleString();
                                productOriginalPriceEl.textContent = (original > price) ?
                                    original.toLocaleString() + ' VND' : '';
                                productOriginalPriceEl.style.display = (original > price) ?
                                    'inline' : 'none';
                                productImageEl.src = imageUrl;
                            });
                        });
                    } else {
                        variantSection.style.display = 'none';
                        variantOptionsEl.innerHTML = '';
                        productVariantIdInput.value = '';
                    }

                    modal.show();
                });
            });
        }
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

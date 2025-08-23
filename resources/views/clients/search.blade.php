@extends('clients.layouts.app')

@section('content')
    <div class="container-fluid fruite py-5" style="margin-top: 80px">
        <div class="container py-5">
            <h4 class="mb-4">Kết quả cho từ khóa: "{{ $keyword }}"</h4>

            @php
                $availableProducts = $products->where('quantity_in_stock', '>', 0);
            @endphp

            @if ($availableProducts->count())
                <div class="row g-4">
                    <div class="col-lg-3">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h4>Danh mục sản phẩm</h4>
                                    <ul class="list-unstyled fruite-categorie">
                                        @php
                                            $categoryIdsInResults = $availableProducts
                                                ->pluck('category_id')
                                                ->unique()
                                                ->toArray();

                                            $categoriesCollection = is_array($categories)
                                                ? collect($categories)
                                                : $categories;

                                            $filteredCategories = $categoriesCollection->filter(function (
                                                $category,
                                            ) use ($categoryIdsInResults) {
                                                return in_array($category->id, $categoryIdsInResults);
                                            });
                                        @endphp

                                        @foreach ($filteredCategories as $categoryItem)
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    @php
                                                        $query = http_build_query([
                                                            'keyword' => $keyword,
                                                            'min_price' => request('min_price'),
                                                            'max_price' => request('max_price'),
                                                        ]);
                                                    @endphp
                                                    <a href="{{ route('clients.search') }}?category_id={{ $categoryItem->id }}&{{ $query }}"
                                                        style="color: #db735b;">
                                                        <i
                                                            class="fas fa-apple-alt me-2"></i>{{ $categoryItem->category_name }}
                                                    </a>
                                                    <span class="text-muted">
                                                        ({{ $availableProducts->where('category_id', $categoryItem->id)->count() }})
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <form action="{{ route('clients.search') }}" method="GET"
                                    class="p-3 bg-light rounded shadow-sm">
                                    <input type="hidden" name="keyword" value="{{ $keyword }}">
                                    @if (request('category_id'))
                                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                    @endif

                                    <h5 class="mb-3 fw-bold">Lọc theo giá</h5>

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

                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="price_range" value="custom"
                                                id="rangeCustom" {{ request('price_range') == 'custom' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rangeCustom">Tùy chọn</label>
                                        </div>
                                    </div>

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
                                        <button type="submit" class="btn btn-sm rounded-pill"
                                            style="background-color: #db735b; color: white;">
                                            <i class="fas fa-filter me-1"></i> Lọc
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <div class="row g-4">
                            @foreach ($availableProducts as $product)
                            @php
                                $hasVariants = $product->product_type === 'variant' && $product->variants && $product->variants->count() > 0;
                                $price = 0;
                                $original = 0;
                                $variants = [];
                        
                                if ($hasVariants) {
                                    // Sản phẩm có biến thể
                                    $firstVariant = $product->variants->firstWhere('quantity', '>', 0);
                                    if ($firstVariant) {
                                        $price = $firstVariant->discounted_price ?? $firstVariant->price;
                                        $original = $firstVariant->price;
                                    }
                        
                                    $variants = $product->variants->map(function ($v) {
                                        return [
                                            'id' => $v->id,
                                            'price' => $v->price,
                                            'discounted_price' => $v->discounted_price,
                                            'quantity' => $v->quantity,
                                            'image' => $v->image,
                                            'attribute_values' => $v->attributeValues->map(function ($attrValue) {
                                                return [
                                                    'attribute_name' => $attrValue->attribute->name,
                                                    'value' => $attrValue->value,
                                                ];
                                            }),
                                        ];
                                    });
                        
                                    $minPrice = $product->variants->map(fn($v) => $v->discounted_price ?? $v->price)->min();
                                    $maxPrice = $product->variants->map(fn($v) => $v->discounted_price ?? $v->price)->max();
                        
                                    $displayPrice = $minPrice == $maxPrice
                                        ? number_format($minPrice, 0, ',', '.')
                                        : number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
                                } else {
                                    // Sản phẩm đơn (simple)
                                    $price = $product->discounted_price && $product->discounted_price < $product->original_price
                                        ? $product->discounted_price
                                        : $product->original_price;
                        
                                    $original = $product->original_price;
                        
                                    if ($product->discounted_price && $product->discounted_price < $product->original_price) {
                                        $displayPrice = number_format($product->discounted_price, 0, ',', '.') .
                                                        ' <del class="text-muted small">' .
                                                        number_format($product->original_price, 0, ',', '.') .
                                                        '</del>';
                                    } else {
                                        $displayPrice = number_format($product->original_price, 0, ',', '.');
                                    }
                                }
                            @endphp
                        
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="card shadow-sm border rounded-4 overflow-hidden h-100"
                                    style="border-color: #db735b !important;">
                                    <div class="position-relative">
                                        <a href="{{ route('product-detail.show', $product->id) }}">
                                            <div class="product-img-wrapper">
                                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                    alt="{{ $product->product_name }}"
                                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                                    class="img-fluid w-100">
                                            </div>
                                        </a>
                                        <span class="badge text-white position-absolute top-0 start-0 m-2"
                                            style="background-color: #db735b;">
                                            {{ $product->category->category_name ?? 'Sản phẩm' }}
                                        </span>
                                    </div>
                        
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="fw-bold text-dark text-truncate">{{ $product->product_name }}</h6>
                                        <p class="text-muted small mb-3">
                                            {{ Str::limit($product->description ?? 'Không có mô tả', 60) }}
                                        </p>
                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold fs-5" style="color: #db735b;">
                                                    {!! $displayPrice !!} VND
                                                </div>
                                            </div>
                        
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-white open-cart-modal"
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->product_name }}"
                                                    data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                                    data-product-category="{{ $product->category->category_name ?? 'Không rõ' }}"
                                                    data-product-price="{{ $price ?? 0 }}"
                                                    data-product-original-price="{{ $original ?? 0 }}"
                                                    data-product-description="{{ $product->description }}"
                                                    data-variants='@json($variants)'
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cartModal">
                                                    <i class="bi bi-cart3 fa-2x" style="color: #db735b;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        

                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search fa-5x text-muted mb-3"></i>
                        <h5 class="text-muted">Không tìm thấy sản phẩm nào</h5>
                        <p class="text-muted">Không tìm thấy sản phẩm nào phù hợp với từ khóa
                            "{{ $keyword }}" hoặc sản phẩm đã hết hàng.</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="btn" style="background-color: #db735b; color: white;">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại cửa hàng
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal chi tiết sản phẩm --}}
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('carts.add') }}" id="modal-add-to-cart-form" class="modal-content">
                @csrf
                <input type="hidden" name="product_id" id="modal-product-id">
                <input type="hidden" name="product_variant_id" id="modal-variant-id">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-black" id="cartModalLabel">Chọn sản phẩm</h5>
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

                            <p class="h5 fw-bold mb-3" style="color: #db735b;">
                                <span id="modal-product-price">0</span> VND
                                <del class="text-secondary fs-6 ms-2" id="modal-product-original-price"></del>
                            </p>

                            <div class="mb-3" id="modal-rating">
                                <!-- Đánh giá (nếu cần) -->
                            </div>

                            <p id="modal-product-description" class="text-muted mb-3" style="min-height: 60px;"></p>

                            <!-- Biến thể -->
                            <div class="mb-3" id="variant-section">
                                <label class="form-label fw-semibold">🍃 Chọn biến thể:</label>
                                <div id="variant-options" class="d-flex flex-wrap gap-2">
                                    <!-- JS sẽ render radio button biến thể -->
                                </div>
                            </div>

                            <!-- Số lượng -->
                            <div class="mb-3">
                                <label for="modal-quantity" class="form-label fw-semibold">🔁 Số lượng:</label>
                                <div class="input-group" style="width: 160px;">
                                    <button type="button" class="btn rounded-end-0" id="decrease-qty"
                                        style="border: 1px solid #db735b; color: #db735b; height: 38px; width: 38px;">-</button>
                                    <input type="number" class="form-control text-center border-start-0 border-end-0"
                                        id="modal-quantity" name="quantity" value="1" min="1"
                                        style="border-color: #db735b; height: 38px; -moz-appearance: textfield;"
                                        onfocus="this.blur()">
                                    <button type="button" class="btn rounded-start-0" id="increase-qty"
                                        style="border: 1px solid #db735b; color: #db735b; height: 38px; width: 38px;">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn w-100 fw-bold py-2"
                        style="background-color: #db735b; color: white;">
                        <i class="bi bi-bag-plus-fill me-1"></i> Thêm vào giỏ hàng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .product-img-wrapper {
            height: 200px;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            max-width: 100%;
            max-height: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 20px rgba(219, 115, 91, 0.2);
            border-color: #db735b !important;
        }

        a {
            color: #db735b;
        }

        a:hover {
            color: #c05a4a;
        }

        .btn-white {
            background-color: white;
            border: 1px solid #db735b;
            border-radius: 10px;
        }

        .btn-white:hover {
            background-color: #db735b;
        }

        #variant-options {
            gap: 10px;
        }

        .variant-card {
            flex: 0 0 auto;
            width: 180px;
            transition: all 0.3s ease;
        }

        .variant-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .variant-card.border-primary {
            border-width: 2px;
            background-color: #eaf4ff;
        }

        .variant-card img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .input-group {
            align-items: center;
        }

        #decrease-qty,
        #increase-qty {
            background-color: white;
            transition: all 0.3s;
        }

        #decrease-qty:hover,
        #increase-qty:hover {
            background-color: #db735b;
            color: white !important;
        }
    </style>

   <script>
document.addEventListener("DOMContentLoaded", function() {
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

    // Quantity +/- buttons
    document.getElementById('increase-qty').addEventListener('click', () => quantityInput.stepUp());
    document.getElementById('decrease-qty').addEventListener('click', () => {
        if (quantityInput.value > 1) quantityInput.stepDown();
    });

    // Format tiền tệ
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    }

    // Mở modal khi nhấn nút
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

            // Reset form
            productIdInput.value = productId;
            productNameEl.textContent = productName;
            productImageEl.src = productImage;
            productCategoryEl.textContent = productCategory;
            productDescEl.textContent = productDescription;
            quantityInput.value = 1;
            variantOptionsEl.innerHTML = '';
            productVariantIdInput.value = '';

            // Render biến thể
            if (variants.length > 0) {
                variantSection.style.display = 'block';

                let firstAvailableVariant = null;

                variants.forEach((variant, index) => {
                    const imageUrl = variant.image
                        ? `{{ asset('storage/') }}/${variant.image}`
                        : productImage;
                    const flavorText = variant.attribute_values?.find(attr => attr.attribute_name === 'Vị')?.value || '';
                    const weightText = variant.attribute_values?.find(attr => attr.attribute_name === 'Khối lượng')?.value || '';
                    const variantPrice = variant.discounted_price || variant.price;
                    const variantOriginal = variant.price;

                    const inStock = variant.quantity && variant.quantity > 0;

                    if (inStock && !firstAvailableVariant) {
                        firstAvailableVariant = variant; // chọn biến thể đầu tiên còn hàng
                    }

                    const html = inStock ? `
                        <div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center"
                            style="cursor: pointer; transition: 0.3s;"
                            data-variant-id="${variant.id}"
                            data-variant-price="${variantPrice}"
                            data-variant-original="${variantOriginal}"
                            data-variant-weight="${weightText}"
                            data-variant-image="${imageUrl}">
                            <img src="${imageUrl}" alt="variant-image"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover;"
                                onerror="this.src='{{ asset('clients/img/default.jpg') }}';">
                            <div>
                                <div class="fw-semibold text-dark">${flavorText} - ${weightText}</div>
                            </div>
                        </div>`
                    :
                        `<div class="variant-card border rounded p-2 mb-2 shadow-sm d-flex align-items-center bg-light text-muted"
                            style="cursor: not-allowed; opacity: 0.6;">
                            <img src="${imageUrl}" alt="variant-image"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover; filter: grayscale(100%);"
                                onerror="this.src='{{ asset('clients/img/default.jpg') }}';">
                            <div>
                                <div class="fw-semibold">${flavorText} - ${weightText}</div>
                                <small class="text-danger">Hết hàng</small>
                            </div>
                        </div>`;

                    variantOptionsEl.insertAdjacentHTML('beforeend', html);
                });

                // Nếu có biến thể còn hàng → chọn mặc định
                if (firstAvailableVariant) {
                    const price = firstAvailableVariant.discounted_price || firstAvailableVariant.price;
                    const original = firstAvailableVariant.price;
                    const imageUrl = firstAvailableVariant.image ? `{{ asset('storage/') }}/${firstAvailableVariant.image}` : productImage;

                    productVariantIdInput.value = firstAvailableVariant.id;
                    productPriceEl.textContent = formatPrice(price);
                    productOriginalPriceEl.textContent = (original > price) ? formatPrice(original) + ' ₫' : '';
                    productOriginalPriceEl.style.display = (original > price) ? 'inline' : 'none';
                    productImageEl.src = imageUrl;

                    // highlight card
                    const defaultCard = variantOptionsEl.querySelector(`[data-variant-id="${firstAvailableVariant.id}"]`);
                    if (defaultCard) defaultCard.classList.add('border-primary', 'shadow');
                }

                // Gán sự kiện click cho mỗi biến thể còn hàng
                variantOptionsEl.querySelectorAll('.variant-card').forEach((card) => {
                    if (card.dataset.variantId) { // chỉ cho biến thể còn hàng
                        card.addEventListener('click', () => {
                            variantOptionsEl.querySelectorAll('.variant-card')
                                .forEach(c => c.classList.remove('border-primary', 'shadow'));
                            card.classList.add('border-primary', 'shadow');

                            const id = card.dataset.variantId;
                            const price = parseInt(card.dataset.variantPrice);
                            const original = parseInt(card.dataset.variantOriginal);
                            const imageUrl = card.dataset.variantImage;

                            productVariantIdInput.value = id;
                            productPriceEl.textContent = formatPrice(price);
                            productOriginalPriceEl.textContent = (original > price) ? formatPrice(original) + ' ₫' : '';
                            productOriginalPriceEl.style.display = (original > price) ? 'inline' : 'none';
                            productImageEl.src = imageUrl;
                        });
                    }
                });

            } else {
                variantSection.style.display = 'none';
                productVariantIdInput.value = '';
                // Hiển thị giá sản phẩm chính nếu không có biến thể
                productPriceEl.textContent = formatPrice(productPrice);
                productOriginalPriceEl.textContent = (productOriginalPrice > productPrice) ?
                    formatPrice(productOriginalPrice) + ' ₫' : '';
                productOriginalPriceEl.style.display = (productOriginalPrice > productPrice) ? 'inline' : 'none';
            }

            modal.show();
        });
    });

    // Validate biến thể trước khi submit
    document.getElementById('modal-add-to-cart-form').addEventListener('submit', function(e) {
        if (variantOptionsEl.innerHTML.trim() !== '' && !productVariantIdInput.value) {
            e.preventDefault();
            alert('⚠️ Vui lòng chọn biến thể trước khi thêm vào giỏ hàng.');
        }
    });

    // Custom price filter
    const radios = document.querySelectorAll('input[name="price_range"]');
    const customInputs = document.getElementById('customPriceInputs');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                customInputs.style.display = '';
            } else {
                customInputs.style.display = 'none';
                document.querySelector('input[name="min_price"]').value = '';
                document.querySelector('input[name="max_price"]').value = '';
            }
        });
    });
});
</script>

@endsection

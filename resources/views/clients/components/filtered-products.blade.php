<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">

<div class="row g-4">
    @foreach ($products as $product)
        @php
            $firstVariant = null;
            $price = null;
            $original = 0;

            if ($product->product_type === 'variant') {
                $firstVariant = $product->variants->firstWhere('quantity_in_stock', '>', 0);
                $price = $firstVariant?->discounted_price ?? $firstVariant?->price;
            } elseif ($product->product_type === 'simple') {
                $price = $product->discounted_price ?? $product->original_price;
            }

            $isOutOfStock = false;

            if ($product->product_type === 'simple') {
                $isOutOfStock = $product->quantity <= 0;
            } elseif ($product->product_type === 'variant') {
                $isOutOfStock = $product->variants->sum('quantity_in_stock') <= 0;
            }
        @endphp

        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div class="rounded position-relative fruite-item h-100 d-flex flex-column">
                <a href="{{ route('product-detail.show', $product->id) }}">
                    <div class="product-img-wrapper">
                        <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                            onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                            alt="{{ $product->product_name }}">
                    </div>
                </a>
                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                    {{ $product->category?->category_name ?? 'Không có danh mục' }}
                </div>

                <div
                    class="product-content p-4 border border-secondary border-top-0 rounded-bottom d-flex flex-column justify-content-between flex-grow-1">
                    <h4 class="text-truncate" title="{{ $product->product_name }}">{{ $product->product_name }}</h4>
                    <p class="text-muted text-truncate">Mã sản phẩm: {{ $product->product_code }}</p>

                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <p class="text-danger fs-5 fw-bold mb-0">
                            {{ $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'Liên hệ' }}
                        </p>

                        @if (!$isOutOfStock)
                            <form action="{{ route('carts.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="button" class="btn btn-white open-cart-modal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->product_name }}"
                                    data-product-price="{{ number_format($price, 0, ',', '.') }}"
                                    data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    data-product-description="{{ Str::limit($product->description, 60) }}"
                                    data-product-category="{{ $product->category->category_name ?? 'Sản phẩm' }}"
                                    data-product-original="{{ number_format($original, 0, ',', '.') }}"
                                    @if ($product->product_type === 'variant' && $firstVariant) data-variant-id="{{ $firstVariant->id }}" @endif
                                    data-bs-toggle="modal" data-bs-target="#cartModal">
                                    <i class="bi bi-cart3 fa-2x text-danger"></i>
                                </button>

                            </form>
                        @else
                            <span class="badge bg-danger text-white">Hết hàng</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="pagination-wrapper d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>

    @if ($products->isEmpty())
        <div class="col-12 text-center">
            <p class="text-muted">Không có sản phẩm nào trong danh mục này.</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();

            const productContainer = document.querySelector('.tab-content #tab-1');
            const url = link.href;

            fetch(url)
                .then(res => res.text())
                .then(data => {
                    productContainer.innerHTML = data;
                    window.scrollTo({
                        top: productContainer.offsetTop - 100,
                        behavior: 'smooth'
                    });
                })
                .catch(err => console.error('Lỗi khi phân trang:', err));
        }
    });
</script>

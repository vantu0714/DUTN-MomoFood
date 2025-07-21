<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">

<div class="tab-content">
    <div id="tab-1" class="tab-pane fade show active p-0">
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

                    $variants = $product->product_type === 'variant'
                        ? $product->variants->map(function ($v) {
                            return [
                                'id' => $v->id,
                                'flavor' => $v->flavor,
                                'size' => $v->size,
                                'price' => $v->price,
                                'discounted_price' => $v->discounted_price,
                                'quantity' => $v->quantity_in_stock,
                            ];
                        })
                        : [];
                @endphp

                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm border border-secondary rounded-4 d-flex flex-column">
                        <a href="{{ route('product-detail.show', $product->id) }}">
                            <div class="product-img-wrapper">
                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                    class="img-fluid w-100">
                            </div>
                        </a>

                        <div class="badge bg-secondary text-white position-absolute top-0 start-0 m-2 rounded-pill px-3 py-1">
                            {{ $product->category?->category_name ?? 'Không rõ' }}
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark text-truncate" title="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                </h6>
                                <p class="text-muted small mb-2">Mã: {{ $product->product_code }}</p>
                            </div>

                            <div class="mb-2">
                                @if ($price && $original && $price < $original)
                                    <div class="text-danger fw-bold fs-5">
                                        {{ number_format($price, 0, ',', '.') }} <small>VND</small>
                                    </div>
                                    <div class="text-muted text-decoration-line-through small">
                                        {{ number_format($original, 0, ',', '.') }} VND
                                    </div>
                                @elseif ($price)
                                    <div class="text-danger fw-bold fs-5">
                                        {{ number_format($price, 0, ',', '.') }} <small>VND</small>
                                    </div>
                                @else
                                    <div class="text-muted">Liên hệ để biết giá</div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-end mt-auto">
                                <button type="button" class="btn btn-white open-cart-modal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->product_name }}"
                                    data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    data-product-category="{{ $product->category->category_name ?? 'Không rõ' }}"
                                    data-product-price="{{ $price ?? 0 }}"
                                    data-product-original-price="{{ $original ?? 0 }}"
                                    data-product-description="{{ $product->description }}"
                                    data-variants='@json($variants)'
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

<script>
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();

            const productContainer = document.querySelector('#tab-1');
            const url = link.href;

            fetch(url)
                .then(res => res.text())
                .then(data => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;
                    const newContent = tempDiv.querySelector('#tab-1')?.innerHTML;

                    if (newContent) {
                        productContainer.innerHTML = newContent;
                        window.scrollTo({
                            top: productContainer.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                })
                .catch(err => console.error('Lỗi khi phân trang:', err));
        }
    });
</script>

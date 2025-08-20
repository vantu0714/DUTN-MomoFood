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

                    $variants =
                        $product->product_type === 'variant'
                            ? $product->variants->map(function ($v) {
                                $flavor = $v->attributeValues->firstWhere('attribute.name', 'Vị')?->value;
                                $weight = $v->attributeValues->firstWhere('attribute.name', 'Khối lượng')?->value;

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

                        <div
                            class="badge bg-secondary text-white position-absolute top-0 start-0 m-2 rounded-pill px-3 py-1">
                            {{ $product->category?->category_name ?? 'Không rõ' }}
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark text-truncate" title="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                </h6>
                                <p class="text-muted small mb-2 product-description">{{ $product->description }}</p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                <div>
                                    @if ($product->product_type === 'variant' && $product->variants->count() > 0)
                                        @php
                                            $minPrice = $product->variants->min(
                                                fn($v) => $v->discounted_price ?? $v->price,
                                            );
                                            $maxPrice = $product->variants->max(
                                                fn($v) => $v->discounted_price ?? $v->price,
                                            );
                                        @endphp

                                        @if ($minPrice != $maxPrice)
                                            <div class="text-danger fw-bold fs-5 mb-0">
                                                {{ number_format($minPrice, 0, ',', '.') }} -
                                                {{ number_format($maxPrice, 0, ',', '.') }}
                                                <small>VND</small>
                                            </div>
                                        @else
                                            <div class="text-danger fw-bold fs-5 mb-0">
                                                {{ number_format($minPrice, 0, ',', '.') }} <small>VND</small>
                                            </div>
                                        @endif
                                    @else
                                        @if ($price && $original && $price < $original)
                                            <div class="text-danger fw-bold fs-5 mb-0">
                                                {{ number_format($price, 0, ',', '.') }} <small>VND</small>
                                            </div>
                                        @elseif ($price)
                                            <div class="text-danger fw-bold fs-5 mb-0">
                                                {{ number_format($price, 0, ',', '.') }} <small>VND</small>
                                            </div>
                                        @else
                                            <div class="text-muted">Liên hệ để biết giá</div>
                                        @endif
                                    @endif
                                </div>

                                <button type="button" class="btn btn-white open-cart-modal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->product_name }}"
                                    data-product-image="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    data-product-category="{{ $product->category->category_name ?? 'Không rõ' }}"
                                    data-product-price="{{ $price ?? 0 }}"
                                    data-product-original-price="{{ $original ?? 0 }}"
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
    </div>
</div>

<style>
    .card-body .btn {
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 6px 10px;
        background-color: #fff;
    }
</style>

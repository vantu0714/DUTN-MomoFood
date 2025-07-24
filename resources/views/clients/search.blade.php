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
                                    $hasVariants = $product->variants && $product->variants->count() > 0;

                                    if ($hasVariants) {
                                        $minPrice =
                                            $product->variants->min('discounted_price') ?:
                                            $product->variants->min('price');
                                        $maxPrice =
                                            $product->variants->max('discounted_price') ?:
                                            $product->variants->max('price');
                                        $displayPrice =
                                            $minPrice == $maxPrice
                                                ? number_format($minPrice, 0, ',', '.')
                                                : number_format($minPrice, 0, ',', '.') .
                                                    ' - ' .
                                                    number_format($maxPrice, 0, ',', '.');
                                    } else {
                                        $displayPrice = number_format(
                                            $product->discounted_price ?: $product->price,
                                            0,
                                            ',',
                                            '.',
                                        );
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
                                            <h6 class="fw-bold text-dark text-truncate">
                                                {{ $product->product_name }}</h6>
                                            <p class="text-muted small mb-3">
                                                {{ Str::limit($product->description ?? 'Không có mô tả', 60) }}</p>

                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold fs-5" style="color: #db735b;">
                                                        {{ $displayPrice }} <small class="text-muted">VND</small>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <form action="{{ route('carts.add') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $product->id }}">
                                                        <button type="submit" class="btn btn-white">
                                                            <i class="bi bi-cart3 fa-2x" style="color: #db735b;"></i>
                                                        </button>
                                                    </form>
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
    </style>
@endsection

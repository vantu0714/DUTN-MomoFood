@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-primary">📊 Thống kê đơn hàng</h3>

        {{-- Biểu mẫu lọc --}}
        <form action="{{ route('admin.dashboard') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter_type" class="form-label">Loại lọc</label>
                    <select name="filter_type" id="filter_type" class="form-select" onchange="toggleFilterInputs()">
                        <option value="">-- Chọn loại lọc --</option>
                        <option value="date" {{ request('filter_type') == 'date' ? 'selected' : '' }}>Theo ngày</option>
                        <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Theo tháng</option>
                        <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Theo năm</option>
                    </select>
                </div>

                <div class="col-md-3 filter-date">
                    <label for="from_date" class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" id="from_date" class="form-control"
                        value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3 filter-date">
                    <label for="to_date" class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" id="to_date" class="form-control"
                        value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2 filter-month">
                    <label for="month" class="form-label">Tháng</label>
                    <select name="month" id="month" class="form-select">
                        <option value="">-- Chọn tháng --</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Tháng
                                {{ $m }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 filter-month filter-year">
                    <label for="year" class="form-label">Năm</label>
                    <select name="year" id="year" class="form-select">
                        @php $currentYear = now()->year; @endphp
                        @for ($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </div>
        </form>

        {{-- Tổng quan --}}
      <div class="row row-cols-1 row-cols-md-5 g-4 mb-4">
    <div class="col">
        <div class="card bg-primary text-white shadow-sm rounded-4 text-center">
            <div class="card-body">
                <h6 class="mb-2">📦 Tổng đơn hàng</h6>
                <h3 class="fw-bold">{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-info text-white shadow-sm rounded-4 text-center">
            <div class="card-body">
                <h6 class="mb-2">💰 Tổng doanh thu</h6>
                <h3 class="fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-success text-white shadow-sm rounded-4 text-center">
            <div class="card-body">
                <h6 class="mb-2">✅ Đơn hàng hoàn thành</h6>
                <h3 class="fw-bold">{{ $completedOrderCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-danger text-white shadow-sm rounded-4 text-center">
            <div class="card-body">
                <h6 class="mb-2">❌ Đơn hàng đã huỷ</h6>
                <h3 class="fw-bold">{{ $cancelledOrderCount }}</h3>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-warning text-white shadow-sm rounded-4 text-center">
            <div class="card-body">
                <h6 class="mb-2">📉 Sản phẩm hết hàng</h6>
                <h3 class="fw-bold">{{ $totalOutOfStock }}</h3>
            </div>
        </div>
    </div>

            <select class="form-select mb-3" id="outOfStockSelect">
                <option value="">-- Chọn sản phẩm hết hàng --</option>

                <optgroup label="Sản phẩm thường đã hết hàng">
                    @foreach ($outOfStockProducts as $product)
                        <option value="product_{{ $product->id }}">
                            {{ $product->product_name }} ({{ $product->product_code }}) - Hết hàng
                        </option>
                    @endforeach
                </optgroup>

                <optgroup label="Biến thể đã hết hàng">
                    @foreach ($outOfStockVariants as $variant)
                        <option value="variant_{{ $variant->id }}">
                            {{ $variant->product->product_name }} - Biến thể: {{ $variant->sku }} - Hết hàng
                        </option>
                    @endforeach
                </optgroup>
            </select>
            <div id="productDetail" class="card d-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="productImage" src="" alt="Ảnh sản phẩm" class="img-fluid border rounded">
                        </div>
                        <div class="col-md-8">
                            <h5 id="productName" class="fw-bold text-primary"></h5>
                            {{-- <p><strong>Danh mục:</strong> <span id="productCategory"></span></p> --}}
                            <p><strong>Số lượng còn:</strong> <span id="productStock"></span></p>
                            <p><strong>Giá gốc:</strong> <span id="originalPrice"></span></p>
                            <p><strong>Giá khuyến mãi:</strong> <span id="salePrice"></span></p>
                            <p><strong>Trạng thái:</strong> <span id="productStatus" class="text-danger">Hết hàng</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Biểu đồ doanh thu theo tháng --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold text-primary">📊 Biểu đồ cột doanh thu theo tháng</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="140"></canvas>
            </div>
        </div>
        {{-- Sản phẩm bán chạy --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <h4>🔥 Top 10 sản phẩm bán chạy</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng đã bán</th>
                        <th>Giá bán gần nhất</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bestSellingProducts as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset('storage/' . ($product->image ?? 'products/default.jpg')) }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                            </td>

                            <td>{{ $product->product_name }}
                                @if ($product->variant_attributes)
                                    <br>
                                    <small class="text-primary">{{ $product->variant_attributes }}</small>
                                @endif
                            </td>
                            <td>{{ $product->total_quantity }}</td>
                            <td>{{ number_format($product->latest_price, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>

        {{-- Khách hàng mua nhiều nhất --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold text-info">👤 Khách hàng mua nhiều nhất</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên khách hàng</th>
                            <th>Số đơn hàng</th>
                            <th>Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topCustomers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->orders_count }}</td>
                                <td>{{ number_format($customer->orders_sum_total_price, 0, ',', '.') }} ₫</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <script>
        const outOfStockProducts = @json($outOfStockProducts);
        const outOfStockVariants = @json($outOfStockVariants);
    </script>
    <script>
        document.getElementById('outOfStockSelect').addEventListener('change', function() {
            const value = this.value;
            const detailCard = document.getElementById('productDetail');

            if (!value) {
                detailCard.classList.add('d-none');
                return;
            }

            let data = null;

            if (value.startsWith('product_')) {
                const id = parseInt(value.replace('product_', ''));
                data = outOfStockProducts.find(p => p.id === id);

                if (data) {
                    document.getElementById('productImage').src = "/storage/" + data.image;
                    document.getElementById('productName').textContent = data.product_name;
                    // document.getElementById('productCategory').textContent = data.category?.name || 'Không có';
                    document.getElementById('productStock').textContent = data.quantity_in_stock;
                    document.getElementById('originalPrice').textContent = formatCurrency(data.original_price);
                    document.getElementById('salePrice').textContent = formatCurrency(data.sale_price || data
                        .original_price);
                    detailCard.classList.remove('d-none');
                }
            }

            if (value.startsWith('variant_')) {
                const id = parseInt(value.replace('variant_', ''));
                data = outOfStockVariants.find(v => v.id === id);

                if (data) {
                    document.getElementById('productImage').src = "/storage/" + (data.image || data.product?.image);
                    document.getElementById('productName').textContent =
                        `${data.product.product_name} - Biến thể: ${data.sku}`;
                    // document.getElementById('productCategory').textContent = data.product.category?.name ||
                    // 'Không có';
                    document.getElementById('productStock').textContent = data.quantity_in_stock;
                    document.getElementById('originalPrice').textContent = formatCurrency(data.price);
                    document.getElementById('salePrice').textContent = formatCurrency(data.price);
                    detailCard.classList.remove('d-none');
                }
            }
        });

        function formatCurrency(number) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(number);
        }
    </script>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>

    {{-- Toggle bộ lọc --}}
    <script>
        function toggleFilterInputs() {
            const type = document.getElementById('filter_type').value;
            document.querySelectorAll('.filter-date, .filter-month, .filter-year').forEach(el => {
                el.style.display = 'none';
            });
            if (type === 'date') {
                document.querySelectorAll('.filter-date').forEach(el => el.style.display = 'block');
            } else if (type === 'month') {
                document.querySelectorAll('.filter-month').forEach(el => el.style.display = 'block');
            } else if (type === 'year') {
                document.querySelectorAll('.filter-year').forEach(el => el.style.display = 'block');
            }
        }
        document.addEventListener('DOMContentLoaded', toggleFilterInputs);
    </script>
@endsection

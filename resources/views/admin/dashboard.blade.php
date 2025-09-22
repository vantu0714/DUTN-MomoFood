@extends('admin.layouts.app')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">
@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-info position-relative" style="font-size: 2rem;">
            📊 Dashboard
            <span
                style="
        display:block;
        height:4px;
        width:80px;
        background: linear-gradient(90deg, #17a2b8, #4e73df);
        margin-top:8px;
        border-radius:2px;
        box-shadow:0 2px 6px rgba(0,0,0,0.15);
        animation: slideIn 1s ease forwards;
    "></span>
        </h3>

        <style>
            @keyframes slideIn {
                from {
                    width: 0;
                }

                to {
                    width: 80px;
                }
            }
        </style>

        {{-- Biểu mẫu lọc --}}
        <form action="{{ route('admin.dashboard') }}" method="GET" class="mb-4">
            <div class="row g-2 justify-content-end align-items-center">

                <!-- Chọn loại lọc -->
                <div class="col-auto">
                    <select name="filter_type" id="filter_type"
                        class="form-select form-select-sm shadow-sm border-0 rounded-pill" onchange="toggleFilterInputs()">
                        <option value="">-- Chọn loại lọc --</option>
                        <option value="date" {{ request('filter_type') == 'date' ? 'selected' : '' }}>📅 Theo ngày
                        </option>
                        <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>📆 Theo tháng
                        </option>
                        <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>📊 Theo năm</option>
                    </select>
                </div>

                <!-- Ngày bắt đầu -->
                <div class="col-auto filter-date">
                    <input type="date" name="from_date" id="from_date"
                        class="form-control form-control-sm shadow-sm border-0 rounded-pill"
                        value="{{ request('from_date') }}">
                </div>

                <!-- Ngày kết thúc -->
                <div class="col-auto filter-date">
                    <input type="date" name="to_date" id="to_date"
                        class="form-control form-control-sm shadow-sm border-0 rounded-pill"
                        value="{{ request('to_date') }}">
                </div>

                <!-- Tháng -->
                <div class="col-auto filter-month">
                    <select name="month" id="month"
                        class="form-select form-select-sm shadow-sm border-0 rounded-pill">
                        <option value="">-- Chọn tháng --</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                Tháng {{ $m }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Năm -->
                <div class="col-auto filter-month filter-year">
                    <select name="year" id="year"
                        class="form-select form-select-sm shadow-sm border-0 rounded-pill">
                        @php $currentYear = now()->year; @endphp
                        @for ($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Nút lọc -->
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3">
                        🔍 Lọc
                    </button>
                </div>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-md-5 g-4 mb-4">
            <div class="col">
                <div class="stat-card" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                    <i class="fas fa-box-open stat-icon"></i>
                    <div class="stat-title">📦 Tổng đơn hàng</div>
                    <div class="stat-value">{{ $totalOrders }}</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card" style="background: linear-gradient(135deg, #36b9cc, #25848d);">
                    <i class="fas fa-coins stat-icon"></i>
                    <div class="stat-title">💰 Tổng doanh thu</div>
                    <div class="stat-value">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card" style="background: linear-gradient(135deg, #1cc88a, #0e7d56);">
                    <i class="fas fa-check-circle stat-icon"></i>
                    <div class="stat-title">✅ Hoàn thành</div>
                    <div class="stat-value">{{ $completedOrderCount }}</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card" style="background: linear-gradient(135deg, #e74a3b, #a51f13);">
                    <i class="fas fa-times-circle stat-icon"></i>
                    <div class="stat-title">❌ Đã huỷ</div>
                    <div class="stat-value">{{ $cancelledOrderCount }}</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card" style="background: linear-gradient(135deg, #f6c23e, #b58e10);">
                    <i class="fas fa-exclamation-triangle stat-icon"></i>
                    <div class="stat-title">📉 Hết hàng</div>
                    <div class="stat-value">{{ $totalOutOfStock }}</div>
                </div>
            </div>
        </div>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        {{-- Select sản phẩm hết hàng --}}
        <div class="mb-4">
            <label for="outOfStockSelect" class="form-label fw-bold text-info">
                <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                Danh sách sản phẩm hết hàng
            </label>

            <select class="form-select shadow-sm border-primary" id="outOfStockSelect">
                <option value="">🔍 Chọn sản phẩm hết hàng...</option>

                <optgroup label="📦 Sản phẩm thường đã hết hàng">
                    @forelse ($outOfStockProducts as $product)
                        <option value="product_{{ $product->id }}">
                            {{ $product->product_name }} (Mã: {{ $product->product_code }}) - ❌ Hết hàng
                        </option>
                    @empty
                        <option disabled>✅ Không có sản phẩm thường nào hết hàng</option>
                    @endforelse
                </optgroup>

                <optgroup label="🎯 Biến thể sản phẩm đã hết hàng">
                    @forelse ($outOfStockVariants as $variant)
                        <option value="variant_{{ $variant->id }}">
                            {{ $variant->product->product_name }}
                            - Biến thể: {{ $variant->sku }} - ❌ Hết hàng
                        </option>
                    @empty
                        <option disabled>✅ Không có biến thể nào hết hàng</option>
                    @endforelse
                </optgroup>
            </select>
        </div>


        {{-- Thông tin sản phẩm chi tiết --}}
        <div id="productDetail" class="card d-none shadow-sm rounded-4">
            <div class="card-body">
                <div class="row g-8 align-items-center">
                    <!-- Ảnh sản phẩm -->
                    <div class="col-md-4">
                        <div class="product-image-box">
                            <img id="productImage" src="" alt="Ảnh sản phẩm" class="product-image"
                                style="width:40%;">
                        </div>
                    </div>

                    <!-- Thông tin sản phẩm -->
                    <div class="col-md-6">
                        <h4 id="productName" class="fw-bold text-info mb-3"></h4>
                        <p><strong>Số lượng còn:</strong> <span id="productStock"></span></p>
                        <p><strong>Giá gốc:</strong> <span id="originalPrice"></span></p>
                        <p><strong>Giá khuyến mãi:</strong> <span id="salePrice"></span></p>
                        <p><strong>Trạng thái:</strong>
                            <span id="productStatus" class="status-label">Hết hàng</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ doanh thu theo tháng --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header bg-white border-0">
                <h3 class="fw-bold text-info">📊 Biểu đồ cột doanh thu theo tháng</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="140"></canvas>
            </div>
        </div>
        <div class="row">
            <h3 class="text-info">📊Trạng thái đơn hàng</h3>
            <div class="d-flex justify-content-center my-3">
                <div style="max-width: 400px; width: 100%;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Sản phẩm bán chạy --}}
        <div class="card shadow-sm rounded-4 mb-4 border-0">
            <div class="card-header bg-info text-white rounded-top-4">
                <h5 class="mb-0">
                    🔥 Top 10 sản phẩm bán chạy
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Hình ảnh</th>
                            <th scope="col">Tên sản phẩm</th>
                            <th scope="col">Số lượng đã bán</th>
                            <th scope="col">Giá bán gần nhất</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bestSellingProducts as $index => $product)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('clients/img/default.jpg') }}"
                                        alt="{{ $product->product_name ?? 'Sản phẩm' }}"
                                        onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                </td>

                                <td class="text-center">
                                    {{ $product->product_name }}
                                    @if ($product->variant_attributes)
                                        <br>
                                        @foreach (explode(',', $product->variant_attributes) as $variant)
                                            <span class="badge-variant">{{ trim($variant) }}</span>
                                        @endforeach
                                    @endif
                                </td>


                                <td class="text-center">{{ $product->total_quantity }}</td>
                                <td class="text-end text-success fw-semibold text-center">
                                    {{ number_format($product->latest_price, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Khách hàng mua nhiều nhất --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-info text-white rounded-top-4">
                <h5 class="mb-0">
                    👤 Top 5 Khách Hàng Mua Nhiều Nhất
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>Tên khách hàng</th>
                            <th class="text-center">Số đơn hàng</th>
                            <th class="text-center">Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topCustomers as $index => $customer)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td class="text-center">{{ $customer->orders_count }}</td>
                                <td class="text-end text-success fw-bold text-center">
                                    {{ number_format($customer->orders_sum_total_price, 0, ',', '.') }} ₫
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Không có dữ liệu
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Danh sách hàng hoàn --}}
        <div class="card mt-4">
            <div class="card-header bg-warning fw-bold">
                Danh sách hàng hoàn
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng hoàn</th>
                            <th>Lý do hoàn trả</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnedItems as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($returnedItems->currentPage() - 1) * $returnedItems->perPage() }}
                                </td>
                                <td>{{ $item->orderDetail->product->product_code ?? '-' }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . ($item->orderDetail->product->image ?? 'products/default.jpg')) }}"
                                        alt="Ảnh sản phẩm"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;"
                                        onerror="this.onerror=null; this.src='{{ asset('clients/img/default.jpg') }}';">
                                </td>
                                <td class="text-center">
                                    {{ $item->orderDetail->product_name_display }}
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->reason }}</td>
                                <td>
                                    @if ($item->isApproved())
                                        <span class="badge bg-success">{{ $item->status_label }}</span>
                                    @elseif($item->isRejected())
                                        <span class="badge bg-danger">{{ $item->status_label }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $item->status_label }}</span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $returnedItems->links('pagination::bootstrap-5') }}
                </div>
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

        const gradientRevenue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientRevenue.addColorStop(0, 'rgba(54, 162, 235, 0.9)');
        gradientRevenue.addColorStop(1, 'rgba(54, 162, 235, 0.3)');

        const gradientOrders = ctx.createLinearGradient(0, 0, 0, 400);
        gradientOrders.addColorStop(0, 'rgba(255, 159, 64, 0.9)');
        gradientOrders.addColorStop(1, 'rgba(255, 159, 64, 0.3)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: {!! json_encode($chartDataRevenue) !!},
                        backgroundColor: gradientRevenue,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 12,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Số đơn hàng',
                        data: {!! json_encode($chartDataOrders) !!},
                        backgroundColor: gradientOrders,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1,
                        borderRadius: 12,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderWidth: 1,
                        borderColor: '#ddd',
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label.includes('Doanh thu')) {
                                    return new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                                }
                                return context.parsed.y + ' đơn';
                            }
                        }
                    },
                    legend: {
                        labels: {
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => new Intl.NumberFormat('vi-VN').format(v)
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Pie chart hiện đại
        const ctx2 = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($orderStatusCount)) !!},
                datasets: [{
                    label: 'Tỷ lệ đơn hàng',
                    data: {!! json_encode(array_values($orderStatusCount)) !!},
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.9)',
                        'rgba(54, 162, 235, 0.9)',
                        'rgba(75, 192, 192, 0.9)',
                        'rgba(255, 99, 132, 0.9)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw} đơn`;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 13,
                                weight: 'bold'
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

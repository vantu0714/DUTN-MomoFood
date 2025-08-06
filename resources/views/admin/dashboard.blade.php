@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-primary">📊 Thống kê đơn hàng</h3>

        {{-- Form lọc --}}
        <form action="{{ route('admin.dashboard') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                {{-- Lọc theo loại --}}
                <div class="col-md-3">
                    <label for="filter_type" class="form-label">Loại lọc</label>
                    <select name="filter_type" id="filter_type" class="form-select" onchange="toggleFilterInputs()">
                        <option value="">-- Chọn loại lọc --</option>
                        <option value="date" {{ request('filter_type') == 'date' ? 'selected' : '' }}>Theo ngày</option>
                        <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Theo tháng</option>
                        <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Theo năm</option>
                    </select>
                </div>

                {{-- Chọn từ ngày đến ngày --}}
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

                {{-- Chọn tháng --}}
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

                {{-- Chọn năm --}}
                <div class="col-md-2 filter-month filter-year">
                    <label for="year" class="form-label">Năm</label>
                    <select name="year" id="year" class="form-select">
                        @php
                            $currentYear = now()->year;
                        @endphp
                        @for ($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Nút lọc --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </div>
        </form>


        {{-- Tổng quan --}}
        <div class="container-fluid py-4">
            <h2 class="mb-4 fw-bold">📊 Thống kê tổng quan</h2>

            <div class="row g-4">
                <!-- Tổng đơn hàng -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm rounded-4">
                        <div class="card-body text-center">
                            <h6 class="mb-2">📦 Tổng đơn hàng</h6>
                            <h3 class="fw-bold">{{ $totalOrders }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Tổng doanh thu -->
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm rounded-4">
                        <div class="card-body text-center">
                            <h6 class="mb-2">💰 Tổng doanh thu</h6>
                            <h3 class="fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
                        </div>
                    </div>
                </div>

                <!-- Sản phẩm đã bán -->


                <!-- Đơn hàng hoàn thành -->
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm rounded-4">
                        <div class="card-body text-center">
                            <h6 class="mb-2">✅ Đơn hàng hoàn thành</h6>
                            <h3 class="fw-bold">{{ $completedOrderCount }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Đơn hàng đã huỷ -->
                <div class="col-md-3">
                    <div class="card bg-danger text-white shadow-sm rounded-4">
                        <div class="card-body text-center">
                            <h6 class="mb-2">❌ Đơn hàng đã huỷ</h6>
                            <h3 class="fw-bold">{{ $cancelledOrderCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-box-open me-2"></i> Sản phẩm đã hết hàng
                        </h5>
                        <span class="badge bg-light text-danger px-3 py-2 rounded-pill">
                            {{ count($outOfStockProducts) }} sản phẩm
                        </span>
                    </div>

                    <div class="card-body">
                        @if ($outOfStockProducts->isEmpty())
                            <p class="text-muted">Không có sản phẩm nào hết hàng.</p>
                        @else
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                                @foreach ($outOfStockProducts as $product)
                                    <div class="col">
                                        <div
                                            class="card h-100 border border-danger-subtle shadow-sm hover-shadow transition">
                                            <div class="position-relative">
                                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">Hết
                                                    hàng</span>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title text-danger fw-semibold">{{ $product->product_name }}
                                                </h6>
                                                <p class="mb-1 text-muted small">Mã SP: {{ $product->id }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Lợi nhuận -->
            </div>
        </div>
    </div>

    {{-- Biểu đồ doanh thu --}}
    <div class="card mb-4 shadow-sm rounded-4">
        <div class="card-header bg-white border-0">
            <h5 class="fw-bold text-primary">📈 Biểu đồ doanh thu theo tháng</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="120"></canvas>
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

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($chartData) !!},
                    fill: true,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    pointBackgroundColor: '#198754',
                    pointRadius: 4
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
                    }
                },
                scales: {
                    y: {
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
    @push('scripts')
        <script>
            function toggleFilterInputs() {
                const type = document.getElementById('filter_type').value;

                // Ẩn hết trước
                document.querySelectorAll('.filter-date, .filter-month, .filter-year').forEach(el => {
                    el.style.display = 'none';
                });

                // Hiện theo loại chọn
                if (type === 'date') {
                    document.querySelectorAll('.filter-date').forEach(el => el.style.display = 'block');
                } else if (type === 'month') {
                    document.querySelectorAll('.filter-month').forEach(el => el.style.display = 'block');
                } else if (type === 'year') {
                    document.querySelectorAll('.filter-year').forEach(el => el.style.display = 'block');
                }
            }

            // Chạy lần đầu để set đúng hiển thị
            document.addEventListener('DOMContentLoaded', toggleFilterInputs);
        </script>
    @endpush
@endsection

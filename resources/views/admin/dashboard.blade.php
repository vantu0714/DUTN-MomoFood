@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-primary">📊 Thống kê đơn hàng</h3>

        {{-- Form lọc --}}
        <form method="GET" class="row g-3 align-items-end bg-light p-3 rounded shadow-sm mb-4" id="filter-form">
            <div class="col-md-3">
                <label for="filter_type" class="form-label">Lọc theo</label>
                <select class="form-select" name="filter_type" id="filter_type">
                    <option value="date" {{ request('filter_type') == 'date' ? 'selected' : '' }}>📅 Khoảng ngày</option>
                    <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>🗓️ Tháng</option>
                    <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>📆 Năm</option>
                </select>
            </div>

            <div class="col-md-3 filter-date">
                <label for="from_date" class="form-label">Từ ngày</label>
                <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
            </div>

            <div class="col-md-3 filter-date">
                <label for="to_date" class="form-label">Đến ngày</label>
                <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
            </div>

            <div class="col-md-2 filter-month">
                <label for="month" class="form-label">Tháng</label>
                <input type="number" name="month" min="1" max="12" class="form-control"
                    value="{{ request('month') }}">
            </div>

            <div class="col-md-2 filter-month filter-year">
                <label for="year" class="form-label">Năm</label>
                <input type="number" name="year" min="2000" class="form-control"
                    value="{{ request('year') ?? now()->year }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fas fa-filter me-1"></i> Lọc</button>
            </div>
        </form>

        {{-- Tổng quan --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white rounded-4">
                    <div class="card-body text-center">
                        <h5 class="card-title">🧾 Tổng đơn hàng</h5>
                        <p class="display-6 fw-bold mb-0">{{ $totalOrders }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success text-white rounded-4">
                    <div class="card-body text-center">
                        <h5 class="card-title">💰 Tổng doanh thu</h5>
                        <p class="display-6 fw-bold mb-0">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-warning text-white rounded-4">
                    <div class="card-body text-center">
                        <h5 class="card-title">📦 Sản phẩm đã bán</h5>
                        <p class="display-6 fw-bold mb-0">{{ $totalSold }}</p>
                    </div>
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
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold text-success">🔥 Sản phẩm bán chạy</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng đã bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bestSellers as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->total_sold }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
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

    {{-- Toggle các input lọc --}}
    {{-- Toggle các input lọc --}}
    <script>
        function toggleFilterFields() {
            const type = document.getElementById('filter_type').value;

            // Ẩn tất cả
            document.querySelectorAll('.filter-date').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.filter-month').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.filter-year').forEach(el => el.classList.add('d-none'));

            // Hiện theo loại lọc
            if (type === 'date') {
                document.querySelectorAll('.filter-date').forEach(el => el.classList.remove('d-none'));
            } else if (type === 'month') {
                document.querySelectorAll('.filter-month').forEach(el => el.classList.remove('d-none'));
                document.querySelectorAll('.filter-year').forEach(el => el.classList.remove('d-none'));
            } else if (type === 'year') {
                document.querySelectorAll('.filter-year').forEach(el => el.classList.remove('d-none'));
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleFilterFields();
            document.getElementById('filter_type').addEventListener('change', toggleFilterFields);
        });
    </script>
@endsection

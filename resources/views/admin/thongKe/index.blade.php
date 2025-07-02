{{-- resources/views/admin/thongke/index.blade.php --}}
@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@stack('page-css')

<div class="container" style="margin-left: 0px; padding-top: 20px;">
    <h2 class="mb-4">Thống kê tổng quan</h2>

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.thongke') }}" class="row mb-4">
        <div class="col-md-3">
            <label for="ngay">Lọc theo ngày</label>
            <input type="date" name="ngay" id="ngay" class="form-control" value="{{ request('ngay') }}">
        </div>
        <div class="col-md-3">
            <label for="thang">Tháng</label>
            <select name="thang" id="thang" class="form-control">
                <option value="">-- Chọn tháng --</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('thang') == $i ? 'selected' : '' }}>Tháng
                        {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label for="nam">Năm</label>
            <select name="nam" id="nam" class="form-control">
                <option value="">-- Chọn năm --</option>
                @for ($y = 2023; $y <= now()->year; $y++)
                    <option value="{{ $y }}" {{ request('nam') == $y ? 'selected' : '' }}>Năm
                        {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    {{-- Tổng quan --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Tổng sản phẩm</h5>
                    <h3>{{ $tongSanPham }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Tổng người dùng</h5>
                    <h3>{{ $tongNguoiDung }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Tổng đơn hàng</h5>
                    <h3>{{ $tongDonHang }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5>
                        Doanh thu
                        @if (request('ngay'))
                            ngày {{ \Carbon\Carbon::parse(request('ngay'))->format('d/m/Y') }}
                        @elseif(request('thang') && request('nam'))
                            tháng {{ request('thang') }}/{{ request('nam') }}
                        @elseif(request('nam'))
                            năm {{ request('nam') }}
                        @else
                            (tổng)
                        @endif
                    </h5>
                    <h3>{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</h3>
                </div>
            </div>
        </div>
    </div>

    <h4>Sản phẩm bán chạy</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng bán</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sanPhamBanChay as $sp)
                <tr>
                    <td>{{ $sp->product_name }}</td>
                    <td>{{ $sp->so_luong_ban }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    {{-- Biểu đồ doanh thu --}}
    <h4 class="mt-5">Biểu đồ doanh thu theo tháng ({{ request('nam') ?? now()->year }})</h4>
    <canvas id="revenueChart" height="100"></canvas>
</div>

{{-- Thêm Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@push('page-js')
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');

        const revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!}, // Ví dụ: ["Tháng 1", "Tháng 2", ...]
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: {!! json_encode($data) !!}, // Ví dụ: [1000000, 2000000, ...]
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush

@include('admin.layouts.footer')
@stack('page-js')

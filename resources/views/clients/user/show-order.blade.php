@extends('clients.layouts.app')

@section('content')
    @php
        $statusLabels = [
            1 => 'Chưa xác nhận',
            2 => 'Đã xác nhận',
            3 => 'Đang giao',
            4 => 'Hoàn thành',
            5 => 'Hoàn hàng',
            6 => 'Hủy đơn',
            7 => 'Chờ xử lý hoàn hàng',
            8 => 'Hoàn hàng thất bại',
        ];

        $paymentStatusLabels = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
        ];

        $statusClasses = [
            1 => 'bg-warning text-dark',
            2 => 'bg-orange text-white',
            3 => 'bg-info text-white',
            4 => 'bg-success text-white',
            5 => 'bg-secondary text-white',
            6 => 'bg-danger text-white',
            7 => 'bg-purple text-white',
            8 => 'bg-danger text-white',
        ];

        $paymentStatusClasses = [
            'unpaid' => 'bg-warning text-dark',
            'paid' => 'bg-success',
        ];

        // Tính toán giá trị
        $subtotal = $order->orderDetails->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $calculatedDiscount = $subtotal + $order->shipping_fee - $order->total_price;
        $calculatedDiscount = max(0, $calculatedDiscount);
    @endphp

    <div class="container mb-5" style="margin-top: 150px">
        <nav class="nav nav-borders">
            <a class="nav-link text-dark" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link text-dark" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link active fw-semibold" href="{{ route('clients.orders') }}">Đơn hàng</a>
            <a href="#" class="nav-link text-dark"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>
        <hr class="mt-0 mb-4">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0" style="font-family: 'Open Sans', sans-serif">Chi tiết đơn hàng
                            #{{ $order->order_code }}
                        </h4>
                        <small class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }} me-3"
                            style="font-size: 1.1em;">
                            {{ $statusLabels[$order->status] ?? 'Không xác định' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-semibold">Thông tin nhận hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Người nhận:</strong></p>
                                            <p class="mb-0">{{ $order->recipient_name ?? Auth::user()->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Số điện thoại:</strong></p>
                                            <p class="mb-0">{{ $order->recipient_phone ?? Auth::user()->phone }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Địa chỉ nhận hàng:</strong></p>
                                            <p class="mb-0">{{ $order->recipient_address ?? Auth::user()->address }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Trạng thái thanh toán:</strong></p>
                                            <span
                                                class="badge {{ $paymentStatusClasses[$order->payment_status] ?? 'bg-secondary' }}">
                                                {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if ($order->status == 6 && $order->reason)
                                    <div class="alert alert-danger p-2 mb-0">
                                        <strong>Lý do hủy:</strong> {{ $order->reason }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Sản phẩm trong đơn hàng</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="ps-2 pe-2">#</th>
                                    <th width="12%" class="ps-2 pe-2">Ảnh</th>
                                    <th width="25%" class="ps-2 pe-2">Sản phẩm</th>
                                    <th width="8%" class="ps-2 pe-2 text-center">SL</th>
                                    <th width="20%" class="ps-2 pe-2 text-end">Đơn giá</th>
                                    <th width="20%" class="ps-2 pe-2 text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $index => $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->productVariant;

                                        $variantAttributes = [];
                                        if ($variant && $variant->attributeValues) {
                                            foreach ($variant->attributeValues as $value) {
                                                $variantAttributes[] = $value->attribute->name . ': ' . $value->value;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-2 pe-2">{{ $index + 1 }}</td>
                                        <td class="ps-2 pe-2">
                                            @if ($product && $product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->product_name }}" class="img-thumbnail"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="ps-2 pe-2">
                                            <div class="d-flex flex-column">
                                                <strong class="mb-1">{{ $product->product_name ?? '[Đã xoá]' }}</strong>
                                                @if ($variant && count($variantAttributes) > 0)
                                                    <div class="mt-1">
                                                        @foreach ($variantAttributes as $attribute)
                                                            <span class="badge bg-info text-dark me-1 mb-1">
                                                                {{ $attribute }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="ps-2 pe-2 text-center">
                                            <span class="badge bg-orange text-white">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="ps-2 pe-2 text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                        <td class="ps-2 pe-2 text-end fw-bold text-orange">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Tổng giá sản phẩm:</span>
                                    <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                                </div>

                                @if ($order->promotion && $calculatedDiscount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold">Giảm giá ({{ $order->promotion }}):</span>
                                        <span
                                            class="text-danger">-{{ number_format($calculatedDiscount, 0, ',', '.') }}₫</span>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                </div>

                                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                    <span class="fw-bold">Tổng thanh toán:</span>
                                    <span
                                        class="fw-bold text-orange">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phần hiển thị trạng thái hoàn hàng --}}
                @if (in_array($order->status, [5, 7, 8]))
                    <div class="mt-4" id="existing-return-status">
                        <div
                            class="card border-{{ $order->status == 7 ? 'purple' : ($order->status == 8 ? 'danger' : 'success') }}">
                            <div
                                class="card-header bg-{{ $order->status == 7 ? 'purple' : ($order->status == 8 ? 'danger' : 'success') }} text-white">
                                <h6 class="mb-0">Trạng thái hoàn hàng</h6>
                            </div>
                            <div class="card-body">
                                @if ($order->return_reason)
                                    <p><strong>Lý do:</strong> {{ $order->return_reason }}</p>
                                @endif

                                @if ($order->status == 7)
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-clock me-2"></i> Yêu cầu hoàn hàng đang chờ xử lý
                                    </div>
                                @elseif($order->status == 5)
                                    <div class="alert alert-success mb-0">
                                        <i class="fas fa-check-circle me-2"></i> Đơn hàng đã được hoàn trả thành công
                                    </div>
                                @elseif($order->status == 8)
                                    <div class="alert alert-danger mb-0">
                                        <i class="fas fa-times-circle me-2"></i> Yêu cầu hoàn hàng đã bị từ chối
                                        @if ($order->return_rejection_reason)
                                            <div class="mt-2">
                                                <strong>Lý do từ chối:</strong> {{ $order->return_rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Hiển thị thông báo --}}
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Phần các nút hành động --}}
                <div class="d-flex justify-content-between mt-4">
                    {{-- Nút Quay lại --}}
                    <a href="{{ route('clients.orders') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>

                    <div>
                        {{-- Nút Yêu cầu hoàn hàng --}}
                        @if ($order->status == 4)
                            @php
                                $returnDeadline = \Carbon\Carbon::parse($order->completed_at)->addHours(24);
                                $canReturn = $order->completed_at && now()->lte($returnDeadline);
                            @endphp

                            @if ($canReturn)
                                <button type="button" class="btn btn-warning ms-2" id="show-return-form-btn">
                                    <i class="fas fa-undo me-2"></i> Yêu cầu hoàn hàng
                                </button>
                            @endif
                        @endif

                        {{-- Nút Hủy đơn hàng --}}
                        @if (in_array($order->status, [1]))
                            <button type="button" class="btn btn-danger ms-2"
                                onclick="document.getElementById('cancel-form').classList.toggle('d-none')">
                                <i class="fas fa-trash-alt me-2"></i>Hủy đơn hàng
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Form yêu cầu hoàn hàng --}}
                @if ($order->status == 4)
                    @php
                        $returnDeadline = \Carbon\Carbon::parse($order->completed_at)->addHours(24);
                        $canReturn = $order->completed_at && now()->lte($returnDeadline);
                    @endphp

                    @if ($canReturn)
                        <div id="return-form" class="mt-3 d-none">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">Yêu cầu hoàn hàng</h6>
                                </div>
                                <div class="card-body">
                                    <div id="return-alerts"></div>
                                    <form id="return-request-form"
                                        action="{{ route('clients.request_return', $order->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Lý do hoàn hàng <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="return_reason" id="return_reason" class="form-control" rows="4" required minlength="10"
                                                maxlength="1000" placeholder="Vui lòng nhập lý do hoàn hàng chi tiết (tối thiểu 10 ký tự)..."></textarea>
                                            <div class="form-text">
                                                <span id="char-count">0</span>/1000 ký tự
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-secondary" id="cancel-return-btn">
                                                Hủy
                                            </button>
                                            <button type="submit" class="btn btn-warning text-white"
                                                id="submit-return-btn">
                                                <i class="fas fa-paper-plane me-2"></i> Gửi yêu cầu
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            @if (!$order->completed_at)
                                Chưa xác định được thời gian hoàn thành đơn hàng
                            @else
                                Thời gian hoàn hàng (24h sau khi nhận hàng) đã kết thúc vào
                                {{ $returnDeadline->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    @endif
                @endif

                {{-- Form hủy đơn hàng --}}
                @if (in_array($order->status, [1]))
                    <div id="cancel-form" class="mt-3 d-none">
                        <div class="card border-danger">
                            <div class="card-header bg-danger">
                                <h6 class="mb-0 text-white">Hủy đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clients.ordercancel', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="cancellation_reason" class="form-label fw-bold text-danger">Lý do hủy
                                            đơn hàng:</label>
                                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" required
                                            placeholder="Vui lòng nhập lý do hủy đơn hàng..." rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger text-white">
                                        <i class="fas fa-times-circle me-2"></i>Xác nhận hủy
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            :root {
                --orange-primary: rgb(219, 115, 91);
                --orange-hover: rgb(190, 90, 68);
            }

            .bg-orange {
                background-color: var(--orange-primary) !important;
            }

            .text-orange {
                color: var(--orange-primary) !important;
            }

            .border-orange {
                border-color: var(--orange-primary) !important;
            }

            .btn-orange {
                background-color: var(--orange-primary);
                border-color: var(--orange-primary);
                color: white;
            }

            .btn-orange:hover {
                background-color: var(--orange-hover);
                border-color: var(--orange-hover);
                color: white;
            }

            .nav-borders .nav-link.active {
                color: var(--orange-primary) !important;
                border-bottom: 2px solid var(--orange-primary);
            }

            .nav-borders .nav-link:hover {
                color: var(--orange-primary);
            }

            .bg-purple {
                background-color: #6f42c1 !important;
            }

            .border-purple {
                border-color: #6f42c1 !important;
            }

            .text-purple {
                color: #6f42c1 !important;
            }

            /* Styling cho badge thuộc tính */
            .badge.bg-info {
                background-color: #17a2b8 !important;
                color: #fff !important;
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
            }

            .badge.bg-orange {
                background-color: var(--orange-primary) !important;
                color: white !important;
            }

            /* Responsive cho bảng */
            @media (max-width: 768px) {
                .table-responsive table {
                    font-size: 0.875rem;
                }

                .badge {
                    font-size: 0.75rem;
                    padding: 0.125rem 0.25rem;
                }
            }

            /* Style cho card thông tin hoàn hàng */
            #return-status-section .card {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border-width: 1px;
            }

            #return-status-section .card-header {
                font-weight: 600;
            }

            #return-status-section .alert-warning {
                border-left: 4px solid #ffc107;
                background-color: #fff3cd;
                border-color: #ffecb5;
            }
        </style>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kiểm tra xem nút hiển thị form có tồn tại không
            const showFormBtn = document.getElementById('show-return-form-btn');
            if (!showFormBtn) return;

            const returnForm = document.getElementById('return-form');
            const cancelBtn = document.getElementById('cancel-return-btn');
            const form = document.getElementById('return-request-form');
            const textarea = document.getElementById('return_reason');
            const charCount = document.getElementById('char-count');
            const alertsContainer = document.getElementById('return-alerts');

            // 1. Xử lý hiển thị form
            showFormBtn.addEventListener('click', function() {
                if (returnForm) {
                    returnForm.classList.toggle('d-none');
                }
                if (textarea) {
                    textarea.focus();
                }
            });

            // 2. Xử lý hủy form
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (returnForm) returnForm.classList.add('d-none');
                    if (form) form.reset();
                    if (charCount) charCount.textContent = '0';
                    if (alertsContainer) alertsContainer.innerHTML = '';
                });
            }

            // 3. Xử lý đếm ký tự
            if (textarea && charCount) {
                textarea.addEventListener('input', function() {
                    const count = this.value.length;
                    charCount.textContent = count;
                    charCount.style.color = count < 10 ? 'red' : (count > 950 ? 'orange' : 'green');
                });
            }

            // 4. Xử lý submit form
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submit-return-btn');
                    if (!submitBtn) return;

                    const originalText = submitBtn.innerHTML;
                    const returnReason = textarea ? textarea.value.trim() : '';

                    // Validate
                    if (!returnReason || returnReason.length < 10 || returnReason.length > 1000) {
                        if (alertsContainer) {
                            alertsContainer.innerHTML = `
                                <div class="alert alert-danger">
                                    Vui lòng nhập lý do hoàn hàng hợp lệ (10-1000 ký tự)
                                </div>`;
                        }
                        return;
                    }

                    // Disable button và hiển thị loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang xử lý...';

                    // Tạo FormData
                    const formData = new FormData(form);

                    // Thêm CSRF token nếu chưa có
                    formData.append('_token', '{{ csrf_token() }}');

                    // Gửi request với async/await để xử lý lỗi tốt hơn
                    (async () => {
                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            // Kiểm tra response.ok thay vì status code
                            if (response.ok) {
                                const data = await response.json();

                                if (data.success) {
                                    // Hiển thị thông báo thành công
                                    if (alertsContainer) {
                                        alertsContainer.innerHTML = `
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>${data.message}
                                            </div>`;
                                    }

                                    // Reload trang sau 1 giây
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    throw new Error(data.message || 'Request failed');
                                }
                            } else {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            if (alertsContainer) {
                                alertsContainer.innerHTML = `
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle me-2"></i> ${error.message || 'Có lỗi xảy ra khi gửi yêu cầu'}
                                    </div>`;
                            }
                        } finally {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })();
                });
            }
        });
    </script>
@endsection

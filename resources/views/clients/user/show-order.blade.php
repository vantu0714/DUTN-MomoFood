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
            'refunded' => 'Hoàn tiền',
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
            'refunded' => 'bg-info text-white',
        ];

        $subtotal = $order->orderDetails->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $calculatedDiscount = max(0, $subtotal + $order->shipping_fee - $order->total_price);

        // Tính toán một lần cho form hoàn hàng
        $returnDeadline = $order->completed_at ? \Carbon\Carbon::parse($order->completed_at)->addHours(24) : null;
        $canReturn = $order->status == 4 && $returnDeadline && now()->lte($returnDeadline);
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
                            #{{ $order->order_code }}</h4>
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
                <!-- Thông báo -->
                @foreach (['info', 'success', 'error'] as $type)
                    @if (session($type))
                        <div class="alert alert-{{ $type }} alert-dismissible fade show">
                            {{ session($type) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                @endforeach

                <!-- Thông tin nhận hàng -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Thông tin nhận hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Người nhận:</strong><br>{{ $order->recipient_name ?? Auth::user()->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Số điện thoại:</strong><br>{{ $order->recipient_phone ?? Auth::user()->phone }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Địa chỉ:</strong><br>{{ $order->recipient_address ?? Auth::user()->address }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Thanh toán:</strong><br>
                                    <span
                                        class="badge {{ $paymentStatusClasses[$order->payment_status] ?? 'bg-secondary' }}">
                                        {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if ($order->status == 6 && $order->reason)
                            <div class="alert alert-danger p-2 mb-0">
                                <strong>Lý do hủy:</strong> {{ $order->reason }}
                            </div>
                        @endif

                        @if (in_array($order->status, [5, 7, 8]))
                            <div
                                class="card mt-3 border-{{ $order->status == 7 ? 'warning' : ($order->status == 8 ? 'danger' : 'secondary') }}">
                                <div
                                    class="card-header bg-{{ $order->status == 7 ? 'warning' : ($order->status == 8 ? 'danger' : 'secondary') }} text-white d-flex align-items-center">
                                    <i
                                        class="fas fa-{{ $order->status == 7 ? 'clock' : ($order->status == 5 ? 'check-circle' : 'times-circle') }} me-2"></i>
                                    <strong>
                                        @if ($order->status == 7)
                                            YÊU CẦU HOÀN HÀNG ĐANG CHỜ XỬ LÝ
                                        @elseif($order->status == 5)
                                            ĐƠN HÀNG ĐÃ ĐƯỢC HOÀN TRẢ
                                        @else
                                            YÊU CẦU HOÀN HÀNG BỊ TỪ CHỐI
                                        @endif
                                    </strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if ($order->return_reason)
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 bg-light rounded">
                                                    <h6 class="fw-bold text-uppercase small">Lý do hoàn hàng</h6>
                                                    <p class="mb-0">{{ $order->return_reason }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($order->status == 8 && $order->return_rejection_reason)
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 bg-light rounded">
                                                    <h6 class="fw-bold text-uppercase small">Lý do từ chối</h6>
                                                    <p class="mb-0 text-danger">{{ $order->return_rejection_reason }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-bold">Ngày yêu cầu:</span>
                                                    {{ $order->return_requested_at ?: 'N/A' }}
                                                </li>
                                                @if ($order->status == 5)
                                                    <li class="mb-2">
                                                        <span class="fw-bold">Ngày hoàn thành:</span>
                                                        {{ $order->completed_at ?: 'N/A' }}
                                                    </li>
                                                @elseif($order->status == 8)
                                                    <li class="mb-2">
                                                        <span class="fw-bold">Ngày từ chối:</span>
                                                        {{ $order->updated_at }}
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    @if ($order->status == 7)
                                        <div class="alert alert-warning mb-0 mt-2">
                                            <i class="fas fa-info-circle me-2"></i> Yêu cầu hoàn hàng của bạn đang được xử
                                            lý. Vui lòng chờ phản hồi từ cửa hàng.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sản phẩm -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Sản phẩm</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="12%">Ảnh</th>
                                    <th width="25%">Tên</th>
                                    <th width="8%" class="text-center">SL</th>
                                    <th width="20%" class="text-end">Đơn giá</th>
                                    <th width="20%" class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $index => $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->productVariant;
                                        $variantAttributes = $variant
                                            ? $variant->attributeValues
                                                ->map(fn($value) => $value->attribute->name . ': ' . $value->value)
                                                ->toArray()
                                            : [];
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
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
                                        <td>
                                            <strong class="d-block">{{ $product->product_name ?? '[Đã xoá]' }}</strong>
                                            @foreach ($variantAttributes as $attribute)
                                                <span class="badge bg-info text-white me-1 mb-1">{{ $attribute }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-orange text-white">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                        <td class="text-end fw-bold text-orange">
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

                                @if ($calculatedDiscount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold">Giảm giá:</span>
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

                <!-- Các nút hành động -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('clients.orders') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>

                    <div>
                        @if ($canReturn)
                            <button type="button" class="btn btn-warning ms-2" data-toggle-form="return-form">
                                <i class="fas fa-undo me-2"></i> Yêu cầu hoàn hàng
                            </button>
                        @endif

                        @if ($order->status == 1)
                            <button type="button" class="btn btn-danger ms-2" data-toggle-form="cancel-form">
                                <i class="fas fa-trash-alt me-2"></i>Hủy đơn hàng
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Form yêu cầu hoàn hàng -->
                @if ($canReturn)
                    <div id="return-form" class="mt-3" style="display: none;">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">Yêu cầu hoàn hàng</h6>
                            </div>
                            <div class="card-body">
                                @if (session('return_error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        {{ session('return_error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('clients.request_return', $order->id) }}" method="POST"
                                    data-confirm="Bạn chắc chắn muốn yêu cầu hoàn hàng này?">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-warning">Lý do hoàn hàng <span
                                                class="text-danger">*</span>:</label>
                                        <textarea name="return_reason" class="form-control" rows="3" required
                                            placeholder="Vui lòng nhập lý do hoàn hàng...">{{ old('return_reason') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning text-white w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Xác nhận yêu cầu hoàn hàng
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form hủy đơn hàng -->
                @if ($order->status == 1)
                    <div id="cancel-form" class="mt-3" style="display: none;">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">Hủy đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clients.ordercancel', $order->id) }}" method="POST"
                                    data-confirm="Bạn chắc chắn muốn hủy đơn hàng này?">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-danger">Lý do hủy:</label>
                                        <textarea name="cancellation_reason" class="form-control" required placeholder="Nhập lý do hủy đơn hàng..."
                                            rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger text-white w-100">
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
        </style>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-toggle-form]').forEach(button => {
                button.addEventListener('click', function() {
                    const formId = this.getAttribute('data-toggle-form');
                    const form = document.getElementById(formId);
                    if (form) {
                        form.style.display = form.style.display === 'none' ? 'block' : 'none';
                    }
                });
            });
        });
    </script>
@endsection

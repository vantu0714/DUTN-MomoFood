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
            9 => 'Đã giao hàng',
            10 => 'Không xác nhận',
            11 => 'Giao hàng thất bại',
            12 => 'Hoàn hàng một phần',
        ];

        $paymentStatusLabels = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'refunded' => 'Hoàn tiền',
        ];

        $statusClasses = [
            1 => 'bg-warning text-dark',
            2 => 'bg-info text-white',
            3 => 'bg-primary text-white',
            4 => 'bg-success text-white',
            5 => 'bg-secondary text-white',
            6 => 'bg-danger text-white',
            7 => 'bg-primary text-white',
            8 => 'bg-danger text-white',
            9 => 'bg-success text-white',
            10 => 'bg-danger text-white',
            11 => 'bg-danger text-white',
            12 => 'bg-warning text-dark',
        ];

        $paymentStatusClasses = [
            'unpaid' => 'bg-warning text-dark',
            'paid' => 'bg-success text-white',
            'refunded' => 'bg-info text-white',
        ];

        $subtotal = $order->orderDetails->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $calculatedDiscount = max(0, $subtotal + $order->shipping_fee - $order->total_price);

        // Tính toán một lần cho form hoàn hàng
        $returnDeadline = $order->received_at ? \Carbon\Carbon::parse($order->received_at)->addHours(24) : null;
        $canReturn = $order->status == 9 && $returnDeadline && now()->lte($returnDeadline);

        $statusHistory = [];

        $statusHistory[] = [
            'label' => 'Đơn hàng được tạo',
            'time' => $order->created_at,
            'icon' => 'fas fa-plus-circle',
            'color' => 'secondary',
        ];

        if ($order->confirmed_at) {
            $statusHistory[] = [
                'label' => 'Đã xác nhận',
                'time' => $order->confirmed_at,
                'icon' => 'fas fa-check-circle',
                'color' => 'info',
            ];
        }

        if ($order->status == 10) {
            $statusHistory[] = [
                'label' => 'Không xác nhận',
                'time' => $order->updated_at,
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
                'note' => $order->reason ? 'Lý do: ' . $order->reason : null,
            ];
        }

        if ($order->delivery_failed_at) {
            $statusHistory[] = [
                'label' => 'Giao hàng thất bại',
                'time' => $order->delivery_failed_at,
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger',
                'note' => $order->reason ? 'Lý do: ' . $order->reason : null,
            ];
        }

        if ($order->delivered_at) {
            $statusHistory[] = [
                'label' => 'Đang giao hàng',
                'time' => $order->delivered_at,
                'icon' => 'fas fa-shipping-fast',
                'color' => 'primary',
            ];
        }

        if ($order->received_at) {
            $statusHistory[] = [
                'label' => 'Đã nhận hàng',
                'time' => $order->received_at,
                'icon' => 'fas fa-check-double',
                'color' => 'success',
            ];
        }

        if ($order->completed_at) {
            $statusHistory[] = [
                'label' => 'Hoàn thành',
                'time' => $order->completed_at,
                'icon' => 'fas fa-medal',
                'color' => 'success',
            ];
        }

        if ($order->return_requested_at) {
            $statusHistory[] = [
                'label' => 'Yêu cầu hoàn hàng',
                'time' => $order->return_requested_at,
                'icon' => 'fas fa-undo',
                'color' => 'warning text-dark',
                'note' => $order->return_reason ? 'Lý do: ' . $order->return_reason : null,
            ];
        }

        if ($order->return_processed_at) {
            $label = '';
            $icon = '';
            $color = '';

            if ($order->status == 5) {
                $label = 'Hoàn hàng thành công';
                $icon = 'fas fa-check-double';
                $color = 'success';
            } elseif ($order->status == 8) {
                $label = 'Yêu cầu bị từ chối';
                $icon = 'fas fa-times-circle';
                $color = 'danger';
            } elseif ($order->status == 12) {
                $label = 'Hoàn hàng một phần';
                $icon = 'fas fa-exclamation-triangle';
                $color = 'warning text-dark';
            }

            if ($label) {
                $statusHistory[] = [
                    'label' => $label,
                    'time' => $order->return_processed_at,
                    'icon' => $icon,
                    'color' => $color,
                    'note' => $order->return_rejection_reason ? 'Lý do: ' . $order->return_rejection_reason : null,
                ];
            }
        }

        if ($order->status == 5 && !$order->return_requested_at) {
            $statusHistory[] = [
                'label' => 'Đã hoàn hàng',
                'time' => $order->return_processed_at ?? $order->updated_at,
                'icon' => 'fas fa-check-double',
                'color' => 'success',
                'note' => $order->reason ? 'Lý do: ' . $order->reason : null,
            ];
        }

        if ($order->status == 6 && $order->updated_at) {
            $statusHistory[] = [
                'label' => 'Đã hủy',
                'time' => $order->updated_at,
                'icon' => 'fas fa-ban',
                'color' => 'danger',
                'note' => $order->reason ? 'Lý do: ' . $order->reason : null,
            ];
        }

        // Sắp xếp theo thời gian
        usort($statusHistory, function ($a, $b) {
            return strtotime($a['time']) - strtotime($b['time']);
        });
    @endphp

    <div class="container mb-5" style="margin-top: 150px">
        <nav class="nav nav-borders">
            <a class="nav-link text-dark" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link text-dark" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link active ms-0 fw-semibold text-decoration-none"
                style="color: rgb(219, 115, 91); border-bottom: 2px solid rgb(219, 115, 91)"
                href="{{ route('clients.orders') }}">Đơn hàng</a>
            <a href="#" class="nav-link text-dark"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>
        <hr class="mt-0 mb-4">

        <div class="card shadow">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Chi tiết đơn hàng #{{ $order->order_code }}</h4>
                        <small class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }} me-3 fs-6">
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
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">Thông tin nhận hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <p class="mb-1"><strong>Người nhận:</strong></p>
                                <p>{{ $order->recipient_name ?? Auth::user()->name }}</p>
                            </div>
                            <div class="col-md-3 mb-2">
                                <p class="mb-1"><strong>Số điện thoại:</strong></p>
                                <p>{{ $order->recipient_phone ?? Auth::user()->phone }}</p>
                            </div>
                            <div class="col-md-3 mb-2">
                                <p class="mb-1"><strong>Địa chỉ:</strong></p>
                                <p>{{ $order->recipient_address ?? Auth::user()->address }}</p>
                            </div>
                            <div class="col-md-3 mb-2">
                                <p class="mb-1"><strong>Thanh toán:</strong></p>
                                <span class="badge {{ $paymentStatusClasses[$order->payment_status] ?? 'bg-secondary' }}">
                                    {{ $paymentStatusLabels[$order->payment_status] ?? 'Không xác định' }}
                                </span>
                            </div>
                        </div>

                        @if ($order->status == 3)
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="fas fa-truck fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Đơn hàng đang được giao</h5>
                                    <p class="mb-0">Đơn hàng của bạn đang trên đường vận chuyển. Vui lòng chú ý điện thoại
                                        để nhận hàng!</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 10)
                            <div class="alert alert-danger d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Đơn hàng không được xác nhận</h5>
                                    <p class="mb-0">Rất tiếc, đơn hàng của bạn không được xác nhận. Vui lòng liên hệ với
                                        cửa hàng để biết thêm thông tin.</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 9)
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Đơn hàng đã giao thành công</h5>
                                    <p class="mb-0">Cảm ơn bạn đã mua hàng! Nếu có bất kỳ vấn đề gì với sản phẩm, bạn có
                                        thể yêu cầu hoàn hàng trong vòng 24 giờ.</p>
                                    @if ($canReturn)
                                        <p class="mb-0 mt-2"><small><i class="fas fa-info-circle me-1"></i> Bạn có thể yêu
                                                cầu hoàn hàng trong vòng 24 giờ sau khi nhận hàng.</small></p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 7)
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <p class="mb-0">Yêu cầu hoàn hàng của bạn đang được xử lý. Vui lòng chờ phản hồi từ
                                        cửa hàng.</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 6)
                            <div class="alert alert-danger d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Đã huỷ đơn hàng</h5>
                                    <p class="mb-0">Đã huỷ đơn hàng thành công. Vui lòng liên hệ với
                                        cửa hàng.</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 5)
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Đơn hàng đã được xác nhận hoàn trả</h5>
                                    <p class="mb-0">Vui lòng liên hệ với cửa hàng để được hoàn trả lại tiền</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 11)
                            <div class="alert alert-danger d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Giao hàng không thành công</h5>
                                    <p class="mb-0">Rất tiếc, đơn hàng của bạn không được giao đúng hẹn. Vui lòng liên hệ
                                        với cửa hàng để biết thêm thông tin.</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 12)
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Đơn hàng đã được hoàn trả một phần</h5>
                                    <p class="mb-0">Một số sản phẩm trong đơn hàng đã được chấp nhận hoàn trả, số còn lại
                                        không đủ điều kiện.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Lịch sử trạng thái đơn hàng -->
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-history me-2"></i>Lịch sử trạng thái đơn hàng
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="timeline-compact">
                            @forelse ($statusHistory as $index => $item)
                                <div class="timeline-item d-flex align-items-start mb-2">
                                    <div class="timeline-marker flex-shrink-0 me-3 mt-1">
                                        <i class="{{ $item['icon'] }} text-{{ explode(' ', $item['color'])[0] }}"></i>
                                    </div>
                                    <div class="timeline-content flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="fw-medium">{{ $item['label'] }}</span>
                                            <small class="text-muted ms-2">
                                                @if ($item['time'])
                                                    {{ \Carbon\Carbon::parse($item['time'])->format('H:i d/m/Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </small>
                                        </div>
                                        @if (isset($item['note']) && $item['note'])
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-sticky-note me-1"></i> {{ $item['note'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-2">
                                    <i class="fas fa-info-circle text-muted me-2"></i>
                                    <span class="text-muted">Không có lịch sử trạng thái</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if ($order->returnItems->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-undo-alt me-2 text-warning"></i>
                                Chi tiết yêu cầu hoàn hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach ($order->returnItems as $returnItem)
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">
                                            {{ $returnItem->orderDetail->product->product_name }}
                                            @if ($returnItem->orderDetail->productVariant)
                                                <span class="text-muted small">
                                                    ({{ $returnItem->orderDetail->productVariant->variant_values ? implode(', ', json_decode($returnItem->orderDetail->productVariant->variant_values, true)) : '' }})
                                                </span>
                                            @endif
                                        </h6>
                                        <span
                                            class="badge
                                            @if ($returnItem->status == 'pending') bg-warning text-dark
                                            @elseif($returnItem->status == 'approved') bg-success
                                            @elseif($returnItem->status == 'rejected') bg-danger @endif">
                                            @if ($returnItem->status == 'pending')
                                                Chờ xử lý
                                            @elseif($returnItem->status == 'approved')
                                                Đã chấp nhận
                                            @elseif($returnItem->status == 'rejected')
                                                Đã từ chối
                                            @endif
                                        </span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Số lượng yêu cầu hoàn trả:</strong>
                                                {{ $returnItem->quantity }}</p>
                                            <p class="mb-1"><strong>Lý do:</strong> {{ $returnItem->reason }}</p>
                                            <p class="mb-1"><strong>Ngày yêu cầu:</strong>
                                                {{ $returnItem->created_at->format('d/m/Y H:i') }}</p>

                                            @if ($returnItem->admin_note)
                                                <p class="mb-1"><strong>Ghi chú từ quản trị viên:</strong>
                                                    {{ $returnItem->admin_note }}</p>
                                            @endif
                                        </div>

                                        @if ($returnItem->attachments->count() > 0)
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Hình ảnh/Video đính kèm:</strong></p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($returnItem->attachments as $attachment)
                                                        @if ($attachment->file_type == 'image')
                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                                data-fancybox="gallery-{{ $returnItem->id }}">
                                                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                                    alt="Attachment" class="img-thumbnail"
                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                            </a>
                                                        @else
                                                            <video width="80" height="80" controls
                                                                class="rounded border">
                                                                <source
                                                                    src="{{ asset('storage/' . $attachment->file_path) }}"
                                                                    type="video/mp4">
                                                                Trình duyệt không hỗ trợ video
                                                            </video>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Sản phẩm -->
                <div class="card mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-semibold">Sản phẩm</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
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
                                                <span
                                                    class="badge bg-info text-white me-1 mb-1">{{ $attribute }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                        <td class="text-end fw-bold text-primary">
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
                                        class="fw-bold text-primary">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
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
                            <div class="card-header bg-warning text-white py-2">
                                <h6 class="mb-0">Yêu cầu hoàn hàng</h6>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session('return_error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        {{ session('return_error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <div id="validation-error" class="alert alert-warning" style="display: none;">
                                    Vui lòng điền đầy đủ thông tin cho các sản phẩm bạn đã chọn.
                                </div>

                                <form action="{{ route('clients.request_return', $order->id) }}" method="POST"
                                    enctype="multipart/form-data" id="returnRequestForm">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Chọn sản phẩm cần hoàn trả:</label>

                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">Chọn</th>
                                                        <th width="40%">Sản phẩm</th>
                                                        <th width="15%">Số lượng mua</th>
                                                        <th width="15%">Số lượng trả</th>
                                                        <th width="25%">Lý do</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($order->orderDetails as $index => $detail)
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                    name="return_items[{{ $index }}][selected]"
                                                                    value="1" class="return-item-checkbox"
                                                                    data-index="{{ $index }}"
                                                                    id="return_item_{{ $index }}">
                                                            </td>
                                                            <td>
                                                                <label for="return_item_{{ $index }}">
                                                                    {{ $detail->product->product_name }}
                                                                </label>
                                                                @if ($detail->productVariant)
                                                                    <div class="small text-muted">
                                                                        {{ $detail->productVariant->variant_values ? implode(', ', json_decode($detail->productVariant->variant_values, true)) : '' }}
                                                                    </div>
                                                                @endif
                                                                <input type="hidden"
                                                                    name="return_items[{{ $index }}][order_detail_id]"
                                                                    value="{{ $detail->id }}">
                                                            </td>
                                                            <td class="text-center">{{ $detail->quantity }}</td>
                                                            <td>
                                                                <input type="number"
                                                                    name="return_items[{{ $index }}][quantity]"
                                                                    class="form-control form-control-sm return-quantity"
                                                                    min="1" max="{{ $detail->quantity }}"
                                                                    data-max="{{ $detail->quantity }}"
                                                                    data-index="{{ $index }}" disabled
                                                                    placeholder="0">
                                                            </td>
                                                            <td>
                                                                <textarea name="return_items[{{ $index }}][reason]" class="form-control form-control-sm return-reason"
                                                                    rows="2" disabled placeholder="Lý do hoàn trả..." data-index="{{ $index }}"></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr class="attachment-row" style="display: none;"
                                                            id="attachment_row_{{ $index }}">
                                                            <td colspan="5">
                                                                <div class="bg-light p-3 rounded">
                                                                    <label class="fw-bold mb-3">
                                                                        <i class="fas fa-paperclip me-2"></i>
                                                                        Đính kèm hình ảnh/video (tối đa 5 file, mỗi file ≤
                                                                        10MB)
                                                                    </label>

                                                                    <div id="attachment-container-{{ $index }}">
                                                                        <div class="mb-3">
                                                                            <div class="d-flex align-items-center">
                                                                                <input type="file"
                                                                                    name="return_items[{{ $index }}][attachments][]"
                                                                                    class="form-control form-control-sm me-2"
                                                                                    accept="image/*,video/*"
                                                                                    data-index="{{ $index }}"
                                                                                    onchange="validateAttachment(this)">

                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-outline-danger remove-file-btn"
                                                                                    style="display: none;"
                                                                                    title="Xóa file này">
                                                                                    <i class="fas fa-times"></i>
                                                                                </button>
                                                                            </div>

                                                                            <div class="file-preview mt-2"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-primary add-more-btn"
                                                                            data-index="{{ $index }}"
                                                                            onclick="addMoreAttachment(this)">
                                                                            <i class="fas fa-plus me-1"></i>
                                                                            Thêm file khác
                                                                        </button>

                                                                        <div class="text-muted small">
                                                                            <span
                                                                                id="file-count-{{ $index }}">0</span>/5
                                                                            file
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-text mt-2">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        <strong>Chấp nhận:</strong> JPEG, PNG, GIF (ảnh) và
                                                                        MP4, MOV, AVI (video)<br>
                                                                        <i
                                                                            class="fas fa-exclamation-triangle me-1 text-warning"></i>
                                                                        <strong>Lưu ý:</strong> Kích thước tối đa mỗi file:
                                                                        10MB, tối đa 5 file
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-warning text-white w-100"
                                        id="submitReturnRequest" disabled>
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
                            <div class="card-header bg-danger text-white py-2">
                                <h6 class="mb-0">Hủy đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clients.ordercancel', $order->id) }}" method="POST"
                                    data-confirm="Bạn chắc chắn muốn hủy đơn hàng này?">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-danger">Lý do hủy:</label>
                                        <textarea name="reason" class="form-control" required placeholder="Nhập lý do hủy đơn hàng..." rows="3"></textarea>
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

    <style>
        .timeline-compact .timeline-item {
            border-left: 2px solid #dee2e6;
            padding-left: 15px;
            margin-left: 10px;
        }

        .timeline-compact .timeline-marker {
            margin-left: -23px;
            background-color: white;
            width: 20px;
            text-align: center;
        }
    </style>

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

        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.return-item-checkbox');
            const submitBtn = document.getElementById('submitReturnRequest');

            document.querySelectorAll('.return-quantity, .return-reason').forEach(field => {
                field.setAttribute('disabled', 'disabled');
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const index = this.getAttribute('data-index');
                    const quantityInput = document.querySelector(
                        `.return-quantity[data-index="${index}"]`);
                    const reasonTextarea = document.querySelector(
                        `.return-reason[data-index="${index}"]`);
                    const attachmentRow = document.getElementById(`attachment_row_${index}`);

                    if (this.checked) {
                        quantityInput.removeAttribute('disabled');
                        reasonTextarea.removeAttribute('disabled');
                        if (attachmentRow) attachmentRow.style.display = 'table-row';

                        if (!quantityInput.value) {
                            quantityInput.value = 1;
                        }
                    } else {
                        quantityInput.setAttribute('disabled', 'disabled');
                        reasonTextarea.setAttribute('disabled', 'disabled');
                        quantityInput.value = '';
                        reasonTextarea.value = '';
                        if (attachmentRow) attachmentRow.style.display = 'none';

                        const fileInput = document.querySelector(
                            `input[name="return_items[${index}][attachments][]"]`);
                        if (fileInput) fileInput.value = '';
                    }

                    validateForm();
                });
            });

            document.querySelectorAll('.return-quantity').forEach(input => {
                input.addEventListener('change', function() {
                    const max = parseInt(this.getAttribute('data-max'));
                    if (this.value > max) {
                        this.value = max;
                        alert('Số lượng hoàn trả không được vượt quá số lượng đã mua (' + max +
                            ')');
                    } else if (this.value < 1) {
                        this.value = 1;
                    }
                    validateForm();
                });

                input.addEventListener('input', validateForm);
            });

            document.querySelectorAll('.return-reason').forEach(textarea => {
                textarea.addEventListener('input', validateForm);
            });

            function validateForm() {
                let hasSelected = false;
                let allValid = true;

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        hasSelected = true;
                        const index = checkbox.getAttribute('data-index');
                        const quantity = document.querySelector(`.return-quantity[data-index="${index}"]`)
                            .value;
                        const reason = document.querySelector(`.return-reason[data-index="${index}"]`)
                            .value;

                        if (!quantity || parseInt(quantity) < 1 || !reason.trim()) {
                            allValid = false;

                            if (!quantity || parseInt(quantity) < 1) {
                                document.querySelector(`.return-quantity[data-index="${index}"]`).classList
                                    .add('is-invalid');
                            } else {
                                document.querySelector(`.return-quantity[data-index="${index}"]`).classList
                                    .remove('is-invalid');
                            }

                            if (!reason.trim()) {
                                document.querySelector(`.return-reason[data-index="${index}"]`).classList
                                    .add('is-invalid');
                            } else {
                                document.querySelector(`.return-reason[data-index="${index}"]`).classList
                                    .remove('is-invalid');
                            }
                        } else {
                            document.querySelector(`.return-quantity[data-index="${index}"]`).classList
                                .remove('is-invalid');
                            document.querySelector(`.return-reason[data-index="${index}"]`).classList
                                .remove('is-invalid');
                        }
                    }
                });

                submitBtn.disabled = !(hasSelected && allValid);

                if (hasSelected && !allValid) {
                    document.getElementById('validation-error').style.display = 'block';
                } else {
                    document.getElementById('validation-error').style.display = 'none';
                }
            }

            document.getElementById('returnRequestForm').addEventListener('submit', function(e) {
                const fileInputs = document.querySelectorAll('input[type="file"]');
                let isValid = true;
                let errorMessage = '';

                fileInputs.forEach(fileInput => {
                    if (fileInput.files.length > 0) {
                        for (let i = 0; i < fileInput.files.length; i++) {
                            const file = fileInput.files[i];
                            const maxSize = 10 * 1024 * 1024; // 10MB

                            if (file.size > maxSize) {
                                isValid = false;
                                errorMessage = 'File ' + file.name +
                                    ' vượt quá kích thước cho phép (10MB)';
                                break;
                            }

                            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif',
                                'image/jpg'
                            ];
                            const validVideoTypes = ['video/mp4', 'video/quicktime',
                                'video/x-msvideo', 'video/avi'
                            ];

                            if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(
                                    file.type)) {
                                isValid = false;
                                errorMessage = 'File ' + file.name +
                                    ' không đúng định dạng cho phép';
                                break;
                            }
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                }
            });
        });

        function validateAttachment(input) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            const validVideoTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/avi'];
            const file = input.files[0];

            if (!file) return;

            if (file.size > maxSize) {
                alert(`File "${file.name}" vượt quá kích thước cho phép (10MB)`);
                input.value = '';
                return;
            }

            if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(file.type)) {
                alert(`File "${file.name}" không đúng định dạng cho phép (JPEG, PNG, GIF, MP4, MOV, AVI)`);
                input.value = '';
                return;
            }

            const removeBtn = input.parentElement.querySelector('.remove-file-btn');
            if (removeBtn) {
                removeBtn.style.display = file ? 'inline-block' : 'none';
            }

            showFilePreview(input, file);
        }

        function showFilePreview(input, file) {
            const previewContainer = input.parentElement.querySelector('.file-preview');
            if (!previewContainer) {
                const preview = document.createElement('div');
                preview.className = 'file-preview mt-2';
                input.parentElement.appendChild(preview);
            }

            const preview = input.parentElement.querySelector('.file-preview');
            preview.innerHTML = '';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.cssText = 'width: 60px; height: 60px; object-fit: cover;';
                img.className = 'rounded border';
                img.onload = () => URL.revokeObjectURL(img.src);
                preview.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.style.cssText = 'width: 60px; height: 60px;';
                video.className = 'rounded border';
                video.controls = false;
                video.muted = true;
                preview.appendChild(video);
            }

            const fileName = document.createElement('small');
            fileName.className = 'd-block text-muted mt-1';
            fileName.textContent = file.name;
            preview.appendChild(fileName);
        }

        function addMoreAttachment(button) {
            const index = button.getAttribute('data-index');
            const container = document.getElementById(`attachment-container-${index}`);
            const currentInputs = container.querySelectorAll('.file-input-wrapper');

            if (currentInputs.length >= 5) {
                alert('Chỉ được phép đính kèm tối đa 5 file');
                return;
            }

            const newWrapper = document.createElement('div');
            newWrapper.className = 'file-input-wrapper mb-2 d-flex align-items-start';

            const newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.name = `return_items[${index}][attachments][]`;
            newInput.className = 'form-control form-control-sm attachment-file';
            newInput.accept = 'image/*,video/*';
            newInput.setAttribute('data-index', index);
            newInput.onchange = function() {
                validateAttachment(this);
            };

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger ms-2 remove-file-btn';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = function() {
                removeFileInput(this);
            };

            newWrapper.appendChild(newInput);
            newWrapper.appendChild(removeBtn);

            container.appendChild(newWrapper);

            if (container.querySelectorAll('.file-input-wrapper').length >= 5) {
                button.style.display = 'none';
            }
        }

        function removeFileInput(button) {
            const wrapper = button.parentElement;
            const container = wrapper.parentElement;
            const index = container.id.split('-')[2];

            const preview = wrapper.querySelector('.file-preview');
            if (preview) {
                preview.remove();
            }

            wrapper.remove();

            const addMoreBtn = document.querySelector(`.add-more-btn[data-index="${index}"]`);
            const remainingInputs = container.querySelectorAll('.file-input-wrapper');
            if (remainingInputs.length < 5 && addMoreBtn) {
                addMoreBtn.style.display = 'inline-block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-file-btn').forEach(btn => {
                btn.onclick = function() {
                    const input = this.parentElement.querySelector('input[type="file"]');
                    if (input) {
                        input.value = '';
                        this.style.display = 'none';

                        const preview = this.parentElement.querySelector('.file-preview');
                        if (preview) {
                            preview.innerHTML = '';
                        }
                    }
                };
            });
        });

        function updateFileCounter(index) {
            const container = document.getElementById(`attachment-container-${index}`);
            const inputs = container.querySelectorAll('input[type="file"]');
            const counter = document.getElementById(`file-count-${index}`);

            let fileCount = 0;
            inputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    fileCount++;
                }
            });

            if (counter) {
                counter.textContent = fileCount;
                counter.parentElement.className = fileCount >= 5 ? 'file-counter text-danger small fw-bold' :
                    'file-counter text-muted small';
            }
        }

        function validateAttachment(input) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            const validVideoTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/avi', 'video/webm'];
            const file = input.files[0];
            const index = input.getAttribute('data-index');

            if (!file) {
                updateFileCounter(index);
                return;
            }

            if (file.size > maxSize) {
                showFileError(`File "${file.name}" vượt quá kích thước cho phép (10MB)`);
                input.value = '';
                return;
            }

            if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(file.type)) {
                showFileError(`File "${file.name}" không đúng định dạng cho phép`);
                input.value = '';
                return;
            }

            const container = document.getElementById(`attachment-container-${index}`);
            const allInputs = container.querySelectorAll('input[type="file"]');
            let totalFiles = 0;

            allInputs.forEach(inp => {
                if (inp.files && inp.files.length > 0) totalFiles++;
            });

            if (totalFiles > 5) {
                showFileError('Chỉ được phép đính kèm tối đa 5 file');
                input.value = '';
                return;
            }

            showFilePreview(input, file);
            updateFileCounter(index);

            const removeBtn = input.parentElement.querySelector('.remove-file-btn');
            if (removeBtn) {
                removeBtn.style.display = 'inline-block';
            }

            const addMoreBtn = document.querySelector(`.add-more-btn[data-index="${index}"]`);
            if (totalFiles >= 5 && addMoreBtn) {
                addMoreBtn.style.display = 'none';
            }

            showFileSuccess(`Đã thêm file "${file.name}" thành công`);
        }

        function showFilePreview(input, file) {
            const wrapper = input.closest('.file-input-wrapper');
            let preview = wrapper.querySelector('.file-preview');

            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'file-preview mt-2';
                wrapper.appendChild(preview);
            }

            preview.innerHTML = '';

            const previewContent = document.createElement('div');
            previewContent.className = 'd-flex align-items-center gap-2 p-2';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.cssText = 'width: 50px; height: 50px; object-fit: cover;';
                img.className = 'rounded border shadow-sm';
                img.onload = () => URL.revokeObjectURL(img.src);

                img.style.cursor = 'pointer';
                img.onclick = () => showImageModal(img.src);

                previewContent.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.style.cssText = 'width: 50px; height: 50px; object-fit: cover;';
                video.className = 'rounded border shadow-sm';
                video.muted = true;
                video.preload = 'metadata';

                previewContent.appendChild(video);
            }

            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex-grow-1';
            fileInfo.innerHTML = `
        <div class="fw-semibold text-truncate" style="max-width: 200px;" title="${file.name}">
            ${file.name}
        </div>
        <div class="small text-muted">
            ${formatFileSize(file.size)} • ${file.type.split('/')[1].toUpperCase()}
        </div>
    `;

            previewContent.appendChild(fileInfo);

            const statusIcon = document.createElement('div');
            statusIcon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
            previewContent.appendChild(statusIcon);

            preview.appendChild(previewContent);
        }

        function addMoreAttachment(button) {
            const index = button.getAttribute('data-index');
            const container = document.getElementById(`attachment-container-${index}`);
            const currentInputs = container.querySelectorAll('.file-input-wrapper');

            if (currentInputs.length >= 5) {
                showFileError('Chỉ được phép đính kèm tối đa 5 file');
                return;
            }

            const newWrapper = document.createElement('div');
            newWrapper.className = 'file-input-wrapper mb-3';
            newWrapper.style.opacity = '0';
            newWrapper.style.transform = 'translateY(-10px)';

            newWrapper.innerHTML = `
        <div class="d-flex align-items-center">
            <input
                type="file"
                name="return_items[${index}][attachments][]"
                class="form-control form-control-sm attachment-file me-2"
                accept="image/*,video/*"
                data-index="${index}"
                onchange="validateAttachment(this)"
                style="flex: 1;">

            <button
                type="button"
                class="btn btn-sm btn-outline-danger remove-file-btn"
                onclick="removeFileInput(this)"
                title="Xóa file này">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="file-preview mt-2"></div>
    `;

            container.appendChild(newWrapper);

            setTimeout(() => {
                newWrapper.style.transition = 'all 0.3s ease';
                newWrapper.style.opacity = '1';
                newWrapper.style.transform = 'translateY(0)';
            }, 10);

            if (container.querySelectorAll('.file-input-wrapper').length >= 5) {
                button.style.display = 'none';
            }

            updateFileCounter(index);
        }

        function removeFileInput(button) {
            const wrapper = button.closest('.file-input-wrapper');
            const container = wrapper.parentElement;
            const index = container.id.split('-')[2];

            wrapper.style.transition = 'all 0.3s ease';
            wrapper.style.opacity = '0';
            wrapper.style.transform = 'translateY(-10px)';
            wrapper.style.maxHeight = wrapper.offsetHeight + 'px';

            setTimeout(() => {
                wrapper.style.maxHeight = '0';
                wrapper.style.marginBottom = '0';
                wrapper.style.paddingTop = '0';
                wrapper.style.paddingBottom = '0';

                setTimeout(() => {
                    wrapper.remove();

                    const addMoreBtn = document.querySelector(`.add-more-btn[data-index="${index}"]`);
                    const remainingInputs = container.querySelectorAll('.file-input-wrapper');
                    if (remainingInputs.length < 5 && addMoreBtn) {
                        addMoreBtn.style.display = 'inline-block';
                    }

                    updateFileCounter(index);
                }, 300);
            }, 100);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function showFileError(message) {
            showToast(message, 'error');
        }

        function showFileSuccess(message) {
            showToast(message, 'success');
        }

        function showToast(message, type = 'info') {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        `;
                document.body.appendChild(toastContainer);
            }

            const toast = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-info';
            const icon = type === 'error' ? 'fa-exclamation-triangle' : type === 'success' ? 'fa-check-circle' :
                'fa-info-circle';

            toast.className = `alert ${bgColor} text-white alert-dismissible fade show mb-2`;
            toast.style.cssText = 'box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none;';
            toast.innerHTML = `
        <i class="fas ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.remove()"></button>
    `;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        function showImageModal(src) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.style.cssText = 'z-index: 10000;';
            modal.innerHTML = `
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xem ảnh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${src}" class="img-fluid rounded" alt="Preview">
                </div>
            </div>
        </div>
    `;

            document.body.appendChild(modal);

            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="attachment-container-"]').forEach(container => {
                const index = container.id.split('-')[2];
                updateFileCounter(index);
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-file-btn')) {
                    const btn = e.target.closest('.remove-file-btn');
                    const input = btn.parentElement.querySelector('input[type="file"]');
                    if (input && input.value) {
                        removeFileInput(btn);
                    } else {
                        input.value = '';
                        btn.style.display = 'none';
                        const preview = btn.parentElement.parentElement.querySelector('.file-preview');
                        if (preview) {
                            preview.innerHTML = '';
                        }

                        const index = input.getAttribute('data-index');
                        updateFileCounter(index);
                    }
                }
            });
        });
    </script>
@endsection

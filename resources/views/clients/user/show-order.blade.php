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
            <a class="nav-link text-dark {{ request()->routeIs('notifications.orders.index') ? 'active' : '' }}"
                href="{{ route('notifications.orders.index') }}" style="text-black">
                Thông báo
            </a>
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
                                    <th width="20%" class="text-end">Thao tác</th>
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

                                        // ✅ Kiểm tra đã đánh giá chưa (theo product_id + variant_id)
                                        $alreadyRated = \App\Models\Comment::where('user_id', Auth::id())
                                            ->where('product_id', $product->id)
                                            ->when($variant, function ($q) use ($variant) {
                                                $q->where('product_variant_id', $variant->id);
                                            })
                                            ->exists();
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
                                        <td>
                                            {{-- Chỉ hiển thị nút nếu đơn hoàn thành --}}
                                            @if ($order->status == 4 && !$alreadyRated)
                                                <button type="button" class="btn btn-danger btn-danh-gia"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reviewModal{{ $item->id }}">
                                                    Đánh giá
                                                </button>
                                            @elseif ($order->status == 4 && $alreadyRated)
                                                <span class="badge bg-success">✅ Đã đánh giá</span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Modal chỉ render khi chưa đánh giá --}}
                                    @if ($order->status == 4 && !$alreadyRated)
                                        <div class="modal fade" id="reviewModal{{ $item->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('clients.comments.store') }}" method="POST"
                                                        enctype="multipart/form-data" id="reviewForm{{ $item->id }}">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden" name="product_variant_id"
                                                            value="{{ $variant->id ?? '' }}">
                                                        <input type="hidden" name="order_id"
                                                            value="{{ $order->id }}">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Đánh giá: {{ $product->product_name }}
                                                            </h5>
                                                            @if ($variantAttributes)
                                                                <div>
                                                                    @foreach ($variantAttributes as $attr)
                                                                        <span
                                                                            class="badge bg-secondary">{{ $attr }}</span>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <!-- Rating -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Chấm sao:</label>
                                                                <select name="rating" class="form-select" required>
                                                                    <option value="">-- Chọn sao --</option>
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <option value="{{ $i }}">
                                                                            {{ $i }} ⭐</option>
                                                                    @endfor
                                                                </select>
                                                            </div>

                                                            <!-- Nội dung -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Nội dung:</label>
                                                                <textarea name="content" class="form-control" rows="3" required></textarea>
                                                            </div>

                                                            <!-- Media upload -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Hình ảnh/Video (tối đa 5
                                                                    file):</label>
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span class="text-muted small">
                                                                        <span class="file-counter"
                                                                            data-index="{{ $item->id }}">0</span>/5
                                                                        file
                                                                    </span>
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm"
                                                                        onclick="document.getElementById('multi-file-input-{{ $item->id }}').click()">
                                                                        <i class="fas fa-cloud-upload-alt me-2"></i>Chọn
                                                                        file
                                                                    </button>
                                                                </div>
                                                                <div class="text-muted small mb-3">
                                                                    Tối đa 5 file, mỗi file ≤ 10MB. Hỗ trợ: JPG, PNG, GIF,
                                                                    MP4, MOV, AVI
                                                                </div>

                                                                <input type="file" class="d-none multi-file-input"
                                                                    id="multi-file-input-{{ $item->id }}"
                                                                    name="media[]" accept="image/*,video/*"
                                                                    data-index="{{ $item->id }}" multiple>

                                                                <div class="file-previews row g-2"
                                                                    id="file-previews-{{ $item->id }}"></div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Gửi đánh
                                                                giá</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
                    <!-- Bên trái -->
                    <div>
                        <a href="{{ route('clients.orders') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>


                    <!-- Bên phải -->
                    <div>
                        @if ($canReturn)
                            <button type="button" class="btn btn-warning ms-2" data-toggle-form="return-form">
                                <i class="fas fa-undo me-2"></i> Yêu cầu hoàn hàng
                            </button>
                        @endif

                        @if ($order->status == 1)
                            <button type="button" class="btn btn-danger ms-2" data-toggle-form="cancel-form">
                                <i class="fas fa-trash-alt me-2"></i> Hủy đơn hàng
                            </button>
                        @endif

                        @if ($order->status == 7)
                            <a href="{{ route('clients.edit_return', $order->id) }}" class="btn btn-info ms-2">
                                <i class="fas fa-edit me-2"></i> Chỉnh sửa yêu cầu hoàn hàng
                            </a>
                        @endif
                    </div>
                </div>


                <!-- Form yêu cầu hoàn hàng -->
                @if ($canReturn)
                    <div id="return-form" class="collapse mt-3">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white py-2">
                                <h6 class="mb-0">Yêu cầu hoàn hàng</h6>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('clients.request_return', $order->id) }}" method="POST"
                                    enctype="multipart/form-data"
                                    data-confirm="Bạn chắc chắn muốn yêu cầu hoàn hàng này?">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Chọn sản phẩm cần hoàn trả:</label>

                                        @foreach ($order->orderDetails as $index => $detail)
                                            <div class="card mb-3 product-item">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                    class="form-check-input return-item-checkbox"
                                                                    name="return_items[{{ $index }}][selected]"
                                                                    value="1" data-index="{{ $index }}"
                                                                    id="return_item_{{ $index }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="return_item_{{ $index }}"
                                                                class="form-check-label fw-semibold">
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
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <div class="small text-muted">Đã mua</div>
                                                            <span class="fw-bold">{{ $detail->quantity }}</span>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label small">Số lượng trả</label>
                                                            <div class="input-group input-group-sm">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary quantity-btn"
                                                                    data-action="decrease"
                                                                    data-index="{{ $index }}" disabled>
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number"
                                                                    class="form-control text-center return-quantity"
                                                                    name="return_items[{{ $index }}][quantity]"
                                                                    min="1" max="{{ $detail->quantity }}"
                                                                    data-max="{{ $detail->quantity }}"
                                                                    data-index="{{ $index }}" value="1"
                                                                    disabled>
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary quantity-btn"
                                                                    data-action="increase"
                                                                    data-index="{{ $index }}" disabled>
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                            <div class="invalid-feedback quantity-error"
                                                                style="display: none;">
                                                                Số lượng trả không được vượt quá số lượng đã mua
                                                            </div>
                                                        </div>

                                                        \ <div class="col-md-4">
                                                            <label class="form-label small">Lý do hoàn trả</label>
                                                            <textarea name="return_items[{{ $index }}][reason]" class="form-control form-control-sm return-reason"
                                                                rows="2" disabled placeholder="Nhập lý do hoàn trả..." data-index="{{ $index }}"></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="attachment-section mt-3" style="display: none;"
                                                        id="attachment_section_{{ $index }}">
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                    <label class="form-label fw-semibold mb-0">
                                                                        <i class="fas fa-paperclip me-2"></i>
                                                                        Đính kèm hình ảnh/video
                                                                    </label>
                                                                    <span class="text-muted small">
                                                                        <span class="file-counter"
                                                                            data-index="{{ $index }}">0</span>/5
                                                                        file
                                                                    </span>
                                                                </div>
                                                                <div class="text-muted small mb-3">
                                                                    Tối đa 5 file, mỗi file ≤ 10MB. Hỗ trợ: JPG, PNG, GIF,
                                                                    MP4, MOV, AVI
                                                                </div>

                                                                <div class="mb-3">
                                                                    <input type="file" class="d-none multi-file-input"
                                                                        id="multi-file-input-{{ $index }}"
                                                                        name="return_items[{{ $index }}][attachments][]"
                                                                        accept="image/*,video/*"
                                                                        data-index="{{ $index }}" multiple>
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm"
                                                                        onclick="document.getElementById('multi-file-input-{{ $index }}').click()">
                                                                        <i class="fas fa-cloud-upload-alt me-2"></i>Chọn
                                                                        file
                                                                    </button>
                                                                </div>

                                                                <div class="file-previews row g-2"
                                                                    id="file-previews-{{ $index }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-warning text-white"
                                            id="submitReturnRequest" disabled>
                                            <i class="fas fa-paper-plane me-2"></i>Xác nhận yêu cầu hoàn hàng
                                        </button>
                                    </div>
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

    <script>
        function setRating(rating, itemId) {
            let stars = document.querySelectorAll(`#reviewModal-${itemId} .star`);
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-muted');
                    star.classList.add('text-warning');
                } else {
                    star.classList.remove('text-warning');
                    star.classList.add('text-muted');
                }
            });
            document.getElementById(`rating-value-${itemId}`).value = rating;
        }
    </script>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach (['info', 'success', 'error'] as $type)
                @if (session($type))
                    showToast('{{ session($type) }}', '{{ $type }}');
                @endif
            @endforeach

            document.querySelectorAll('[data-toggle-form]').forEach(button => {
                button.addEventListener('click', function() {
                    const formId = this.getAttribute('data-toggle-form');
                    const form = document.getElementById(formId);
                    if (form) {
                        form.style.display = form.style.display === 'none' ? 'block' : 'none';
                    }
                });
            });

            initializeReturnForm();

            initializeReviewForms();
        });

        function initializeReturnForm() {
            const checkboxes = document.querySelectorAll('.return-item-checkbox');
            const submitBtn = document.getElementById('submitReturnRequest');

            checkboxes.forEach(checkbox => {
                const index = checkbox.dataset.index;
                toggleProductFields(index, false);

                checkbox.addEventListener('change', function() {
                    toggleProductFields(index, this.checked);
                    validateForm();
                });
            });

            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', handleQuantityChange);
            });

            document.querySelectorAll('.return-quantity').forEach(input => {
                input.addEventListener('input', function() {
                    validateQuantity(this);
                    validateForm();
                });
                input.addEventListener('blur', function() {
                    validateQuantity(this);
                    validateForm();
                });
                input.addEventListener('change', function() {
                    validateQuantity(this);
                    validateForm();
                });
            });

            document.querySelectorAll('.return-reason').forEach(textarea => {
                textarea.addEventListener('input', validateForm);
            });

            document.querySelectorAll('.multi-file-input').forEach(input => {
                input.addEventListener('change', function() {
                    handleMultipleFileSelect(this);
                });
            });

            document.getElementById('returnRequestForm').addEventListener('submit', handleFormSubmit);
        }

        function toggleProductFields(index, enabled) {
            const quantityInput = document.querySelector(`.return-quantity[data-index="${index}"]`);
            const reasonTextarea = document.querySelector(`.return-reason[data-index="${index}"]`);
            const quantityBtns = document.querySelectorAll(`.quantity-btn[data-index="${index}"]`);
            const attachmentSection = document.getElementById(`attachment_section_${index}`);

            if (enabled) {
                quantityInput.disabled = false;
                reasonTextarea.disabled = false;
                quantityBtns.forEach(btn => btn.disabled = false);
                attachmentSection.style.display = 'block';

                if (!quantityInput.value || quantityInput.value < 1) {
                    quantityInput.value = 1;
                }
            } else {
                quantityInput.disabled = true;
                reasonTextarea.disabled = true;
                reasonTextarea.value = '';
                quantityBtns.forEach(btn => btn.disabled = true);
                attachmentSection.style.display = 'none';

                const fileInput = document.getElementById(`multi-file-input-${index}`);
                if (fileInput) {
                    fileInput.value = '';
                }
                const previewContainer = document.getElementById(`file-previews-${index}`);
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                }
                updateFileCounter(index, 0);
            }
        }

        function handleQuantityChange(e) {
            const action = e.currentTarget.dataset.action;
            const index = e.currentTarget.dataset.index;
            const quantityInput = document.querySelector(`.return-quantity[data-index="${index}"]`);
            const max = parseInt(quantityInput.dataset.max);
            let currentValue = parseInt(quantityInput.value) || 1;

            if (action === 'increase') {
                if (currentValue < max) {
                    quantityInput.value = currentValue + 1;
                } else {
                    showToast('Số lượng trả không được vượt quá số lượng đã mua', 'error');
                    return;
                }
            } else if (action === 'decrease') {
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            }

            validateQuantity(quantityInput);
            validateForm();
        }

        function validateQuantity(input) {
            const max = parseInt(input.dataset.max);
            const value = parseInt(input.value) || 0;
            const errorElement = input.closest('.col-md-2').querySelector('.quantity-error');

            if (value > max) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
                showToast('Số lượng trả không được vượt quá số lượng đã mua', 'error');

                setTimeout(() => {
                    input.value = max;
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                }, 1000);

                return false;
            } else if (value < 1) {
                input.classList.add('is-invalid');
                errorElement.textContent = 'Số lượng trả phải lớn hơn 0';
                errorElement.style.display = 'block';
                showToast('Số lượng trả phải lớn hơn 0', 'error');

                setTimeout(() => {
                    input.value = 1;
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                }, 1000);

                return false;
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
                return true;
            }
        }

        function validateForm() {
            const checkboxes = document.querySelectorAll('.return-item-checkbox:checked');
            const submitBtn = document.getElementById('submitReturnRequest');
            let isValid = true;

            checkboxes.forEach(checkbox => {
                const index = checkbox.dataset.index;
                const quantityInput = document.querySelector(`.return-quantity[data-index="${index}"]`);
                const reasonTextarea = document.querySelector(`.return-reason[data-index="${index}"]`);

                if (!validateQuantity(quantityInput)) {
                    isValid = false;
                }

                const reason = reasonTextarea.value.trim();
                if (!reason) {
                    reasonTextarea.classList.add('is-invalid');
                    isValid = false;
                } else {
                    reasonTextarea.classList.remove('is-invalid');
                }
            });

            submitBtn.disabled = checkboxes.length === 0 || !isValid;
        }

        const selectedFiles = {};

        function handleMultipleFileSelect(input) {
            const files = input.files;
            const index = input.dataset.index;
            const previewContainer = document.getElementById(`file-previews-${index}`);

            if (!files || files.length === 0) return;

            if (!selectedFiles[index]) {
                selectedFiles[index] = [];
            }

            let validFilesCount = 0;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (validateFile(file) && selectedFiles[index].length < 5) {
                    selectedFiles[index].push(file);
                    validFilesCount++;

                    showFilePreview(previewContainer, file, index);
                } else if (selectedFiles[index].length >= 5) {
                    showToast('Chỉ được phép tải lên tối đa 5 file', 'error');
                    break;
                }
            }

            updateFileCounter(index, selectedFiles[index].length);

            updateFileInput(index);

            if (validFilesCount > 0) {
                showToast(`Đã thêm ${validFilesCount} file thành công`, 'success');
            }
        }

        function validateFile(file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/jpg',
                'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/avi', 'video/webm'
            ];

            if (file.size > maxSize) {
                showToast(`File "${file.name}" vượt quá kích thước cho phép (10MB)`, 'error');
                return false;
            }

            if (!validTypes.includes(file.type)) {
                showToast(`File "${file.name}" không đúng định dạng cho phép`, 'error');
                return false;
            }

            return true;
        }

        function showFilePreview(container, file, index) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'col-6 col-md-4 col-lg-3 file-preview';
            previewDiv.dataset.fileName = file.name;

            const previewCard = document.createElement('div');
            previewCard.className = 'card h-100';

            let previewContent = '';

            if (file.type.startsWith('image/')) {
                previewContent = `
                    <img src="${URL.createObjectURL(file)}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="${file.name}">
                `;
            } else {
                previewContent = `
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 120px;">
                        <i class="fas fa-video text-primary" style="font-size: 3rem;"></i>
                    </div>
                `;
            }

            previewCard.innerHTML = previewContent + `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-truncate small" title="${file.name}">${file.name}</div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFilePreview(this, '${index}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="text-muted small">${formatFileSize(file.size)}</div>
                </div>
            `;

            previewDiv.appendChild(previewCard);
            container.appendChild(previewDiv);
        }

        function removeFilePreview(button, index) {
            const previewCard = button.closest('.file-preview');
            const fileName = previewCard.dataset.fileName;

            if (selectedFiles[index]) {
                selectedFiles[index] = selectedFiles[index].filter(file => file.name !== fileName);
                updateFileInput(index);
            }

            previewCard.remove();

            const fileCount = selectedFiles[index] ? selectedFiles[index].length : 0;
            updateFileCounter(index, fileCount);
        }

        function updateFileInput(index) {
            const fileInput = document.getElementById(`multi-file-input-${index}`);

            const dataTransfer = new DataTransfer();

            if (selectedFiles[index]) {
                selectedFiles[index].forEach(file => {
                    dataTransfer.items.add(file);
                });
            }

            fileInput.files = dataTransfer.files;
        }

        function updateFileCounter(index, count) {
            const counter = document.querySelector(`.file-counter[data-index="${index}"]`);
            if (counter) {
                counter.textContent = count;
            }
        }

        function handleFormSubmit(e) {
            const submitBtn = document.getElementById('submitReturnRequest');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Đang xử lý...';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();

            const bgClass = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-info';
            const icon = type === 'error' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' :
                'fa-info-circle';

            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas ${icon} me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            bsToast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        function initializeReviewForms() {
            document.querySelectorAll('.multi-file-input').forEach(input => {
                input.addEventListener('change', function() {
                    handleReviewFileSelect(this);
                });
            });
        }

        const reviewSelectedFiles = {};

        function handleReviewFileSelect(input) {
            const files = input.files;
            const index = input.dataset.index;
            const previewContainer = document.getElementById(`file-previews-${index}`);

            if (!files || files.length === 0) return;

            if (!reviewSelectedFiles[index]) {
                reviewSelectedFiles[index] = [];
            }

            let validFilesCount = 0;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (validateFile(file) && reviewSelectedFiles[index].length < 5) {
                    reviewSelectedFiles[index].push(file);
                    validFilesCount++;

                    showReviewFilePreview(previewContainer, file, index);
                } else if (reviewSelectedFiles[index].length >= 5) {
                    showToast('Chỉ được phép tải lên tối đa 5 file', 'error');
                    break;
                }
            }

            updateReviewFileCounter(index, reviewSelectedFiles[index].length);
            updateReviewFileInput(index);

            if (validFilesCount > 0) {
                showToast(`Đã thêm ${validFilesCount} file thành công`, 'success');
            }
        }

        function showReviewFilePreview(container, file, index) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'col-6 col-md-4 col-lg-3 file-preview';
            previewDiv.dataset.fileName = file.name;

            const previewCard = document.createElement('div');
            previewCard.className = 'card h-100';

            let previewContent = '';

            if (file.type.startsWith('image/')) {
                previewContent = `
                    <img src="${URL.createObjectURL(file)}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="${file.name}">
                `;
            } else {
                previewContent = `
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 120px;">
                        <i class="fas fa-video text-primary" style="font-size: 3rem;"></i>
                    </div>
                `;
            }

            previewCard.innerHTML = previewContent + `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-truncate small" title="${file.name}">${file.name}</div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeReviewFilePreview(this, '${index}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="text-muted small">${formatFileSize(file.size)}</div>
                </div>
            `;

            previewDiv.appendChild(previewCard);
            container.appendChild(previewDiv);
        }

        function removeReviewFilePreview(button, index) {
            const previewCard = button.closest('.file-preview');
            const fileName = previewCard.dataset.fileName;

            if (reviewSelectedFiles[index]) {
                reviewSelectedFiles[index] = reviewSelectedFiles[index].filter(file => file.name !== fileName);
                updateReviewFileInput(index);
            }

            previewCard.remove();

            const fileCount = reviewSelectedFiles[index] ? reviewSelectedFiles[index].length : 0;
            updateReviewFileCounter(index, fileCount);
        }

        function updateReviewFileInput(index) {
            const fileInput = document.getElementById(`multi-file-input-${index}`);

            const dataTransfer = new DataTransfer();

            if (reviewSelectedFiles[index]) {
                reviewSelectedFiles[index].forEach(file => {
                    dataTransfer.items.add(file);
                });
            }

            fileInput.files = dataTransfer.files;
        }

        function updateReviewFileCounter(index, count) {
            const counter = document.querySelector(`.file-counter[data-index="${index}"]`);
            if (counter) {
                counter.textContent = count;
            }
        }
    </script>
@endsection

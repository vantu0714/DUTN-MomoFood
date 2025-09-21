@extends('admin.layouts.app')

@section('content')
    @php
        $cancelledItemIds = [];
        if ($order->cancellation) {
            $cancelledItemIds = $order->cancellation->items->pluck('order_detail_id')->toArray();
        }

        $pendingReturnItems = [];
        $completedReturnItems = [];
        $rejectedReturnItems = [];

        if ($order->returnItems->count() > 0) {
            foreach ($order->returnItems as $returnItem) {
                if ($returnItem->status == 'pending') {
                    $pendingReturnItems[] = $returnItem->order_detail_id;
                } elseif ($returnItem->status == 'approved') {
                    $completedReturnItems[] = $returnItem->order_detail_id;
                } elseif ($returnItem->status == 'rejected') {
                    $rejectedReturnItems[] = $returnItem->order_detail_id;
                }
            }
        }

        $isReturnStatus = in_array($order->status, [5, 7, 8, 12]);
    @endphp

    <div class="container-fluid my-4">
        <h3 class="text-center text-primary">Chi tiết đơn hàng #{{ $order->id }}</h3>
        <h5 class="text-center">{{ $order->order_code }}</h5>

        {{-- Người nhận --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-primary text-white fw-bold">Thông tin người nhận</div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4"><strong>Họ tên:</strong> {{ $order->recipient_name }}</div>
                    <div class="col-md-4"><strong>SĐT:</strong> {{ $order->recipient_phone }}</div>
                    <div class="col-md-4"><strong>Địa chỉ:</strong> {{ $order->recipient_address }}</div>
                    <div class="col-12"><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</div>
                </div>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-info text-white fw-bold d-flex justify-content-between align-items-center">
                <span>Danh sách sản phẩm</span>
                @if ($order->cancellation && $order->cancellation->items->count() > 0)
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Đã hủy {{ $order->cancellation->items->count() }} sản phẩm
                    </span>
                @endif
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $subtotal = 0;
                            $cancelledAmount = 0;
                        @endphp

                        <!-- Hiển thị sản phẩm đang hoạt động -->
                        @foreach ($order->orderDetails as $detail)
                            @php
                                $variant = $detail->productVariant;
                                $product = $variant?->product ?? $detail->product;
                                $image = $variant?->image ?? ($product?->image ?? null);
                                $productName = $product?->product_name ?? 'Không rõ sản phẩm';
                                $price = $detail->price;
                                $quantity = $detail->quantity;
                                $lineTotal = $price * $quantity;

                                $isCancelled =
                                    $order->cancellation &&
                                    $order->cancellation->items->contains('order_detail_id', $detail->id);

                                if ($isCancelled) {
                                    $cancelledAmount += $lineTotal;
                                } else {
                                    $subtotal += $lineTotal;
                                }
                            @endphp
                            <tr class="{{ $isCancelled ? 'text-muted' : '' }}">
                                <td>
                                    <div class="d-flex align-items-start gap-2">
                                        @if ($image)
                                            <img src="{{ asset('storage/' . $image) }}" alt="Ảnh sản phẩm" width="60"
                                                class="rounded border {{ $isCancelled ? 'opacity-50' : '' }}">
                                        @else
                                            <div class="text-muted {{ $isCancelled ? 'opacity-50' : '' }}"
                                                style="width: 60px;">Không có ảnh</div>
                                        @endif

                                        <div>
                                            <div
                                                class="fw-semibold {{ $isCancelled ? 'text-decoration-line-through' : '' }}">
                                                {{ $productName }}
                                            </div>

                                            {{-- Hiển thị các thuộc tính của biến thể --}}
                                            @if ($variant && $variant->values->count())
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    @foreach ($variant->values as $value)
                                                        <span
                                                            class="badge bg-info text-white px-2 py-1 {{ $isCancelled ? 'opacity-50' : '' }}">
                                                            {{ $value->attribute->name }}: {{ $value->value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">₫{{ number_format($price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $quantity }}</td>
                                <td class="text-end {{ $isCancelled ? '' : 'text-danger fw-bold' }}">
                                    ₫{{ number_format($lineTotal, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $isItemCancelled = in_array($detail->id, $cancelledItemIds);
                                        $isOrderRejected = $order->status == 10;
                                        $isOrderFullyCancelled = $order->status == 6;

                                        $showAsRejected = $isOrderRejected && !$isItemCancelled;
                                        $showAsCancelled = $isItemCancelled || $isOrderFullyCancelled;

                                        $isItemPendingReturn = in_array($detail->id, $pendingReturnItems);
                                        $isItemCompletedReturn = in_array($detail->id, $completedReturnItems);
                                        $isItemRejectedReturn = in_array($detail->id, $rejectedReturnItems);
                                    @endphp

                                    @if ($isReturnStatus)
                                        @if ($showAsCancelled)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban me-1"></i>Đã hủy
                                            </span>
                                        @elseif ($isItemPendingReturn)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Đang chờ hoàn hàng
                                            </span>
                                        @elseif ($isItemCompletedReturn)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Đã hoàn hàng
                                            </span>
                                        @elseif ($isItemRejectedReturn)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Không được hoàn hàng
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Giao thành công
                                            </span>
                                        @endif
                                    @else
                                        @if ($showAsCancelled)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban me-1"></i>Đã hủy
                                            </span>
                                        @elseif ($isItemPendingReturn)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Đang chờ hoàn hàng
                                            </span>
                                        @elseif ($isItemCompletedReturn)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Đã hoàn hàng
                                            </span>
                                        @elseif ($isItemRejectedReturn)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Không được hoàn hàng
                                            </span>
                                        @elseif ($showAsRejected)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Không xác nhận
                                            </span>
                                        @elseif ($isOrderFullyCancelled)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban me-1"></i>Đã hủy
                                            </span>
                                        @else
                                            @if ($order->status == 9 && $order->received_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Giao thành công
                                                </span>
                                            @elseif ($order->status == 11 && $order->delivery_failed_at)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Giao thất bại
                                                </span>
                                            @elseif ($order->status == 4)
                                                <span class="badge bg-success">Giao thành công</span>
                                            @else
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Thông tin đơn hàng --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-success text-white fw-bold">Thông tin đơn hàng</div>
            <div class="card-body">
                @php
                    $totalBeforeDiscount = $order->orderDetails->sum(fn($item) => $item->price * $item->quantity);
                    $discount = $totalBeforeDiscount + $order->shipping_fee - $order->total_price;
                    $statusLabels = [
                        1 => ['label' => 'Chưa xác nhận', 'class' => 'secondary'],
                        2 => ['label' => 'Đã xác nhận', 'class' => 'info'],
                        3 => ['label' => 'Đang giao', 'class' => 'primary'],
                        4 => ['label' => 'Hoàn thành', 'class' => 'success'],
                        5 => ['label' => 'Hoàn hàng', 'class' => 'dark'],
                        6 => ['label' => 'Hủy đơn', 'class' => 'danger'],
                        7 => ['label' => 'Chờ xử lý hoàn hàng', 'class' => 'warning'],
                        8 => ['label' => 'Hoàn hàng thất bại', 'class' => 'danger'],
                        9 => ['label' => 'Đã giao hàng', 'class' => 'success'],
                        10 => ['label' => 'Không xác nhận', 'class' => 'danger'],
                        11 => ['label' => 'Giao hàng thất bại', 'class' => 'danger'],
                        12 => ['label' => 'Hoàn hàng một phần', 'class' => 'warning'],
                    ];
                    $status = $statusLabels[$order->status] ?? ['label' => 'Không rõ', 'class' => 'light'];

                    $paymentMethodMap = [
                        'cod' => ['label' => 'Thanh toán khi nhận hàng (COD)', 'class' => 'secondary'],
                        'vnpay' => ['label' => 'Thanh toán qua VNPAY', 'class' => 'info'],
                    ];
                    $payment_method = $paymentMethodMap[$order->payment_method] ?? [
                        'label' => 'Không rõ',
                        'class' => 'light',
                    ];

                    $totalBeforeCancellation = $order->orderDetails->sum(fn($item) => $item->price * $item->quantity);

                    $subtotal = 0;
                    $cancelledAmount = 0;

                    foreach ($order->orderDetails as $detail) {
                        $lineTotal = $detail->price * $detail->quantity;
                        $isCancelled =
                            $order->cancellation &&
                            $order->cancellation->items->contains('order_detail_id', $detail->id);

                        if ($isCancelled) {
                            $cancelledAmount += $lineTotal;
                        } else {
                            $subtotal += $lineTotal;
                        }
                    }

                    $actualDiscount = max(
                        0,
                        $totalBeforeCancellation + $order->shipping_fee - $order->total_price - $cancelledAmount,
                    );

                    $expectedTotal = $totalBeforeCancellation + $order->shipping_fee - $actualDiscount;
                @endphp

                <div class="row gy-3">
                    @if ($order->cancellation && $order->cancellation->items->count() > 0)
                        @php
                            $totalProducts = $order->orderDetails->count();
                            $cancelledProducts = $order->cancellation->items->count();
                            $isFullyCancelled = $cancelledProducts === $totalProducts;
                        @endphp

                        <div class="col-12">
                            <div class="alert alert-{{ $isFullyCancelled ? 'danger' : 'warning' }}">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="fas fa-{{ $isFullyCancelled ? 'ban' : 'exclamation-triangle' }} fa-2x me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">
                                            {{ $isFullyCancelled ? 'Đơn hàng đã hủy toàn bộ' : 'Đơn hàng đã hủy một phần' }}
                                        </h6>
                                        <p class="mb-0">
                                            @if ($isFullyCancelled)
                                                Toàn bộ đơn hàng đã được hủy vào
                                                {{ $order->cancellation->cancelled_at->format('d/m/Y H:i') }}.
                                                Lý do: {{ $order->cancellation->reason }}
                                            @else
                                                Một số sản phẩm trong đơn hàng đã được hủy vào
                                                {{ $order->cancellation->cancelled_at->format('d/m/Y H:i') }}.
                                                Lý do: {{ $order->cancellation->reason }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6"><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }}đ</div>

                    @if ($actualDiscount > 0)
                        <div class="col-md-6">
                            <strong>Giảm giá:</strong>
                            <span class="text-success">-{{ number_format($actualDiscount) }}đ</span>
                            @if ($order->promotion)
                                <small class="text-muted d-block">(Mã: {{ $order->promotion }})</small>
                            @endif
                        </div>
                    @else
                        <div class="col-md-6"><strong>Mã giảm giá:</strong> {{ $order->promotion ?? 'Không có' }}</div>
                    @endif

                    <div class="col-12">
                        <hr>
                    </div>

                    @if ($order->status != 6)
                        <div class="col-md-6">
                            <strong>Tổng giá sản phẩm:</strong>
                            <span class="fw-semibold">{{ number_format($subtotal) }}đ</span>
                        </div>

                        <div class="col-md-6">
                            <strong>Tổng thanh toán:</strong>
                            <span class="text-danger fw-bold">{{ number_format($order->total_price) }}đ</span>
                        </div>

                        @if ($order->cancellation && $cancelledAmount > 0 && $expectedTotal != $order->total_price)
                            <div class="col-12 mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tổng thanh toán đã được điều chỉnh từ {{ number_format($expectedTotal) }}đ
                                    xuống {{ number_format($order->total_price) }}đ
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-ban fa-2x me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Đơn hàng đã hủy</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <strong>Trạng thái thanh toán:</strong>
                        @if ($order->payment_status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="badge bg-info">Hoàn tiền</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái đơn hàng:</strong>

                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                            class="d-inline-block">
                            @csrf
                            @method('PATCH')

                            <select name="status" id="order-status-select" class="form-select form-select-sm mt-2"
                                onchange="handleStatusChange(this)">
                                @php
                                    $statusOptions = [
                                        1 => ['label' => 'Chưa xác nhận', 'class' => 'secondary'],
                                        10 => ['label' => 'Không xác nhận', 'class' => 'danger'],
                                        2 => ['label' => 'Đã xác nhận', 'class' => 'info'],
                                        3 => ['label' => 'Đang giao', 'class' => 'primary'],
                                        9 => ['label' => 'Đã giao hàng', 'class' => 'success'],
                                        4 => ['label' => 'Hoàn thành', 'class' => 'success'],
                                        5 => ['label' => 'Hoàn hàng', 'class' => 'dark'],
                                        6 => ['label' => 'Hủy đơn', 'class' => 'danger'],
                                        7 => ['label' => 'Chờ xử lý hoàn hàng', 'class' => 'warning'],
                                        8 => ['label' => 'Hoàn hàng thất bại', 'class' => 'danger'],
                                        11 => ['label' => 'Giao hàng thất bại', 'class' => 'danger'],
                                        12 => ['label' => 'Hoàn hàng một phần', 'class' => 'warning'],
                                    ];

                                    $allowedTransitions = [
                                        1 => [2], // Chưa xác nhận → Đã xác nhận
                                        2 => [3], // Đã xác nhận → Đang giao
                                        3 => [9], // Đang giao → Đã giao
                                        9 => [4], // Đã giao → Hoàn thành
                                        4 => [], // Hoàn thành
                                        5 => [], // Hoàn hàng
                                        6 => [], // Hủy đơn
                                        7 => [], // Chờ xử lý hoàn hàng
                                        8 => [], // Hoàn hàng thất bại
                                        10 => [], // Không xác nhận
                                        11 => [], // Giao hàng thất bại
                                        12 => [], // Hoàn hàng một phần
                                    ];

                                    $currentStatus = $order->status;
                                    $allowedStatuses = $allowedTransitions[$currentStatus] ?? [];
                                    $allowedStatuses[] = $currentStatus;
                                @endphp
                                @foreach ($statusOptions as $key => $info)
                                    @php
                                        $isAllowed = in_array($key, $allowedStatuses);
                                    @endphp

                                    <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}
                                        {{ $isAllowed ? '' : 'disabled' }}>
                                        {{ $info['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <strong>Phương thức thanh toán:</strong>
                        <span class="badge bg-{{ $payment_method['class'] }}">{{ $payment_method['label'] }}</span>
                    </div>

                    @if (($order->status == 6 || $order->status == 10 || $order->status == 11) && $order->reason)
                        <div class="col-12"><strong>Lý do:</strong> {{ $order->reason }}</div>
                    @endif

                    @if ($order->status == 1)
                        <div class="mt-3">
                            <button class="btn btn-danger"
                                onclick="document.getElementById('reject-form').classList.toggle('d-none')">
                                Không xác nhận đơn hàng
                            </button>

                            <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST"
                                class="mt-3 d-none" id="reject-form">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Lý do không xác nhận đơn hàng</label>
                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Nhập lý do..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">Xác nhận</button>
                            </form>
                        </div>
                    @endif

                    @if ($order->status == 3)
                        <div class="mt-3">
                            <button class="btn btn-warning"
                                onclick="document.getElementById('delivery-failed-form').classList.toggle('d-none')">
                                Giao hàng thất bại
                            </button>

                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                class="mt-3 d-none" id="delivery-failed-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="11">
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Lý do giao hàng thất bại</label>
                                    <textarea name="reason" class="form-control" rows="3" required
                                        placeholder="Nhập lý do giao hàng thất bại..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning">Xác nhận giao hàng thất bại</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lịch sử trạng thái đơn hàng --}}
        <div class="card mb-4 shadow-sm rounded">
            <div class="card-header bg-info text-white fw-bold">Lịch sử trạng thái đơn hàng</div>
            <div class="card-body">
                <div class="timeline">
                    @php
                        $statusHistory = [];

                        $statusHistory[] = [
                            'label' => 'Đơn hàng được tạo',
                            'time' => $order->created_at,
                            'icon' => 'fas fa-plus-circle',
                            'color' => 'secondary',
                        ];

                        if ($order->delivered_at) {
                            $statusHistory[] = [
                                'label' => 'Đang giao hàng',
                                'time' => $order->delivered_at,
                                'icon' => 'fas fa-box-open',
                                'color' => 'success',
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
                                'color' => 'warning',
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
                                $color = 'warning';
                            }

                            if ($label) {
                                $statusHistory[] = [
                                    'label' => $label,
                                    'time' => $order->return_processed_at,
                                    'icon' => $icon,
                                    'color' => $color,
                                    'note' => $order->return_rejection_reason
                                        ? 'Lý do: ' . $order->return_rejection_reason
                                        : null,
                                ];
                            }
                        }

                        if ($order->status == 10 && $order->updated_at) {
                            $statusHistory[] = [
                                'label' => 'Không xác nhận đơn hàng',
                                'time' => $order->updated_at,
                                'icon' => 'fas fa-ban',
                                'color' => 'danger',
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

                    @forelse ($statusHistory as $index => $item)
                        <div class="timeline-item {{ $loop->last ? 'last' : '' }}">
                            <div class="timeline-marker bg-{{ $item['color'] }}">
                                <i class="{{ $item['icon'] }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $item['label'] }}</h6>
                                        @if (isset($item['note']) && $item['note'])
                                            <p class="mb-0 small text-muted">{{ $item['note'] }}</p>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        @if ($item['time'])
                                            {{ \Carbon\Carbon::parse($item['time'])->format('d/m/Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Không có lịch sử trạng thái
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Thông tin hoàn hàng --}}
        @if ($order->returnItems->count() > 0)
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-undo me-2"></i> Chi tiết yêu cầu hoàn hàng
                    </h6>
                    <div>
                        <span class="badge bg-success me-1">Đồng ý:
                            {{ $order->returnItems->where('status', 'approved')->count() }}</span>
                        <span class="badge bg-danger me-1">Từ chối:
                            {{ $order->returnItems->where('status', 'rejected')->count() }}</span>
                        <span class="badge bg-warning text-dark">Chờ xử lý:
                            {{ $order->returnItems->where('status', 'pending')->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($order->returnItems as $returnItem)
                        <div
                            class="card mb-3 border-{{ $returnItem->status == 'pending' ? 'warning' : ($returnItem->status == 'approved' ? 'success' : 'danger') }}">
                            <div
                                class="card-header bg-{{ $returnItem->status == 'pending' ? 'warning' : ($returnItem->status == 'approved' ? 'success' : 'danger') }} text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    @if ($returnItem->status == 'pending')
                                        <i class="fas fa-clock me-1"></i> Chờ xử lý
                                    @elseif($returnItem->status == 'approved')
                                        <i class="fas fa-check-circle me-1"></i> Đã chấp nhận
                                    @else
                                        <i class="fas fa-times-circle me-1"></i> Đã từ chối
                                    @endif
                                </h6>
                                <span class="badge bg-light text-dark">
                                    ID: {{ $returnItem->id }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Thông tin sản phẩm -->
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Thông tin sản phẩm</h6>
                                        <div class="d-flex align-items-start mb-3">
                                            @if ($returnItem->orderDetail->product->image)
                                                <img src="{{ asset('storage/' . $returnItem->orderDetail->product->image) }}"
                                                    alt="Ảnh sản phẩm" width="60" class="rounded me-3">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="mb-1 fw-semibold">
                                                    {{ $returnItem->orderDetail->product->product_name }}</p>
                                                @if ($returnItem->orderDetail->productVariant)
                                                    <p class="mb-1 small text-muted">
                                                        @foreach ($returnItem->orderDetail->productVariant->attributeValues as $value)
                                                            <span class="badge bg-secondary me-1">
                                                                {{ $value->attribute->name }}: {{ $value->value }}
                                                            </span>
                                                        @endforeach
                                                    </p>
                                                @endif
                                                <p class="mb-0 small">Số lượng mua:
                                                    {{ $returnItem->orderDetail->quantity }}</p>
                                                <p class="mb-0 small">Số lượng yêu cầu hoàn: {{ $returnItem->quantity }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Thông tin hoàn hàng -->
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Thông tin hoàn hàng</h6>
                                        <p class="mb-1"><strong>Lý do:</strong> {{ $returnItem->reason }}</p>
                                        <p class="mb-1"><strong>Ngày yêu cầu:</strong>
                                            {{ $returnItem->created_at->format('d/m/Y H:i') }}</p>

                                        @if ($returnItem->admin_note)
                                            <p class="mb-1"><strong>Ghi chú của admin:</strong>
                                                {{ $returnItem->admin_note }}</p>
                                        @endif

                                        @if ($returnItem->status != 'pending')
                                            <p class="mb-0">
                                                <strong>Trạng thái:</strong>
                                                <span
                                                    class="badge bg-{{ $returnItem->status == 'approved' ? 'success' : 'danger' }}">
                                                    {{ $returnItem->status == 'approved' ? 'Đã chấp nhận' : 'Đã từ chối' }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Hình ảnh/video đính kèm -->
                                @if ($returnItem->attachments->count() > 0)
                                    <div class="mt-3">
                                        <h6 class="fw-bold">Hình ảnh/Video đính kèm:</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($returnItem->attachments as $attachment)
                                                @if ($attachment->file_type == 'image')
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                        data-fancybox="gallery-{{ $returnItem->id }}">
                                                        <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                            alt="Attachment"
                                                            style="width: 100px; height: 100px; object-fit: cover;"
                                                            class="rounded border">
                                                    </a>
                                                @else
                                                    <video width="100" height="100" controls class="rounded border">
                                                        <source src="{{ asset('storage/' . $attachment->file_path) }}"
                                                            type="video/mp4">
                                                        Trình duyệt không hỗ trợ video
                                                    </video>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Nút đồng ý/từ chối (chỉ hiện khi pending) -->
                                @if ($returnItem->status == 'pending')
                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <form
                                                    action="{{ route('admin.orders.approve_return_item', $returnItem->id) }}"
                                                    method="POST" class="mb-2">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label for="admin_note_approve_{{ $returnItem->id }}"
                                                            class="form-label small">Ghi chú (nếu có):</label>
                                                        <textarea name="admin_note" id="admin_note_approve_{{ $returnItem->id }}" class="form-control form-control-sm"
                                                            rows="2" placeholder="Nhập ghi chú..."></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check me-1"></i> Đồng ý
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-md-6">
                                                <form
                                                    action="{{ route('admin.orders.reject_return_item', $returnItem->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label for="admin_note_reject_{{ $returnItem->id }}"
                                                            class="form-label small">Lý do từ chối:</label>
                                                        <textarea name="admin_note" id="admin_note_reject_{{ $returnItem->id }}" class="form-control form-control-sm"
                                                            rows="2" placeholder="Nhập lý do từ chối..." required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times me-1"></i> Từ chối
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Nút xử lý toàn bộ đơn hàng -->
                    @if ($order->status == 7)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold mb-3">Xử lý toàn bộ đơn hàng:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('admin.orders.approve_return', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle me-1"></i> Đồng ý tất cả
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-outline-danger" id="show-reject-all-form">
                                        <i class="fas fa-times-circle me-1"></i> Từ chối tất cả
                                    </button>

                                    <form id="reject-all-form" class="mt-3 p-3 bg-light rounded border d-none"
                                        action="{{ route('admin.orders.reject_return', $order->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Lý do từ chối tất cả:</label>
                                            <textarea name="return_rejection_reason" class="form-control" rows="3"
                                                placeholder="Nhập lý do từ chối tất cả yêu cầu..." required></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-secondary"
                                                id="cancel-reject-all">Hủy</button>
                                            <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- JavaScript cho form từ chối tất cả -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const showRejectAllBtn = document.getElementById('show-reject-all-form');
                    const rejectAllForm = document.getElementById('reject-all-form');
                    const cancelRejectAllBtn = document.getElementById('cancel-reject-all');

                    if (showRejectAllBtn && rejectAllForm && cancelRejectAllBtn) {
                        showRejectAllBtn.addEventListener('click', function() {
                            rejectAllForm.classList.toggle('d-none');
                            showRejectAllBtn.classList.toggle('d-none');
                        });

                        cancelRejectAllBtn.addEventListener('click', function() {
                            rejectAllForm.classList.add('d-none');
                            showRejectAllBtn.classList.remove('d-none');
                        });
                    }
                });
            </script>
        @endif

        {{-- Quay lại --}}
        <div class="text-end mt-4">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
@endsection

<script>
    function toggleRejectForm() {
        const form = document.getElementById('reject-form');
        const rejectBtn = document.getElementById('show-reject-form-btn');
        const approveForm = document.getElementById('approve-return-form');

        form.classList.toggle('d-none');
        rejectBtn.classList.toggle('d-none');

        if (approveForm) {
            approveForm.classList.toggle('d-none');
        }
    }

    function handleStatusChange(select) {
        const selectedValue = parseInt(select.value);
        if (selectedValue === 5) {
            alert("Vui lòng bấm nút 'Hoàn hàng' bên dưới để nhập lý do.");
            select.value = "{{ $order->status }}";
        } else if (selectedValue === 11) {
            alert("Vui lòng bấm nút 'Giao hàng thất bại' bên dưới để nhập lý do.");
            select.value = "{{ $order->status }}";
        } else {
            select.form.submit();
        }
    }
</script>

<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item.last {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -1.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: #0d6efd;
        top: 0.25rem;
    }

    .timeline-item.last .timeline-marker {
        background-color: #198754;
    }

    .timeline-content {
        padding-left: 1rem;
    }

    .timeline-item:not(.last)::after {
        content: '';
        position: absolute;
        left: -1rem;
        top: 1.25rem;
        height: calc(100% - 1rem);
        width: 2px;
        background-color: #dee2e6;
    }
</style>

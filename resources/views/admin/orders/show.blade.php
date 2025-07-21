@extends('admin.layouts.app')

@section('content')
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
            <div class="card-header bg-info text-white fw-bold">Danh sách sản phẩm</div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                            @php
                                $variant = $detail->productVariant;
                                $product = $variant?->product ?? $detail->product;
                                $image = $variant?->image ?? ($product?->image ?? null);
                                $productName = $product?->product_name ?? 'Không rõ sản phẩm';
                                $price = $detail->price;
                                $quantity = $detail->quantity;
                                $lineTotal = $price * $quantity;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start gap-2">
                                        @if ($image)
                                            <img src="{{ asset('storage/' . $image) }}" alt="Ảnh sản phẩm" width="60"
                                                class="rounded border">
                                        @else
                                            <div class="text-muted" style="width: 60px;">Không có ảnh</div>
                                        @endif

                                        <div>
                                            <div class="fw-semibold">{{ $productName }}</div>

                                            {{-- Hiển thị các thuộc tính của biến thể --}}
                                            @if ($variant && $variant->values->count())
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    @foreach ($variant->values as $value)
                                                        <span class="badge bg-info text-white px-2 py-1">
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
                                <td class="text-end text-danger fw-bold">₫{{ number_format($lineTotal, 0, ',', '.') }}</td>
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
                @endphp

                <div class="row gy-3">
                    <div class="col-md-6"><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }}đ</div>
                    <div class="col-md-6"><strong>Mã giảm giá:</strong> {{ $order->promotion ?? 'Không có' }}</div>
                    @if ($discount > 0)
                        <div class="col-md-6"><strong>Giảm giá:</strong> -{{ number_format($discount) }}đ</div>
                    @endif
                    <div class="col-md-6"><strong>Tổng tiền:</strong> <span
                            class="text-danger fw-bold">{{ number_format($order->total_price) }}đ</span></div>
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

                            @php
                                $statusOptions = [
                                    1 => ['label' => 'Chưa xác nhận', 'class' => 'secondary'],
                                    2 => ['label' => 'Đã xác nhận', 'class' => 'info'],
                                    3 => ['label' => 'Đang giao', 'class' => 'primary'],
                                    4 => ['label' => 'Hoàn thành', 'class' => 'success'],
                                    5 => ['label' => 'Hoàn hàng', 'class' => 'dark'],
                                    6 => ['label' => 'Hủy đơn', 'class' => 'danger'],
                                    7 => ['label' => 'Chờ xử lý hoàn hàng', 'class' => 'warning'],
                                    8 => ['label' => 'Hoàn hàng thất bại', 'class' => 'danger'],
                                ];
                            @endphp

                            <select name="status" id="order-status-select" class="form-select form-select-sm mt-2"
                                onchange="handleStatusChange(this)"
                                {{ in_array($order->status, [5, 6, 8]) ? 'disabled' : '' }}>
                                @foreach ($statusOptions as $key => $info)
                                    @php
                                        $canSelect = false;
                                        // Cho phép chuyển đến trạng thái kế tiếp
                                        if ($key == $order->status || $key == $order->status + 1) {
                                            $canSelect = true;
                                        }

                                        // Cho phép chuyển từ Đang giao (3) → Hoàn hàng (5)
                                        if ($order->status == 3 && $key == 5) {
                                            $canSelect = true;
                                        }

                                        // Không cho chuyển từ 5 (Hoàn hàng) → 6 (Hủy)
                                        if ($order->status == 5 && $key == 6) {
                                            $canSelect = false;
                                        }
                                    @endphp

                                    <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}
                                        {{ $canSelect ? '' : 'disabled' }}>
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

                    @if ($order->status == 6 && $order->reason)
                        <div class="col-12"><strong>Lý do hủy:</strong> {{ $order->reason }}</div>
                    @endif

                    @if ($order->status == 1)
                        <div class="mt-3">
                            <button class="btn btn-danger"
                                onclick="document.getElementById('cancel-form').classList.toggle('d-none')">
                                Hủy đơn
                            </button>

                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST"
                                class="mt-3 d-none" id="cancel-form">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Lý do hủy đơn</label>
                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Nhập lý do..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                            </form>
                        </div>
                    @endif

                    @if ($order->status == 3)
                        <div class="mt-3">
                            <button class="btn btn-dark"
                                onclick="document.getElementById('return-form').classList.toggle('d-none')">
                                Hoàn hàng
                            </button>

                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                class="mt-3 d-none" id="return-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="5">
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Lý do hoàn hàng</label>
                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Nhập lý do hoàn hàng..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-dark">Xác nhận hoàn hàng</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Thông tin hoàn hàng --}}
        @if (in_array($order->status, [5, 7, 8]))
            <div
                class="card mt-4 border-{{ $order->status == 5 ? 'success' : ($order->status == 7 ? 'warning' : 'danger') }}">
                <div
                    class="card-header bg-{{ $order->status == 5 ? 'success' : ($order->status == 7 ? 'warning' : 'danger') }} text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        @if ($order->status == 5)
                            <i class="fas fa-check-circle me-2"></i> Thông tin hoàn hàng thành công
                        @elseif($order->status == 7)
                            <i class="fas fa-clock me-2"></i> Yêu cầu hoàn hàng đang chờ xử lý
                        @else
                            <i class="fas fa-times-circle me-2"></i> Thông tin hoàn hàng thất bại
                        @endif
                    </h6>
                    @if ($order->return_requested_at)
                        <span
                            class="badge bg-white text-{{ $order->status == 5 ? 'success' : ($order->status == 7 ? 'warning' : 'danger') }}">
                            <i class="far fa-clock me-1"></i> {{ $order->return_requested_at }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Lý do hoàn hàng --}}
                    @if ($order->return_reason)
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fas fa-comment-dots text-primary me-2"></i>Lý do hoàn hàng:
                            </h6>
                            <div class="p-3 bg-light rounded border-start border-4 border-primary">
                                <p class="mb-0 text-dark">{{ $order->return_reason }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Thời gian xử lý --}}
                    @if ($order->return_processed_at)
                        <div class="mb-3 d-flex align-items-center">
                            <i class="fas fa-calendar-check text-muted me-2"></i>
                            <span class="fw-medium">Xử lý lúc: <span
                                    class="text-dark">{{ $order->return_processed_at }}</span></span>
                        </div>
                    @endif

                    {{-- Lý do từ chối --}}
                    @if ($order->return_rejection_reason)
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fas fa-ban text-danger me-2"></i>Lý do từ chối:
                            </h6>
                            <div class="p-3 bg-light rounded border-start border-4 border-danger">
                                <p class="mb-0 text-dark">{{ $order->return_rejection_reason }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Nút hành động cho trạng thái chờ xử lý --}}
                    @if ($order->status == 7)
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <form id="approve-return-form"
                                    action="{{ route('admin.orders.approve_return', $order->id) }}" method="POST"
                                    class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 py-2">
                                        <i class="fas fa-check-circle me-2"></i> Chấp nhận hoàn hàng
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-danger w-100 py-2" id="show-reject-form-btn"
                                    onclick="toggleRejectForm()">
                                    <i class="fas fa-times-circle me-2"></i> Từ chối yêu cầu
                                </button>
                            </div>
                        </div>

                        {{-- Form từ chối --}}
                        <form id="reject-form" class="mt-4 p-4 bg-light rounded shadow-sm d-none"
                            action="{{ route('admin.orders.reject_return', $order->id) }}" method="POST">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Nhập lý do từ chối
                            </h6>
                            <div class="mb-3">
                                <textarea name="return_rejection_reason" id="return_rejection_reason" class="form-control" rows="4"
                                    placeholder="Vui lòng nhập lý do từ chối yêu cầu hoàn hàng..." required></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary px-4"
                                    onclick="toggleRejectForm()">
                                    <i class="fas fa-times me-1"></i> Hủy
                                </button>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-paper-plane me-1"></i> Gửi
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
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
        } else {
            select.form.submit();
        }
    }
</script>

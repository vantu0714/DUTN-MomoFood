@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">
<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="container-fluid page-header py-5 bg-primary text-white">
            <h1 class="text-center display-6">Giỏ hàng</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-white">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-white">Trang</a></li>
                <li class="breadcrumb-item active text-white">Giỏ hàng</li>
            </ol>
        </div>

        <div class="container-fluid py-5">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            @endif

            @php $total = 0; @endphp
            <form action="{{ route('carts.removeSelected') }}" method="POST" id="delete-selected-form"
                onsubmit="return checkSelectedItems()">
                @csrf

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Danh sách sản phẩm trong giỏ</h5>
                    <button type="submit" class="btn btn-danger btn-sm" {{ count($carts) == 0 ? 'disabled' : '' }}>
                        🗑️ Xóa các sản phẩm đã chọn
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle text-center table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tạm tính</th>
                                <th>Xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($carts) > 0)
                                @foreach ($carts as $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->productVariant;

                                        $image = $product->image ?? 'clients/img/default.png';
                                        $productName = $product->product_name ?? 'Không có tên';

                                        // ✅ Ghép thông tin thuộc tính: Vị: Ngọt, Size: M
                                        $variantName = '';
                                        if ($variant && $variant->attributeValues) {
                                            $variantName = $variant->attributeValues
                                                ->map(function ($val) {
                                                    return $val->attribute->name . ': ' . $val->value;
                                                })
                                                ->implode(', ');
                                        }

                                        $stock = $variant->quantity_in_stock ?? ($product->quantity_in_stock ?? 0);
                                        $price = $item->discounted_price ?? 0;
                                        $subTotal = $price * $item->quantity;
                                        $total += $subTotal;
                                    @endphp

                                    <tr class="cart-item" data-id="{{ $item->id }}"
                                        data-stock="{{ $stock }}">
                                        <td>
                                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}"
                                                class="select-item" data-subtotal="{{ $subTotal ?? 0 }}">
                                        </td>
                                        <td>
                                            <img src="{{ asset('storage/' . $image) }}" class="rounded"
                                                style="width: 60px; height: 60px;" />
                                        </td>
                                        <td class="text-start">
                                            <strong>{{ $productName }}</strong>
                                            @if ($variant && $variant->attributeValues->count())
                                                <div class="mt-1">
                                                    @foreach ($variant->attributeValues as $value)
                                                        <span class="badge bg-info text-dark me-1">
                                                            {{ $value->attribute->name }}:
                                                            <strong>{{ $value->value }}</strong>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ number_format($price, 0, ',', '.') }} đ</td>
                                        <td>
                                            <div class="input-group input-group-sm quantity-control mx-auto"
                                                style="max-width: 130px;">
                                                <button type="button"
                                                    class="btn btn-outline-secondary quantity-decrease">−</button>
                                                <input type="number" class="form-control text-center quantity-input"
                                                    value="{{ $item->quantity }}" min="1"
                                                    data-old-value="{{ $item->quantity }}">
                                                <button type="button"
                                                    class="btn btn-outline-secondary quantity-increase">+</button>
                                            </div>
                                        </td>
                                        <td class="sub-total">{{ number_format($subTotal, 0, ',', '.') }} đ</td>
                                        <td>
                                            <a href="{{ route('carts.remove', $item->id) }}"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Giỏ hàng của bạn đang trống</td>
                                </tr>
                            @endif
                        </tbody>

                    </table>
                </div>
            </form>
        </div>

        @php
            $shipping = 30000;
            $discount = session('discount', 0);
            $promotionName = session('promotion.name', '');
            $grandTotal = $total + $shipping - $discount;

            if ($grandTotal < 0) {
                $grandTotal = 0;
            }
        @endphp

        @if ($carts->count() > 0)
            <div class="row justify-content-end mt-5">
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div id="cart-summary" class="bg-white rounded-4 shadow-sm p-4">
                        <h5 class="mb-4 text-primary">Tóm tắt đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span id="total-price">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span id="shipping-fee">{{ number_format($shipping, 0, ',', '.') }} đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                            <span>Tổng cộng:</span>
                            <span id="grand-total">{{ number_format($grandTotal, 0, ',', '.') }} đ</span>
                        </div>

                        <form id="checkout-form" action="{{ route('clients.order') }}" method="GET">
                            <input type="hidden" id="selected-items-input">
                            <button type="submit" class="btn btn-primary w-100 mt-4 py-2 text-uppercase">
                                Thanh toán
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal chi tiết đơn hàng -->
@if (session('orderSuccess'))
    @php
        $order = \App\Models\Order::with(['orderDetails.product', 'orderDetails.productVariant'])->find(
            session('orderSuccess'),
        );
    @endphp

    @if ($order)
        <!-- Modal chi tiết đơn hàng -->
        <div class="modal fade" id="orderSuccessModal" tabindex="-1" role="dialog"
            aria-labelledby="orderSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="orderSuccessModalLabel">
                            <i class="fas fa-check-circle"></i> Đặt hàng thành công!
                        </h5>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-success">
                            <strong>Cảm ơn bạn đã đặt hàng!</strong> Đơn hàng của bạn đã được tiếp nhận và đang được xử
                            lý.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Thông tin đơn hàng</h6>
                                <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Trạng thái:</strong>
                                    {{ $order->payment_status }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Thông tin giao hàng</h6>
                                <p><strong>Người nhận:</strong> {{ $order->recipient_name }}</p>
                                <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $order->recipient_address }}</p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold">Chi tiết sản phẩm</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $item)
                                        @php
                                            $product = $item->product;
                                            $variant = $item->productVariant;
                                            $image = $product->image
                                                ? asset('storage/' . $product->image)
                                                : asset('images/default.jpg');
                                            $variantDetails = '';
                                            if ($variant && $variant->attributeValues) {
                                                $variantDetails = $variant->attributeValues
                                                    ->map(fn($val) => $val->attribute->name . ': ' . $val->value)
                                                    ->implode(', ');
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $image }}" class="me-2 rounded"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold">{{ $product->product_name }}</div>
                                                        @if ($variantDetails)
                                                            <div class="text-muted small">{{ $variantDetails }}</div>
                                                        @endif
                                                        <div class="text-muted small">Loại:
                                                            {{ $product->category->category_name ?? 'Không rõ' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-danger fw-bold">
                                                {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tạm tính:</strong>
                                    {{ number_format($order->orderDetails->sum(fn($i) => $i->price * $i->quantity)) }}₫
                                </p>
                                <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }}₫</p>
                                <p><strong>Voucher:</strong> {{ $order->promotion ?? 'Không áp dụng' }}</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <h5><strong>Tổng cộng: <span
                                            class="text-danger">{{ number_format($order->total_price) }}₫</span></strong>
                                </h5>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            Chúng tôi sẽ liên hệ với bạn trong vòng 24 giờ để xác nhận đơn hàng.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Đóng
                        </button>

                        <a href="{{ route('home') }}" class="btn btn-success">
                            <i class="fas fa-shopping-cart"></i> Tiếp tục mua sắm
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endif
@endif

@include('clients.layouts.footer')
{{-- AJAX cập nhật số lượng --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.cart-item');
        const alertBox = document.getElementById('cart-error-alert');

        const modalEl = document.getElementById('orderSuccessModal');
        if (modalEl) {
            const orderSuccessModal = new bootstrap.Modal(modalEl);
            orderSuccessModal.show();
        }

        function showError(message) {
            if (alertBox) {
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
                alertBox.classList.add('d-block');

                setTimeout(() => {
                    alertBox.classList.add('d-none');
                    alertBox.classList.remove('d-block');
                }, 3000);
            }
        }

        function updateQuantityAjax(id, quantity, row, input) {
            fetch("{{ route('carts.updateAjax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id,
                        quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật hiển thị
                        row.querySelector('.sub-total').textContent = data.sub_total + ' đ';
                        document.getElementById('total-price').textContent = data.total + ' đ';
                        document.getElementById('grand-total').textContent = data.grand_total + ' đ';

                        // 👉 CẬP NHẬT THÊM: cập nhật data-subtotal của checkbox tương ứng
                        const checkbox = row.querySelector('.select-item');
                        if (checkbox) {
                            const cleanValue = data.sub_total.replace(/[^\d]/g, '');
                            checkbox.setAttribute('data-subtotal', parseInt(cleanValue));
                        }

                        // 👉 Gọi lại hàm tính tổng theo sản phẩm đã chọn
                        updateSummaryFromSelectedItems();
                    } else {
                        showError(data.message || 'Vượt quá số lượng sản phẩm còn trong kho.');
                        if (input && input.dataset.oldValue) {
                            input.value = input.dataset.oldValue;
                        } else {
                            location.reload();
                        }
                    }
                });
        }



        rows.forEach(row => {
            const input = row.querySelector('.quantity-input');
            const btnIncrease = row.querySelector('.quantity-increase');
            const btnDecrease = row.querySelector('.quantity-decrease');
            const id = row.dataset.id;
            const stock = parseInt(row.dataset.stock) || 1;

            if (!input) return; // Không có input thì bỏ qua

            input.dataset.oldValue = input.value;

            btnIncrease?.addEventListener('click', () => {
                let quantity = parseInt(input.value) || 1;
                if (quantity >= stock) {
                    showError('Không thể vượt quá tồn kho: ' + stock);
                    return;
                }
                input.dataset.oldValue = quantity;
                quantity++;
                input.value = quantity;
                updateQuantityAjax(id, quantity, row, input);
            });

            btnDecrease?.addEventListener('click', () => {
                let quantity = parseInt(input.value) || 1;
                input.dataset.oldValue = quantity;
                if (quantity > 1) {
                    quantity--;
                    input.value = quantity;
                    updateQuantityAjax(id, quantity, row, input);
                }
            });

            input.addEventListener('change', () => {
                let quantity = parseInt(input.value) || 1;
                if (quantity < 1) quantity = 1;
                if (quantity > stock) {
                    showError('Không thể vượt quá tồn kho: ' + stock);
                    input.value = input.dataset.oldValue;
                    return;
                }
                input.dataset.oldValue = quantity;
                input.value = quantity;
                updateQuantityAjax(id, quantity, row, input);
            });
        });

        // XỬ LÝ CHỌN TẤT CẢ CHECKBOX
        const selectAll = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('.select-item');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSummaryFromSelectedItems(); // GỌI HÀM cập nhật lại tổng tiền
            });

            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else {
                        const allChecked = Array.from(itemCheckboxes).every(i => i.checked);
                        selectAll.checked = allChecked;
                    }
                    updateSummaryFromSelectedItems(); // GỌI HÀM khi user chọn riêng lẻ
                });
            });
        }
    });

    function checkSelectedItems() {
        const selected = document.querySelectorAll('.select-item:checked');
        if (selected.length === 0) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm để xóa!');
            return false;
        }
        return confirm('Bạn có chắc muốn xóa các sản phẩm đã chọn?');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const alertBox = document.querySelector('.alert');
        if (alertBox) {
            setTimeout(() => {
                alertBox.classList.add('fade');
                alertBox.classList.remove('show');
            }, 4000); // Ẩn sau 4 giây

            setTimeout(() => {
                alertBox.remove();
            }, 5000); // Xoá khỏi DOM sau 5 giây
        }
    });

    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN').format(number) + ' đ';
    }

    function updateSummaryFromSelectedItems() {
        const selectedItems = document.querySelectorAll('.select-item:checked');
        let total = 0;

        selectedItems.forEach(item => {
            const raw = item.dataset.subtotal;
            const numeric = parseFloat(raw?.replace(/[^\d]/g, '') || 0);
            total += numeric;
        });

        const shipping = total > 0 ? 30000 : 0;
        const discount = parseInt("{{ $discount }}") || 0;
        let grandTotal = total + shipping - discount;
        if (grandTotal < 0) grandTotal = 0;

        document.getElementById('total-price').textContent = formatCurrency(total);
        document.getElementById('shipping-fee').textContent = formatCurrency(shipping);
        document.getElementById('grand-total').textContent = formatCurrency(grandTotal);
    }


    document.querySelectorAll('.select-item').forEach(cb => {
        cb.addEventListener('change', updateSummaryFromSelectedItems);
    });

    document.getElementById('select-all')?.addEventListener('change', updateSummaryFromSelectedItems);

    updateSummaryFromSelectedItems(); // Gọi khi tải trang

    document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
        const selected = Array.from(document.querySelectorAll('.select-item:checked'))
            .map(cb => cb.value);

        if (selected.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
            return;
        }

        // Xoá input cũ nếu có
        document.querySelectorAll('#checkout-form input[name="selected_items[]"]').forEach(el => el.remove());

        // Thêm input hidden dạng mảng
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = id;
            document.getElementById('checkout-form').appendChild(input);
        });
    });
    const modalEl = document.getElementById('orderSuccessModal');
    if (modalEl) {
        const orderSuccessModal = new bootstrap.Modal(modalEl);
        orderSuccessModal.show();
    }
    // Xóa session sau 5 giây (gợi ý – cần backend hỗ trợ thêm nếu cần thiết)
</script>

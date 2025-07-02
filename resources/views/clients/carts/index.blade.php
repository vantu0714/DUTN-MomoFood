@include('clients.layouts.header')
@include('clients.layouts.sidebar')

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
                                        $variantName = $variant->name ?? null;
                                        $stock = $variant->quantity ?? ($product->quantity ?? 0);
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
                                            @if ($variantName)
                                                <br><small class="text-muted">Biến thể: {{ $variantName }}</small>
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

        @if ($carts->count() > 0)
            <!-- Nút chọn voucher -->
            <button class="btn btn-outline-primary my-3" data-bs-toggle="modal" data-bs-target="#voucherModal">
                🎟️ Chọn Voucher
            </button>
        @endif

        <!-- Modal voucher giống Shopee -->
        <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">Voucher của Shop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Form nhập mã voucher -->
                        <form action="{{ route('carts.applyCoupon') }}" method="POST" class="d-flex mb-4">
                            @csrf
                            <input type="text" name="promotion" class="form-control me-2"
                                placeholder="Nhập mã voucher của Shop">
                            <button class="btn btn-outline-success" type="submit">Áp dụng</button>
                        </form>

                        <!-- Danh sách voucher -->
                        @foreach ($vouchers as $voucher)
                            <div class="border rounded p-3 mb-3 position-relative">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-danger fw-bold">Giảm
                                            {{ $voucher->discount_type === 'percent' ? $voucher->discount_value . '%' : number_format($voucher->discount_value) . 'đ' }}
                                        </div>
                                        <small class="text-muted">
                                            Đơn tối thiểu: {{ number_format($voucher->min_total_spent ?? 0) }}đ <br>
                                            HSD: {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <form method="POST" action="{{ route('carts.applyCoupon') }}">
                                        @csrf
                                        <input type="hidden" name="promotion"
                                            value="{{ $voucher->promotion_name }}">
                                        <button class="btn btn-outline-danger">Lưu</button>
                                    </form>
                                </div>

                                @if ($total < ($voucher->min_total_spent ?? 0))
                                    <div class="alert alert-warning mt-2 p-2 mb-0">
                                        <i class="bi bi-info-circle"></i> Mua thêm
                                        {{ number_format($voucher->min_total_spent - $total) }}đ để sử dụng Voucher
                                        này.
                                    </div>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
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
                        @if ($discount > 0 && $promotionName)
                            <div class="d-flex justify-content-between mb-2 text-success fw-bold">
                                <span>Giảm giá ({{ $promotionName }}):</span>
                                <span>-{{ number_format($discount, 0, ',', '.') }} đ</span>
                            </div>
                        @endif
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


@include('clients.layouts.footer')
{{-- AJAX cập nhật số lượng --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.cart-item');
        const alertBox = document.getElementById('cart-error-alert');

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
</script>

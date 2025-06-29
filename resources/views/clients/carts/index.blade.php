@include('clients.layouts.header')
@include('clients.layouts.sidebar')
{{-- ForMatCode --}}
<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Giỏ hàng</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#">Trang</a></li>
                <li class="breadcrumb-item active text-white">Giỏ hàng</li>
            </ol>
        </div>

        <div class="container-fluid py-5">
            @php $total = 0; @endphp
            <form action="{{ route('carts.removeSelected') }}" method="POST" id="delete-selected-form"
                onsubmit="return checkSelectedItems()">

                @csrf

                <button type="submit" class="btn btn-danger mb-3" {{ count($carts) == 0 ? 'disabled' : '' }}>
                    🗑️ Xóa các sản phẩm đã chọn
                </button>

                <div class="table-responsive">
                    <table class="table" id="cart-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Sản phẩm</th>
                                <th>Tên</th>
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
                                                class="select-item">
                                        </td>
                                        <td>
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded-circle"
                                                style="width: 80px; height: 80px;" />
                                        </td>
                                        <td>
                                            {{ $productName }}
                                            @if ($variantName)
                                                <br><small class="text-muted">Biến thể: {{ $variantName }}</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($price, 0, ',', '.') }} đ</td>
                                        <td>
                                            <div class="input-group quantity-control" style="max-width: 130px;">
                                                <button type="button"
                                                    class="btn btn-outline-secondary quantity-decrease">-</button>
                                                <input type="number" class="form-control quantity-input text-center"
                                                    value="{{ $item->quantity }}" min="1"
                                                    data-old-value="{{ $item->quantity }}">
                                                <button type="button"
                                                    class="btn btn-outline-secondary quantity-increase">+</button>
                                            </div>
                                        </td>

                                        <td class="sub-total">{{ number_format($subTotal, 0, ',', '.') }} đ</td>
                                        <td>
                                            <a href="{{ route('carts.remove', $item->id) }}"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                            
                                <tr>
                                    <td colspan="7" class="text-center">Giỏ hàng trống</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </form>

        </div>

        {{-- MÃ GIẢM GIÁ --}}
        <div class="mt-5">
            <form action="{{ route('carts.applyCoupon') }}" method="POST" class="d-flex">
                @csrf
                <input type="text" name="promotion" class="form-control border-0 border-bottom rounded me-3 py-3"
                    placeholder="Nhập mã giảm giá" style="text-transform: uppercase;"
                    oninput="this.value = this.value.replace(/\s/g, '')">
                <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="submit">
                    Áp dụng mã
                </button>
            </form>
        </div>

        {{-- TÍNH TỔNG --}}
        @php
            $shipping = 30000;
            $discount = 0;
            $promotionName = '';

            if (session()->has('promotion')) {
                $promotion = session('promotion');
                $promotionName = $promotion['name'] ?? '';
                
                <div class="row g-4 justify-content-end mt-5">
                    <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                        <div class="bg-light rounded">
                            <div class="p-4">
                                <h4 class="mb-4">Tóm tắt đơn hàng</h4>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="total-price">{{ number_format($total, 0, ',', '.') }} đ</span>
                                </div>

                                @if ($total > 0)
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
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Tổng cộng:</span>
                                        <span id="grand-total">{{ number_format($grandTotal, 0, ',', '.') }} đ</span>
                                    </div>
                                @endif
                            </div>

                            @if ($total > 0)
                                <a href="{{ route('clients.order') }}"
                                    class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4">
                                    Thanh Toán
                                </a>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('clients.order') }}"
                        class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4">
                        Thanh Toán
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

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
                        row.querySelector('.sub-total').textContent = data.sub_total + ' đ';
                        document.getElementById('total-price').textContent = data.total + ' đ';
                        document.getElementById('grand-total').textContent = data.grand_total + ' đ';
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
            });

            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else {
                        const allChecked = Array.from(itemCheckboxes).every(i => i.checked);
                        selectAll.checked = allChecked;
                    }
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
</script>

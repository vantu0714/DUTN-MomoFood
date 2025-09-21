@extends('clients.layouts.app')

@section('content')
    <div class="container-fluid featurs py-5">
        <div class="container pt-5 mt-5">
            {{-- THÔNG BÁO --}}
            {{-- Ở chỗ hiển thị thông báo --}}
            @if (session('success'))
                <script>
                    Toastify({
                        text: "{{ session('success') }}",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#4CAF50", // xanh lá cho success
                        stopOnFocus: true
                    }).showToast();
                </script>
            @endif

            @if (session('error'))
                <script>
                    Toastify({
                        text: "{{ session('error') }}",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#f44336", // đỏ cho error
                        stopOnFocus: true
                    }).showToast();
                </script>
            @endif


            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            @endif

            {{-- FORM THANH TOÁN --}}
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    {{-- CỘT TRÁI: Địa chỉ + sản phẩm --}}
                    <div class="col-md-8">
                        {{-- Địa chỉ nhận hàng --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <h5 class="mb-0 text-danger">Địa Chỉ Nhận Hàng</h5>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addressModal">
                                        Thay Đổi
                                    </button>
                                </div>

                                {{-- Hiển thị địa chỉ mặc định --}}
                                <div id="address-display">
                                    @if (!empty($recipient))
                                        <strong>{{ $recipient->recipient_name }} (+84
                                            {{ $recipient->recipient_phone }})</strong>
                                        <div class="text-muted mt-1">
                                            {{ $recipient->recipient_address }}
                                        </div>
                                        @if ($recipient->is_default)
                                            <span class="badge bg-danger mt-1">Mặc Định</span>
                                        @endif
                                    @else
                                        <strong class="text-danger">Chưa chọn địa chỉ nhận hàng</strong>
                                        @if ($errors->has('recipient_id'))
                                            <div class="text-danger mt-1">{{ $errors->first('recipient_id') }}</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Sản phẩm trong giỏ --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="badge bg-success me-2">Yêu thích+</span>
                                    </div>
                                </div>

                                <div class="row text-muted small border-bottom pb-2 mb-3">
                                    <div class="col-5">Sản phẩm</div>
                                    <div class="col-2 text-center">Đơn giá</div>
                                    <div class="col-2 text-center">Số lượng</div>
                                    <div class="col-3 text-end">Thành tiền</div>
                                </div>

                                @php $total = 0; @endphp
                                @forelse ($cartItems as $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->productVariant;

                                        $total += $item->item_total;

                                        $image = $product->image
                                            ? asset('storage/' . $product->image)
                                            : asset('images/default.jpg');
                                        $productName = $product->product_name ?? 'Sản phẩm không tên';
                                        $categoryName = $product->category->category_name ?? 'Không rõ';

                                        // Ghép thông tin thuộc tính: Vị, Size, v.v.
                                        $variantDetails = '';
                                        if ($variant && $variant->attributeValues) {
                                            $variantDetails = $variant->attributeValues
                                                ->map(fn($val) => $val->attribute->name . ': ' . $val->value)
                                                ->implode(', ');
                                        }
                                    @endphp

                                    <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
                                    <div class="row align-items-center py-3 border-bottom">
                                        <div class="col-5">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $image }}" class="me-3 rounded"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold">{{ $productName }}</div>
                                                    @if ($variantDetails)
                                                        <div class="text-muted small">{{ $variantDetails }}</div>
                                                    @endif
                                                    <div class="text-muted small">Loại: {{ $categoryName }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">
                                            ₫{{ number_format($item->calculated_price, 0, ',', '.') }}</div>
                                        <div class="col-2 text-center">{{ $item->quantity }}</div>
                                        <div class="col-3 text-end text-danger fw-bold">
                                            ₫{{ number_format($item->item_total, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">Không có sản phẩm nào</div>
                                @endforelse


                                {{-- Voucher --}}
                                <div class="row py-3 border-top align-items-center">
                                    <div class="col-6 d-flex align-items-center">
                                        <i class="fas fa-ticket-alt text-warning me-2"></i> Voucher của Shop
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#voucherModal">
                                            Nhập mã voucher
                                        </button>
                                    </div>
                                </div>

                                <div class="row py-3 border-top">
                                    <div class="col-2">
                                        <label class="form-label mb-0">Lời nhắn:</label>
                                    </div>
                                    <div class="col-10">
                                        <input type="text" name="note" class="form-control"
                                            placeholder="Lưu ý cho người bán..."
                                            value="{{ old('note', $recipient['note'] ?? '') }}">
                                    </div>
                                </div>

                                {{-- Phương thức vận chuyển --}}
                                <div class="row py-3 border-top align-items-center">
                                    <div class="col-3"><strong>Vận chuyển:</strong></div>
                                    <div class="col-6">
                                        <div class="fw-bold">Nhanh</div>
                                        <div class="text-muted small">Nhận voucher ₫15.000 nếu giao trễ</div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <span class="fw-bold">₫30.000</span><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: Thanh toán --}}
                    <div class="col-md-4">
                        <div class="card shadow-sm sticky-top">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Thanh toán</h5>

                                @php
                                    $shippingFee = 30000; // hoặc giữ lại logic tính động nếu cần
                                    $discount = session('discount', 0);
                                    $subtotal = $total ?? 0; // hoặc bạn lấy lại từ $cart nếu có
                                    $grandTotal = max(0, $subtotal + $shippingFee - $discount);
                                    $promotion = session('promotion');
                                @endphp

                                {{-- Phương thức thanh toán --}}
                                <div class="mb-3">
                                    <label class="form-label">Phương thức thanh toán</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="">-- Chọn phương thức --</option>
                                        <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>COD
                                        </option>
                                        <option value="vnpay" {{ old('payment_method') == 'vnpay' ? 'selected' : '' }}>
                                            VNPAY</option>
                                    </select>
                                </div>

                                <hr>

                                {{-- Chi tiết giá tiền --}}
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span>₫{{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span>₫{{ number_format($shippingFee, 0, ',', '.') }}</span>
                                </div>

                                @if (session('discount') > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>
                                            Mã giảm giá
                                            @if (session('promotion.code'))
                                                ({{ session('promotion.code') }})
                                            @endif
                                            :
                                        </span>
                                        <span>-₫{{ number_format(session('discount'), 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <hr>

                                <div class="d-flex justify-content-between mb-4">
                                    <h5>Tổng cộng:</h5>
                                    <span class="text-danger fw-bold fs-5 mb-0">
                                        ₫{{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>

                                {{-- Hidden inputs để gửi đi --}}
                                <input type="hidden" name="shipping_fee" value="{{ $shippingFee }}">
                                <input type="hidden" name="grand_total" value="{{ $grandTotal }}">
                                <input type="hidden" name="discount_amount" value="{{ $discount }}">
                                <input type="hidden" name="promotion" value="{{ session('promotion.code') }}">

                                <input type="hidden" name="recipient_id" id="recipient_id"
                                    value="{{ old('recipient_id', $recipient['id'] ?? '') }}">

                                <button type="submit" class="btn btn-danger w-100 mb-3">Đặt Hàng</button>
                                <a href="{{ route('carts.index') }}" class="text-decoration-none">Quay lại</a>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Bấm "Đặt hàng" là bạn đồng ý với
                                        <a href="#" class="text-decoration-none">Điều khoản MomoShop</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

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
                    <form action="{{ route('order.applyCoupon') }}" method="POST" class="d-flex mb-4">
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
                                <form method="POST" action="{{ route('order.applyCoupon') }}">
                                    @csrf
                                    <input type="hidden" name="promotion" value="{{ $voucher->code }}">
                                    <button class="btn btn-outline-danger">Dùng</button>
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

    <!-- Modal chọn địa chỉ giao hàng -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Tiêu đề Modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Địa Chỉ Của Tôi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <!-- Nội dung Modal -->
                <div class="modal-body">
                    <!-- Form chọn địa chỉ giao hàng -->
                    <form action="{{ route('clients.order') }}" method="GET">
                        @csrf
                        @foreach ($savedRecipients as $recipientItem)
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="recipient_id"
                                    id="recipient{{ $recipientItem->id }}" value="{{ $recipientItem->id }}">
                                <label class="form-check-label" for="recipient{{ $recipientItem->id }}">
                                    <strong>{{ $recipientItem->recipient_name }} (+84
                                        {{ $recipientItem->recipient_phone }})</strong><br>
                                    <span class="text-muted">{{ $recipientItem->recipient_address }}</span>
                                    @if ($recipientItem->is_default)
                                        <span class="badge bg-danger ms-2">Mặc định</span>
                                    @endif
                                </label>

                                <!-- Nút mở modal -->
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2 ms-2"
                                    data-bs-toggle="modal" data-bs-target="#editRecipientModal{{ $recipientItem->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-danger w-100 mt-2">Chọn địa chỉ này</button>
                    </form>

                    <!-- Modal sửa địa chỉ -->
                    @foreach ($savedRecipients as $recipientItem)
                        <div class="modal fade" id="editRecipientModal{{ $recipientItem->id }}" tabindex="-1"
                            aria-labelledby="editRecipientLabel{{ $recipientItem->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('clients.recipients.update', $recipientItem->id) }}"
                                    method="POST" class="form-edit-recipient" data-id="{{ $recipientItem->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRecipientLabel{{ $recipientItem->id }}">
                                                Sửa địa chỉ
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Tên người nhận</label>
                                                <input type="text" class="form-control" name="recipient_name"
                                                    value="{{ $recipientItem->recipient_name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Số điện thoại</label>
                                                <input type="text" class="form-control" name="recipient_phone"
                                                    value="{{ $recipientItem->recipient_phone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Địa chỉ</label>
                                                <textarea class="form-control" name="recipient_address" rows="2" required>{{ $recipientItem->recipient_address }}</textarea>
                                            </div>

                                            <div class="form-check">
                                                <input type="hidden" name="is_default" value="0">
                                                <input class="form-check-input" type="checkbox" name="is_default"
                                                    id="default{{ $recipientItem->id }}" value="1"
                                                    {{ $recipientItem->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label" for="default{{ $recipientItem->id }}">
                                                    Đặt làm mặc định
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    <hr>

                    <!-- Thêm địa chỉ mới -->
                    <button type="button" class="btn btn-outline-success w-100" onclick="toggleAddressForm()">+ Thêm Địa
                        Chỉ Mới</button>

                    <!-- Form thêm địa chỉ mới -->
                    <form id="add-address-form" action="{{ route('recipients.store') }}" method="POST">
                        @csrf
                        <div id="address-form" class="mt-3" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="recipient_name" class="form-control"
                                    value="{{ old('recipient_name') }}" required>
                                @error('recipient_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="recipient_phone" class="form-control"
                                    value="{{ old('recipient_phone') }}" required>
                                @error('recipient_phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="province">Tỉnh / Thành phố</label>
                                <select id="province" name="province_code" class="form-control">
                                    <option value="">-- Chọn tỉnh/thành --</option>
                                    @foreach ($locations as $province)
                                        <option value="{{ $province['code'] }}">{{ $province['name_with_type'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="district">Quận / Huyện</label>
                                <select id="district" name="district_code" class="form-control">
                                    <option value="">-- Chọn quận/huyện --</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="ward">Phường / Xã</label>
                                <select id="ward" name="ward_code" class="form-control">
                                    <option value="">-- Chọn phường/xã --</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="detail_address">Địa chỉ chi tiết</label>
                                <input type="text" id="detail_address" name="recipient_address" class="form-control"
                                    placeholder="Số nhà, tên đường...">
                            </div>

                            <!-- Hidden input để gộp lại thành recipient_address -->
                            <input type="hidden" name="recipient_address" id="recipient_address">

                            <div class="form-check">
                                <input type="hidden" name="is_default" value="0">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default"
                                    value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">Đặt làm địa chỉ mặc định</label>
                            </div>
                            <button type="submit" name="save_recipient" value="1"
                                class="btn btn-success mt-3 w-100">Lưu địa chỉ</button>
                        </div>
                    </form>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                </div>

            </div>
        </div>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gán giá trị recipient_id từ radio
        document.querySelectorAll('input[name="recipient_id"]').forEach(item => {
            item.addEventListener('change', () => {
                document.getElementById('recipient_id').value = item.value;
            });
        });

        // Mở modal thêm địa chỉ dùng Bootstrap API
        document.getElementById('open-address-modal')?.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('addressModal'));
            modal.show();
        });

        window.toggleAddressForm = function() {
            const addressForm = document.getElementById('address-form');
            if (addressForm.style.display === 'none' || addressForm.style.display === '') {
                addressForm.style.display = 'block';
            } else {
                addressForm.style.display = 'none';
            }
        };

        // Validate đơn giản các input
        const nameInput = document.querySelector('[name="recipient_name"]');
        const phoneInput = document.querySelector('[name="recipient_phone"]');
        const addressInput = document.querySelector('[name="recipient_address"]');

        function showError(input, message) {
            let errorEl = input.parentElement.querySelector('.error-message');
            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.classList.add('text-danger', 'small', 'error-message');
                input.parentElement.appendChild(errorEl);
            }
            errorEl.textContent = message;
        }

        function clearError(input) {
            const errorEl = input.parentElement.querySelector('.error-message');
            if (errorEl) errorEl.remove();
        }

        nameInput?.addEventListener('blur', () => {
            if (nameInput.value.trim() === '') {
                showError(nameInput, 'Vui lòng nhập họ và tên.');
            } else {
                clearError(nameInput);
            }
        });

        phoneInput?.addEventListener('blur', () => {
            const phoneRegex = /^0[0-9]{9}$/;
            if (phoneInput.value.trim() === '') {
                showError(phoneInput, 'Vui lòng nhập số điện thoại.');
            } else if (!phoneRegex.test(phoneInput.value.trim())) {
                showError(phoneInput, 'Số điện thoại không đúng định dạng.');
            } else {
                clearError(phoneInput);
            }
        });

        addressInput?.addEventListener('blur', () => {
            if (addressInput.value.trim() === '') {
                showError(addressInput, 'Vui lòng nhập địa chỉ chi tiết.');
            } else {
                clearError(addressInput);
            }
        });

        // Xử lý địa chỉ
        const locations = @json($locations);

        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        const detailInput = document.getElementById('detail_address');
        const fullAddressInput = document.getElementById('recipient_address');

        let currentProvince = '',
            currentDistrict = '',
            currentWard = '';

        if (!provinceSelect || !districtSelect || !wardSelect || !detailInput || !fullAddressInput) {
            console.warn('❗ Không tìm thấy một số phần tử trong DOM.');
            return;
        }

        provinceSelect.addEventListener('change', function() {
            const provinceCode = this.value;
            currentProvince = this.options[this.selectedIndex].text;
            districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
            wardSelect.disabled = true;

            const province = locations.find(p => p.code == provinceCode);
            if (province?.districts) {
                province.districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.code;
                    opt.textContent = d.name_with_type;
                    districtSelect.appendChild(opt);
                });
                districtSelect.disabled = false;
            }
            updateFullAddress();
        });

        districtSelect.addEventListener('change', function() {
            const districtCode = this.value;
            currentDistrict = this.options[this.selectedIndex].text;
            wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';

            const provinceCode = provinceSelect.value;
            const province = locations.find(p => p.code == provinceCode);
            const district = province?.districts?.find(d => d.code == districtCode);

            if (district?.wards) {
                district.wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.code;
                    opt.textContent = w.name_with_type;
                    wardSelect.appendChild(opt);
                });
                wardSelect.disabled = false;
            }
            updateFullAddress();
        });

        wardSelect.addEventListener('change', function() {
            currentWard = this.options[this.selectedIndex].text;
            updateFullAddress();
        });

        detailInput.addEventListener('input', updateFullAddress);

        function updateFullAddress() {
            const detail = detailInput.value.trim();
            const addressParts = [detail, currentWard, currentDistrict, currentProvince].filter(Boolean);
            fullAddressInput.value = addressParts.join(', ');
        }

        // Tự động trigger lại nếu đã có dữ liệu (ví dụ sau validation)
        if (provinceSelect.value) {
            provinceSelect.dispatchEvent(new Event('change'));
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alertBox => {
            setTimeout(() => {
                const alert = new bootstrap.Alert(alertBox);
                alert.close();
            }, 5000);
        });
    });
</script>

<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
{{-- <script src="{{ mix('js/app.js') }}"></script> --}}
<script>
    window.Echo.channel('promotions')
        .listen('.promotion.updated', (e) => {
            console.log("🔄 Promotion updated:", e.promotion);

            // Xử lý format text giảm giá
            let discountText = e.promotion.discount_type === 'percent' ?
                `Giảm ${e.promotion.discount_value}%` :
                `Giảm ${Number(e.promotion.discount_value).toLocaleString()}đ`;

            // Cập nhật DOM hiển thị
            document.getElementById('promotion-box').innerHTML = `
            <strong>${e.promotion.promotion_name}</strong> <br>
            ${discountText}<br>
            <small>Đơn tối thiểu: ${Number(e.promotion.min_total_spent).toLocaleString()}đ</small><br>
            <small>HSD: ${new Date(e.promotion.end_date).toLocaleString('vi-VN')}</small>
        `;

            // Thêm thông báo Toast
            Toastify({
                text: "Mã giảm giá đã được cập nhật!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#4caf50"
            }).showToast();
        });
</script>

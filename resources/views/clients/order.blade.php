@extends('clients.layouts.app')

@section('content')
    <div class="container-fluid featurs py-5">
        <div class="container pt-5 mt-5">
            {{-- THÔNG BÁO --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM THANH TOÁN --}}
            <form action="{{ route('clients.create-payment') }}" method="POST">
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
                                    <a href="#" class="btn btn-outline-primary btn-sm">Thay Đổi</a>
                                </div>
                                <div>
                                    <strong>{{ old('recipient_name', $recipient['recipient_name'] ?? 'Đào Văn Tú') }} (+84)
                                        {{ old('recipient_phone', $recipient['recipient_phone'] ?? '922701084') }}</strong>
                                    <div class="text-muted mt-1">
                                        {{ old('recipient_address', $recipient['recipient_address'] ?? '25 ngách 7 Xóm Phúc Sung, Xã Cao Viên, Huyện Thanh Oai, Hà Nội') }}
                                    </div>
                                    <span class="badge bg-danger">Mặc Định</span>
                                </div>

                                {{-- Hidden inputs --}}
                                <input type="hidden" name="recipient_name"
                                    value="{{ old('recipient_name', $recipient['recipient_name'] ?? 'Đào Văn Tú') }}">
                                <input type="hidden" name="recipient_phone"
                                    value="{{ old('recipient_phone', $recipient['recipient_phone'] ?? '922701084') }}">
                                <input type="hidden" name="recipient_address"
                                    value="{{ old('recipient_address', $recipient['recipient_address'] ?? '25 ngách 7 Xóm Phúc Sung, Xã Cao Viên, Huyện Thanh Oai, Hà Nội') }}">
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
                                        $price = $item->discounted_price ?? ($item->original_price ?? 59000);
                                        $itemTotal = $price * $item->quantity;
                                        $total += $itemTotal;
                                    @endphp
                                    <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
                                    <div class="row align-items-center py-3 border-bottom">
                                        <div class="col-5">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('images/default.jpg') }}"
                                                    class="me-3 rounded"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold">
                                                        {{ $item->product->product_name ?? 'Sản phẩm không tên' }}</div>
                                                    <div class="text-muted small">
                                                        Loại: {{ $item->product->category->category_name ?? 'Không rõ' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2 text-center">₫{{ number_format($price, 0, ',', '.') }}</div>
                                        <div class="col-2 text-center">{{ $item->quantity }}</div>
                                        <div class="col-3 text-end text-danger fw-bold">
                                            ₫{{ number_format($itemTotal, 0, ',', '.') }}</div>
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

                                @if (session('promotion_name'))
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Mã giảm giá ({{ session('promotion_name') }}):</span>
                                        <span>-₫{{ number_format($discount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <hr>

                                <div class="d-flex justify-content-between mb-4">
                                    <h5>Tổng cộng:</h5>
                                    <h5 class="text-danger">₫{{ number_format($grandTotal, 0, ',', '.') }}</h5>
                                </div>

                                {{-- Hidden inputs để gửi đi --}}
                                <input type="hidden" name="shipping_fee" value="{{ $shippingFee }}">
                                <input type="hidden" name="grand_total" value="{{ $grandTotal }}">
                                <input type="hidden" name="discount_amount" value="{{ $discount }}">

                                <button type="submit" class="btn btn-danger w-100 mb-3">Đặt Hàng</button>
                                <a href="{{ route('order.removeCoupon') }}" class="text-decoration-none">Quay lại</a>

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
                                    <input type="hidden" name="promotion" value="{{ $voucher->promotion_name }}">
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

@endsection

@extends('clients.layouts.app')

@section('content')
    <div class="container py-5 mt-5">
        <h2 class="mb-4 text-center">Thanh toán</h2>

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

        {{-- FORM --}}
        <form action="{{ route('order.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- THÔNG TIN NGƯỜI NHẬN --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Thông tin người nhận</h5>

                            <div class="mb-3">
                                <label class="form-label">Họ tên</label>
                                <input type="text" name="recipient_name" class="form-control" required
                                    value="{{ old('recipient_name', $recipient['recipient_name'] ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="recipient_phone" class="form-control" required
                                    value="{{ old('recipient_phone', $recipient['recipient_phone'] ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="recipient_address" class="form-control" rows="2" required>{{ old('recipient_address', $recipient['recipient_address'] ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="2">{{ old('note', $recipient['note'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GIỎ HÀNG --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Giỏ hàng</h5>

                            @php
                                $cartItems = session('cart', []);
                                $total = 0;
                                foreach ($cartItems as $item) {
                                    $total += $item['price'] * $item['quantity'];
                                }

                                $shipping = old('shipping_fee', 30000);
                                $discount = 0;
                                $promotionName = 'Không có mã giảm giá';

                                if (session()->has('promotion')) {
                                    $promotion = session('promotion');
                                    $promotionName = $promotion['name'] ?? 'Không có mã giảm giá';

                                    if ($promotion['type'] === 'fixed') {
                                        $discount = $promotion['value'];
                                    } elseif ($promotion['type'] === 'percent') {
                                        $discount = $total * ($promotion['value'] / 100);
                                        if (!empty($promotion['max']) && $discount > $promotion['max']) {
                                            $discount = $promotion['max'];
                                        }
                                    }
                                }

                                $grandTotal = $total + $shipping - $discount;
                                if ($grandTotal < 0) {
                                    $grandTotal = 0;
                                }
                            @endphp

                            {{-- DANH SÁCH SẢN PHẨM --}}
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Mã</th>
                                        <th>SL</th>
                                        <th>Giá</th>
                                        <th>Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cartItems as $item)
                                        <tr>
                                            <td>{{ $item['product_name'] ?? '---' }}</td>
                                            <td>{{ $item['product_code'] ?? '---' }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>{{ number_format($item['price']) }}đ</td>
                                            <td>{{ number_format($item['price'] * $item['quantity']) }}đ</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Giỏ hàng đang trống</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- TỔNG KẾT ĐƠN HÀNG --}}
                            <div class="mb-2">Tạm tính: <strong>{{ number_format($total) }}đ</strong></div>
                            <div class="mb-2">Phí vận chuyển: <strong>{{ number_format($shipping) }}đ</strong></div>

                            @if ($discount > 0)
                                <div class="mb-2 text-success">
                                    Giảm giá (<strong>{{ $promotionName }}</strong>):
                                    <strong>-{{ number_format($discount) }}đ</strong>
                                </div>
                            @else
                                <div class="mb-2 text-muted">
                                    Không có mã giảm giá
                                </div>
                            @endif

                            {{-- Nếu có mã thì gửi sang controller --}}
                            @if ($promotionName && $promotionName !== 'Không có mã giảm giá')
                                <input type="hidden" name="promotion" value="{{ $promotionName }}">
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Phí vận chuyển</label>
                                <input type="number" name="shipping_fee" class="form-control" value="{{ $shipping }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">-- Chọn phương thức --</option>
                                    <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Thanh
                                        toán khi nhận hàng (COD)</option>
                                    <option value="vnpay" {{ old('payment_method') == 'vnpay' ? 'selected' : '' }}>Thanh
                                        toán qua VNPAY</option>
                                </select>
                            </div>

                            <hr>
                            <h5 class="text-end">Tổng cộng: <strong>{{ number_format($grandTotal) }}đ</strong></h5>

                            <button type="submit" class="btn btn-success w-100 mt-3">Xác nhận đặt hàng</button>
                            <a href="{{ route('carts.index') }}" class="d-block mt-2 text-center">Quay lại giỏ hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

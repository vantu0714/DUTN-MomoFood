@extends('clients.layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <h2 class="mb-4 text-center">Thanh toán</h2>

    {{-- Thông báo --}}
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

    {{-- Form --}}
    <form action="{{ route('order.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Thông tin người nhận --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Thông tin người nhận</h5>

                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="recipient_name" class="form-control" required value="{{ old('recipient_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="recipient_phone" class="form-control" required value="{{ old('recipient_phone') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="recipient_address" class="form-control" rows="2" required>{{ old('recipient_address') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Giỏ hàng --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Giỏ hàng</h5>

                        @php $cartItems = session('cart', []); $total = 0; @endphp

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
                                    @php
                                        $itemTotal = $item['price'] * $item['quantity'];
                                        $total += $itemTotal;
                                    @endphp
                                    <tr>
                                        <td>{{ $item['product_name'] ?? '---' }}</td>
                                        <td>{{ $item['product_code'] ?? '---' }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($item['price']) }}đ</td>
                                        <td>{{ number_format($itemTotal) }}đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Giỏ hàng đang trống</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Tổng tiền hàng --}}
                        <div class="mb-3">
                            <label class="form-label">Tổng tiền hàng</label>
                            <input type="text" class="form-control" value="{{ number_format($total) }}đ" disabled>
                        </div>

                        {{-- Mã giảm giá --}}
                        <div class="mb-3">
                            <label class="form-label">Mã giảm giá</label>
                            <input type="text" name="promotion" class="form-control" placeholder="Nhập mã nếu có" value="{{ old('promotion') }}">
                        </div>

                        {{-- Phí ship --}}
                        <div class="mb-3">
                            <label class="form-label">Phí vận chuyển</label>
                            <input type="number" name="shipping_fee" class="form-control" value="{{ old('shipping_fee', 30000) }}" required>
                        </div>

                        {{-- Thanh toán --}}
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">-- Chọn phương thức --</option>
                                <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                                <option value="vnpay" {{ old('payment_method') == 'vnpay' ? 'selected' : '' }}>Thanh toán qua VNPAY</option>
                            </select>
                        </div>

                        {{-- Tổng cộng --}}
                        @php
                            $shipping = old('shipping_fee', 30000);
                            $grandTotal = $total + $shipping;
                        @endphp
                        <hr>
                        <h5 class="text-end">Tổng cộng: <strong>{{ number_format($grandTotal) }}đ</strong></h5>

                        <button type="submit" class="btn btn-success w-100 mt-3">Xác nhận đặt hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

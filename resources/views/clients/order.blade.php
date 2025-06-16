@extends('clients.layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <h2 class="mb-4 text-center">Thanh toán</h2>

    <form action="#" method="POST">
        @csrf

        <div class="row g-4"> 
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Thông tin người nhận</h5>

                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="recipient_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="recipient_phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="recipient_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GIỎ HÀNG & THANH TOÁN --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Giỏ hàng</h5>

                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Biến thể</th>
                                    <th>SL</th>
                                    <th>Giá</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @forelse ($cartItems ?? [] as $item)
                                    <tr>
                                        <td>{{ $item->product->product_name ?? '' }}</td>
                                        <td>{{ $item->productVariant->name ?? 'Không có' }}</td>
                                        <td>{{ $item->quantity ?? 0 }}</td>
                                        <td>{{ number_format($item->price ?? 0) }}đ</td>
                                        <td>{{ number_format(($item->price ?? 0) * ($item->quantity ?? 0)) }}đ</td>
                                    </tr>
                                    @php $total += ($item->price ?? 0) * ($item->quantity ?? 0); @endphp
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Giỏ hàng đang trống</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Tổng tiền --}}
                        <div class="mb-3">
                            <label class="form-label">Tổng tiền hàng</label>
                            <input type="text" class="form-control" value="{{ number_format($total) }}đ" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mã giảm giá</label>
                            <input type="text" name="promotion" class="form-control" placeholder="Nhập mã nếu có">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phí vận chuyển</label>
                            <input type="number" name="shipping_fee" class="form-control" value="30000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="">-- Chọn phương thức --</option>
                                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                <option value="vnpay">Thanh toán qua VnPay</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-3">Xác nhận đặt hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

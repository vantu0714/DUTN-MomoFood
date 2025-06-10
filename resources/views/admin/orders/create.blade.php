@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Thêm đơn hàng</h2>

        <form action="" method="POST">
            @csrf

            <div class="mb-3">
                <label for="recipient_name" class="form-label">Người nhận</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pending">Chờ xử lý</option>
                    <option value="processing">Đang xử lý</option>
                    <option value="completed">Hoàn tất</option>@extends('admin.layouts.app')

                    @section('content')
                        <h2>Thêm đơn hàng</h2>
                    
                        <form action="#" method="POST">
                            @csrf
                    
                            <div>
                                <label for="user_id">ID người dùng:</label>
                                <input type="number" name="user_id" id="user_id" required>
                            </div>
                    
                            <div>
                                <label for="recipient_name">Tên người nhận:</label>
                                <input type="text" name="recipient_name" id="recipient_name" required>
                            </div>
                    
                            <div>
                                <label for="recipient_phone">Số điện thoại người nhận:</label>
                                <input type="text" name="recipient_phone" id="recipient_phone" required>
                            </div>
                    
                            <div>
                                <label for="recipient_address">Địa chỉ người nhận:</label>
                                <textarea name="recipient_address" id="recipient_address" required></textarea>
                            </div>
                    
                            <div>
                                <label for="promotion">Mã khuyến mãi (nếu có):</label>
                                <input type="text" name="promotion" id="promotion">
                            </div>
                    
                            <div>
                                <label for="shipping_fee">Phí vận chuyển:</label>
                                <input type="number" step="0.01" name="shipping_fee" id="shipping_fee" required>
                            </div>
                    
                            <div>
                                <label for="total_price">Tổng tiền:</label>
                                <input type="number" step="0.01" name="total_price" id="total_price" required>
                            </div>
                    
                            <div>
                                <label for="payment_method">Phương thức thanh toán:</label>
                                <input type="text" name="payment_method" id="payment_method" required>
                            </div>
                    
                            <div>
                                <label for="payment_status">Trạng thái thanh toán:</label>
                                <select name="payment_status" id="payment_status" required>
                                    <option value="paid">Đã thanh toán</option>
                                    <option value="unpaid">Chưa thanh toán</option>
                                </select>
                            </div>
                    
                            <div>
                                <label for="status">Trạng thái đơn hàng:</label>
                                <select name="status" id="status" required>
                                    <option value="pending">Chờ xử lý</option>
                                    <option value="processing">Đang xử lý</option>
                                    <option value="completed">Hoàn tất</option>
                                    <option value="cancelled">Đã hủy</option>
                                </select>
                            </div>
                    
                            <div>
                                <label for="note">Ghi chú:</label>
                                <input type="text" name="note" id="note">
                            </div>
                    
                            <div>
                                <label for="cancellation_reason">Lý do hủy (nếu có):</label>
                                <input type="text" name="cancellation_reason" id="cancellation_reason">
                            </div>
                    
                            <div>
                                <button type="submit">Tạo đơn hàng</button>
                            </div>
                        </form>
                    @endsection
                    
                    <option value="cancelled">Đã hủy</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="payment_status" class="form-label">Thanh toán</label>
                <select class="form-select" id="payment_status" name="payment_status" required>
                    <option value="unpaid">Chưa thanh toán</option>
                    <option value="paid">Đã thanh toán</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Tạo đơn hàng</button>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection

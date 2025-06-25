@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa đơn hàng')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Chỉnh sửa đơn hàng</h4>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Thông tin khách hàng --}}
                    <div class="mb-4 p-3 border rounded">
                        <h5 class="mb-3 text-primary">Thông tin khách hàng</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Khách hàng</label>
                                    <p class="form-control-plaintext">
                                        @if ($order->user_id)
                                            {{ $order->user->name ?? 'Không có tên' }}
                                            ({{ $order->user->email ?? 'Không có email' }})
                                        @else
                                            Khách vãng lai
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số điện thoại</label>
                                    <p class="form-control-plaintext">
                                        @if ($order->user_id && $order->user->phone)
                                            {{ $order->user->phone }}
                                        @else
                                            Không có thông tin
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin người nhận --}}
                    <div class="mb-4 p-3 border rounded">
                        <h5 class="mb-3 text-primary">Thông tin người nhận</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên người nhận</label>
                                    <p class="form-control-plaintext">qqqq aa</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">SĐT người nhận</label>
                                    <p class="form-control-plaintext">0922701084</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Địa chỉ</label>
                                    <p class="form-control-plaintext">hhaa</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin thanh toán --}}
                    <div class="mb-4 p-3 border rounded">
                        <h5 class="mb-3 text-primary">Thông tin thanh toán</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Phương thức thanh toán</label>
                                    <p class="form-control-plaintext">Thanh toán khi nhận hàng (COD)</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái thanh toán</label>
                                    <select name="payment_status" class="form-select">
                                        <option value="pending" selected>Chưa thanh toán</option>
                                        <option value="paid">Đã thanh toán</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mã giảm giá</label>
                                    <p class="form-control-plaintext">Không có</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trạng thái đơn hàng --}}
                    <div class="mb-4 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái đơn hàng</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>Chưa xác nhận
                                        </option>
                                        <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>Đã xác nhận
                                        </option>
                                        <option value="3" {{ $order->status == 3 ? 'selected' : '' }}>Đang giao
                                        </option>
                                        <option value="4" {{ $order->status == 4 ? 'selected' : '' }}> Giao thành công
                                        </option>
                                        <option value="5" {{ $order->status == 5 ? 'selected' : '' }}>Hoàn hàng
                                        </option>
                                        <option value="6" {{ $order->status == 6 ? 'selected' : '' }}>Hủy đơn
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chú</label>
                                    <textarea name="note" class="form-control" rows="2">{{ old('note', $order->note) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lý do hủy --}}
                    <div class="mb-4 p-3 border rounded" id="cancel-reason-container" style="display: {{ $order->status == 6 ? 'block' : 'none' }}">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lý do hủy đơn (nếu có)</label>
                            <input type="text" name="cancellation_reason" class="form-control"
                                value="{{ old('cancellation_reason', $order->cancellation_reason) }}"
                                {{ $order->status == 6 ? '' : 'readonly' }}>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Cập nhật đơn hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-control-plaintext {
            padding: 0.375rem 0;
            margin-bottom: 0;
            background-color: transparent;
            border: solid transparent;
            border-width: 1px 0;
        }

        .border-rounded {
            border-radius: 0.5rem;
        }
    </style>

    <script>
        // Hiển thị trường lý do hủy khi chọn trạng thái "Đã hủy"
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            const reasonField = document.querySelector('input[name="cancellation_reason"]');
            if (this.value === 'cancelled') {
                reasonField.setAttribute('required', 'required');
            } else {
                reasonField.removeAttribute('required');
            }
        });
    </script>
@endsection

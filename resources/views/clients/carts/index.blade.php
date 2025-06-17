@include('clients.layouts.header')
@include('clients.layouts.sidebar')

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">

<<<<<<< feature/thanhtoan-vnpay
        <!-- Single Page Header start -->
=======
        <!-- Header -->
>>>>>>> main
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Giỏ hàng</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#">Trang</a></li>
                <li class="breadcrumb-item active text-white">Giỏ hàng</li>
            </ol>
        </div>

        <!-- Cart Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">

                <!-- Thông báo -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Bảng giỏ hàng -->
                <div class="table-responsive">
                    <table class="table" id="cart-table">
                        <thead>
<<<<<<< feature/thanhtoan-vnpay
                            <tr>
                                <th scope="col">Các sản phẩm</th>
                                <th scope="col">Tên</th>
                                <th scope="col">Giá</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Tổng cộng</th>
                                <th scope="col">Xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('clients/img/vegetable-item-3.png') }}"
                                            class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;"
                                            alt="">
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4">Quả chuối lớn</p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">2.99 $</p>
                                </td>
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm text-center border-0"
                                            value="1">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">2.99 $</p>
                                </td>
                                <td>
                                    <button class="btn btn-md rounded-circle bg-light border mt-4">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </td>

                            </tr>
                            <tr>
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('clients/img/vegetable-item-5.jpg') }}"
                                            class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;"
                                            alt="" alt="">
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4">Khoai tây</p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">2.99 $</p>
                                </td>
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm text-center border-0"
                                            value="1">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">2.99 $</p>
                                </td>
                                <td>
                                    <button class="btn btn-md rounded-circle bg-light border mt-4">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('clients/img/vegetable-item-2.jpg') }}"
                                            class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;"
                                            alt="" alt="">
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4">Súp lơ tuyệt vời</p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">2.99 $</p>
                                </td>
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-sm text-center border-0"
                                            value="1">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">2.99 $</p>
                                </td>
                                <td>
                                    <button class="btn btn-md rounded-circle bg-light border mt-4">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </td>
=======
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Tên</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tạm tính</th>
                                <th>Xử lý</th>
>>>>>>> main
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @forelse($carts as $id => $item)
                                @php
                                    $subTotal = $item['price'] * $item['quantity'];
                                    $total += $subTotal;
                                @endphp
                                <tr class="cart-item" data-id="{{ $id }}">
                                    <td>
                                        <img src="{{ asset($item['image'] ?? 'clients/img/default.png') }}" class="img-fluid rounded-circle" style="width: 80px; height: 80px;" />
                                    </td>
                                    <td>{{ $item['product_name'] }}</td>
                                    <td class="price" data-price="{{ $item['price'] }}">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                    <td>
                                        <input type="number"
                                               class="form-control quantity-input"
                                               name="quantities[{{ $id }}]"
                                               value="{{ $item['quantity'] }}"
                                               min="1"
                                               style="width: 60px;">
                                    </td>
                                    <td class="sub-total">{{ number_format($subTotal, 0, ',', '.') }} đ</td>
                                    <td>
                                        <a href="{{ route('carts.remove', $id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Giỏ hàng trống</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mã giảm giá -->
                <div class="mt-5">
<<<<<<< feature/thanhtoan-vnpay
                    <input type="text" class="border-0 border-bottom rounded me-5 py-3 mb-4"
                        placeholder="Nhập phiếu giảm giá">
                    <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="button">Áp dụng
                        phiếu giảm giá</button>
=======
                    <form action="{{ route('carts.applyCoupon') }}" method="POST" class="d-flex">
                        @csrf
                        <input type="text" name="coupon_code" class="form-control border-0 border-bottom rounded me-3 py-3" placeholder="Nhập mã giảm giá">
                        <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="submit">Áp dụng mã</button>
                    </form>
>>>>>>> main
                </div>

                <!-- Tổng cộng -->
                @php $shipping = 30000; @endphp
                <div class="row g-4 justify-content-end mt-5">
                    <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                        <div class="bg-light rounded">
                            <div class="p-4">
                                <h4 class="mb-4">Tóm tắt đơn hàng</h4>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="total-price">{{ number_format($total, 0, ',', '.') }} đ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span id="shipping-fee">{{ number_format($shipping, 0, ',', '.') }} đ</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span id="grand-total">{{ number_format($total + $shipping, 0, ',', '.') }} đ</span>
                                </div>
                            </div>
<<<<<<< feature/thanhtoan-vnpay
                            <button
                                class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4"
                                type="button">Thanh toán</button>
=======
                          
                            <a href="{{route('clients.order')}}" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4">Thanh Toán</a>
>>>>>>> main
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Cart Page End -->

    </div>
</div>

@include('clients.layouts.footer')

<!-- JavaScript xử lý tính tổng động -->
<script>
    document.querySelectorAll('.quantity-input').forEach(function (input) {
        input.addEventListener('change', function () {
            let row = this.closest('.cart-item');
            let price = parseFloat(row.querySelector('.price').dataset.price);
            let quantity = parseInt(this.value);
            let subtotalCell = row.querySelector('.sub-total');
            let newSubtotal = price * quantity;
            subtotalCell.textContent = newSubtotal.toLocaleString('vi-VN') + ' đ';

<<<<<<< feature/thanhtoan-vnpay
@include('clients.layouts.footer')
=======
            // Cập nhật tổng tiền
            updateTotal();
        });
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.cart-item').forEach(function (row) {
            let price = parseFloat(row.querySelector('.price').dataset.price);
            let quantity = parseInt(row.querySelector('.quantity-input').value);
            total += price * quantity;
        });

        let shipping = {{ $shipping }};
        document.getElementById('total-price').textContent = total.toLocaleString('vi-VN') + ' đ';
        document.getElementById('grand-total').textContent = (total + shipping).toLocaleString('vi-VN') + ' đ';
    }
</script>
>>>>>>> main

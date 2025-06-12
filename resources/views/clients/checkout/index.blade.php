@include('clients.layouts.header')
@include('clients.layouts.sidebar')

<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0">

         <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tìm kiếm theo từ khóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->


        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Thanh toán</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#">Trang</a></li>
                <li class="breadcrumb-item active text-white">Thanh toán</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Checkout Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <h1 class="mb-4">Chi tiết thanh toán</h1>
                <form action="#">
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-7">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Tên<sup>*</sup></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Họ<sup>*</sup></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Tên công ty<sup>*</sup></label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Địa chỉ<sup>*</sup></label>
                                <input type="text" class="form-control" placeholder="House Number Street Name">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Thị trấn/Thành phố<sup>*</sup></label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Quốc gia<sup>*</sup></label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Mã bưu chính/Zip<sup>*</sup></label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Điện thoại di động<sup>*</sup></label>
                                <input type="tel" class="form-control">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Địa chỉ email<sup>*</sup></label>
                                <input type="email" class="form-control">
                            </div>
                            <div class="form-check my-3">
                                <input type="checkbox" class="form-check-input" id="Account-1" name="Accounts" value="Accounts">
                                <label class="form-check-label" for="Account-1">Tạo một tài khoản?</label>
                            </div>
                            <hr>
                            <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" id="Address-1" name="Address" value="Address">
                                <label class="form-check-label" for="Address-1">Gửi đến một địa chỉ khác?</label>
                            </div>
                            <div class="form-item">
                                <textarea name="text" class="form-control" spellcheck="false" cols="30" rows="11" placeholder="Oreder Notes (Optional)"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-5">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Các sản phẩm</th>
                                            <th scope="col">Tên</th>
                                            <th scope="col">Giá</th>
                                            <th scope="col">Số lượng</th>
                                            <th scope="col">Tổng cộng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <div class="d-flex align-items-center mt-2">
                                                    <img src="img/vegetable-item-2.jpg" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" alt="">
                                                </div>
                                            </th>
                                            <td class="py-5">Bông cải xanh tuyệt vời</td>
                                            <td class="py-5">$69.00</td>
                                            <td class="py-5">2</td>
                                            <td class="py-5">$138.00</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                <div class="d-flex align-items-center mt-2">
                                                    <img src="img/vegetable-item-5.jpg" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" alt="">
                                                </div>
                                            </th>
                                            <td class="py-5">khoai tây</td>
                                            <td class="py-5">$69.00</td>
                                            <td class="py-5">2</td>
                                            <td class="py-5">$138.00</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                <div class="d-flex align-items-center mt-2">
                                                    <img src="img/vegetable-item-3.png" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" alt="">
                                                </div>
                                            </th>
                                            <td class="py-5">Quả chuối lớn</td>
                                            <td class="py-5">$69.00</td>
                                            <td class="py-5">2</td>
                                            <td class="py-5">$138.00</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                            </th>
                                            <td class="py-5"></td>
                                            <td class="py-5"></td>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark py-3">Tổng phụ</p>
                                            </td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    <p class="mb-0 text-dark">$414.00</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                            </th>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark py-4">vận chuyển</p>
                                            </td>
                                            <td colspan="3" class="py-5">
                                                <div class="form-check text-start">
                                                    <input type="checkbox" class="form-check-input bg-primary border-0" id="Shipping-1" name="Shipping-1" value="Shipping">
                                                    <label class="form-check-label" for="Shipping-1">Miễn phí vận chuyển</label>
                                                </div>
                                                <div class="form-check text-start">
                                                    <input type="checkbox" class="form-check-input bg-primary border-0" id="Shipping-2" name="Shipping-1" value="Shipping">
                                                    <label class="form-check-label" for="Shipping-2">Tỷ giá cố định: $15.00</label>
                                                </div>
                                                <div class="form-check text-start">
                                                    <input type="checkbox" class="form-check-input bg-primary border-0" id="Shipping-3" name="Shipping-1" value="Shipping">
                                                    <label class="form-check-label" for="Shipping-3">Nhận hàng tại địa phương: $8.00</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                            </th>
                                            <td class="py-5">
                                                <p class="mb-0 text-dark text-uppercase py-3">TỔNG CỘNG</p>
                                            </td>
                                            <td class="py-5"></td>
                                            <td class="py-5"></td>
                                            <td class="py-5">
                                                <div class="py-3 border-bottom border-top">
                                                    <p class="mb-0 text-dark">$135.00</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="checkbox" class="form-check-input bg-primary border-0" id="Transfer-1" name="Transfer" value="Transfer">
                                        <label class="form-check-label" for="Transfer-1">Chuyển khoản ngân hàng trực tiếp</label>
                                    </div>
                                    <p class="text-start text-dark">Thanh toán trực tiếp vào tài khoản ngân hàng của chúng tôi. Vui lòng sử dụng Mã đơn hàng của bạn làm tham chiếu thanh toán. 
                                        Đơn hàng của bạn sẽ không được giao cho đến khi tiền được chuyển vào tài khoản của chúng tôi.</p>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="checkbox" class="form-check-input bg-primary border-0" id="Payments-1" name="Payments" value="Payments">
                                        <label class="form-check-label" for="Payments-1">Kiểm tra thanh toán</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="checkbox" class="form-check-input bg-primary border-0" id="Delivery-1" name="Delivery" value="Delivery">
                                        <label class="form-check-label" for="Delivery-1">Tiền mặt khi giao hàng</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="checkbox" class="form-check-input bg-primary border-0" id="Paypal-1" name="Paypal" value="Paypal">
                                        <label class="form-check-label" for="Paypal-1">Paypal</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                <button type="button" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Đặt hàng</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Checkout Page End -->


    </div>
</div>



@include('clients.layouts.footer')
 
       

        
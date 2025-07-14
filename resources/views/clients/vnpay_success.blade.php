@extends('clients.layouts.app')

@section('content')
    <br><br><br><br><br><br>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="loadingBox" class="bg-white p-5 shadow rounded text-center">
                    <div class="spinner-border text-success" role="status" style="width: 4rem; height: 4rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 fs-5">Đang xử lý thanh toán...</p>
                </div>

                <div id="successBox"
                    class="bg-white p-5 shadow rounded text-center d-none animate__animated animate__fadeIn">
                    <h2 class="text-success mb-4 display-4">✅ Thanh toán thành công!</h2>
                    <p class="fs-5">Cảm ơn bạn đã đặt hàng tại <strong>MomoFood</strong>.</p>
                    <p class="text-muted">Chúng tôi sẽ sớm xử lý đơn hàng và giao đến bạn trong thời gian sớm nhất.</p>
                    <a href="{{ route('home') }}" class="btn btn-success">
                        🏠 Về trang chủ
                    </a>
                    <a href="{{ route('carts.index') }}" class="btn btn-outline-secondary">
                        🛒 Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                document.getElementById("loadingBox").classList.add('d-none');
                document.getElementById("successBox").classList.remove('d-none');
            }, 2000); // Thời gian xoay 2 giây
        });
    </script>
@endsection

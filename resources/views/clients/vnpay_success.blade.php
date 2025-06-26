@extends('clients.layouts.app') 

@section('content')
<br><br><br><br><br><br>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="bg-white p-5 shadow rounded text-center">
                <h2 class="text-success mb-4">✅ Thanh toán thành công!</h2>
                <p class="fs-5">Cảm ơn bạn đã đặt hàng tại <strong>MomoFood</strong>.</p>
                <p class="text-muted">Chúng tôi sẽ sớm xử lý đơn hàng và giao đến bạn trong thời gian sớm nhất.</p>
                <a href="{{ route('home') }}" class="btn btn-success mt-4">Quay lại trang chủ</a>
            </div>
        </div>
    </div>
</div>
@endsection
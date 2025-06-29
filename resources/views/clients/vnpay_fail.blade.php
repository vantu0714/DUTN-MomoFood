@extends('clients.layouts.app') 

@section('content')
<br><br><br><br><br><br>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="bg-white p-5 shadow rounded text-center">
                <h2 class="text-danger mb-4">❌ Thanh toán thất bại</h2>
                <p class="fs-5">Rất tiếc, giao dịch của bạn không thành công.</p>
                <p class="text-muted">Vui lòng kiểm tra lại thông tin hoặc thử lại sau.</p>

                @if(isset($message))
                    <p class="text-danger mt-3"><strong>Lý do:</strong> {{ $message }}</p>
                @endif

                <a href="{{ route('carts.index') }}" class="btn btn-outline-danger mt-4">Quay lại giỏ hàng</a>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('clients.layouts.app')

@section('content')
    <br><br><br><br><br><br>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="bg-white shadow rounded text-center p-5 animate__animated animate__fadeInDown">
                    <div class="mb-4">
                        <svg width="80" height="80" fill="none" viewBox="0 0 24 24" stroke="#dc3545"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3m0 3h.01m-6.938 4h13.856c1.54 0 2.502-1.668 1.732-3L13.732 4.5c-.77-1.332-2.694-1.332-3.464 0L3.34 16c-.77 1.332.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h2 class="text-danger fw-bold mb-3">Thanh toán thất bại</h2>

                    <p class="fs-5 mb-2 text-muted">
                        Giao dịch của bạn chưa được xử lý thành công.
                    </p>
                    <p class="text-muted">
                        Vui lòng kiểm tra lại thông tin thanh toán hoặc thử lại sau.
                    </p>

                    @if (isset($message))
                        <div class="alert alert-danger mt-4 mx-auto" style="max-width: 500px;">
                            <strong>Lý do từ VNPAY:</strong> {{ $message }}
                        </div>
                    @endif

                    <div class="mt-4 d-flex justify-content-center flex-wrap gap-3">
                        <a href="{{ route('carts.index') }}" class="btn btn-outline-danger px-4 py-2">
                            🛒 Quay lại giỏ hàng
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-secondary px-4 py-2">
                            🏠 Về trang chủ
                        </a>
                    </div>

                    <div class="mt-3 text-muted small">
                        Tự động chuyển về <a href="{{ route('home') }}">Trang chủ</a> sau <span id="countdown">5</span>
                        giây...
                    </div>
                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <script>
        let seconds = 5;
        const countdownElement = document.getElementById("countdown");

        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('home') }}";
            }
        }, 1000);
    </script>
@endsection

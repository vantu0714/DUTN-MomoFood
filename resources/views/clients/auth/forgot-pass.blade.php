@extends('clients.layouts.app')

@push('styles')
    <style>
        body {
            background-color: #f9f9f9;
        }

        .register-card {
            background-color: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .register-left {
            background-color: #ffffff;
            padding: 40px;
        }

        .register-right {
            background-color: #9cd62b;
            color: white;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .btn-green {
            background-color: #9cd62b;
            color: white;
            border: none;
        }

        .btn-green:hover {
            background-color: #8ec027;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center"
        style="padding-top: 180px; padding-bottom: 1%; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 register-card shadow">
                    <div class="row">
                        {{-- Form quên mật khẩu --}}
                        <div class="col-md-7 register-left">
                            <h2>Quên mật khẩu</h2>
                            <p class="text-muted">Nhập địa chỉ email để nhận liên kết đặt lại mật khẩu</p>
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Nhập email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-green px-4">Gửi</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Phần giới thiệu bên phải --}}
                        <div class="col-md-5 register-right">
                            <h4>Quay lại trang đăng nhập</h4>
                            <p class="mt-2 text-white text-center">
                                Bạn đã nhớ lại mật khẩu?<br>
                                <a href="{{ route('login') }}" class="text-white text-decoration-underline">Hãy nhấn vào đây
                                    để quay lại trang đăng nhập</a>
                            </p>
                            <i class="fa fa-envelope fa-4x mt-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

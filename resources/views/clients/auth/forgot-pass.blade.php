@extends('clients.layouts.app')

@push('styles')
    <style>
        .btn-custom {
            background-color: rgb(219, 115, 91);
            border-color: rgb(219, 115, 91);
            color: white;
        }

        .btn-custom:hover,
        .btn-custom:focus {
            background-color: rgb(200, 100, 75);
            border-color: rgb(200, 100, 75);
            color: white;
        }

        .btn-custom:focus {
            box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25);
        }

        .bg-custom {
            background-color: rgb(219, 115, 91);
        }

        .form-control:focus {
            border-color: rgb(219, 115, 91);
            box-shadow: 0 0 0 0.2rem rgba(219, 115, 91, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center"
        style="padding-top: 180px; padding-bottom: 1%; min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 rounded shadow overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-7 bg-white p-5">
                            <h2 class="mb-3">Quên mật khẩu</h2>
                            <p class="text-muted mb-4">Nhập địa chỉ email để nhận liên kết đặt lại mật khẩu</p>
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Nhập email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-custom px-4">Gửi</button>
                                </div>
                            </form>
                        </div>

                        <div
                            class="col-md-5 bg-custom text-white p-5 d-flex flex-column justify-content-center align-items-center">
                            <h4 class="mb-3">Quay lại trang đăng nhập</h4>
                            <p class="text-center text-white mb-4">
                                Bạn đã nhớ lại mật khẩu?<br>
                                <a href="{{ route('login') }}" class="text-white text-decoration-underline">Hãy nhấn vào đây
                                    để quay lại trang đăng nhập</a>
                            </p>
                            <i class="fa fa-envelope fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

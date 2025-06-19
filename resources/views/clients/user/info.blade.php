@extends('clients.layouts.app')

@push('styles')
    <style>
        body {
            background-color: #fff;
            color: #1a202c;
        }

        .main-wrapper {
            min-height: calc(100vh - 120px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 80px;
            padding-bottom: 0px;
        }

        .content-box {
            width: 100%;
            max-width: 1200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-container {
            max-width: 1400px;
        }

        .card {
            width: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1), 0 1px 2px rgba(0, 0, 0, .06);
            border-radius: 0.5rem;
        }

        .info-row {
            margin-bottom: 12px;
        }

        .info-divider {
            border-top: 1px solid #dee2e6;
            margin: 12px 0;
        }

        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745;
        }
    </style>
@endpush

@section('content')
    <div class="main-wrapper">
        <div class="container custom-container">
            <h2 class="text-center mb-4 text-success">Thông tin khách hàng</h2>
            <div class="row gutters-sm justify-content-center">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar"
                                            class="rounded-circle" width="150">
                                        <div class="mt-3">
                                            <h4>{{ Auth::user()->name }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row info-row">
                                        <div class="col-sm-3"><strong>Tên</strong></div>
                                        <div class="col-sm-9">{{ Auth::user()->name }}</div>
                                    </div>
                                    <hr class="info-divider">
                                    <div class="row info-row">
                                        <div class="col-sm-3"><strong>Email</strong></div>
                                        <div class="col-sm-9">{{ Auth::user()->email }}</div>
                                    </div>
                                    <hr class="info-divider">

                                    <div class="row info-row">
                                        <div class="col-sm-3"><strong>Số điện thoại</strong></div>
                                        <div class="col-sm-9">{{ Auth::user()->phone }}</div>
                                    </div>
                                    <hr class="info-divider">

                                    <div class="row info-row">
                                        <div class="col-sm-3"><strong>Địa chỉ</strong></div>
                                        <div class="col-sm-9">{{ Auth::user()->address }}</div>
                                    </div>
                                    <hr class="info-divider">

                                    <div class="row info-row">
                                        <div class="col-sm-3"><strong>Ngày tạo</strong></div>
                                        <div class="col-sm-9">{{ Auth::user()->created_at->format('d-m-Y') }}
                                        </div>
                                    </div>
                                    <hr class="info-divider">

                                    <div class="row info-row mb-4">
                                        <div class="col-sm-3"><strong>Cập nhật</strong></div>
                                        <div class="col-sm-9">{{ Auth::user()->updated_at->format('d-m-Y') }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <a class="btn btn-success" href="{{ route('clients.edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i> Sửa thông tin
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

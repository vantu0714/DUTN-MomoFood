@extends('admin.layouts.app')
@push('page-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .user-card {
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border: none;
            overflow: hidden;
        }

        .user-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            padding: 1.25rem 1.5rem;
        }

        .user-avatar-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            padding: 2rem 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .user-avatar-container:hover {
            transform: translateY(-5px);
        }

        .user-avatar {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .user-name {
            font-weight: 700;
            margin-top: 1.5rem;
            font-size: 1.25rem;
            color: #333;
        }

        .user-code {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .verification-badge {
            margin-top: 1.25rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .user-info-section {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            height: 100%;
        }

        .user-info-row {
            padding: 1rem 0;
            border-bottom: 1px solid #eaeaea;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
        }

        .user-info-row:last-child {
            border-bottom: none;
        }

        .user-info-row:hover {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding-left: 10px;
        }

        .user-info-label {
            font-weight: 600;
            color: #495057;
        }

        .user-info-value {
            color: #212529;
        }

        .user-info-icon {
            margin-right: 8px;
            color: #6c757d;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .status-badge i {
            margin-right: 0.35rem;
            font-size: 0.8rem;
        }

        .bio-content {
            line-height: 1.6;
            padding: 0.75rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-left: 3px solid #6c757d;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            padding: 0.5rem 1.25rem;
            margin-right: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .action-buttons .btn i {
            margin-right: 0.5rem;
        }

        .page-title-box {
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item a {
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .user-avatar {
                width: 120px;
                height: 120px;
            }

            .user-avatar-container {
                margin-bottom: 1.5rem;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card user-card">
                    <div class="card-header user-card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="mdi mdi-account-circle me-1"></i>
                                Thông tin chi tiết:
                                <span class="text-primary">{{ $user->name }}</span>
                            </h5>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            <!-- User Avatar Column -->
                            <div class="col-lg-3 col-md-4">
                                <div class="user-avatar-container">
                                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar của {{ $user->name }}"
                                        class="img-fluid rounded-circle user-avatar">
                                    <h5 class="user-name">{{ $user->name }}</h5>
                                </div>
                            </div>

                            <!-- User Info Column -->
                            <div class="col-lg-9 col-md-8">
                                <div class="user-info-section">
                                    <div class="row user-info-row">
                                        <div class="col-md-3 user-info-label">
                                            <i class="mdi mdi-account user-info-icon"></i>Họ và tên:
                                        </div>
                                        <div class="col-md-9 user-info-value">{{ $user->name }}</div>
                                    </div>

                                    <div class="row user-info-row">
                                        <div class="col-md-3 user-info-label">
                                            <i class="mdi mdi-email user-info-icon"></i>Email:
                                        </div>
                                        <div class="col-md-9 user-info-value">{{ $user->email }}</div>
                                    </div>

                                    <div class="row user-info-row">
                                        <div class="col-md-3 user-info-label">
                                            <i class="mdi mdi-phone user-info-icon"></i>Số điện thoại:
                                        </div>
                                        <div class="col-md-9 user-info-value">
                                            @if (!empty($user->phone))
                                                {{ $user->phone }}
                                            @else
                                                <span class="text-muted fst-italic">Chưa có thông tin</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row user-info-row">
                                        <div class="col-md-3 user-info-label">
                                            <i class="mdi mdi-map-marker user-info-icon"></i>Địa chỉ:
                                        </div>
                                        <div class="col-md-9 user-info-value">
                                            @if (!empty($user->address))
                                                {{ $user->address }}
                                            @else
                                                <span class="text-muted fst-italic">Chưa có thông tin</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row user-info-row">
                                        <div class="col-md-3 user-info-label">
                                            <i class="mdi mdi-account-check user-info-icon"></i>Trạng thái:
                                        </div>
                                        <div class="col-md-9 user-info-value">
                                            @if (isset($user->status))
                                                @if ($user->status == 1)
                                                    <span class="badge bg-success">Kích hoạt</span>
                                                @else
                                                    <span class="badge bg-danger">Khóa</span>
                                                @endif
                                            @else
                                                <span class="text-muted fst-italic">Không rõ trạng thái</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="action-buttons">
                                        <a href="{{ route('users.index') }}" class="btn btn-light">
                                            <i class="mdi mdi-arrow-left"></i>Quay lại
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
@endsection

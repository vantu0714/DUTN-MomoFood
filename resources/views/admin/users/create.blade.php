@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4 fw-bold text-primary">Thêm người dùng mới</h4>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class="profile-card">
                        <div class="profile-header">
                            <h5 class="profile-title">
                                <i class="fas fa-user-circle"></i>
                                Thông tin cá nhân
                            </h5>
                        </div>
                        <div class="profile-body">
                            <div class="avatar-section">
                                <label for="imageInput" class="avatar-label">Ảnh đại diện</label>
                                <div class="avatar-upload" onclick="document.getElementById('imageInput').click()">
                                    <img id="imagePreview" class="avatar-preview"
                                        src="{{ asset('admin/img/default-avatar.png') }}">
                                    <div class="avatar-overlay">
                                        <i class="fas fa-camera"></i>
                                        <span>Thay đổi ảnh</span>
                                    </div>
                                </div>
                                <input type="file" id="imageInput" name="avatar" style="display: none;" accept="image/*"
                                    onchange="previewImage(event)">
                                <p class="avatar-hint">Click vào ảnh để thay đổi</p>
                                @error('avatar')
                                    <small class="error-message">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="info-section">
                                <div class="info-item">
                                    <i class="fas fa-user-tag"></i>
                                    <div class="info-content">
                                        <span class="info-label">Vai trò</span>
                                        <span class="info-value" id="selectedRole">Chưa chọn</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-toggle-on"></i>
                                    <div class="info-content">
                                        <span class="info-label">Trạng thái</span>
                                        <span class="info-value status-active">Mặc định kích hoạt</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-card">
                        <div class="form-header">
                            <h5 class="form-title">
                                <i class="fas fa-edit"></i>
                                Thông tin chi tiết
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Họ và tên
                                </label>
                                <input type="text" name="name" class="custom-input" placeholder="Nhập họ và tên">
                                @error('name')
                                    <small class="error-message">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </label>
                                <input type="email" name="email" class="custom-input" placeholder="example@email.com">
                                @error('email')
                                    <small class="error-message">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Số điện thoại
                                </label>
                                <input type="text" name="phone" class="custom-input" placeholder="0xxx xxx xxx">
                                @error('phone')
                                    <small class="error-message">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Địa chỉ
                                </label>
                                <input type="text" name="address" class="custom-input" placeholder="Nhập địa chỉ">
                                @error('address')
                                    <small class="error-message">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Mật khẩu
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="password" id="passwordInput" class="custom-input"
                                        placeholder="••••••••">
                                    <button type="button" class="password-toggle" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <small class="error-message">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role_id" class="form-label">
                                            <i class="fas fa-user-shield"></i>
                                            Vai trò người dùng
                                        </label>
                                        <select name="role_id" id="roleSelect" class="custom-select"
                                            onchange="updateRole()">
                                            <option value="">-- Chọn vai trò --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <small class="error-message">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-toggle-on"></i>
                                            Trạng thái
                                        </label>
                                        <select name="status" class="custom-select">
                                            <option value="1" selected>Kích hoạt</option>
                                            <option value="0">Khóa</option>
                                        </select>
                                        @error('status')
                                            <small class="error-message">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('admin.users.index') }}" class="btn-back">
                                    <i class="fas fa-arrow-left"></i>
                                    Quay lại
                                </a>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-save"></i>
                                    Thêm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page-js')
    <script>
        document.getElementById('imageInput').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = () => {
                    const preview = document.getElementById('imagePreview');
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });


        document.getElementById('roleSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const display = document.getElementById('selectedRole');
            display.textContent = selectedOption.value ? selectedOption.text : 'Chưa chọn';
        });
    </script>
@endpush

<style>
    /* Card Styles */
    .profile-card,
    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f3f5;
        overflow: hidden;
        margin-bottom: 20px;
    }

    /* Headers */
    .profile-header,
    .form-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        padding: 20px;
        border-bottom: 2px solid #f1f3f5;
    }

    .profile-title,
    .form-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-title i,
    .form-title i {
        color: #3498db;
        font-size: 18px;
    }

    /* Body */
    .profile-body,
    .form-body {
        padding: 25px;
    }

    /* Avatar Section */
    .avatar-section {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f3f5;
    }

    .avatar-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
    }

    .avatar-upload {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
        cursor: pointer;
    }

    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f1f3f5;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(52, 152, 219, 0.9);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: white;
    }

    .avatar-upload:hover .avatar-overlay {
        opacity: 1;
    }

    .avatar-upload:hover .avatar-preview {
        transform: scale(1.05);
    }

    .avatar-overlay i {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .avatar-overlay span {
        font-size: 12px;
        font-weight: 500;
    }

    .avatar-hint {
        margin-top: 10px;
        font-size: 12px;
        color: #95a5a6;
    }

    /* Info Section */
    .info-section {
        margin-top: 20px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .info-item i {
        width: 32px;
        height: 32px;
        background: white;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3498db;
        font-size: 14px;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        display: block;
        font-size: 11px;
        color: #95a5a6;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin-top: 2px;
    }

    .status-active {
        color: #28a745;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-label i {
        font-size: 12px;
        color: #95a5a6;
    }

    .custom-input,
    .custom-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        color: #495057;
        background: white;
        transition: all 0.3s ease;
    }

    .custom-input:focus,
    .custom-select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .custom-input::placeholder {
        color: #adb5bd;
    }

    /* Password Toggle */
    .password-input-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #95a5a6;
        cursor: pointer;
        padding: 5px;
    }

    .password-toggle:hover {
        color: #3498db;
    }

    /* Error Message */
    .error-message {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #f1f3f5;
    }

    .btn-back,
    .btn-submit {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-back {
        background: white;
        color: #6c757d;
        border: 2px solid #dee2e6;
    }

    .btn-back:hover {
        background: #f8f9fa;
        color: #495057;
        transform: translateX(-3px);
    }

    .btn-submit {
        background: #3498db;
        color: white;
    }

    .btn-submit:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
            gap: 10px;
        }

        .btn-back,
        .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    // Preview ảnh khi chọn
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('passwordInput');
        const passwordIcon = document.getElementById('passwordIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }

    // Update role display
    function updateRole() {
        const roleSelect = document.getElementById('roleSelect');
        const selectedText = roleSelect.options[roleSelect.selectedIndex].text;
        document.getElementById('selectedRole').textContent = selectedText || 'Chưa chọn';
        
        const roleValue = document.getElementById('selectedRole');
        if (roleSelect.value) {
            roleValue.style.color = '#3498db';
        } else {
            roleValue.style.color = '#2c3e50';
        }
    }
</script>

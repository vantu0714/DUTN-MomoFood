@extends('clients.layouts.app')

@section('content')
    @php
        $returnDeadline = $order->received_at ? \Carbon\Carbon::parse($order->received_at)->addHours(24) : null;
        $canEdit = $order->status == 7 && $returnDeadline && now()->lte($returnDeadline);

        $processedItems = $order->returnItems->where('status', '!=', 'pending')->count();
        $isProcessed = $processedItems > 0;
    @endphp

    <div class="container mb-5" style="margin-top: 150px">
        <nav class="nav nav-borders">
            <a class="nav-link text-dark" href="{{ route('clients.info') }}">Thông tin</a>
            <a class="nav-link text-dark" href="{{ route('clients.changepassword') }}">Đổi mật khẩu</a>
            <a class="nav-link active ms-0 fw-semibold text-decoration-none"
                style="color: rgb(219, 115, 91); border-bottom: 2px solid rgb(219, 115, 91)"
                href="{{ route('clients.orders') }}">Đơn hàng</a>
            <a href="#" class="nav-link text-dark"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            <a class="nav-link text-dark {{ request()->routeIs('notifications.orders.index') ? 'active' : '' }}"
                href="{{ route('notifications.orders.index') }}">
                Thông báo
            </a>
        </nav>
        <hr class="mt-0 mb-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Chỉnh sửa yêu cầu hoàn hàng</h2>
            <a href="{{ route('clients.orderdetail', $order->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        @if ($isProcessed)
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Không thể chỉnh sửa</h5>
                        <p class="mb-0">Yêu cầu hoàn hàng không thể chỉnh sửa vì quản trị viên đã bắt đầu xử lý.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Bạn đang chỉnh sửa yêu cầu hoàn hàng cho đơn hàng #{{ $order->order_code }}.
                Thời hạn chỉnh sửa: {{ $returnDeadline ? $returnDeadline->format('H:i d/m/Y') : 'N/A' }}
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Chỉnh sửa yêu cầu hoàn hàng</h5>
                @if ($isProcessed)
                    <span class="badge bg-danger">Đã được xử lý</span>
                @endif
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($isProcessed)
                    <div class="text-center py-4">
                        <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Yêu cầu hoàn hàng đã được xử lý</h5>
                        <p class="text-muted">Bạn không thể chỉnh sửa yêu cầu hoàn hàng vì quản trị viên đã bắt đầu xử lý.
                        </p>
                        <a href="{{ route('clients.orderdetail', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại chi tiết đơn hàng
                        </a>
                    </div>
                @else
                    <form action="{{ route('clients.update_return', $order->id) }}" method="POST"
                        enctype="multipart/form-data" id="returnRequestForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Chọn sản phẩm cần hoàn trả:</label>

                            @foreach ($order->orderDetails as $index => $detail)
                                @php
                                    $product = $detail->product;
                                    $variant = $detail->productVariant;

                                    // Xử lý hiển thị biến thể
                                    $variantDisplay = '';
                                    if ($variant) {
                                        if (
                                            $variant->variant_values &&
                                            is_array(json_decode($variant->variant_values, true))
                                        ) {
                                            $variantValues = json_decode($variant->variant_values, true);
                                            $variantDisplay = implode(', ', $variantValues);
                                        } elseif ($variant->variant_name) {
                                            $variantDisplay = $variant->variant_name;
                                        }

                                        // Lấy thông tin chi tiết biến thể từ relationship nếu có
                                        if ($variant->attributeValues && $variant->attributeValues->count() > 0) {
                                            $attributeDetails = $variant->attributeValues
                                                ->map(function ($attrValue) {
                                                    return $attrValue->attribute->name . ': ' . $attrValue->value;
                                                })
                                                ->implode(', ');
                                            $variantDisplay = $attributeDetails;
                                        }
                                    }

                                    // Kiểm tra xem sản phẩm đã bị hủy chưa
                                    $isCancelled =
                                        $order->cancellation &&
                                        $order->cancellation->items->contains('order_detail_id', $detail->id);

                                    $existingReturnItem = $order->returnItems
                                        ->where('order_detail_id', $detail->id)
                                        ->first();
                                    $isSelected = $existingReturnItem ? true : false;
                                    $returnQuantity = $existingReturnItem ? $existingReturnItem->quantity : 1;
                                    $returnReason = $existingReturnItem ? $existingReturnItem->reason : '';
                                    $isItemProcessed = $existingReturnItem && $existingReturnItem->status != 'pending';
                                @endphp

                                {{-- Ẩn sản phẩm đã hủy --}}
                                @if (!$isCancelled)
                                    <div class="card mb-3 product-item {{ $isItemProcessed ? 'border-danger' : '' }}">
                                        <div class="card-body">
                                            @if ($isItemProcessed)
                                                <div class="alert alert-warning py-2 mb-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Sản phẩm này đã được xử lý và không thể chỉnh sửa
                                                </div>
                                            @endif

                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input return-item-checkbox"
                                                            name="return_items[{{ $index }}][selected]"
                                                            value="1" data-index="{{ $index }}"
                                                            id="return_item_{{ $index }}"
                                                            {{ $isSelected ? 'checked' : '' }}
                                                            {{ $isItemProcessed ? 'disabled' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="return_item_{{ $index }}"
                                                        class="form-check-label fw-semibold {{ $isItemProcessed ? 'text-muted' : '' }}">
                                                        {{ $product->product_name ?? '[Đã xoá]' }}
                                                        @if ($isItemProcessed)
                                                            <span class="badge bg-secondary ms-1">Đã xử lý</span>
                                                        @endif
                                                    </label>

                                                    {{-- Hiển thị biến thể --}}
                                                    @if ($variantDisplay)
                                                        <div class="small text-muted mt-1">
                                                            <span class="text-muted">Biến thể:</span>
                                                            <div class="mt-1">
                                                                @foreach (explode(', ', $variantDisplay) as $variantItem)
                                                                    <span
                                                                        class="badge bg-info text-white me-1 mb-1">{{ trim($variantItem) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @elseif ($variant && $variant->sku)
                                                        <div class="small mt-1">
                                                            <span class="text-muted">SKU:</span>
                                                            <span class="badge bg-secondary">{{ $variant->sku }}</span>
                                                        </div>
                                                    @endif

                                                    <input type="hidden"
                                                        name="return_items[{{ $index }}][order_detail_id]"
                                                        value="{{ $detail->id }}">
                                                </div>

                                                <div class="col-md-2 text-center">
                                                    <div class="small text-muted">Đã mua</div>
                                                    <span class="fw-bold">{{ $detail->quantity }}</span>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label small">Số lượng trả</label>
                                                    <div class="input-group input-group-sm">
                                                        <button type="button"
                                                            class="btn btn-outline-secondary quantity-btn"
                                                            data-action="decrease" data-index="{{ $index }}"
                                                            {{ $isSelected && !$isItemProcessed ? '' : 'disabled' }}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number"
                                                            class="form-control text-center return-quantity {{ $isItemProcessed ? 'bg-light' : '' }}"
                                                            name="return_items[{{ $index }}][quantity]"
                                                            min="1" max="{{ $detail->quantity }}"
                                                            data-max="{{ $detail->quantity }}"
                                                            data-index="{{ $index }}"
                                                            value="{{ $returnQuantity }}"
                                                            {{ $isSelected && !$isItemProcessed ? '' : 'disabled' }}
                                                            {{ $isItemProcessed ? 'readonly' : '' }}>
                                                        <button type="button"
                                                            class="btn btn-outline-secondary quantity-btn"
                                                            data-action="increase" data-index="{{ $index }}"
                                                            {{ $isSelected && !$isItemProcessed ? '' : 'disabled' }}>
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="invalid-feedback quantity-error" style="display: none;">
                                                        Số lượng trả không được vượt quá số lượng đã mua
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label small">Lý do hoàn trả</label>
                                                    <textarea name="return_items[{{ $index }}][reason]"
                                                        class="form-control form-control-sm return-reason {{ $isItemProcessed ? 'bg-light' : '' }}" rows="2"
                                                        {{ $isSelected && !$isItemProcessed ? '' : 'disabled' }} placeholder="Nhập lý do hoàn trả..."
                                                        data-index="{{ $index }}" {{ $isItemProcessed ? 'readonly' : '' }}>{{ $returnReason }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Phần đính kèm file -->
                                            <div class="attachment-section mt-3"
                                                id="attachment_section_{{ $index }}"
                                                style="{{ $isSelected ? '' : 'display: none;' }}">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="form-label fw-semibold mb-0">
                                                                <i class="fas fa-paperclip me-2"></i>
                                                                Đính kèm hình ảnh/video
                                                            </label>
                                                            <span class="text-muted small">
                                                                <span class="file-counter"
                                                                    data-index="{{ $index }}">{{ $existingReturnItem ? $existingReturnItem->attachments->count() : 0 }}</span>/5
                                                                file
                                                            </span>
                                                        </div>
                                                        <div class="text-muted small mb-3">
                                                            Tối đa 5 file, mỗi file ≤ 10MB. Hỗ trợ: JPG, PNG, GIF,
                                                            MP4, MOV, AVI
                                                        </div>

                                                        <!-- Hiển thị file đính kèm hiện có -->
                                                        @if ($existingReturnItem && $existingReturnItem->attachments->count() > 0)
                                                            <div class="mb-3">
                                                                <p class="small fw-semibold mb-2">File đã đính kèm:</p>
                                                                <div class="d-flex flex-wrap gap-2 mb-3">
                                                                    @foreach ($existingReturnItem->attachments as $attachment)
                                                                        <div class="existing-attachment position-relative"
                                                                            style="width: 80px; height: 80px;">
                                                                            @if ($attachment->file_type == 'image')
                                                                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                                                    class="img-thumbnail w-100 h-100"
                                                                                    style="object-fit: cover;"
                                                                                    alt="Attachment">
                                                                            @else
                                                                                <div
                                                                                    class="bg-light d-flex align-items-center justify-content-center w-100 h-100 rounded border">
                                                                                    <i
                                                                                        class="fas fa-video text-primary"></i>
                                                                                </div>
                                                                            @endif
                                                                            <div
                                                                                class="form-check position-absolute top-0 start-0 m-1">
                                                                                <input type="checkbox"
                                                                                    class="form-check-input"
                                                                                    name="return_items[{{ $index }}][existing_attachments][]"
                                                                                    value="{{ $attachment->id }}" checked>
                                                                            </div>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-existing-attachment"
                                                                                style="width: 20px; height: 20px; line-height: 1; font-size: 10px;">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="mb-3">
                                                            <input type="file" class="d-none multi-file-input"
                                                                id="multi-file-input-{{ $index }}"
                                                                name="return_items[{{ $index }}][attachments][]"
                                                                accept="image/*,video/*" data-index="{{ $index }}"
                                                                multiple>
                                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                                onclick="document.getElementById('multi-file-input-{{ $index }}').click()">
                                                                <i class="fas fa-cloud-upload-alt me-2"></i>Thêm file mới
                                                            </button>
                                                        </div>

                                                        <div class="file-previews row g-2"
                                                            id="file-previews-{{ $index }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <!-- Nút hủy yêu cầu hoàn hàng -->
                            <button type="button" class="btn btn-danger me-md-2" data-bs-toggle="modal"
                                data-bs-target="#cancelReturnModal">
                                <i class="fas fa-times me-2"></i>Hủy yêu cầu
                            </button>

                            <!-- Nút cập nhật yêu cầu hoàn hàng -->
                            <button type="submit" class="btn btn-warning text-white" id="submitReturnRequest">
                                <i class="fas fa-save me-2"></i>Cập nhật yêu cầu
                            </button>
                        </div>
                    </form>

                    <!-- Modal xác nhận hủy yêu cầu hoàn hàng -->
                    <div class="modal fade" id="cancelReturnModal" tabindex="-1"
                        aria-labelledby="cancelReturnModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelReturnModalLabel">Xác nhận hủy yêu cầu</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Bạn có chắc chắn muốn hủy yêu cầu hoàn hàng này không?</p>
                                    <p class="text-danger"><small>Hành động này không thể hoàn tác.</small></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Quay
                                        lại</button>
                                    <form action="{{ route('clients.cancel_return', $order->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (!$isProcessed)
                initializeReturnForm();

                // Xử lý nút xóa file đính kèm hiện có
                document.querySelectorAll('.remove-existing-attachment').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const attachmentDiv = this.closest('.existing-attachment');
                        const checkbox = attachmentDiv.querySelector('input[type="checkbox"]');
                        checkbox.checked = false;
                        attachmentDiv.style.display = 'none';
                    });
                });
            @endif
        });

        // Khởi tạo form chỉnh sửa
        function initializeReturnForm() {
            const checkboxes = document.querySelectorAll('.return-item-checkbox:not(:disabled)');
            const submitBtn = document.getElementById('submitReturnRequest');

            checkboxes.forEach(checkbox => {
                const index = checkbox.dataset.index;
                const isChecked = checkbox.checked;

                toggleProductFields(index, isChecked);

                checkbox.addEventListener('change', function() {
                    toggleProductFields(index, this.checked);
                    validateForm();
                });
            });

            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', handleQuantityChange);
            });

            document.querySelectorAll('.return-quantity').forEach(input => {
                input.addEventListener('input', function() {
                    validateQuantity(this);
                    validateForm();
                });
                input.addEventListener('blur', function() {
                    validateQuantity(this);
                    validateForm();
                });
                input.addEventListener('change', function() {
                    validateQuantity(this);
                    validateForm();
                });
            });

            document.querySelectorAll('.return-reason').forEach(textarea => {
                textarea.addEventListener('input', validateForm);
            });

            document.querySelectorAll('.multi-file-input').forEach(input => {
                input.addEventListener('change', function() {
                    handleMultipleFileSelect(this);
                });
            });

            document.getElementById('returnRequestForm').addEventListener('submit', handleFormSubmit);

            validateForm();
        }

        function toggleProductFields(index, enabled) {
            const quantityInput = document.querySelector(`.return-quantity[data-index="${index}"]`);
            const reasonTextarea = document.querySelector(`.return-reason[data-index="${index}"]`);
            const quantityBtns = document.querySelectorAll(`.quantity-btn[data-index="${index}"]`);
            const attachmentSection = document.getElementById(`attachment_section_${index}`);

            if (enabled) {
                quantityInput.disabled = false;
                reasonTextarea.disabled = false;
                quantityBtns.forEach(btn => btn.disabled = false);
                if (attachmentSection) attachmentSection.style.display = 'block';

                if (!quantityInput.value || quantityInput.value < 1) {
                    quantityInput.value = 1;
                }
            } else {
                quantityInput.disabled = true;
                reasonTextarea.disabled = true;
                reasonTextarea.value = '';
                quantityBtns.forEach(btn => btn.disabled = true);
                if (attachmentSection) attachmentSection.style.display = 'none';

                const fileInput = document.getElementById(`multi-file-input-${index}`);
                if (fileInput) {
                    fileInput.value = '';
                }
                const previewContainer = document.getElementById(`file-previews-${index}`);
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                }
                updateFileCounter(index, 0);
            }
        }

        // Xử lý thay đổi số lượng
        function handleQuantityChange(e) {
            const action = e.currentTarget.dataset.action;
            const index = e.currentTarget.dataset.index;
            const quantityInput = document.querySelector(`.return-quantity[data-index="${index}"]`);
            const max = parseInt(quantityInput.dataset.max);
            let currentValue = parseInt(quantityInput.value) || 1;

            if (action === 'increase') {
                if (currentValue < max) {
                    quantityInput.value = currentValue + 1;
                } else {
                    showToast('Số lượng trả không được vượt quá số lượng đã mua', 'error');
                    return;
                }
            } else if (action === 'decrease') {
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            }

            validateQuantity(quantityInput);
            validateForm();
        }

        // Validate số lượng
        function validateQuantity(input) {
            const max = parseInt(input.dataset.max);
            const value = parseInt(input.value) || 0;
            const errorElement = input.closest('.col-md-2').querySelector('.quantity-error');

            if (value > max) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
                showToast('Số lượng trả không được vượt quá số lượng đã mua', 'error');

                setTimeout(() => {
                    input.value = max;
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                }, 1000);

                return false;
            } else if (value < 1) {
                input.classList.add('is-invalid');
                errorElement.textContent = 'Số lượng trả phải lớn hơn 0';
                errorElement.style.display = 'block';
                showToast('Số lượng trả phải lớn hơn 0', 'error');

                setTimeout(() => {
                    input.value = 1;
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                }, 1000);

                return false;
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
                return true;
            }
        }

        // Validate form
        function validateForm() {
            const checkboxes = document.querySelectorAll('.return-item-checkbox:checked');
            const submitBtn = document.getElementById('submitReturnRequest');
            let isValid = true;

            checkboxes.forEach(checkbox => {
                const index = checkbox.dataset.index;
                const quantityInput = document.querySelector(`.return-quantity[data-index="${index}"]`);
                const reasonTextarea = document.querySelector(`.return-reason[data-index="${index}"]`);

                if (!validateQuantity(quantityInput)) {
                    isValid = false;
                }

                const reason = reasonTextarea.value.trim();
                if (!reason) {
                    reasonTextarea.classList.add('is-invalid');
                    isValid = false;
                } else {
                    reasonTextarea.classList.remove('is-invalid');
                }
            });

            submitBtn.disabled = checkboxes.length === 0 || !isValid;
        }

        const selectedFiles = {};

        // Xử lý chọn nhiều file
        function handleMultipleFileSelect(input) {
            const files = input.files;
            const index = input.dataset.index;
            const previewContainer = document.getElementById(`file-previews-${index}`);

            if (!files || files.length === 0) return;

            if (!selectedFiles[index]) {
                selectedFiles[index] = [];
            }

            let validFilesCount = 0;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (validateFile(file) && selectedFiles[index].length < 5) {
                    selectedFiles[index].push(file);
                    validFilesCount++;

                    showFilePreview(previewContainer, file, index);
                } else if (selectedFiles[index].length >= 5) {
                    showToast('Chỉ được phép tải lên tối đa 5 file', 'error');
                    break;
                }
            }

            updateFileCounter(index, selectedFiles[index].length);
            updateFileInput(index);

            if (validFilesCount > 0) {
                showToast(`Đã thêm ${validFilesCount} file thành công`, 'success');
            }
        }

        // Validate file
        function validateFile(file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const validTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/jpg',
                'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/avi', 'video/webm'
            ];

            if (file.size > maxSize) {
                showToast(`File "${file.name}" vượt quá kích thước cho phép (10MB)`, 'error');
                return false;
            }

            if (!validTypes.includes(file.type)) {
                showToast(`File "${file.name}" không đúng định dạng cho phép`, 'error');
                return false;
            }

            return true;
        }

        // Hiển thị preview file
        function showFilePreview(container, file, index) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'col-6 col-md-4 col-lg-3 file-preview';
            previewDiv.dataset.fileName = file.name;

            const previewCard = document.createElement('div');
            previewCard.className = 'card h-100';

            let previewContent = '';

            if (file.type.startsWith('image/')) {
                previewContent = `
                <img src="${URL.createObjectURL(file)}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="${file.name}">
            `;
            } else {
                previewContent = `
                <div class="card-body d-flex align-items-center justify-content-center" style="height: 120px;">
                    <i class="fas fa-video text-primary" style="font-size: 3rem;"></i>
                </div>
            `;
            }

            previewCard.innerHTML = previewContent + `
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-truncate small" title="${file.name}">${file.name}</div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFilePreview(this, '${index}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="text-muted small">${formatFileSize(file.size)}</div>
            </div>
        `;

            previewDiv.appendChild(previewCard);
            container.appendChild(previewDiv);
        }

        // Xóa preview file
        function removeFilePreview(button, index) {
            const previewCard = button.closest('.file-preview');
            const fileName = previewCard.dataset.fileName;

            if (selectedFiles[index]) {
                selectedFiles[index] = selectedFiles[index].filter(file => file.name !== fileName);
                updateFileInput(index);
            }

            previewCard.remove();

            const fileCount = selectedFiles[index] ? selectedFiles[index].length : 0;
            updateFileCounter(index, fileCount);
        }

        // Cập nhật input file
        function updateFileInput(index) {
            const fileInput = document.getElementById(`multi-file-input-${index}`);

            const dataTransfer = new DataTransfer();

            if (selectedFiles[index]) {
                selectedFiles[index].forEach(file => {
                    dataTransfer.items.add(file);
                });
            }

            fileInput.files = dataTransfer.files;
        }

        // Cập nhật bộ đếm file
        function updateFileCounter(index, count) {
            const counter = document.querySelector(`.file-counter[data-index="${index}"]`);
            if (counter) {
                counter.textContent = count;
            }
        }

        // Xử lý submit form
        function handleFormSubmit(e) {
            const submitBtn = document.getElementById('submitReturnRequest');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Đang xử lý...';
        }

        // Định dạng kích thước file
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        // Hiển thị thông báo toast
        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();

            const bgClass = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-info';
            const icon = type === 'error' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' :
                'fa-info-circle';

            const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${icon} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            bsToast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    </script>
@endsection

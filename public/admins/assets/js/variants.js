
let variantIndex = 1;
const productName = window.productName;

// Format number with VND currency
function formatVND(number) {
    if (!number || isNaN(number)) return '';
    return new Intl.NumberFormat('vi-VN').format(number) + ' VND';
}

// Validate price input to prevent negative values
function validatePrice(input) {
    const value = parseFloat(input.value);
    if (value < 0) {
        input.value = 0;
        showPriceError(input, 'Giá không được âm');
        return false;
    }
    hidePriceError(input);
    return true;
}

function showPriceError(input, message) {
    let errorDiv = input.parentNode.parentNode.querySelector('.price-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'price-error';
        input.parentNode.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    input.classList.add('is-invalid');
}

function hidePriceError(input) {
    const errorDiv = input.parentNode.parentNode.querySelector('.price-error');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    input.classList.remove('is-invalid');
}

function updateSKUs(variantItem) {
    const productCode = document.getElementById('product_code').value.trim();
    const mainAttr = variantItem.querySelector('input[name*="[main_attribute][value]"]').value.trim().toUpperCase()
        .replace(/\s+/g, '-');

    const subRows = variantItem.querySelectorAll('.sub-attribute-row');
    subRows.forEach(row => {
        const sizeSelect = row.querySelector('select[name*="[attribute_value_id]"]');
        const skuInput = row.querySelector('.sku-input');
        const selectedSizeText = sizeSelect?.selectedOptions[0]?.text?.trim().toUpperCase().replace(/\s+/g,
            '-') || '';
        let sku = productCode;
        if (mainAttr) sku += `-${mainAttr}`;
        if (selectedSizeText) sku += `-${selectedSizeText}`;
        if (skuInput) skuInput.value = sku;
    });
}

function updatePreviewTable() {
    const tableBody = document.querySelector('#preview-variants-table tbody');
    const emptyMessage = document.getElementById('preview-empty');
    tableBody.innerHTML = '';

    let hasData = false;

    document.querySelectorAll('.variant-item').forEach((variantItem) => {
        const flavor = variantItem.querySelector('input[name*="[main_attribute][value]"]').value;
        const subRows = variantItem.querySelectorAll('.sub-attribute-row');

        subRows.forEach((row) => {
            const size = row.querySelector('select option:checked')?.textContent || '';
            const price = row.querySelector('input[name*="[price]"]')?.value || '';
            const quantity = row.querySelector('input[name*="[quantity_in_stock]"]')?.value || '';
            const sku = row.querySelector('input[name*="[sku]"]')?.value || '';
            const fileInput = row.querySelector('input[type="file"]');

            if (flavor || price || quantity) {
                hasData = true;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                        <td style="max-width: 80px; word-wrap: break-word; padding: 8px 4px; font-size: 11px;">${productName.length > 15 ? productName.substring(0, 15) + '...' : productName}</td>
                        <td style="padding: 8px 4px;"><span class="badge bg-primary" style="font-size: 10px;">${flavor || 'Chưa nhập'}</span></td>
                        <td style="padding: 8px 4px;"><span class="badge bg-secondary" style="font-size: 10px;">${size}</span></td>
                        <td class="img-cell text-center" style="padding: 8px 4px;"></td>
                        <td class="text-end" style="padding: 8px 4px; font-size: 11px;">${price ? formatVND(price) : '-'}</td>
                        <td class="text-center" style="padding: 8px 4px; font-size: 11px;">${quantity || '-'}</td>
                        <td style="font-size: 10px; max-width: 100px; word-wrap: break-word; font-family: 'Courier New', monospace; color: #5a5c69; font-weight: 600; padding: 8px 4px;">${sku}</td>
                    `;

            tableBody.appendChild(tr);

            // Handle image
            const imgCell = tr.querySelector('.img-cell');
            if (fileInput?.files?.[0]) {
                const img = document.createElement('img');
                img.style.maxWidth = '40px';
                img.style.maxHeight = '40px';
                img.style.borderRadius = '4px';
                img.style.objectFit = 'cover';

                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    imgCell.appendChild(img);
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                imgCell.innerHTML =
                    '<i class="fas fa-image text-muted" style="font-size: 12px;"></i>';
            }
        });
    });

    emptyMessage.style.display = hasData ? 'none' : 'block';
}

function attachEvents(variantItem) {
    const flavorInput = variantItem.querySelector('input[name*="[main_attribute][value]"]');
    const selects = variantItem.querySelectorAll('select[name*="[attribute_value_id]"]');
    const fileInputs = variantItem.querySelectorAll('input[type="file"]');
    const priceInputs = variantItem.querySelectorAll('input[name*="[price]"]');
    const quantityInputs = variantItem.querySelectorAll('input[name*="[quantity_in_stock]"]');

    flavorInput?.addEventListener('input', () => {
        updateSKUs(variantItem);
        updatePreviewTable();
    });

    selects.forEach(select => {
        select.addEventListener('change', () => {
            updateDisabledSizes(variantItem);
            updateSKUs(variantItem);
            updatePreviewTable();
        });
    });

    fileInputs.forEach(input => {
        input.addEventListener('change', updatePreviewTable);
    });

    priceInputs.forEach(input => {
        // Prevent negative values on input
        input.addEventListener('input', function () {
            validatePrice(this);
            updatePreviewTable();
        });

        // Additional validation on blur
        input.addEventListener('blur', function () {
            validatePrice(this);
        });

        // Prevent negative values on keydown
        input.addEventListener('keydown', function (e) {
            // Allow backspace, delete, tab, escape, enter
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Prevent minus sign
            if (e.keyCode === 189 || e.keyCode === 109) {
                e.preventDefault();
            }
        });
    });

    quantityInputs.forEach(input => {
        input.addEventListener('input', updatePreviewTable);
    });
}

function updateDisabledSizes(variantItem) {
    const selects = variantItem.querySelectorAll('select[name*="[attribute_value_id]"]');
    const selectedValues = Array.from(selects).map(s => s.value);
    selects.forEach(select => {
        const currentValue = select.value;
        Array.from(select.options).forEach(option => {
            option.disabled = selectedValues.includes(option.value) && option.value !==
                currentValue;
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const variantsContainer = document.getElementById('variants-container');
    const firstVariant = document.querySelector('.variant-item');

    attachEvents(firstVariant);
    updateDisabledSizes(firstVariant);
    updateSKUs(firstVariant);
    updatePreviewTable();

    document.getElementById('add-variant').addEventListener('click', function () {
        const template = document.querySelector('.variant-item');
        const clone = template.cloneNode(true);

        clone.querySelector('h6').innerHTML =
            `<i class="fas fa-cube me-1"></i>Biến thể #${variantIndex + 1}`;
        clone.querySelectorAll('input, select').forEach(el => {
            if (el.type !== 'file') el.value = '';
            el.classList.remove('is-invalid'); // Remove validation classes
            let name = el.getAttribute('name');
            if (name) {
                name = name.replace(/variants\[\d+]/, `variants[${variantIndex}]`);
                name = name.replace(/sub_attributes\[\d+]/g, 'sub_attributes[0]');
                el.setAttribute('name', name);
            }
        });

        // Remove any error messages from cloned variant
        clone.querySelectorAll('.price-error').forEach(errorDiv => errorDiv.remove());

        const subRows = clone.querySelectorAll('.sub-attribute-row');
        for (let i = 1; i < subRows.length; i++) subRows[i].remove();

        variantsContainer.appendChild(clone);
        attachEvents(clone);
        updateDisabledSizes(clone);
        updateSKUs(clone);
        updatePreviewTable();
        variantIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant') || e.target.closest('.remove-variant')) {
            const items = document.querySelectorAll('.variant-item');
            if (items.length > 1) {
                const variantItem = e.target.closest('.variant-item');
                variantItem.style.animation = 'slideOutDown 0.3s ease-in';
                setTimeout(() => {
                    variantItem.remove();
                    updatePreviewTable();
                }, 300);
            }
        }

        if (e.target.classList.contains('add-sub-attribute') || e.target.closest(
            '.add-sub-attribute')) {
            const variantItem = e.target.closest('.variant-item');
            const tableBody = variantItem.querySelector('.sub-attributes-table');
            const rows = tableBody.querySelectorAll('.sub-attribute-row');
            const lastRow = rows[rows.length - 1];
            const newRow = lastRow.cloneNode(true);

            const variantIdx = variantItem.querySelector('input[name^="variants["]').name.match(
                /variants\[(\d+)]/)[1];
            const subIdx = rows.length;

            const selects = newRow.querySelectorAll('select');
            const inputs = newRow.querySelectorAll('input');

            if (selects.length) {
                selects[0].name =
                    `variants[${variantIdx}][sub_attributes][${subIdx}][attribute_value_id]`;
                selects[0].selectedIndex = 0;
            }

            if (inputs.length) {
                inputs[0].name = `variants[${variantIdx}][sub_attributes][${subIdx}][price]`;
                inputs[0].value = '';
                inputs[0].classList.remove('is-invalid'); // Remove validation classes
                inputs[1].name =
                    `variants[${variantIdx}][sub_attributes][${subIdx}][quantity_in_stock]`;
                inputs[1].value = '';
                inputs[2].name = `variants[${variantIdx}][sub_attributes][${subIdx}][image]`;
                inputs[2].value = '';
                inputs[3].name = `variants[${variantIdx}][sub_attributes][${subIdx}][sku]`;
                inputs[3].value = '';
            }

            // Remove any error messages from cloned row
            newRow.querySelectorAll('.price-error').forEach(errorDiv => errorDiv.remove());

            tableBody.appendChild(newRow);
            attachEvents(variantItem);
            updateDisabledSizes(variantItem);
            updateSKUs(variantItem);
            updatePreviewTable();
        }

        if (e.target.classList.contains('remove-sub-attribute') || e.target.closest(
            '.remove-sub-attribute')) {
            const tableBody = e.target.closest('.sub-attributes-table');
            const rows = tableBody.querySelectorAll('.sub-attribute-row');
            if (rows.length > 1) {
                const row = e.target.closest('.sub-attribute-row');
                row.style.animation = 'fadeOut 0.2s ease-in';
                setTimeout(() => {
                    row.remove();
                    const variantItem = e.target.closest('.variant-item');
                    updateDisabledSizes(variantItem);
                    updateSKUs(variantItem);
                    updatePreviewTable();
                }, 200);
            }
        }
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        const visibleGroup = document.querySelector('.product-variant-group:not(.d-none)');
        const originalPrice = parseFloat(visibleGroup?.querySelector('.original-price')?.value || 0);
        console.log('Original Price:', originalPrice);
        let valid = true;
        let hasInvalidPrice = false;
        document.querySelectorAll('input[name*="[quantity_in_stock]"]').forEach(input => {
            const quantity = parseInt(input.value);
            if (isNaN(quantity) || quantity <= 0) {
                input.classList.add('is-invalid');
                hasInvalidQuantity = true;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (hasInvalidQuantity) {
            alert('Số lượng tồn kho phải lớn hơn 0 cho tất cả biến thể.');
            e.preventDefault();
            return;
        }

        // Validate all price inputs
        document.querySelectorAll('input[name*="[price]"]').forEach(input => {
            const price = parseFloat(input.value);

            // Check for negative values
            if (price < 0) {
                hasInvalidPrice = true;
                showPriceError(input, 'Giá không được âm');
                valid = false;
            }

            // Check if price is lower than original price
            if (!isNaN(price) && price < originalPrice) {
                valid = false;
                showPriceError(input,
                    `Giá biến thể không được thấp hơn giá gốc: ${formatVND(originalPrice)}`
                );
            }
        });

        if (hasInvalidPrice) {
            alert(
                'Vui lòng kiểm tra lại giá! Giá không được âm và phải lớn hơn hoặc bằng giá gốc.');
            e.preventDefault();
        } else if (!valid) {
            alert(`Giá biến thể không được thấp hơn giá gốc: ${formatVND(originalPrice)}`);
            e.preventDefault();
        }
    });
});
document.addEventListener('DOMContentLoaded', function () {
    // Enhanced delete confirmation
    window.confirmDelete = function (button) {
        const form = button.closest('form');

        // Create custom modal instead of basic confirm
        const modal = document.createElement('div');
        modal.className = 'delete-modal-overlay';
        modal.innerHTML = `
                <div class="delete-modal">
                    <div class="delete-modal-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Xác nhận xóa</h3>
                    <p>Bạn có chắc chắn muốn xóa biến thể này không?<br>Hành động này không thể hoàn tác.</p>
                    <div class="delete-modal-actions">
                        <button class="cancel-btn" onclick="closeDeleteModal()">Hủy</button>
                        <button class="confirm-btn" onclick="confirmDeleteAction()">Xóa</button>
                    </div>
                </div>
            `;

        // Add modal styles
        const style = document.createElement('style');
        style.textContent = `
                .delete-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    animation: fadeIn 0.3s ease;
                }
                
                .delete-modal {
                    background: white;
                    padding: 2rem;
                    border-radius: 12px;
                    text-align: center;
                    max-width: 400px;
                    margin: 1rem;
                    animation: slideIn 0.3s ease;
                }
                
                .delete-modal-icon {
                    font-size: 3rem;
                    color: #ef4444;
                    margin-bottom: 1rem;
                }
                
                .delete-modal h3 {
                    color: #1f2937;
                    margin-bottom: 0.5rem;
                }
                
                .delete-modal p {
                    color: #64748b;
                    margin-bottom: 2rem;
                    line-height: 1.5;
                }
                
                .delete-modal-actions {
                    display: flex;
                    gap: 1rem;
                    justify-content: center;
                }
                
                .cancel-btn, .confirm-btn {
                    padding: 0.75rem 1.5rem;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                
                .cancel-btn {
                    background: #f1f5f9;
                    color: #64748b;
                }
                
                .cancel-btn:hover {
                    background: #e2e8f0;
                }
                
                .confirm-btn {
                    background: #ef4444;
                    color: white;
                }
                
                .confirm-btn:hover {
                    background: #dc2626;
                }
                
                @keyframes slideIn {
                    from {
                        transform: scale(0.9) translateY(-20px);
                        opacity: 0;
                    }
                    to {
                        transform: scale(1) translateY(0);
                        opacity: 1;
                    }
                }
            `;

        document.head.appendChild(style);
        document.body.appendChild(modal);

        // Store form reference for confirmation
        window.currentDeleteForm = form;
    };

    // Close modal function
    window.closeDeleteModal = function () {
        const modal = document.querySelector('.delete-modal-overlay');
        if (modal) {
            modal.remove();
        }
        window.currentDeleteForm = null;
    };

    // Confirm delete action
    window.confirmDeleteAction = function () {
        if (window.currentDeleteForm) {
            window.currentDeleteForm.submit();
        }
        closeDeleteModal();
    };

    // Close modal on overlay click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-modal-overlay')) {
            closeDeleteModal();
        }
    });

    // Enhanced search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const value = this.value;
            if (value.length > 0) {
                this.style.paddingRight = '3rem';

                // Add clear button if not exists
                let clearBtn = this.parentNode.querySelector('.search-clear-btn');
                if (!clearBtn) {
                    clearBtn = document.createElement('button');
                    clearBtn.type = 'button';
                    clearBtn.className = 'search-clear-btn';
                    clearBtn.innerHTML = '<i class="fas fa-times"></i>';
                    clearBtn.style.cssText = `
                            position: absolute;
                            right: 1rem;
                            top: 50%;
                            transform: translateY(-50%);
                            background: none;
                            border: none;
                            color: #64748b;
                            cursor: pointer;
                            padding: 0.25rem;
                            border-radius: 4px;
                            transition: all 0.3s ease;
                        `;

                    clearBtn.addEventListener('click', function () {
                        searchInput.value = '';
                        searchInput.style.paddingRight = '2.5rem';
                        this.remove();
                        searchInput.focus();
                    });

                    clearBtn.addEventListener('mouseenter', function () {
                        this.style.background = '#f1f5f9';
                        this.style.color = '#ef4444';
                    });

                    clearBtn.addEventListener('mouseleave', function () {
                        this.style.background = 'none';
                        this.style.color = '#64748b';
                    });

                    this.parentNode.appendChild(clearBtn);
                }
            } else {
                this.style.paddingRight = '2.5rem';
                const clearBtn = this.parentNode.querySelector('.search-clear-btn');
                if (clearBtn) {
                    clearBtn.remove();
                }
            }
        });
    }

    // Enhanced image hover effects
    const variantImages = document.querySelectorAll('.variant-image');
    variantImages.forEach(img => {
        img.addEventListener('click', function () {
            // Create image preview modal
            const modal = document.createElement('div');
            modal.className = 'image-preview-modal';
            modal.innerHTML = `
                    <div class="image-preview-overlay" onclick="closeImagePreview()">
                        <div class="image-preview-container">
                            <img src="${this.src}" alt="Product Variant" class="preview-image">
                            <button class="close-preview-btn" onclick="closeImagePreview()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;

            // Add modal styles
            const style = document.createElement('style');
            style.textContent = `
                    .image-preview-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        z-index: 9999;
                        animation: fadeIn 0.3s ease;
                    }
                    
                    .image-preview-overlay {
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.9);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                    }
                    
                    .image-preview-container {
                        position: relative;
                        max-width: 90vw;
                        max-height: 90vh;
                        cursor: default;
                    }
                    
                    .preview-image {
                        max-width: 100%;
                        max-height: 90vh;
                        object-fit: contain;
                        border-radius: 12px;
                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                    }
                    
                    .close-preview-btn {
                        position: absolute;
                        top: -50px;
                        right: 0;
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: white;
                        padding: 1rem;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 1.2rem;
                        transition: all 0.3s ease;
                        backdrop-filter: blur(10px);
                    }
                    
                    .close-preview-btn:hover {
                        background: rgba(255, 255, 255, 0.2);
                        transform: scale(1.1);
                    }
                `;

            document.head.appendChild(style);
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
        });
    });

    // Close image preview
    window.closeImagePreview = function () {
        const modal = document.querySelector('.image-preview-modal');
        if (modal) {
            modal.remove();
            document.body.style.overflow = 'auto';
        }
    };

    // Enhanced product card interactions
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        const header = card.querySelector('.product-header');
        const expandIcon = card.querySelector('.expand-icon i');
        const tableContainer = card.querySelector('.variants-table-container');

        // Initial state
        let isExpanded = true;

        header.addEventListener('click', function () {
            isExpanded = !isExpanded;

            if (isExpanded) {
                tableContainer.style.maxHeight = tableContainer.scrollHeight + 'px';
                expandIcon.style.transform = 'rotate(0deg)';
                tableContainer.style.opacity = '1';
            } else {
                tableContainer.style.maxHeight = '0';
                expandIcon.style.transform = 'rotate(-90deg)';
                tableContainer.style.opacity = '0';
            }
        });

        // Set initial styles
        tableContainer.style.transition = 'all 0.3s ease';
        tableContainer.style.overflow = 'hidden';
        expandIcon.style.transition = 'transform 0.3s ease';
    });

    // Enhanced stock badge animations
    const stockBadges = document.querySelectorAll('.stock-badge');
    stockBadges.forEach(badge => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    badge.style.animation = 'bounceIn 0.6s ease';
                }
            });
        });
        observer.observe(badge);
    });

    // Add bounce animation
    const bounceStyle = document.createElement('style');
    bounceStyle.textContent = `
            @keyframes bounceIn {
                0% {
                    transform: scale(0.3);
                    opacity: 0;
                }
                50% {
                    transform: scale(1.05);
                }
                70% {
                    transform: scale(0.9);
                }
                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }
        `;
    document.head.appendChild(bounceStyle);

    // Enhanced filter functionality
    const filterSelect = document.querySelector('.filter-select');
    if (filterSelect) {
        filterSelect.addEventListener('change', function () {
            const form = this.closest('form');
            if (this.value !== '') {
                // Auto-submit when filter is selected
                setTimeout(() => {
                    form.submit();
                }, 300);
            }
        });
    }

    // Add loading states
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function () {
            const submitBtn = this.querySelector('.search-btn');
            const originalContent = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Đang tìm...</span>';
            submitBtn.disabled = true;

            // Re-enable after a delay (in case of errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            }, 5000);
        });
    }

    // Enhanced tooltips for action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function () {
            const tooltip = document.createElement('div');
            tooltip.className = 'action-tooltip';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.cssText = `
                    position: absolute;
                    background: #1f2937;
                    color: white;
                    padding: 0.5rem 0.75rem;
                    border-radius: 6px;
                    font-size: 0.8rem;
                    white-space: nowrap;
                    z-index: 1000;
                    pointer-events: none;
                    transform: translateX(-50%);
                    bottom: 100%;
                    left: 50%;
                    margin-bottom: 0.5rem;
                    opacity: 0;
                    animation: tooltipFadeIn 0.3s ease forwards;
                `;

            const tooltipStyle = document.createElement('style');
            tooltipStyle.textContent = `
                    @keyframes tooltipFadeIn {
                        from {
                            opacity: 0;
                            transform: translateX(-50%) translateY(10px);
                        }
                        to {
                            opacity: 1;
                            transform: translateX(-50%) translateY(0);
                        }
                    }
                `;
            document.head.appendChild(tooltipStyle);

            this.style.position = 'relative';
            this.appendChild(tooltip);
        });

        btn.addEventListener('mouseleave', function () {
            const tooltip = this.querySelector('.action-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });

    // Performance optimization: Remove lazy loading that might cause issues
    // Just ensure images load properly
    const images = document.querySelectorAll('.variant-image');
    images.forEach(img => {
        img.addEventListener('load', function () {
            this.style.opacity = '1';
        });

        img.addEventListener('error', function () {
            // If image fails to load, show placeholder
            const wrapper = this.closest('.image-wrapper');
            if (wrapper) {
                wrapper.innerHTML = `
                        <div class="no-image">
                            <i class="fas fa-image"></i>
                            <small>Lỗi tải ảnh</small>
                        </div>
                    `;
            }
        });
    });

    // Enhanced keyboard navigation
    document.addEventListener('keydown', function (e) {
        // ESC key to close modals
        if (e.key === 'Escape') {
            closeDeleteModal();
            closeImagePreview();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Add smooth scrolling for better UX
    document.documentElement.style.scrollBehavior = 'smooth';

    // Performance monitoring
    if (window.performance && window.performance.mark) {
        window.performance.mark('enhanced-ui-loaded');
        console.log('Enhanced Product Variants UI loaded successfully');
    }
});
                                                        //create-multiple.blade
document.addEventListener('DOMContentLoaded', function () {
    console.log('Script loaded');

    const productSelector = document.getElementById('productSelector');
    const allVariantGroups = document.querySelectorAll('.product-variant-group');
    const submitSection = document.getElementById('submit-section');
    const form = document.getElementById('variantForm');

    // Function to disable validation for hidden elements
    const toggleValidation = (container, enable) => {
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (enable) {
                // Enable validation for visible elements
                if (input.hasAttribute('data-original-required')) {
                    input.setAttribute('required', '');
                    input.removeAttribute('data-original-required');
                }
            } else {
                // Disable validation for hidden elements
                if (input.hasAttribute('required')) {
                    input.setAttribute('data-original-required', '');
                    input.removeAttribute('required');
                }
            }
        });
    };

    // Product selector handler
    if (productSelector) {
        productSelector.addEventListener('change', function () {
            const selectedId = this.value;

            // Hide all groups and disable their validation
            allVariantGroups.forEach(group => {
                group.classList.add('d-none');
                toggleValidation(group, false); // Disable validation for hidden groups
            });

            if (selectedId) {
                const selectedGroup = document.querySelector(`[data-product-id="${selectedId}"]`);
                if (selectedGroup) {
                    selectedGroup.classList.remove('d-none');
                    toggleValidation(selectedGroup, true); // Enable validation for visible group
                    submitSection.style.display = 'block';
                }
            } else {
                submitSection.style.display = 'none';
            }
        });
    }

    // Initialize - disable validation for all hidden groups
    allVariantGroups.forEach(group => {
        if (group.classList.contains('d-none')) {
            toggleValidation(group, false);
        }
    });

    // Build SKU function
    const buildSku = (productCode, flavor, size) => {
        const cleanFlavor = flavor.replace(/\s+/g, '').replace(/[^a-zA-Z0-9]/g, '');
        const cleanSize = size.replace(/\s+/g, '').replace(/[^a-zA-Z0-9]/g, '');
        return `${productCode}-${cleanFlavor}-${cleanSize}`.toUpperCase();
    };

    // Format currency
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('vi-VN').format(value);
    };

    // Custom validation function (bypasses HTML5 validation)
    const customValidateForm = () => {
        const visibleGroup = document.querySelector('.product-variant-group:not(.d-none)');
        if (!visibleGroup) {
            alert('Vui lòng chọn sản phẩm để thêm biến thể!');
            return false;
        }

        // Check main attributes (flavors) - only for visible group
        const flavorInputs = visibleGroup.querySelectorAll('input[name*="[main_attribute][value]"]');
        for (let input of flavorInputs) {
            if (!input.value.trim()) {
                alert('Vui lòng nhập vị cho tất cả biến thể!');
                input.focus();
                return false;
            }
        }

        // Check size selections - only for visible group
        const sizeSelects = visibleGroup.querySelectorAll('select[name*="[attribute_value_id]"]');
        for (let select of sizeSelects) {
            if (!select.value) {
                alert('Vui lòng chọn size cho tất cả lựa chọn!');
                select.focus();
                return false;
            }
        }

        // Check prices - only for visible group
        const priceInputs = visibleGroup.querySelectorAll('input[name*="[price]"]');
        for (let input of priceInputs) {
            const price = parseFloat(input.value);
            if (!input.value || isNaN(price) || price < 0) {
                alert('Vui lòng nhập giá hợp lệ cho tất cả lựa chọn!');
                input.focus();
                return false;
            }
        }

        // Check quantities - only for visible group
        const quantityInputs = visibleGroup.querySelectorAll('input[name*="[quantity_in_stock]"]');
        for (let input of quantityInputs) {
            const quantity = parseInt(input.value);
            if (input.value === '' || isNaN(quantity) || quantity < 0) {
                alert('Vui lòng nhập số lượng hợp lệ cho tất cả lựa chọn!');
                input.focus();
                return false;
            }
        }

        return true;
    };

    // Setup events for product groups
    allVariantGroups.forEach((group) => {
        const productCodeEl = group.querySelector('.product-code');
        const originalPriceEl = group.querySelector('.original-price');

        if (!productCodeEl || !originalPriceEl) return;

        const productCode = productCodeEl.value;
        const originalPrice = parseFloat(originalPriceEl.value) || 0;
        let variantIndex = 1;

        // Function to update SKUs
        const updateSkus = (variantItem) => {
            try {
                const mainInput = variantItem.querySelector('input[name*="[main_attribute][value]"]');
                if (!mainInput) return;

                const flavor = mainInput?.value?.trim().toLowerCase() || '';
                if (!flavor) return;

                variantItem.querySelectorAll('.sub-attribute-row').forEach(row => {
                    const sizeSelect = row.querySelector('select');
                    const skuInput = row.querySelector('.sku-input');
                    if (sizeSelect && skuInput && sizeSelect.selectedIndex > 0) {
                        const sizeText = sizeSelect.options[sizeSelect.selectedIndex].text.trim();
                        if (flavor && sizeText) {
                            const sku = buildSku(productCode, flavor, sizeText);
                            skuInput.value = sku;
                        }
                    }
                });
            } catch (e) {
                console.error('Error updating SKUs:', e);
            }
        };

        // Function to validate duplicate sizes
        const validateDuplicateSizes = (variantItem) => {
            const sizeSelects = variantItem.querySelectorAll('.size-select');
            const selectedSizes = [];
            let isValid = true;

            // Clear previous errors
            variantItem.querySelectorAll('.duplicate-size-error').forEach(el => el.remove());
            sizeSelects.forEach(select => select.classList.remove('is-invalid'));

            sizeSelects.forEach(select => {
                const value = select.value;
                if (value) {
                    if (selectedSizes.includes(value)) {
                        isValid = false;
                        select.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'duplicate-size-error text-danger small mt-1';
                        errorDiv.textContent = 'Size này đã được chọn!';
                        select.parentNode.appendChild(errorDiv);
                    } else {
                        selectedSizes.push(value);
                    }
                }
            });

            return isValid;
        };

        // Attach events to variant item
        const attachVariantEvents = (variantItem) => {
            try {
                // Main attribute input
                const mainInput = variantItem.querySelector('input[name*="[main_attribute][value]"]');
                if (mainInput) {
                    mainInput.addEventListener('input', () => updateSkus(variantItem));
                    mainInput.addEventListener('blur', () => updateSkus(variantItem));
                }

                // Size selects
                variantItem.querySelectorAll('.size-select').forEach(select => {
                    select.addEventListener('change', () => {
                        updateSkus(variantItem);
                        validateDuplicateSizes(variantItem);
                    });
                });

                // Remove sub-attribute buttons
                variantItem.querySelectorAll('.remove-sub-attribute').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const row = e.target.closest('.sub-attribute-row');
                        const subGroup = row.closest('.sub-attributes-group');
                        if (subGroup.querySelectorAll('.sub-attribute-row').length > 1) {
                            row.remove();
                            validateDuplicateSizes(variantItem);
                        } else {
                            alert('Mỗi biến thể phải có ít nhất một size!');
                        }
                    });
                });

                // Add sub-attribute button
                const addSubBtn = variantItem.querySelector('.add-sub-attribute');
                if (addSubBtn) {
                    addSubBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const container = variantItem.querySelector('.sub-attributes-group');
                        const rows = container.querySelectorAll('.sub-attribute-row');
                        const firstRow = rows[0];
                        const newRow = firstRow.cloneNode(true);
                        const subIdx = rows.length;

                        // Clear values and remove required attribute temporarily
                        newRow.querySelectorAll('input, select').forEach(el => {
                            if (el.type !== 'hidden') {
                                el.value = '';
                                el.classList.remove('is-invalid');
                            }
                            // Update names
                            if (el.name) {
                                el.name = el.name.replace(/sub_attributes\[\d+\]/g, `sub_attributes[${subIdx}]`);
                            }
                        });

                        newRow.querySelectorAll('.duplicate-size-error, .price-error').forEach(el => el.remove());

                        container.appendChild(newRow);

                        // Re-attach events for the new row
                        attachSubAttributeEvents(newRow, variantItem);
                    });
                }

                // Remove variant button
                const removeBtn = variantItem.querySelector('.remove-variant');
                if (removeBtn) {
                    removeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (group.querySelectorAll('.variant-item').length > 1) {
                            variantItem.remove();
                        } else {
                            alert('Phải có ít nhất một biến thể!');
                        }
                    });
                }
            } catch (e) {
                console.error('Error attaching variant events:', e);
            }
        };

        // Separate function to attach events to sub-attribute rows
        const attachSubAttributeEvents = (row, variantItem) => {
            // Size select
            const sizeSelect = row.querySelector('.size-select');
            if (sizeSelect) {
                sizeSelect.addEventListener('change', () => {
                    updateSkus(variantItem);
                    validateDuplicateSizes(variantItem);
                });
            }

            // Remove button
            const removeBtn = row.querySelector('.remove-sub-attribute');
            if (removeBtn) {
                removeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const subGroup = row.closest('.sub-attributes-group');
                    if (subGroup.querySelectorAll('.sub-attribute-row').length > 1) {
                        row.remove();
                        validateDuplicateSizes(variantItem);
                    } else {
                        alert('Mỗi biến thể phải có ít nhất một size!');
                    }
                });
            }
        };

        // Add variant button
        const addVariantBtn = group.querySelector('.add-variant-btn');
        if (addVariantBtn) {
            addVariantBtn.addEventListener('click', (e) => {
                e.preventDefault();
                try {
                    const container = group.querySelector('.variants-container');
                    const template = container.querySelector('.variant-item');
                    const newVariant = template.cloneNode(true);
                    const newIdx = variantIndex++;

                    // Clear and update
                    newVariant.querySelectorAll('input, select').forEach(el => {
                        if (el.type !== 'file' && el.type !== 'hidden') {
                            el.value = '';
                            el.classList.remove('is-invalid');
                        }
                        if (el.name) {
                            el.name = el.name.replace(/variants\[\d+\]/g, `variants[${newIdx}]`)
                                .replace(/sub_attributes\[\d+\]/g, 'sub_attributes[0]');
                        }
                    });

                    newVariant.querySelectorAll('.duplicate-size-error, .price-error').forEach(el => el.remove());
                    const titleEl = newVariant.querySelector('h6');
                    if (titleEl) {
                        titleEl.innerHTML = `<i class="fas fa-cube me-2"></i>Biến thể #${newIdx + 1}`;
                    }

                    container.appendChild(newVariant);
                    attachVariantEvents(newVariant);
                } catch (e) {
                    console.error('Error adding variant:', e);
                }
            });
        }

        // Initialize existing variants
        group.querySelectorAll('.variant-item').forEach(item => {
            attachVariantEvents(item);
        });
    });

    // Form submit handler with custom validation
    if (form) {
        form.addEventListener('submit', function (e) {
            console.log('Form submit event triggered');
            e.preventDefault();

            try {
                // Use custom validation instead of HTML5 validation
                if (!customValidateForm()) {
                    return false;
                }

                // Show loading state
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...';
                }

                // Create FormData and include CSRF token
                const formData = new FormData(form);

                // Submit via fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json, text/plain, */*'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            // Check if response is JSON
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json();
                            } else {
                                // If it's a redirect response, follow it
                                if (response.redirected) {
                                    window.location.href = response.url;
                                    return;
                                }
                                // For other successful responses, redirect to index
                                window.location.href = '/admin/product-variants';
                                return;
                            }
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Network response was not ok');
                            });
                        }
                    })
                    .then(data => {
                        if (data) {
                            if (data.success) {
                                // Show success message
                                alert(data.message || 'Biến thể đã được tạo thành công!');
                                // Redirect to success page or refresh
                                window.location.href = data.redirect || '/admin/product-variants';
                            } else {
                                // Show error message
                                alert(data.message || 'Có lỗi xảy ra khi lưu dữ liệu!');
                                throw new Error(data.message || 'Validation failed');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi lưu dữ liệu: ' + error.message);
                    })
                    .finally(() => {
                        // Reset button state
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Lưu tất cả biến thể';
                        }
                    });

            } catch (e) {
                console.error('Error in form submit handler:', e);
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Lưu tất cả biến thể';
                }
            }
        });
    }

    console.log('Script initialization completed');
});




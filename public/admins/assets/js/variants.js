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
        errorDiv.className = 'price-error text-danger small mt-1';
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
// Function to validate duplicate sizes within a variant
function validateDuplicateSizes(variantItem) {
    const sizeSelects = variantItem.querySelectorAll('select[name*="[attribute_value_id]"]');
    const selectedSizes = [];
    let hasError = false;

    // Clear previous errors
    variantItem.querySelectorAll('.size-error').forEach(el => el.remove());
    sizeSelects.forEach(select => {
        select.classList.remove('is-invalid');
        select.style.borderColor = '';
    });

    sizeSelects.forEach((select, index) => {
        const selectedValue = select.value;

        if (selectedValue && selectedValue !== '') {
            if (selectedSizes.includes(selectedValue)) {
                // Mark as duplicate
                select.classList.add('is-invalid');
                select.style.borderColor = '#dc3545';
                hasError = true;

                // Add error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'size-error text-danger small mt-1';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Size này đã được chọn!';
                select.parentNode.appendChild(errorDiv);
            } else {
                selectedSizes.push(selectedValue);
            }
        }
    });

    return !hasError;
}
// Function to update available options (disable selected sizes)
function updateSizeOptions(variantItem) {
    const sizeSelects = variantItem.querySelectorAll('select[name*="[attribute_value_id]"]');
    const selectedSizes = [];

    // Get all selected sizes
    sizeSelects.forEach(select => {
        if (select.value && select.value !== '') {
            selectedSizes.push(select.value);
        }
    });

    // Update each select's options
    sizeSelects.forEach(currentSelect => {
        const options = currentSelect.querySelectorAll('option');

        options.forEach(option => {
            if (option.value === '') return; // Skip empty option

            // If option is selected in another select, disable it
            if (selectedSizes.includes(option.value) && currentSelect.value !== option.value) {
                option.disabled = true;
                option.style.color = '#999';
                option.style.fontStyle = 'italic';
                if (!option.textContent.includes('(Đã chọn)')) {
                    option.textContent = option.textContent + ' (Đã chọn)';
                }
            } else {
                option.disabled = false;
                option.style.color = '';
                option.style.fontStyle = '';
                option.textContent = option.textContent.replace(' (Đã chọn)', '');
            }
        });
    });
}
function updateSKUs(variantItem) {
    const productCode = document.getElementById('product_code')?.value?.trim() || 'SP';
    const mainAttr = variantItem.querySelector('input[name*="[main_attribute][value]"]')?.value?.trim()?.toUpperCase()
        ?.replace(/\s+/g, '-') || '';

    const subRows = variantItem.querySelectorAll('.sub-attribute-row');
    subRows.forEach(row => {
        const sizeSelect = row.querySelector('select[name*="[attribute_value_id]"]');
        const skuInput = row.querySelector('.sku-input');

        if (sizeSelect && skuInput) {
            const selectedSizeText = sizeSelect.selectedOptions[0]?.text?.trim().toUpperCase().replace(/\s+/g, '-').replace(' (ĐÃ CHỌN)', '') || '';

            // Only generate SKU if size is selected
            if (sizeSelect.value && sizeSelect.value !== '') {
                let sku = productCode;
                if (mainAttr) sku += `-${mainAttr}`;
                if (selectedSizeText) sku += `-${selectedSizeText}`;

                skuInput.value = sku;
            } else {
                skuInput.value = '';
            }
        }
    });
}
function updatePreviewTable() {
    const previewBody = document.getElementById('preview-variants-body');
    if (!previewBody) return;

    previewBody.innerHTML = '';

    const variantItems = document.querySelectorAll('.variant-item');
    const productCodeInput = document.getElementById('product_code');
    const productCode = productCodeInput ? productCodeInput.value.trim() : 'SP';

    variantItems.forEach((variantEl, variantIndex) => {
        const flavorInput = variantEl.querySelector('[name^="variants[' + variantIndex + '][main_attribute][value]"]');
        const flavor = flavorInput ? flavorInput.value.trim() : '';

        const subRows = variantEl.querySelectorAll('.sub-attribute-row');

        subRows.forEach((row, subIndex) => {
            const sizeSelect = row.querySelector('select');
            const size = sizeSelect?.selectedOptions[0]?.text?.trim().replace(' (Đã chọn)', '') || '';

            // Only show in preview if size is selected
            if (sizeSelect && sizeSelect.value && sizeSelect.value !== '') {
                const priceInput = row.querySelector('input[name*="[price]"]');
                const quantityInput = row.querySelector('input[name*="[quantity_in_stock]"]');
                const skuInput = row.querySelector('input[name*="[sku]"]');

                const price = priceInput?.value || '0';
                const quantity = quantityInput?.value || '0';

                if (skuInput) {
                    const sku = `${productCode}-${flavor.toUpperCase()}-${size.toUpperCase()}`.replace(/\s+/g, '-');
                    skuInput.value = sku;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">${window.productName || 'Sản phẩm'}</td>
                    <td class="text-center">${flavor}</td>
                    <td class="text-center">${size}</td>
                    <td class="text-center text-muted"><i class="fas fa-image"></i></td>
                    <td class="text-end">${Number(price).toLocaleString()} VND</td>
                    <td class="text-center">${quantity}</td>
                    <td class="text-center">${skuInput?.value || ''}</td>
                `;
                previewBody.appendChild(tr);
            }
        });
    });
}
// Enhanced function to handle size selection changes
function handleSizeChange(select) {
    const variantItem = select.closest('.variant-item');

    // Validate duplicates
    validateDuplicateSizes(variantItem);

    // Update available options
    updateSizeOptions(variantItem);

    // Update SKUs
    updateSKUs(variantItem);

    // Update preview table
    updatePreviewTable();
}
// Enhanced function to attach events to sub-attribute rows
function attachSubAttributeEvents(row, variantItem) {
    // Size select change event
    const sizeSelect = row.querySelector('select[name*="[attribute_value_id]"]');
    if (sizeSelect) {
        // Remove existing event listeners to prevent duplicates
        sizeSelect.replaceWith(sizeSelect.cloneNode(true));
        const newSizeSelect = row.querySelector('select[name*="[attribute_value_id]"]');

        newSizeSelect.addEventListener('change', function () {
            handleSizeChange(this);
        });
    }

    // Remove button event - Fixed icon issue
    const removeBtn = row.querySelector('.remove-sub-attribute');
    if (removeBtn) {
        // Clone the button to remove all existing event listeners
        const newRemoveBtn = removeBtn.cloneNode(true);
        removeBtn.parentNode.replaceChild(newRemoveBtn, removeBtn);

        newRemoveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const subAttributesTable = this.closest('.sub-attributes-table');
            const rows = subAttributesTable.querySelectorAll('.sub-attribute-row');

            if (rows.length > 1) {
                // Add animation before removing
                const rowToRemove = this.closest('.sub-attribute-row');
                rowToRemove.style.animation = 'fadeOut 0.3s ease-in';

                setTimeout(() => {
                    rowToRemove.remove();

                    // Re-validate after removal
                    validateDuplicateSizes(variantItem);
                    updateSizeOptions(variantItem);
                    updateSKUs(variantItem);
                    updatePreviewTable();
                }, 300);
            } else {
                alert('Mỗi biến thể phải có ít nhất một size!');
            }
        });
    }

    // Price and quantity inputs
    const priceInput = row.querySelector('input[name*="[price]"]');
    const quantityInput = row.querySelector('input[name*="[quantity_in_stock]"]');

    if (priceInput) {
        priceInput.addEventListener('input', function () {
            validatePrice(this);
            updatePreviewTable();
        });

        priceInput.addEventListener('blur', function () {
            validatePrice(this);
        });
    }

    if (quantityInput) {
        quantityInput.addEventListener('input', updatePreviewTable);
    }
}
function attachEvents(variantItem) {
    // Main attribute (flavor) input
    const flavorInput = variantItem.querySelector('input[name*="[main_attribute][value]"]');
    if (flavorInput) {
        flavorInput.addEventListener('input', function () {
            updateSKUs(variantItem);
            updatePreviewTable();

            // Kiểm tra trùng vị khi nhập
            const allFlavorInputs = document.querySelectorAll('input[name*="[main_attribute][value]"]');
            const currentValue = this.value.trim().toLowerCase();
            let count = 0;
            allFlavorInputs.forEach(input => {
                if (input.value.trim().toLowerCase() === currentValue) count++;
            });

            const parent = this.parentNode;
            const oldError = parent.querySelector('.flavor-error');
            if (count > 1) {
                this.classList.add('is-invalid');
                if (!oldError) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'flavor-error text-danger small mt-1';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Vị này đã được thêm!';
                    parent.appendChild(errorDiv);
                }
            } else {
                this.classList.remove('is-invalid');
                if (oldError) oldError.remove();
            }
        });
    }
    // Attach events to all existing sub-attribute rows
    const subRows = variantItem.querySelectorAll('.sub-attribute-row');
    subRows.forEach(row => {
        attachSubAttributeEvents(row, variantItem);
    });

    // Add sub-attribute button
    const addSubBtn = variantItem.querySelector('.add-sub-attribute');
    if (addSubBtn) {
        // Clone to remove existing event listeners
        const newAddSubBtn = addSubBtn.cloneNode(true);
        addSubBtn.parentNode.replaceChild(newAddSubBtn, addSubBtn);

        newAddSubBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const subAttributesTable = variantItem.querySelector('.sub-attributes-table');
            const rows = subAttributesTable.querySelectorAll('.sub-attribute-row');
            const lastRow = rows[rows.length - 1];

            // Clone the last row
            const newRow = lastRow.cloneNode(true);

            // Get variant index
            const variantIdx = variantItem.querySelector('input[name^="variants["]')?.name?.match(/variants\[(\d+)\]/)?.[1] || '0';
            const subIdx = rows.length;

            // Clear values and update names
            newRow.querySelectorAll('input, select').forEach(el => {
                if (el.type !== 'hidden') {
                    el.value = '';
                    el.selectedIndex = 0; // Reset to first option (should be "Chọn size")
                }
                el.classList.remove('is-invalid');

                // Update names with correct indices
                if (el.name) {
                    if (el.name.includes('[attribute_value_id]')) {
                        el.name = `variants[${variantIdx}][sub_attributes][${subIdx}][attribute_value_id]`;
                    } else if (el.name.includes('[price]')) {
                        el.name = `variants[${variantIdx}][sub_attributes][${subIdx}][price]`;
                    } else if (el.name.includes('[quantity_in_stock]')) {
                        el.name = `variants[${variantIdx}][sub_attributes][${subIdx}][quantity_in_stock]`;
                    } else if (el.name.includes('[image]')) {
                        el.name = `variants[${variantIdx}][sub_attributes][${subIdx}][image]`;
                    } else if (el.name.includes('[sku]')) {
                        el.name = `variants[${variantIdx}][sub_attributes][${subIdx}][sku]`;
                    }
                }
            });

            // Remove any error messages
            newRow.querySelectorAll('.size-error, .price-error, .invalid-feedback').forEach(el => el.remove());

            // Add the new row
            subAttributesTable.appendChild(newRow);

            // Attach events to the new row
            attachSubAttributeEvents(newRow, variantItem);

            // Update validation and options
            updateSizeOptions(variantItem);
            updateSKUs(variantItem);
            updatePreviewTable();
        });
    }

    // Remove variant button
    const removeVariantBtn = variantItem.querySelector('.remove-variant');
    if (removeVariantBtn) {
        // Clone to remove existing event listeners
        const newRemoveVariantBtn = removeVariantBtn.cloneNode(true);
        removeVariantBtn.parentNode.replaceChild(newRemoveVariantBtn, removeVariantBtn);

        newRemoveVariantBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const variantItems = document.querySelectorAll('.variant-item');
            if (variantItems.length > 1) {
                const variantToRemove = this.closest('.variant-item');
                variantToRemove.style.animation = 'slideOutDown 0.3s ease-in';

                setTimeout(() => {
                    variantToRemove.remove();
                    updatePreviewTable();
                }, 300);
            } else {
                alert('Phải có ít nhất một biến thể!');
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    const variantsContainer = document.getElementById('variants-container');

    // Reset all size selects to "Chọn size" option
    function resetSizeSelects() {
        const sizeSelects = document.querySelectorAll('select[name*="[attribute_value_id]"]');
        sizeSelects.forEach(select => {
            // Ensure first option is empty/default
            if (select.options.length > 0) {
                select.selectedIndex = 0;
                // If first option doesn't have empty value, add it
                if (select.options[0].value !== '') {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Chọn khối lượng';
                    defaultOption.selected = true;
                    select.insertBefore(defaultOption, select.firstChild);
                } else {
                    // Update text of existing empty option
                    select.options[0].textContent = 'Chọn khối lượng';
                }
            }
        });
    }

    // Initialize existing variants
    const existingVariants = document.querySelectorAll('.variant-item');
    existingVariants.forEach(variant => {
        attachEvents(variant);
        updateSizeOptions(variant);
        updateSKUs(variant);
    });

    // Reset all size selects
    resetSizeSelects();

    // Initialize preview table
    updatePreviewTable();

    // Add variant button

    const addVariantBtn = document.getElementById('add-variant');
if (addVariantBtn) {
    addVariantBtn.addEventListener('click', function (e) {
        e.preventDefault();

        const template = document.querySelector('.variant-item');
        if (!template) return;

        const clone = template.cloneNode(true);

        // Tính lại số lượng biến thể hiện tại để đặt chỉ số mới
        const currentVariantCount = document.querySelectorAll('.variant-item').length;

        // Cập nhật tiêu đề biến thể
        clone.querySelector('h5').innerHTML = `<i class="fas fa-cube me-2"></i>Biến thể #${currentVariantCount + 1}`;

        // Clear & cập nhật input/select
        clone.querySelectorAll('input, select').forEach(el => {
            if (el.type !== 'file' && el.type !== 'hidden') {
                el.value = '';
                el.selectedIndex = 0;
            }

            el.classList.remove('is-invalid');

            // Cập nhật lại name theo chỉ số mới
            if (el.name) {
                el.name = el.name.replace(/variants\[\d+\]/g, `variants[${currentVariantCount}]`);
                el.name = el.name.replace(/sub_attributes\[\d+\]/g, 'sub_attributes[0]');
            }
        });

        // Xử lý input vị (main_attribute[value])
        const flavorInput = clone.querySelector('input[name*="[main_attribute][value]"]');
        if (flavorInput) {
            flavorInput.value = '';
            flavorInput.classList.remove('is-invalid');

            flavorInput.name = `variants[${currentVariantCount}][main_attribute][value]`;

            // Xoá lỗi cũ nếu có
            const oldError = flavorInput.parentNode.querySelector('.flavor-error');
            if (oldError) oldError.remove();

            // Xóa input hidden cũ và thêm mới
            const oldHidden = clone.querySelector('input[name*="[main_attribute][name]"]');
            if (oldHidden) oldHidden.remove();

            const attrNameInput = document.createElement('input');
            attrNameInput.type = 'hidden';
            attrNameInput.name = `variants[${currentVariantCount}][main_attribute][name]`;
            attrNameInput.value = 'Vị';
            flavorInput.insertAdjacentElement('afterend', attrNameInput);
        }

        // Xoá lỗi hiển thị cũ
        clone.querySelectorAll('.size-error, .price-error, .invalid-feedback').forEach(el => el.remove());

        // Chỉ giữ lại dòng size đầu tiên
        const subRows = clone.querySelectorAll('.sub-attribute-row');
        for (let i = 1; i < subRows.length; i++) {
            subRows[i].remove();
        }

        // Reset dropdown size
        const sizeSelects = clone.querySelectorAll('select[name*="[attribute_value_id]"]');
        sizeSelects.forEach(select => {
            select.selectedIndex = 0;
            if (select.options[0].value !== '') {
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Chọn khối lượng';
                defaultOption.selected = true;
                select.insertBefore(defaultOption, select.firstChild);
            } else {
                select.options[0].textContent = 'Chọn khối lượng';
            }
        });

        // Thêm vào DOM
        variantsContainer.appendChild(clone);

        // Kích hoạt các sự kiện
        attachEvents(clone);
        updateSizeOptions(clone);
        updateSKUs(clone);
        updatePreviewTable();
    });
}




    // Form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            let hasError = false;

            // Check if any size is not selected
            const sizeSelects = document.querySelectorAll('select[name*="[attribute_value_id]"]');
            sizeSelects.forEach(select => {
                if (!select.value || select.value === '') {
                    select.classList.add('is-invalid');
                    hasError = true;

                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'size-error text-danger small mt-1';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Vui lòng chọn khối lượng!';

                    // Remove existing error first
                    const existingError = select.parentNode.querySelector('.size-error');
                    if (existingError) existingError.remove();

                    select.parentNode.appendChild(errorDiv);
                }
            });

            // Validate all variants
            document.querySelectorAll('.variant-item').forEach(variant => {
                if (!validateDuplicateSizes(variant)) {
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('Vui lòng kiểm tra lại! Phải chọn size cho tất cả biến thể và không được chọn khối lượng trùng lặp.');
                return false;
            }

            // Other existing validations...
            const visibleGroup = document.querySelector('.product-variant-group:not(.d-none)');
            if (visibleGroup) {
                const originalPrice = parseFloat(visibleGroup.querySelector('.original-price')?.value || 0);

                // Validate prices
                const priceInputs = document.querySelectorAll('input[name*="[price]"]');
                priceInputs.forEach(input => {
                    const price = parseFloat(input.value);
                    if (price < 0) {
                        hasError = true;
                        showPriceError(input, 'Giá không được âm');
                    } else if (price < originalPrice) {
                        hasError = true;
                        showPriceError(input, `Giá biến thể không được thấp hơn giá gốc: ${formatVND(originalPrice)}`);
                    }
                });

                // Validate quantities
                const quantityInputs = document.querySelectorAll('input[name*="[quantity_in_stock]"]');
                quantityInputs.forEach(input => {
                    const quantity = parseInt(input.value);
                    if (isNaN(quantity) || quantity <= 0) {
                        input.classList.add('is-invalid');
                        hasError = true;
                    }
                });
            }

            if (hasError) {
                e.preventDefault();
                alert('Vui lòng kiểm tra lại thông tin đã nhập!');
                return false;
            }
        });
    }
});

// Add necessary CSS styles
const style = document.createElement('style');
style.textContent = `
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .size-error, .price-error {
        display: block !important;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    select option:disabled {
        color: #999 !important;
        font-style: italic !important;
        background-color: #f8f9fa !important;
    }
    
    select option[value=""] {
        color: #6c757d;
        font-style: italic;
    }
    
    @keyframes slideOutDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100px);
            opacity: 0;
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    
    .remove-sub-attribute, .remove-variant {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .remove-sub-attribute:hover, .remove-variant:hover {
        transform: scale(1.1);
        color: #dc3545;
    }
`;
document.head.appendChild(style);
/*......edit....*/
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

    // === Tiện ích ===
    const normalize = str => (str || '').toString().trim().toLowerCase().replace(/\s+/g, '-');
    const buildSku = (code, flavor, weight) =>
        `${code}-${normalize(flavor)}-${normalize(weight)}`.toUpperCase();

    // === Hiển thị nhóm biến thể theo sản phẩm ===
    function handleProductSelection() {
        const selector = document.getElementById('productSelector');
        const groups = document.querySelectorAll('.product-variant-group');
        const submitSection = document.getElementById('submit-section');

        selector?.addEventListener('change', function () {
            const selectedId = this.value;
            groups.forEach(group => {
                if (group.dataset.productId === selectedId) {
                    group.style.display = 'block';
                    submitSection.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            });
        });
    }

    // === Tự động sinh SKU ===
    function handleSkuAutoGenerate() {
        document.body.addEventListener('change', function (e) {
            if (
                e.target.matches('.weight-select, .size-select') ||
                e.target.matches('.main-attribute-input')
            ) {
                const row = e.target.closest('.sub-attribute-row');
                const variantItem = e.target.closest('.variant-item');
                const group = e.target.closest('.product-variant-group');

                const flavorInput = variantItem.querySelector('.main-attribute-input');
                const weightSelect = row.querySelector('.weight-select, .size-select');
                const skuInput = row.querySelector('.sku-input');
                const productCode = group.querySelector('.product-code')?.value;

                const flavor = flavorInput?.value.trim();
                const weight = weightSelect?.selectedOptions[0]?.text.trim();

                if (productCode && flavor && weight) {
                    skuInput.value = buildSku(productCode, flavor, weight);
                }
            }
        });
    }

    // === Thêm biến thể ===
    function handleAddVariant() {
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.add-variant-btn');
            if (!btn) return;

            const group = btn.closest('.product-variant-group');
            if (!group) return;

            const container = group.querySelector('.variants-container');
            const productId = group.dataset.productId;
            const variantItems = container.querySelectorAll('.variant-item');
            const newIndex = variantItems.length;
            const firstVariant = variantItems[0];
            if (!firstVariant) return;

            const newVariant = firstVariant.cloneNode(true);
            newVariant.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
                el.classList.remove('is-invalid');
            });

            newVariant.querySelectorAll('[name]').forEach(el => {
                const oldName = el.name;
                const updatedName = oldName
                    .replace(/products\[\d+]\[variants]\[\d+]/,
                        `products[${productId}][variants][${newIndex}]`)
                    .replace(/\[sub_attributes]\[\d+]/g, `[sub_attributes][0]`);
                el.name = updatedName;
            });

            const header = newVariant.querySelector('.card-header h6');
            if (header) {
                header.innerHTML = `<i class="fas fa-cube me-2"></i> Biến thể #${newIndex + 1}`;
            }

            container.appendChild(newVariant);
            updateDisabledWeights(group);
        });
    }

    // === Xoá biến thể ===
    function handleRemoveVariant() {
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-variant');
            if (!btn) return;

            const item = btn.closest('.variant-item');
            const container = item.closest('.variants-container');

            if (container.querySelectorAll('.variant-item').length > 1) {
                item.remove();
                updateDisabledWeights(container.closest('.product-variant-group'));
            } else {
                alert('Phải có ít nhất 1 biến thể.');
            }
        });
    }

    // === Thêm khối lượng ===
    function handleAddSubAttribute() {
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.add-sub-attribute');
            if (!btn) return;

            const variantItem = btn.closest('.variant-item');
            const subAttrGroup = variantItem.querySelector('.sub-attributes-group');
            const subAttrRows = subAttrGroup.querySelectorAll('.sub-attribute-row');
            const newIndex = subAttrRows.length;
            const firstRow = subAttrRows[0];
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('input, select').forEach(input => {
                input.value = '';
                input.classList.remove('is-invalid');
            });

            newRow.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[sub_attributes]\[\d+]/g,
                    `[sub_attributes][${newIndex}]`);
            });

            subAttrGroup.appendChild(newRow);
            updateDisabledWeights(variantItem.closest('.product-variant-group'));
        });
    }

    // === Xoá khối lượng ===
    function handleRemoveSubAttribute() {
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-sub-attribute');
            if (!btn) return;

            const row = btn.closest('.sub-attribute-row');
            const group = row.closest('.sub-attributes-group');

            if (group.querySelectorAll('.sub-attribute-row').length > 1) {
                row.remove();
                updateDisabledWeights(group.closest('.product-variant-group'));
            } else {
                alert('Phải có ít nhất 1 khối lượng cho mỗi biến thể.');
            }
        });
    }

    // === Cập nhật khối lượng đã chọn để ẩn option ===
    function updateDisabledWeights(group) {
        group.querySelectorAll('.variant-item').forEach(variant => {
            const usedWeights = new Set();
            const rows = variant.querySelectorAll('.sub-attribute-row');

            // Thu thập các weight đã chọn trong cùng 1 biến thể
            rows.forEach(row => {
                const select = row.querySelector('.weight-select, .size-select');
                if (select?.value) {
                    usedWeights.add(select.value);
                }
            });

            // Disable trong cùng biến thể nếu trùng
            rows.forEach(row => {
                const select = row.querySelector('.weight-select, .size-select');
                const currentValue = select?.value;
                const options = select?.querySelectorAll('option') || [];
                options.forEach(option => {
                    if (option.value === '') return;
                    option.disabled = usedWeights.has(option.value) && option
                        .value !== currentValue;
                });
            });
        });
    }


    // === Validate trước khi submit ===
    function handleFormValidation() {
        const submitBtn = document.getElementById('save-variants');
        if (!submitBtn) return;

        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const selectedGroup = document.querySelector(
                '.product-variant-group:not([style*="display: none"])');
            if (!selectedGroup) return;

            const variantItems = selectedGroup.querySelectorAll('.variant-item');

            const flavorMap = new Map();

            for (let i = 0; i < variantItems.length; i++) {
                const variant = variantItems[i];
                const flavorInput = variant.querySelector('.main-attribute-input');
                const flavor = normalize(flavorInput?.value);

                // Kiểm tra ô vị
                if (!flavor) {
                    flavorInput?.classList.add('is-invalid');
                    alert(`Vui lòng nhập vị cho biến thể thứ ${i + 1}`);
                    return;
                }

                if (flavorMap.has(flavor)) {
                    flavorInput?.classList.add('is-invalid');
                    alert(`Vị "${flavorInput.value}" bị trùng!`);
                    return;
                } else {
                    flavorMap.set(flavor, true);
                    flavorInput?.classList.remove('is-invalid');
                }

                const subRows = variant.querySelectorAll('.sub-attribute-row');
                const weightSet = new Set();
                const existingVariants = JSON.parse(selectedGroup.querySelector(
                    '.existing-variants')?.value || '[]');

                for (let row of subRows) {
                    const weightSelect = row.querySelector('.weight-select, .size-select');
                    const selectedWeight = normalize(weightSelect?.selectedOptions[0]?.text);

                    const priceInput = row.querySelector('.price-input');
                    const quantityInput = row.querySelector('.quantity-input');

                    // Kiểm tra đã chọn khối lượng chưa
                    if (!selectedWeight) {
                        weightSelect?.classList.add('is-invalid');
                        alert(`Vui lòng chọn khối lượng cho vị "${flavorInput.value}"`);
                        return;
                    }

                    // Trùng khối lượng trong cùng vị
                    if (weightSet.has(selectedWeight)) {
                        weightSelect?.classList.add('is-invalid');
                        alert(
                            `Khối lượng "${selectedWeight}" bị trùng trong cùng vị "${flavorInput.value}"`
                        );
                        return;
                    } else {
                        weightSet.add(selectedWeight);
                        weightSelect?.classList.remove('is-invalid');
                    }

                    // Trùng với dữ liệu đã có
                    if (existingVariants.some(item =>
                        normalize(item.flavor) === flavor && normalize(item.size) ===
                        selectedWeight)) {
                        weightSelect?.classList.add('is-invalid');
                        alert(
                            `Khối lượng "${selectedWeight}" cho vị "${flavorInput.value}" đã tồn tại trong hệ thống!`
                        );
                        return;
                    }

                    // Kiểm tra giá
                    const price = parseFloat(priceInput?.value);
                    if (!priceInput || isNaN(price) || price <= 0) {
                        priceInput?.classList.add('is-invalid');
                        alert(
                            `Giá không hợp lệ cho vị "${flavorInput.value}" và khối lượng "${selectedWeight}"`
                        );
                        return;
                    } else {
                        priceInput?.classList.remove('is-invalid');
                    }

                    // Kiểm tra số lượng
                    const quantity = parseInt(quantityInput?.value);
                    if (!quantityInput || isNaN(quantity) || quantity <= 0) {
                        quantityInput?.classList.add('is-invalid');
                        alert(
                            `Số lượng không hợp lệ cho vị "${flavorInput.value}" và khối lượng "${selectedWeight}"`
                        );
                        return;
                    } else {
                        quantityInput?.classList.remove('is-invalid');
                    }
                }
            }

            // Nếu tất cả hợp lệ → Submit form
            selectedGroup.closest('form').submit();
        });
    }

    function handleLiveFlavorDuplicateCheck() {
        document.body.addEventListener('input', function (e) {
            if (!e.target.classList.contains('main-attribute-input')) return;

            const currentInput = e.target;
            const currentValue = normalize(currentInput.value);
            const allFlavorInputs = document.querySelectorAll('.main-attribute-input');

            let isDuplicate = false;

            allFlavorInputs.forEach(input => {
                const value = normalize(input.value);

                if (
                    value !== '' &&
                    value === currentValue &&
                    input !== currentInput
                ) {
                    isDuplicate = true;
                }
            });

            // Xử lý hiển thị lỗi
            if (isDuplicate) {
                currentInput.classList.add('is-invalid');
                // Nếu chưa có thông báo lỗi thì thêm
                if (!currentInput.nextElementSibling || !currentInput.nextElementSibling.classList
                    .contains('invalid-feedback')) {
                    const msg = document.createElement('div');
                    msg.classList.add('invalid-feedback');
                    msg.textContent = 'Vị này đã được thêm ở biến thể khác!';
                    currentInput.insertAdjacentElement('afterend', msg);
                }
            } else {
                currentInput.classList.remove('is-invalid');
                const next = currentInput.nextElementSibling;
                if (next && next.classList.contains('invalid-feedback')) {
                    next.remove();
                }
            }
        });
    }




    // === Khởi chạy toàn bộ ===
    function init() {
        handleProductSelection();
        handleSkuAutoGenerate();
        handleAddVariant();
        handleRemoveVariant();
        handleAddSubAttribute();
        handleRemoveSubAttribute();
        handleFormValidation();
        handleLiveFlavorDuplicateCheck();
    }

    init(); // Bắt đầu
});










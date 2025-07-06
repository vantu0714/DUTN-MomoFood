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
        
        newSizeSelect.addEventListener('change', function() {
            handleSizeChange(this);
        });
    }

    // Remove button event - Fixed icon issue
    const removeBtn = row.querySelector('.remove-sub-attribute');
    if (removeBtn) {
        // Clone the button to remove all existing event listeners
        const newRemoveBtn = removeBtn.cloneNode(true);
        removeBtn.parentNode.replaceChild(newRemoveBtn, removeBtn);
        
        newRemoveBtn.addEventListener('click', function(e) {
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
        priceInput.addEventListener('input', function() {
            validatePrice(this);
            updatePreviewTable();
        });
        
        priceInput.addEventListener('blur', function() {
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
        flavorInput.addEventListener('input', function() {
            updateSKUs(variantItem);
            updatePreviewTable();
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
        
        newAddSubBtn.addEventListener('click', function(e) {
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
        
        newRemoveVariantBtn.addEventListener('click', function(e) {
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
document.addEventListener('DOMContentLoaded', function() {
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
                    defaultOption.textContent = 'Chọn size';
                    defaultOption.selected = true;
                    select.insertBefore(defaultOption, select.firstChild);
                } else {
                    // Update text of existing empty option
                    select.options[0].textContent = 'Chọn size';
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
        addVariantBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const template = document.querySelector('.variant-item');
            if (!template) return;
            
            const clone = template.cloneNode(true);
            
            // Update variant title
            clone.querySelector('h5').innerHTML = `<i class="fas fa-cube me-2"></i>Biến thể #${variantIndex + 1}`;
            
            // Clear and update form elements
            clone.querySelectorAll('input, select').forEach(el => {
                if (el.type !== 'file' && el.type !== 'hidden') {
                    el.value = '';
                    el.selectedIndex = 0;
                }
                el.classList.remove('is-invalid');
                
                // Update names with new variant index
                if (el.name) {
                    el.name = el.name.replace(/variants\[\d+\]/, `variants[${variantIndex}]`);
                    el.name = el.name.replace(/sub_attributes\[\d+\]/g, 'sub_attributes[0]');
                }
            });
            
            // Remove error messages
            clone.querySelectorAll('.size-error, .price-error, .invalid-feedback').forEach(el => el.remove());
            
            // Keep only the first sub-attribute row
            const subRows = clone.querySelectorAll('.sub-attribute-row');
            for (let i = 1; i < subRows.length; i++) {
                subRows[i].remove();
            }
            
            // Reset size selects in new variant
            const sizeSelects = clone.querySelectorAll('select[name*="[attribute_value_id]"]');
            sizeSelects.forEach(select => {
                select.selectedIndex = 0;
                if (select.options[0].value !== '') {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Chọn size';
                    defaultOption.selected = true;
                    select.insertBefore(defaultOption, select.firstChild);
                } else {
                    select.options[0].textContent = 'Chọn size';
                }
            });
            
            // Add to container
            variantsContainer.appendChild(clone);
            
            // Attach events
            attachEvents(clone);
            updateSizeOptions(clone);
            updateSKUs(clone);
            updatePreviewTable();
            
            variantIndex++;
        });
    }

    // Form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
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
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Vui lòng chọn size!';
                    
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
                alert('Vui lòng kiểm tra lại! Phải chọn size cho tất cả biến thể và không được chọn size trùng lặp.');
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
/*..........*/
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




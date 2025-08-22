<!-- Modal chi tiết sản phẩm -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('carts.add') }}" id="modal-add-to-cart-form" class="modal-content">
            @csrf
            <input type="hidden" name="product_id" id="modal-product-id">
            <input type="hidden" name="product_variant_id" id="modal-variant-id">

            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary" id="cartModalLabel">Chọn sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- Hình ảnh -->
                    <div class="col-md-6 text-center">
                        <img id="modal-product-image" src="" alt="Hình sản phẩm"
                            class="img-fluid rounded shadow-sm"
                            style="max-height: 500px; object-fit: cover; width: 100%;">
                    </div>

                    <!-- Thông tin sản phẩm -->
                    <div class="col-md-6">
                        <h4 id="modal-product-name" class="fw-bold mb-2 text-dark"></h4>
                        <p class="text-muted mb-2">
                            Danh mục: <span id="modal-product-category" class="fw-medium text-dark"></span>
                        </p>

                        <p class="h5 text-danger fw-bold mb-3 tabular-numbers">
                            <span id="modal-product-price">0</span>
                            <span class="text-muted fs-6">VND</span>
                            <del class="text-secondary fs-6 ms-2" id="modal-product-original-price"></del>
                        </p>

                        <div class="mb-3" id="modal-rating">
                            <!-- Đánh giá (nếu cần) -->
                        </div>

                        <p id="modal-product-description" class="text-muted mb-3" style="min-height: 60px;"></p>


                        <!-- Biến thể -->
                        <div class="mb-3" id="variant-section">
                            <label class="form-label fw-semibold">🍃 Chọn biến thể:</label>
                            <div id="variant-options" class="d-flex flex-wrap gap-2">
                                <!-- JS sẽ render radio button biến thể -->
                            </div>
                        </div>


                        <!-- Số lượng -->
                        <div class="mb-3">
                            <label for="modal-quantity" class="form-label fw-semibold">🔁 Số lượng:</label>
                            <div class="input-group" style="width: 160px;">
                                <button type="button" class="btn btn-outline-secondary" id="decrease-qty">-</button>
                                <input type="number" class="form-control text-center" id="modal-quantity"
                                    name="quantity" value="1" min="1">
                                <button type="button" class="btn btn-outline-secondary" id="increase-qty">+</button>
                                <br>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                    <i class="bi bi-bag-plus-fill me-1"></i> Thêm vào giỏ hàng
                </button>
            </div>
        </form>
    </div>
</div>

<<script>
    const modal = new bootstrap.Modal(document.getElementById('cartModal'));
    const variantOptionsDiv = document.getElementById('variant-options');
    const productVariantIdInput = document.getElementById('modal-variant-id');
    const stockInfoEl = document.getElementById('availableStock');
    const quantityInput = document.getElementById('modal-quantity');

    // Khi bấm nút mở modal
    document.querySelectorAll('.open-cart-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            const productId = btn.dataset.productId;
            const productName = btn.dataset.productName;
            const productImage = btn.dataset.productImage;
            const productCategory = btn.dataset.productCategory;
            const productPrice = parseInt(btn.dataset.productPrice || 0);
            const productOriginalPrice = parseInt(btn.dataset.productOriginalPrice || 0);
            const productDesc = btn.dataset.productDescription || '';
            const variants = JSON.parse(btn.dataset.variants || '[]');
            const totalStock = parseInt(btn.dataset.totalStock || 0);

            // Reset modal
            document.getElementById('modal-product-id').value = productId;
            document.getElementById('modal-product-name').textContent = productName;
            document.getElementById('modal-product-image').src = productImage;
            document.getElementById('modal-product-category').textContent = productCategory;
            document.getElementById('modal-product-description').textContent = productDesc;
            document.getElementById('modal-product-price').textContent = productPrice.toLocaleString();
            document.getElementById('modal-product-original-price').textContent = 
                (productOriginalPrice > productPrice) ? productOriginalPrice.toLocaleString() + " VND" : "";
            quantityInput.value = 1;
            quantityInput.removeAttribute("max");
            variantOptionsDiv.innerHTML = "";
            stockInfoEl.textContent = `Sản phẩm có sẵn: ${totalStock}`;

            // Render biến thể
            if (variants.length > 0) {
                variants.forEach(variant => {
                    const stock = variant.quantity_in_stock ?? 0;
                    const disabled = (variant.status == 0 || stock <= 0);

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'variant';
                    radio.value = variant.id;
                    radio.id = `variant-${variant.id}`;
                    radio.className = 'btn-check';
                    if (disabled) radio.disabled = true;

                    const label = document.createElement('label');
                    label.className = `btn btn-outline-secondary d-flex align-items-center ${disabled ? 'opacity-50' : ''}`;
                    label.setAttribute('for', radio.id);
                    label.style = "width: 150px; flex-direction: column; padding: 10px;";

                    const img = document.createElement('img');
                    img.src = `/storage/${variant.image || productImage}`;
                    img.style = "width: 60px; height: 60px; object-fit: cover; border-radius: 8px;";
                    img.alt = variant.name || '';

                    const text = document.createElement('div');
                    text.className = 'text-center mt-2 text-dark fw-medium';
                    text.innerText = `${variant.name || ''} - ${variant.weight || ''}`;

                    if (disabled) {
                        const soldOut = document.createElement('small');
                        soldOut.className = "text-danger fw-bold mt-1";
                        soldOut.innerText = "Hết hàng";
                        label.appendChild(soldOut);
                    }

                    label.appendChild(img);
                    label.appendChild(text);

                    variantOptionsDiv.appendChild(radio);
                    variantOptionsDiv.appendChild(label);

                    // Gán sự kiện khi chọn biến thể
                    if (!disabled) {
                        radio.addEventListener('change', () => {
                            productVariantIdInput.value = variant.id;
                            document.getElementById('modal-product-price').textContent =
                                (variant.discounted_price || variant.price).toLocaleString();
                            document.getElementById('modal-product-original-price').textContent =
                                (variant.price > variant.discounted_price) ? variant.price.toLocaleString() + " VND" : "";
                            stockInfoEl.textContent = `Sản phẩm có sẵn: ${stock}`;
                            quantityInput.max = stock;
                        });
                    }
                });
            }

            modal.show();
        });
    });
</script>


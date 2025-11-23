@extends('frontend.layouts.app')

@section('title', 'Giỏ hàng - ' . config('app.name'))

@push('styles')
    <style>
        .cart-page {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: 70vh;
        }

        .cart-header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .cart-items {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .cart-item {
            display: flex;
            padding: 20px 0;
            border-bottom: 1px solid #e9ecef;
            transition: background 0.3s;
            position: relative;
        }

        .cart-item-checkbox {
            margin-right: 15px;
            display: flex;
            align-items: center;
        }

        .cart-item-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--secondary-color);
        }

        .cart-item-checkbox label {
            display: none;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item:hover {
            background: #f8f9fa;
        }

        .cart-item.out-of-stock {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding-left: 16px;
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .cart-item-info {
            flex: 1;
            padding: 0 20px;
        }

        .cart-item-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .cart-item-name a {
            color: #333;
            text-decoration: none;
        }

        .cart-item-name a:hover {
            color: var(--secondary-color);
        }

        .cart-item-variant {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .cart-item-price {
            font-size: 18px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 15px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .quantity-control button {
            background: #f8f9fa;
            border: none;
            width: 35px;
            height: 35px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .quantity-control button:hover:not(:disabled) {
            background: #e9ecef;
        }

        .quantity-control button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-control input {
            width: 60px;
            height: 35px;
            border: none;
            text-align: center;
            font-size: 16px;
        }

        .cart-item-subtotal {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }

        .btn-remove-item {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 14px;
        }

        .btn-remove-item:hover {
            background: #c82333;
        }

        .out-of-stock-badge {
            display: inline-block;
            background: #dc3545;
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .cart-summary {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }

        .cart-summary h3 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .summary-row:last-of-type {
            border-bottom: none;
        }

        .summary-label {
            color: #666;
            font-size: 16px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .summary-total {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: var(--secondary-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .btn-checkout:hover {
            background: #0056b3;
        }

        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .empty-cart-icon {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            font-size: 24px;
            color: #666;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #999;
            margin-bottom: 30px;
        }

        .similar-products {
            margin-top: 40px;
        }

        .similar-products h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .alert-cart {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }

        .voucher-section {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .voucher-input-group .input-group {
            margin-bottom: 0;
        }

        .voucher-input-group .form-control {
            font-size: 14px;
        }

        .voucher-input-group .btn {
            font-size: 14px;
        }

        .alert-sm {
            padding: 10px;
            font-size: 13px;
            margin-bottom: 0;
        }

        #voucher-message {
            font-size: 13px;
        }

        #voucher-message.alert-success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 8px 12px;
            border-radius: 4px;
        }

        #voucher-message.alert-danger {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 8px 12px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="cart-page">
        <div class="container">
            <div class="cart-header">
                <h2><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của tôi</h2>
            </div>

            @if ($cart && $cart->items->count() > 0)
                @php
                    // Tính lại tổng tiền từ các sản phẩm không bị out of stock (mặc định được chọn)
                    // Làm tròn từng item và tổng để đảm bảo không có số thập phân
                    $initialSubtotal = 0;
                    foreach ($cart->items as $item) {
                        if (!$item->isOutOfStock()) {
                            $itemSubtotal = round($item->quantity * $item->price_at_time);
                            $initialSubtotal += $itemSubtotal;
                        }
                    }
                    // Làm tròn tổng cuối cùng
                    $initialSubtotal = round($initialSubtotal);
                @endphp
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-items">
                            {{-- Checkbox chọn tất cả --}}
                            <div class="select-all-section mb-3 p-3 bg-light rounded">
                                <label style="cursor: pointer; display: flex; align-items: center; margin: 0;">
                                    <input type="checkbox" id="select-all-items" onchange="toggleSelectAll(this)" style="width: 18px; height: 18px; margin-right: 10px; accent-color: var(--secondary-color);">
                                    <strong>Chọn tất cả sản phẩm</strong>
                                </label>
                            </div>
                            @foreach ($cart->items as $item)
                                @php
                                    $product = $item->productVariant->product;
                                    $variant = $item->productVariant;
                                    $isOutOfStock = $item->isOutOfStock();

                                    $mainImage = $product->image_main
                                        ? asset('storage/' . $product->image_main)
                                        : ($product->images->first()
                                            ? asset('storage/' . $product->images->first()->image_path)
                                            : asset('img/no-image.png'));

                                    $variantAttributes = $variant->attributeValues->pluck('value')->join(', ');
                                    
                                    // Tính lại subtotal và làm tròn thành số nguyên
                                    $itemSubtotal = round($item->quantity * $item->price_at_time);
                                @endphp

                                <div class="cart-item {{ $isOutOfStock ? 'out-of-stock' : '' }}"
                                    data-item-id="{{ $item->id }}">
                                    <div class="cart-item-checkbox">
                                        <input type="checkbox" 
                                            class="item-checkbox" 
                                            id="item_{{ $item->id }}" 
                                            data-item-id="{{ $item->id }}"
                                            data-price="{{ $itemSubtotal }}"
                                            {{ !$isOutOfStock ? 'checked' : '' }}
                                            {{ $isOutOfStock ? 'disabled' : '' }}
                                            onchange="updateSelectedItems()">
                                        <label for="item_{{ $item->id }}"></label>
                                    </div>
                                    <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="cart-item-image">

                                    <div class="cart-item-info">
                                        <div class="cart-item-name">
                                            <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                                            @if ($isOutOfStock)
                                                <span class="out-of-stock-badge">Hết hàng</span>
                                            @endif
                                        </div>

                                        @if ($variantAttributes)
                                            <div class="cart-item-variant">
                                                <strong>Phân loại:</strong> {{ $variantAttributes }}
                                            </div>
                                        @endif

                                        <div class="cart-item-price">
                                            {{ number_format($item->price_at_time, 0, ',', '.') }}₫
                                        </div>

                                        @if ($isOutOfStock)
                                            <div class="alert alert-warning alert-cart mt-2">
                                                <small>
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Sản phẩm này hiện chỉ còn {{ $variant->quantity }} sản phẩm.
                                                    Vui lòng giảm số lượng hoặc xóa khỏi giỏ hàng.
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="cart-item-actions">
                                        <div class="quantity-control">
                                            <button type="button" class="btn-decrease" data-item-id="{{ $item->id }}"
                                                onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="quantity-input" value="{{ $item->quantity }}"
                                                min="1" max="{{ $variant->quantity }}"
                                                data-item-id="{{ $item->id }}"
                                                onchange="updateQuantity({{ $item->id }}, this.value)"
                                                onblur="validateQuantity({{ $item->id }}, {{ $variant->quantity }})">
                                            <button type="button" class="btn-increase" data-item-id="{{ $item->id }}"
                                                onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                {{ $item->quantity >= $variant->quantity ? 'disabled' : '' }}>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>

                                        <div class="cart-item-subtotal" id="subtotal-{{ $item->id }}">
                                            {{ number_format($itemSubtotal, 0, ',', '.') }}₫
                                        </div>

                                        <button type="button" class="btn-remove-item"
                                            onclick="removeItem({{ $item->id }})">
                                            <i class="fas fa-trash me-1"></i>Xóa
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="cart-summary">
                            <h3>Tóm tắt đơn hàng</h3>

                            <div class="summary-row">
                                <span class="summary-label">Tạm tính:</span>
                                <span class="summary-value" id="cart-subtotal">
                                    {{ number_format($initialSubtotal, 0, ',', '.') }}₫
                                </span>
                            </div>

                            <div class="summary-row">
                                <span class="summary-label">Phí vận chuyển:</span>
                                <span class="summary-value">Tính khi thanh toán</span>
                            </div>

                            <!-- Mã giảm giá -->
                            <div class="voucher-section mb-3">
                                @if ($cart->voucher)
                                    <div class="alert alert-success alert-sm mb-2" id="voucher-applied">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-tag me-1"></i>
                                                <strong>{{ $cart->voucher->code }}</strong>
                                                <small class="d-block text-muted">{{ $cart->voucher->name }}</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeVoucher()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="voucher-input-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="voucher-code-input" 
                                                placeholder="Nhập mã giảm giá" maxlength="50">
                                            <button type="button" class="btn btn-outline-secondary" onclick="applyVoucher()" id="btn-apply-voucher">
                                                <i class="fas fa-check"></i> Áp dụng
                                            </button>
                                        </div>
                                        <div id="voucher-message" class="mt-2"></div>
                                    </div>
                                @endif
                            </div>

                            <div class="summary-row">
                                <span class="summary-label">Giảm giá:</span>
                                <span class="summary-value" id="cart-discount">
                                    {{ number_format(round($cart->discount_amount ?? 0), 0, ',', '.') }}₫
                                </span>
                            </div>

                            <div class="summary-row">
                                <span class="summary-label summary-total">Tổng cộng:</span>
                                <span class="summary-value summary-total" id="cart-total">
                                    {{ number_format(round($initialSubtotal - ($cart->discount_amount ?? 0)), 0, ',', '.') }}₫
                                </span>
                            </div>


                            @php
                            $hasOutOfStock = $cart->items->filter(fn($item) => $item->isOutOfStock())->count() > 0;
                            @endphp

                            <button type="button" class="btn-checkout" id="btn-checkout"
                                onclick="proceedToCheckout()">
                                Tiến hành thanh toán
                            </button>

                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>

                @if ($similarProducts && $similarProducts->count() > 0)
                    <div class="similar-products">
                        <h3><i class="fas fa-heart me-2"></i>Sản phẩm bạn có thể thích</h3>
                        <div class="products-grid">
                            @foreach ($similarProducts as $product)
                                @include('frontend.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Giỏ hàng của bạn đang trống</h3>
                    <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateQuantity(itemId, quantity) {
            if (quantity < 1) {
                quantity = 1;
            }

            const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
            const item = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
            const btnDecrease = item.querySelector('.btn-decrease');
            const btnIncrease = item.querySelector('.btn-increase');

            // Disable buttons while loading
            btnDecrease.disabled = true;
            btnIncrease.disabled = true;
            input.disabled = true;

            fetch(`{{ url('cart') }}/${itemId}/update`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: parseInt(quantity)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update quantity input
                        input.value = quantity;

                        // Update subtotal
                        document.getElementById(`subtotal-${itemId}`).textContent = data.subtotal;

                        // Cập nhật giá của item trong checkbox
                        const checkbox = document.querySelector(`.item-checkbox[data-item-id="${itemId}"]`);
                        if (checkbox) {
                            // Parse từ chuỗi đã format (có dấu phẩy và " đ" hoặc "₫") và làm tròn
                            const newSubtotal = Math.round(parseFloat(data.subtotal.replace(/[^\d]/g, '')) || 0);
                            checkbox.setAttribute('data-price', newSubtotal);
                        }

                        // Update cart total (chỉ tính sản phẩm đã chọn)
                        if (data.discount_amount) {
                            document.getElementById('cart-discount').textContent = data.discount_amount;
                        }
                        updateSelectedItems();

                        // Update cart count in header
                        updateCartCount(data.cart_count);

                        // Check stock and update buttons
                        const maxQuantity = parseInt(input.getAttribute('max'));
                        btnDecrease.disabled = quantity <= 1;
                        btnIncrease.disabled = quantity >= maxQuantity;

                        // Check if out of stock
                        if (quantity > maxQuantity) {
                            item.classList.add('out-of-stock');
                        } else {
                            item.classList.remove('out-of-stock');
                        }

                        // Check checkout button
                        checkCheckoutButton();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                        // Reset input
                        input.value = input.getAttribute('data-original-value') || 1;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật số lượng');
                    input.value = input.getAttribute('data-original-value') || 1;
                })
                .finally(() => {
                    input.disabled = false;
                    btnDecrease.disabled = false;
                    btnIncrease.disabled = false;
                });
        }

        function validateQuantity(itemId, maxQuantity) {
            const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
            let quantity = parseInt(input.value);

            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
            }

            if (quantity > maxQuantity) {
                quantity = maxQuantity;
                alert(`Số lượng tối đa là ${maxQuantity}`);
            }

            if (quantity != input.value) {
                input.setAttribute('data-original-value', input.value);
                updateQuantity(itemId, quantity);
            }
        }

        function removeItem(itemId) {
            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                return;
            }

            const item = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
            item.style.opacity = '0.5';
            item.style.pointerEvents = 'none';

            fetch(`{{ url('cart') }}/${itemId}/remove`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        item.remove();

                        // Xóa checkbox của item đã xóa
                        const checkbox = document.querySelector(`.item-checkbox[data-item-id="${itemId}"]`);
                        if (checkbox) {
                            checkbox.remove();
                        }

                        // Update cart total (chỉ tính sản phẩm đã chọn)
                        if (data.discount_amount) {
                            document.getElementById('cart-discount').textContent = data.discount_amount;
                        }
                        updateSelectedItems();

                        // Update cart count
                        updateCartCount(data.cart_count);

                        // Check if cart is empty
                        const remainingItems = document.querySelectorAll('.cart-item').length;
                        if (remainingItems === 0) {
                            location.reload();
                        }

                        checkCheckoutButton();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                        item.style.opacity = '1';
                        item.style.pointerEvents = 'auto';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm');
                    item.style.opacity = '1';
                    item.style.pointerEvents = 'auto';
                });
        }

        function updateCartCount(count) {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = count;
            });
        }

        // Cập nhật tổng tiền theo sản phẩm đã chọn
        function updateSelectedItems() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            let selectedTotal = 0;

            checkboxes.forEach(checkbox => {
                // Lấy giá từ data-price (đã là số thuần từ PHP)
                const priceStr = checkbox.getAttribute('data-price') || '0';
                // Parse trực tiếp vì data-price đã là số thuần
                const price = parseFloat(priceStr) || 0;
                selectedTotal += price;
            });

            // Làm tròn tổng thành số nguyên
            selectedTotal = Math.round(selectedTotal);

            // Cập nhật tổng tiền
            const discountText = document.getElementById('cart-discount').textContent;
            // Parse discount từ text đã format (có dấu phẩy) và làm tròn
            const discount = Math.round(parseFloat(discountText.replace(/[^\d]/g, '')) || 0);
            const finalTotal = Math.round(Math.max(0, selectedTotal - discount));

            // Cập nhật hiển thị (format không có số thập phân)
            document.getElementById('cart-subtotal').textContent = 
                new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(selectedTotal) + '₫';
            document.getElementById('cart-total').textContent = 
                new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(finalTotal) + '₫';

            // Cập nhật trạng thái nút checkout
            checkCheckoutButton();

            // Cập nhật checkbox "Chọn tất cả"
            updateSelectAllCheckbox();
        }

        // Chọn/bỏ chọn tất cả
        function toggleSelectAll(checkbox) {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
            itemCheckboxes.forEach(cb => {
                const itemElement = document.querySelector(`.cart-item[data-item-id="${cb.getAttribute('data-item-id')}"]`);
                if (!itemElement || !itemElement.classList.contains('out-of-stock')) {
                    cb.checked = checkbox.checked;
                }
            });
            updateSelectedItems();
        }

        // Cập nhật trạng thái checkbox "Chọn tất cả"
        function updateSelectAllCheckbox() {
            const allCheckboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
            const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked:not(:disabled)');
            const selectAllCheckbox = document.getElementById('select-all-items');
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
            }
        }

        // Xử lý thanh toán với sản phẩm đã chọn
        function proceedToCheckout() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedItemIds = [];

            selectedCheckboxes.forEach(checkbox => {
                selectedItemIds.push(checkbox.getAttribute('data-item-id'));
            });

            if (selectedItemIds.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán');
                return;
            }

            // Kiểm tra sản phẩm hết hàng
            let hasOutOfStock = false;
            selectedCheckboxes.forEach(checkbox => {
                const itemId = checkbox.getAttribute('data-item-id');
                const itemElement = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                if (itemElement && itemElement.classList.contains('out-of-stock')) {
                    hasOutOfStock = true;
                }
            });

            if (hasOutOfStock) {
                alert('Vui lòng xử lý các sản phẩm hết hàng trước khi thanh toán');
                return;
            }

            // Chuyển đến trang checkout với danh sách item IDs đã chọn
            const itemIdsParam = selectedItemIds.join(',');
            window.location.href = '{{ route("checkout.index") }}?selected_items=' + itemIdsParam;
        }

        function checkCheckoutButton() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const checkoutBtn = document.getElementById('btn-checkout');

            if (selectedCheckboxes.length === 0) {
                checkoutBtn.disabled = true;
                checkoutBtn.title = 'Vui lòng chọn ít nhất một sản phẩm để thanh toán';
                return;
            }

            // Kiểm tra sản phẩm hết hàng trong danh sách đã chọn
            let hasOutOfStock = false;
            selectedCheckboxes.forEach(checkbox => {
                const itemId = checkbox.getAttribute('data-item-id');
                const itemElement = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                if (itemElement && itemElement.classList.contains('out-of-stock')) {
                    hasOutOfStock = true;
                }
            });

            if (hasOutOfStock) {
                checkoutBtn.disabled = true;
                checkoutBtn.title = 'Vui lòng xử lý các sản phẩm hết hàng trước khi thanh toán';
            } else {
                checkoutBtn.disabled = false;
                checkoutBtn.title = '';
            }
        }

        function updateCartTotal() {
            // Gọi updateSelectedItems thay vì tính từ text
            updateSelectedItems();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Store original values
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.setAttribute('data-original-value', input.value);
            });

            // Khởi tạo tổng tiền theo sản phẩm đã chọn
            updateSelectedItems();
            checkCheckoutButton();
        });

        function applyVoucher() {
            const voucherCode = document.getElementById('voucher-code-input').value.trim();
            const btnApply = document.getElementById('btn-apply-voucher');
            const messageDiv = document.getElementById('voucher-message');

            if (!voucherCode) {
                messageDiv.innerHTML = '<div class="alert-danger">Vui lòng nhập mã giảm giá</div>';
                return;
            }

            btnApply.disabled = true;
            btnApply.innerHTML = '<span class="loading-spinner"></span> Đang xử lý...';
            messageDiv.innerHTML = '';

            fetch('{{ route("cart.apply-voucher") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        voucher_code: voucherCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show updated voucher
                        location.reload();
                    } else {
                        messageDiv.innerHTML = '<div class="alert-danger">' + (data.message || 'Có lỗi xảy ra') + '</div>';
                        btnApply.disabled = false;
                        btnApply.innerHTML = '<i class="fas fa-check"></i> Áp dụng';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.innerHTML = '<div class="alert-danger">Có lỗi xảy ra khi áp dụng mã giảm giá</div>';
                    btnApply.disabled = false;
                    btnApply.innerHTML = '<i class="fas fa-check"></i> Áp dụng';
                });
        }

        function removeVoucher() {
            if (!confirm('Bạn có chắc chắn muốn xóa mã giảm giá?')) {
                return;
            }

            fetch('{{ route("cart.remove-voucher") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa mã giảm giá');
                });
        }

        function updateCartTotal() {
            const subtotalText = document.getElementById('cart-subtotal').textContent;
            const discountText = document.getElementById('cart-discount').textContent;
            
            const subtotal = parseFloat(subtotalText.replace(/[^\d]/g, '')) || 0;
            const discount = parseFloat(discountText.replace(/[^\d]/g, '')) || 0;
            const total = subtotal - discount;
            
            document.getElementById('cart-total').textContent = 
                new Intl.NumberFormat('vi-VN').format(total) + '₫';
        }

        // Allow Enter key to apply voucher
        document.addEventListener('DOMContentLoaded', function() {
            const voucherInput = document.getElementById('voucher-code-input');
            if (voucherInput) {
                voucherInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyVoucher();
                    }
                });
            }
        });
    </script>
@endpush

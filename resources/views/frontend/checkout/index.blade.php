@extends('frontend.layouts.app')

@section('title', 'Thanh toán')

<style>
    /* ===== Breadcrumb ===== */
    .breadcrumb-option {
        padding: 30px 0;
        background: #f5f5f5;
        border-bottom: 1px solid #eee;
    }

    .breadcrumb__text h4 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .breadcrumb__links a {
        font-size: 14px;
        color: #666;
        margin-right: 6px;
        text-decoration: none;
    }

    .breadcrumb__links a:hover {
        color: #111;
    }

    .breadcrumb__links span {
        font-size: 14px;
        color: #111;
    }

    /* ===== Checkout layout ===== */
    .checkout {
        padding: 50px 0 70px;
        background: #fafafa;
    }

    .checkout__form form {
        background: #fff;
        padding: 30px 25px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    }

    .checkout__title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    /* ===== Inputs bên trái ===== */
    .checkout__input {
        margin-bottom: 18px;
    }

    .checkout__input p {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .checkout__input p span {
        color: #e53637;
        margin-left: 2px;
    }

    .checkout__input input[type="text"],
    .checkout__input input[type="tel"],
    .checkout__input input[type="number"],
    .checkout__input input[type="email"],
    .checkout__input textarea {
        width: 100%;
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        padding: 10px 12px;
        font-size: 14px;
        color: #333;
        outline: none;
        transition: all 0.2s ease;
    }

    .checkout__input input:focus,
    .checkout__input textarea:focus {
        border-color: #111;
        box-shadow: 0 0 0 2px rgba(17, 17, 17, 0.05);
    }

    .checkout__input__add {
        margin-bottom: 10px;
    }

    .text-danger.small {
        font-size: 12px;
    }

    /* ===== Cột phải - Order summary ===== */
    .checkout__order {
        background: #fafafa;
        border-radius: 10px;
        padding: 20px 18px 22px;
        border: 1px solid #eee;
    }

    .checkout__order .order__title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
        text-transform: uppercase;
    }

    .checkout__order__products {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
        border-bottom: 1px solid #e5e5e5;
        padding-bottom: 8px;
    }

    .checkout__order__products span {
        float: right;
    }

    .checkout__total__products {
        margin-bottom: 12px;
        list-style: none;
        padding-left: 0;
        max-height: 220px;
        overflow-y: auto;
    }

    .checkout__total__products li {
        font-size: 13px;
        color: #333;
        margin-bottom: 6px;
    }

    .checkout__total__products li span {
        float: right;
        font-weight: 500;
    }

    .checkout__total__all {
        list-style: none;
        padding-left: 0;
        border-top: 1px solid #e5e5e5;
        padding-top: 10px;
        margin-bottom: 16px;
    }

    .checkout__total__all li {
        font-size: 14px;
        color: #333;
        margin-bottom: 6px;
    }

    .checkout__total__all li span {
        float: right;
        font-weight: 600;
    }

    .checkout__total__all li:last-child {
        font-size: 15px;
        font-weight: 600;
        color: #e53637;
    }

    /* ===== Radio/checkbox custom ===== */
    .checkout__input__checkbox {
        margin-bottom: 10px;
    }

    .checkout__input__checkbox label {
        position: relative;
        padding-left: 28px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        user-select: none;
    }

    .checkout__input__checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkout__input__checkbox .checkmark {
        position: absolute;
        left: 0;
        top: 2px;
        height: 18px;
        width: 18px;
        border-radius: 50%;
        border: 1px solid #ccc;
        background-color: #fff;
    }

    .checkout__input__checkbox input:checked~.checkmark {
        background-color: #111;
        border-color: #111;
    }

    .checkout__input__checkbox .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .checkout__input__checkbox input:checked~.checkmark:after {
        display: block;
    }

    .checkout__input__checkbox .checkmark:after {
        left: 5px;
        top: 3px;
        width: 6px;
        height: 10px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* ===== Nút ĐẶT HÀNG ===== */
    .site-btn {
        display: inline-block;
        background: #111111;
        color: #fff;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: 600;
        padding: 12px 25px;
        border-radius: 30px;
        border: none;
        outline: none;
        cursor: pointer;
        width: 100%;
        text-align: center;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
    }

    .site-btn:hover {
        background: #e53637;
    }

    /* ===== Saved Addresses ===== */
    .saved-address-item {
        transition: all 0.2s ease;
    }

    .saved-address-item:hover {
        background: #f8f9fa !important;
        border-color: #28a745 !important;
    }

    .saved-address-item input[type="radio"] {
        margin-top: 4px;
        cursor: pointer;
    }

    .address-radio {
        cursor: pointer;
    }

    /* ===== Responsive nhỏ nhỏ ===== */
    @media (max-width: 991.98px) {
        .checkout__form form {
            padding: 20px 15px;
        }

        .checkout__order {
            margin-top: 25px;
        }
    }
</style>

@section('content')
    @php
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += $item->quantity * $item->price_at_time;
        }

        $discountAmount = $cart->discount_amount ?? 0;
        $initialShippingFee = $initialShippingFee ?? 0;
        $total = $subtotal - $discountAmount + $initialShippingFee;

        $currentCity = old('receiver_city', '');
        $currentDistrict = old('receiver_district', '');
        $districtOptions =
            $currentCity && isset($locations[$currentCity]['districts']) ? $locations[$currentCity]['districts'] : [];
    @endphp


    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Thanh toán</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Trang chủ</a>
                            <a href="{{ route('products.index') }}">Sản phẩm</a>
                            <span>Thanh toán</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Checkout Section Begin -->
    <section class="checkout spad">
        <div class="container">
            <div class="checkout__form">
                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    @if (!empty($selectedItemIds))
                        <input type="hidden" name="selected_items" value="{{ implode(',', $selectedItemIds) }}">
                    @endif
                    <div class="row">
                        {{-- ================== CỘT TRÁI: THÔNG TIN GIAO HÀNG ================== --}}
                        <div class="col-lg-8 col-md-6">
                            @if ($cart->voucher)
                                <div class="alert alert-success mb-3"
                                    style="padding: 12px 15px; border-radius: 6px; background: #d4edda; border: 1px solid #c3e6cb; color: #155724;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-tag me-2"></i>
                                            <strong>Mã giảm giá đã áp dụng: {{ $cart->voucher->code }}</strong>
                                            <small class="d-block text-muted mt-1">
                                                {{ $cart->voucher->name }} - Giảm
                                                {{ number_format($discountAmount, 0, ',', '.') }}₫
                                            </small>
                                        </div>
                                        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-link text-danger p-0"
                                            style="text-decoration: none;">
                                            <i class="fas fa-edit me-1"></i>Thay đổi
                                        </a>
                                    </div>
                                </div>
                            @endif
                            <h6 class="checkout__title">Thông tin người nhận</h6>

                            {{-- Danh sách địa chỉ đã lưu --}}
                            @if ($savedAddresses && $savedAddresses->count() > 0)
                                <div class="mb-4">
                                    <p class="mb-2" style="font-size: 14px; font-weight: 600;">Địa chỉ đã lưu:</p>
                                    <div class="saved-addresses" style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($savedAddresses as $address)
                                            <div class="saved-address-item mb-2 p-3 border rounded"
                                                style="cursor: pointer; transition: all 0.2s;">
                                                <div class="d-flex justify-content-between align-items-start"
                                                    onclick="selectAddress({{ $address->id }})">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong style="font-size: 14px;">
                                                                {{ $address->receiver_name ?? $address->name }}
                                                            </strong>
                                                            @if ($address->is_default)
                                                                <span class="badge bg-success ms-2"
                                                                    style="font-size: 11px;">Mặc định</span>
                                                            @endif
                                                        </div>
                                                        <p class="mb-1" style="font-size: 13px; color: #666;">
                                                            {{ $address->receiver_phone ?? $address->phone }}
                                                        </p>
                                                        <p class="mb-0" style="font-size: 13px; color: #666;">
                                                            {{ $address->receiver_address_detail ?? $address->address_line }},
                                                            {{ $address->receiver_district ?? $address->district }},
                                                            {{ $address->receiver_city ?? $address->province }}
                                                        </p>
                                                    </div>
                                                    <input type="radio" name="selected_address_id"
                                                        value="{{ $address->id }}" id="address_{{ $address->id }}"
                                                        class="address-radio" style="margin-top: 4px;">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                        onclick="showNewAddressForm()">
                                        <i class="fas fa-plus me-1"></i>Thêm địa chỉ mới
                                    </button>
                                </div>
                            @endif

                            {{-- FORM ĐỊA CHỈ (ban đầu trống) --}}
                            <div id="address-form">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="checkout__input">
                                            <p>Họ và tên<span>*</span></p>
                                            <input type="text" name="receiver_name" id="receiver_name"
                                                value="{{ old('receiver_name') }}">
                                            @error('receiver_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="checkout__input">
                                            <p>Thành phố<span>*</span></p>
                                            <select name="receiver_city" id="receiver_city" class="form-select">
                                                <option value="">-- Chọn thành phố --</option>
                                                @foreach ($locations as $code => $city)
                                                    <option value="{{ $code }}" @selected($currentCity === $code)>
                                                        {{ $city['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('receiver_city')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout__input">
                                            <p>Quận / Huyện<span>*</span></p>
                                            <select name="receiver_district" id="receiver_district" class="form-select"
                                                data-selected="{{ $currentDistrict }}">
                                                <option value="">-- Chọn quận / huyện --</option>
                                                @foreach ($districtOptions as $code => $name)
                                                    <option value="{{ $code }}" @selected($currentDistrict === $code)>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('receiver_district')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="checkout__input">
                                    <p>Địa chỉ cụ thể<span>*</span></p>
                                    <input type="text" name="receiver_address_detail" id="receiver_address_detail"
                                        class="checkout__input__add" placeholder="Số nhà, đường, phường/xã..."
                                        value="{{ old('receiver_address_detail') }}">
                                    @error('receiver_address_detail')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="checkout__input">
                                            <p>Số điện thoại<span>*</span></p>
                                            <input type="text" name="receiver_phone" id="receiver_phone"
                                                value="{{ old('receiver_phone') }}">
                                            @error('receiver_phone')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="checkout__input">
                                            <p>Email</p>
                                            <input type="text" value="{{ $user->email }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="checkout__input">
                                    <p>Ghi chú đơn hàng</p>
                                    <input type="text" name="note"
                                        placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."
                                        value="{{ old('note') }}">
                                </div>

                                {{-- Checkbox lưu địa chỉ 
                                <div class="checkout__input__checkbox mb-3">
                                    <label for="save_address">
                                        <input type="checkbox" id="save_address" name="save_address" value="1">
                                        <span class="checkmark"></span>
                                        Lưu địa chỉ này để sử dụng sau
                                    </label>
                                </div>

                                <div class="checkout__input__checkbox mb-3" id="set_default_wrapper"
                                    style="display: none;">
                                    <label for="set_as_default">
                                        <input type="checkbox" id="set_as_default" name="set_as_default" value="1">
                                        <span class="checkmark"></span>
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div> --}}
                            </div>
                        </div>

                        {{-- ================== CỘT PHẢI: TÓM TẮT ĐƠN HÀNG ================== --}}
                        <div class="col-lg-4 col-md-6">
                            <div class="checkout__order">
                                <h4 class="order__title">Đơn hàng của bạn</h4>
                                <div class="checkout__order__products">
                                    Sản phẩm <span>Tổng</span>
                                </div>

                                <ul class="checkout__total__products">
                                    @foreach ($cart->items as $index => $item)
                                        @php
                                            $product = $item->productVariant->product;
                                            $variant = $item->productVariant;
                                            $variantAttributes = $variant->attributeValues->pluck('value')->join(', ');
                                            $itemSubtotal = $item->quantity * $item->price_at_time;
                                        @endphp
                                        <li>
                                            {{ sprintf('%02d.', $index + 1) }}
                                            {{ $product->name }}
                                            @if ($variantAttributes)
                                                ({{ $variantAttributes }})
                                            @endif
                                            x{{ $item->quantity }}
                                            <span>{{ number_format($itemSubtotal, 0, ',', '.') }}₫</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <ul class="checkout__total__all">
                                    <li>Tạm tính
                                        <span id="checkout-subtotal"
                                            data-value="{{ $subtotal }}">{{ number_format($subtotal, 0, ',', '.') }}₫</span>

                                    </li>
                                    @if ($cart->voucher && $discountAmount > 0)
                                        <li style="color: #28a745;">
                                            Giảm giá ({{ $cart->voucher->code }})
                                            <span id="checkout-discount" data-value="{{ $discountAmount }}"
                                                style="color: #28a745;">
                                                -{{ number_format($discountAmount, 0, ',', '.') }}₫
                                            </span>
                                        </li>
                                    @else
                                        <li style="display: none;">
                                            Giảm giá
                                            <span id="checkout-discount"
                                                data-value="{{ $discountAmount }}">-{{ number_format($discountAmount, 0, ',', '.') }}₫</span>
                                        </li>
                                    @endif
                                    <li>Phí vận chuyển
                                        <span id="checkout-shipping"
                                            data-value="{{ $initialShippingFee }}">{{ number_format($initialShippingFee, 0, ',', '.') }}₫</span>

                                    </li>
                                    <li>Tổng cộng
                                        <span id="checkout-total">
                                            {{ number_format($total, 0, ',', '.') }}₫
                                        </span>
                                    </li>
                                </ul>

                                <h4 class="order__title">Phương thức thanh toán</h4>

                                @if (isset($paymentMethods) && $paymentMethods->count())
                                    @foreach ($paymentMethods as $pm)
                                        <div class="checkout__input__checkbox">
                                            <label for="pm-{{ $pm->slug }}">
                                                @if ($pm->icon)
                                                    <img src="{{ asset('storage/' . $pm->icon) }}"
                                                        alt="{{ $pm->display_name ?? $pm->name }}"
                                                        style="height:20px;margin-right:6px;vertical-align:middle;">
                                                @endif

                                                {{ $pm->display_name ?? $pm->name }}

                                                <input type="radio" id="pm-{{ $pm->slug }}" name="payment_method"
                                                    value="{{ $pm->slug }}"
                                                    {{ old('payment_method', optional($paymentMethods->first())->slug) === $pm->slug ? 'checked' : '' }}>
                                                <span class="checkmark"></span>
                                            </label>

                                            @if ($pm->description)
                                                <small class="text-muted d-block">{{ $pm->description }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="checkout__input__checkbox">
                                        <label for="payment_cod">
                                            Thanh toán khi nhận hàng (COD)
                                            <input type="radio" id="payment_cod" name="payment_method" value="cod"
                                                {{ old('payment_method', 'COD') === 'COD' ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                @endif

                                @error('payment_method')
                                    <span class="text-danger small d-block mb-2">{{ $message }}</span>
                                @enderror

                                <button type="submit" class="site-btn">ĐẶT HÀNG</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Checkout Section End -->

    <script>
        const savedAddresses = @json($savedAddresses);
        const locations = @json($locations);

        // ==== CHỌN ĐỊA CHỈ ĐÃ LƯU -> ĐỔ DỮ LIỆU XUỐNG FORM (CÓ CITY + DISTRICT) ====
        function selectAddress(addressId) {
            const list = Array.isArray(savedAddresses) ? savedAddresses : [];
            const address = list.find(a => a.id === addressId);
            if (!address) return;

            const nameInput = document.getElementById('receiver_name');
            const phoneInput = document.getElementById('receiver_phone');
            const addrDetailInput = document.getElementById('receiver_address_detail');
            const citySelect = document.getElementById('receiver_city');
            const districtSelect = document.getElementById('receiver_district');

            // 1. Họ tên + SĐT
            if (nameInput) nameInput.value = address.receiver_name || address.name || '';
            if (phoneInput) phoneInput.value = address.receiver_phone || address.phone || '';

            // 2. Địa chỉ chi tiết (cứ để phần line / detail, city & district đã có dropdown riêng)
            if (addrDetailInput) {
                addrDetailInput.value = address.receiver_address_detail ||
                    address.address_line ||
                    '';
            }

            // 3. Map TÊN tỉnh / thành phố sang MÃ city trong locations
            const cityMap = locations || {};
            const rawCity = address.receiver_city || address.province || ''; // VD: "Ninh Bình"
            let cityCode = '';

            // 3.1. Nếu rawCity trùng luôn key thì dùng luôn
            if (rawCity && cityMap[rawCity]) {
                cityCode = rawCity;
            } else {
                // 3.2. Ngược lại, tìm code theo name
                for (const [code, cityObj] of Object.entries(cityMap)) {
                    if (cityObj && cityObj.name === rawCity) {
                        cityCode = code;
                        break;
                    }
                }
            }

            // Nếu vẫn không map được thì fallback "other" nếu có
            if (!cityCode) {
                if (cityMap.other) {
                    cityCode = 'other';
                }
            }

            if (citySelect) {
                citySelect.value = cityCode || '';
            }

            // 4. Map TÊN quận/huyện sang MÃ district trong locations[cityCode].districts
            const rawDistrict = address.receiver_district || address.district || ''; // VD: "Gia Viễn"
            let districtCode = '';

            if (cityCode && cityMap[cityCode] && cityMap[cityCode].districts) {
                const districts = cityMap[cityCode].districts;

                // 4.1. Nếu rawDistrict trùng key
                if (rawDistrict && districts[rawDistrict]) {
                    districtCode = rawDistrict;
                } else {
                    // 4.2. Tìm theo name
                    for (const [code, name] of Object.entries(districts)) {
                        if (name === rawDistrict) {
                            districtCode = code;
                            break;
                        }
                    }
                }

                // 4.3. Fallback "other" nếu không tìm được
                if (!districtCode && districts.other) {
                    districtCode = 'other';
                }
            }

            // 5. Gọi populateDistricts để fill dropdown + chọn sẵn districtCode
            if (typeof window.populateDistricts === 'function') {
                window.populateDistricts(cityCode, districtCode);
            } else if (districtSelect) {
                // nếu vì lý do nào đó populateDistricts chưa có thì set value trực tiếp
                districtSelect.value = districtCode || '';
            }

            // 6. Đánh dấu radio đã chọn
            const radio = document.getElementById('address_' + addressId);
            if (radio) radio.checked = true;

            // 7. Cập nhật lại phí ship / tổng tiền
            if (typeof window.updateTotals === 'function') {
                window.updateTotals();
            }
        }

        // Thêm địa chỉ mới: form trống, bỏ chọn radio
        function showNewAddressForm() {
            document.querySelectorAll('.address-radio').forEach(radio => (radio.checked = false));

            const nameInput = document.getElementById('receiver_name');
            const phoneInput = document.getElementById('receiver_phone');
            const addrDetailInput = document.getElementById('receiver_address_detail');
            const citySelect = document.getElementById('receiver_city');
            const districtSelect = document.getElementById('receiver_district');

            if (nameInput) nameInput.value = '';
            if (phoneInput) phoneInput.value = '';
            if (addrDetailInput) addrDetailInput.value = '';

            if (citySelect) citySelect.value = '';
            if (districtSelect) {
                districtSelect.innerHTML = '<option value="">-- Chọn quận / huyện --</option>';
            }

            if (typeof window.updateTotals === 'function') {
                window.updateTotals();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const citySelect = document.getElementById('receiver_city');
            const districtSelect = document.getElementById('receiver_district');
            const subtotalEl = document.getElementById('checkout-subtotal');
            const shippingEl = document.getElementById('checkout-shipping');
            const totalEl = document.getElementById('checkout-total');
            const locationMap = locations || {};

            // Checkbox "Lưu địa chỉ"
            const saveAddressCheckbox = document.getElementById('save_address');
            const setDefaultWrapper = document.getElementById('set_default_wrapper');
            if (saveAddressCheckbox && setDefaultWrapper) {
                saveAddressCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        setDefaultWrapper.style.display = 'block';
                    } else {
                        setDefaultWrapper.style.display = 'none';
                        const setAsDefault = document.getElementById('set_as_default');
                        if (setAsDefault) setAsDefault.checked = false;
                    }
                });
            }

            if (!citySelect || !districtSelect || !subtotalEl || !shippingEl || !totalEl) return;

            const baseSubtotalText = subtotalEl.dataset.value || '0';
            const baseSubtotal = parseFloat(baseSubtotalText.toString().replace(/[^\d]/g, '')) || 0;
            const originalDistrict = districtSelect.dataset.selected || '';

            const formatCurrency = (value) =>
                new Intl.NumberFormat('vi-VN').format(value) + '₫';

            const calculateFeeFromCity = (cityCode) => {
                if (!cityCode) return 0;
                return cityCode === 'hanoi' ? 30000 : 40000;
            };

            const populateDistricts = (cityCode, preset) => {
                districtSelect.innerHTML = '<option value="">-- Chọn quận / huyện --</option>';
                if (!cityCode || !locationMap[cityCode]) return;

                const districts = locationMap[cityCode].districts || {};
                Object.entries(districts).forEach(([code, name]) => {
                    const option = document.createElement('option');
                    option.value = code;
                    option.textContent = name;
                    if (preset && preset === code) option.selected = true;
                    districtSelect.appendChild(option);
                });

                if (!districtSelect.value && preset) {
                    districtSelect.value = preset;
                }
            };

            window.populateDistricts = populateDistricts;

            const updateTotals = () => {
                const subtotal = parseInt(subtotalEl.dataset.value) || 0;
                const discount = parseInt(document.getElementById('checkout-discount').dataset.value) || 0;
                const shipping = calculateFeeFromCity(citySelect.value);

                shippingEl.dataset.value = shipping;
                shippingEl.textContent = formatCurrency(shipping);

                const total = subtotal - discount + shipping;
                totalEl.textContent = formatCurrency(total);
            };



            window.updateTotals = updateTotals;

            if (citySelect.value) {
                populateDistricts(citySelect.value, originalDistrict);
            } else {
                districtSelect.innerHTML = '<option value="">-- Chọn quận / huyện --</option>';
            }
            updateTotals();

            citySelect.addEventListener('change', () => {
                populateDistricts(citySelect.value, null);
                updateTotals();
            });

            // Validate trước khi submit
            const checkoutForm = document.querySelector('.checkout__form form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    const fields = ['receiver_name', 'receiver_phone', 'receiver_address_detail'];
                    const cityEl = document.getElementById('receiver_city');
                    const districtEl = document.getElementById('receiver_district');
                    let hasError = false;

                    [...fields, 'receiver_city', 'receiver_district'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.style.borderColor = '#e5e5e5';
                    });

                    const markError = (el) => {
                        if (!el) return;
                        el.style.borderColor = '#e53637';
                        hasError = true;
                    };

                    fields.forEach(id => {
                        const el = document.getElementById(id);
                        if (el && !el.value.trim()) markError(el);
                    });

                    if (cityEl && !cityEl.value) markError(cityEl);
                    if (districtEl && !districtEl.value) markError(districtEl);

                    if (hasError) {
                        e.preventDefault();
                        alert('Vui lòng điền đầy đủ thông tin nhận hàng trước khi đặt hàng.');
                    }
                });
            }
        });
    </script>
@endsection

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
        $subtotal = $cart->total_price;
        $discountAmount = $cart->discount_amount ?? 0;
        $initialShippingFee = $shippingFee ?? 0;
        $initialTotal = $subtotal - $discountAmount + $initialShippingFee;
        $currentCity = $selectedCity ?? array_key_first($locations ?? []);
        $currentDistrict = $selectedDistrict ?? null;
        $districtOptions = $locations[$currentCity]['districts'] ?? [];
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
                    <div class="row">
                        {{-- ================== CỘT TRÁI: THÔNG TIN GIAO HÀNG ================== --}}
                        <div class="col-lg-8 col-md-6">
                            @if ($cart->voucher)
                                <div class="alert alert-success mb-3" style="padding: 12px 15px; border-radius: 6px; background: #d4edda; border: 1px solid #c3e6cb; color: #155724;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-tag me-2"></i>
                                            <strong>Mã giảm giá đã áp dụng: {{ $cart->voucher->code }}</strong>
                                            <small class="d-block text-muted mt-1">{{ $cart->voucher->name }} - Giảm {{ number_format($discountAmount, 0, ',', '.') }}₫</small>
                                        </div>
                                        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-link text-danger p-0" style="text-decoration: none;">
                                            <i class="fas fa-edit me-1"></i>Thay đổi
                                        </a>
                                    </div>
                                </div>
                            @endif
                            <h6 class="checkout__title">Thông tin người nhận</h6>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Họ và tên<span>*</span></p>
                                        <input type="text" name="receiver_name"
                                            value="{{ old('receiver_name', $user->full_name ?? $user->name) }}">
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
                                            @foreach ($locations as $code => $city)
                                                <option value="{{ $code }}"
                                                    @selected(old('receiver_city', $currentCity) === $code)>
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
                                            data-selected="{{ old('receiver_district', $currentDistrict) }}">
                                            @foreach ($districtOptions as $code => $name)
                                                <option value="{{ $code }}"
                                                    @selected(old('receiver_district', $currentDistrict) === $code)>
                                                    {{ $name }}
                                                </option>
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
                                <input type="text" name="receiver_address_detail" class="checkout__input__add"
                                    placeholder="Số nhà, đường, phường/xã..."
                                    value="{{ old('receiver_address_detail') }}">
                                @error('receiver_address_detail')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Số điện thoại<span>*</span></p>
                                        <input type="text" name="receiver_phone"
                                            value="{{ old('receiver_phone', $user->phone ?? '') }}">
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
                                        @endphp
                                        <li>
                                            {{ sprintf('%02d.', $index + 1) }}
                                            {{ $product->name }}
                                            @if ($variantAttributes)
                                                ({{ $variantAttributes }})
                                            @endif
                                            x{{ $item->quantity }}
                                            <span>{{ number_format($item->subtotal, 0, ',', '.') }}₫</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <ul class="checkout__total__all">
                                    <li>Tạm tính
                                        <span id="checkout-subtotal" data-value="{{ $subtotal }}">
                                            {{ number_format($subtotal, 0, ',', '.') }}₫
                                        </span>
                                    </li>
                                    @if ($cart->voucher && $discountAmount > 0)
                                        <li style="color: #28a745;">
                                            Giảm giá ({{ $cart->voucher->code }})
                                            <span id="checkout-discount" data-value="{{ $discountAmount }}" style="color: #28a745;">
                                                -{{ number_format($discountAmount, 0, ',', '.') }}₫
                                            </span>
                                        </li>
                                    @else
                                        <li style="display: none;">
                                            Giảm giá
                                            <span id="checkout-discount" data-value="0" style="color: #28a745;">0₫</span>
                                        </li>
                                    @endif
                                    <li>Phí vận chuyển
                                        <span id="checkout-shipping" data-value="{{ $initialShippingFee }}">
                                            {{ number_format($initialShippingFee, 0, ',', '.') }}₫
                                        </span>
                                    </li>
                                    <li>Tổng cộng
                                        <span id="checkout-total">
                                            {{ number_format($initialTotal, 0, ',', '.') }}₫
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
                                    {{-- fallback nếu chưa cấu hình payment_methods --}}
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
        document.addEventListener('DOMContentLoaded', () => {
            const citySelect = document.querySelector('select[name="receiver_city"]');
            const districtSelect = document.querySelector('select[name="receiver_district"]');
            const subtotalEl = document.getElementById('checkout-subtotal');
            const shippingEl = document.getElementById('checkout-shipping');
            const totalEl = document.getElementById('checkout-total');
            const locationMap = @json($locations);

            if (!citySelect || !districtSelect || !subtotalEl || !shippingEl || !totalEl) {
                return;
            }

            const baseSubtotal = Number(subtotalEl.dataset.value || 0);
            const originalDistrict = districtSelect.dataset.selected;

            const formatCurrency = (value) => {
                return new Intl.NumberFormat('vi-VN').format(value) + '₫';
            };

            const calculateFeeFromCity = (cityCode) => {
                if (!cityCode) {
                    return 0;
                }
                return cityCode === 'hanoi' ? 30000 : 40000;
            };

            const populateDistricts = (cityCode, preset) => {
                const districts = locationMap[cityCode]?.districts || {};
                districtSelect.innerHTML = '';
                let firstOptionValue = null;

                Object.entries(districts).forEach(([code, name], index) => {
                    const option = document.createElement('option');
                    option.value = code;
                    option.textContent = name;
                    if (index === 0) {
                        firstOptionValue = code;
                    }
                    if (preset && preset === code) {
                        option.selected = true;
                    }
                    districtSelect.appendChild(option);
                });

                if (!districtSelect.value && firstOptionValue) {
                    districtSelect.value = firstOptionValue;
                }
            };

            const updateTotals = () => {
                const fee = calculateFeeFromCity(citySelect.value);
                shippingEl.dataset.value = fee;
                shippingEl.textContent = formatCurrency(fee);
                
                // Lấy discount amount từ element hoặc từ PHP
                const discountEl = document.getElementById('checkout-discount');
                const discountAmount = discountEl ? Number(discountEl.dataset.value || 0) : 0;
                
                // Tính tổng: subtotal - discount + shipping
                const finalTotal = baseSubtotal - discountAmount + fee;
                totalEl.textContent = formatCurrency(finalTotal);
            };

            populateDistricts(citySelect.value, originalDistrict);
            updateTotals();

            citySelect.addEventListener('change', () => {
                populateDistricts(citySelect.value);
                updateTotals();
            });
        });
    </script>
@endsection

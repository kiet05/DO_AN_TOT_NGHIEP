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
                            {{-- Nếu sau dùng voucher thì bật đoạn này --}}
                            {{-- 
                            <h6 class="coupon__code">
                                <span class="icon_tag_alt"></span>
                                Bạn có mã giảm giá? <a href="#">Nhấn vào đây</a> để nhập mã
                            </h6>
                            --}}
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

                            <div class="checkout__input">
                                <p>Địa chỉ nhận hàng<span>*</span></p>
                                <input type="text" name="receiver_address" class="checkout__input__add"
                                    placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                    value="{{ old('receiver_address', $user->address ?? '') }}">
                                @error('receiver_address')
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
                                    <li>Tạm tính <span>{{ number_format($cart->total_price, 0, ',', '.') }}₫</span></li>
                                    <li>Phí vận chuyển <span>0₫</span></li>
                                    <li>Tổng cộng
                                        <span>{{ number_format($cart->total_price, 0, ',', '.') }}₫</span>
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
@endsection

@extends('frontend.layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Đang xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];
        $statusText = $statusLabels[$order->order_status] ?? ucfirst($order->order_status ?? 'pending');
    @endphp
    <style>
        .thankyou-hero {
            padding: 70px 0;
            background: linear-gradient(135deg, #fef3f4 0%, #f7f4ff 100%);
            text-align: center;
        }

        .thankyou-hero__icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 15px 35px rgba(229, 54, 55, 0.2);
        }

        .thankyou-hero__icon svg {
            width: 30px;
            height: 30px;
            color: #27ae60;
        }

        .thankyou-hero h2 {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #111;
        }

        .thankyou-hero p {
            color: #555;
            margin-bottom: 0;
        }

        .thankyou-meta {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .thankyou-meta__item {
            background: #fff;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .thankyou-actions {
            margin-top: 35px;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .thankyou-btn {
            min-width: 220px;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .thankyou-btn--primary {
            background: #111;
            color: #fff;
        }

        .thankyou-btn--primary:hover {
            background: #e53637;
            color: #fff;
        }

        .thankyou-btn--outline {
            border: 1px solid #111;
            color: #111;
        }

        .thankyou-btn--outline:hover {
            background: #111;
            color: #fff;
        }

        .order-detail {
            padding: 60px 0 80px;
            background: #fff;
        }

        .order-card {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            height: 100%;
        }

        .order-card h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .order-summary__list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .order-summary__list li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
            color: #333;
        }

        .order-summary__list li.total {
            font-weight: 700;
            font-size: 16px;
            color: #e53637;
        }

        .order-items {
            margin-top: 35px;
        }

        .order-items__item {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
            background: #fafafa;
        }

        .order-items__info h5 {
            margin-bottom: 6px;
            font-size: 16px;
            color: #111;
        }

        .order-items__info span {
            display: block;
            font-size: 14px;
            color: #666;
        }

        .order-items__price {
            font-weight: 600;
            font-size: 16px;
            color: #111;
        }
    </style>

    <section class="thankyou-hero">
        <div class="container">
            <div class="thankyou-hero__icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <h2>Cảm ơn bạn đã đặt hàng!</h2>
            <p>Đơn hàng của bạn đã được ghi nhận và chuyển đến quản trị viên.</p>
            <p>Nhân viên sẽ sớm liên hệ qua số <strong>{{ $order->receiver_phone }}</strong>
                @if($order->user?->email)
                    hoặc email <strong>{{ $order->user->email }}</strong>
                @endif
                để xác nhận giao hàng.</p>

            <div class="thankyou-meta">
                <div class="thankyou-meta__item">
                    <strong>Mã đơn</strong>
                    <div>#{{ $order->id }}</div>
                </div>
                <div class="thankyou-meta__item">
                    <strong>Tổng thanh toán</strong>
                    <div>{{ number_format($order->final_amount, 0, ',', '.') }} đ</div>
                </div>
                <div class="thankyou-meta__item">
                    <strong>Trạng thái</strong>
                    <div>{{ $statusText }}</div>
                </div>
            </div>

            <div class="thankyou-actions">
                <a href="{{ route('products.index') }}" class="thankyou-btn thankyou-btn--primary">Tiếp tục mua sắm</a>
                <a href="#order-detail" class="thankyou-btn thankyou-btn--outline">Xem lại đơn hàng đã đặt</a>
            </div>
        </div>
    </section>

    <section id="order-detail" class="order-detail">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="order-card">
                        <h4>Tóm tắt thanh toán</h4>
                        <ul class="order-summary__list">
                            <li>
                                <span>Tổng tiền hàng</span>
                                <span>{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                            </li>
                            <li>
                                <span>Phí vận chuyển</span>
                                <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                            </li>
                            <li class="total">
                                <span>Thanh toán</span>
                                <span>{{ number_format($order->final_amount, 0, ',', '.') }} đ</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="order-card">
                        <h4>Thông tin giao hàng</h4>
                        <ul class="order-summary__list">
                            <li>
                                <span>Người nhận</span>
                                <span>{{ $order->receiver_name }}</span>
                            </li>
                            <li>
                                <span>Số điện thoại</span>
                                <span>{{ $order->receiver_phone }}</span>
                            </li>
                            <li style="flex-direction: column; align-items: flex-start;">
                                <span>Địa chỉ</span>
                                <span>{{ $order->receiver_address }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="order-items">
                <h4>Danh sách sản phẩm</h4>
                @foreach ($order->orderItems as $item)
                    @php
                        $productName = $item->productVariant?->product?->name ?? $item->product?->name ?? 'Sản phẩm';
                    @endphp
                    <div class="order-items__item">
                        <div class="order-items__info">
                            <h5>{{ $productName }}</h5>
                            <span>Số lượng: {{ $item->quantity }}</span>
                            <span>Đơn giá: {{ number_format($item->price, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="order-items__price">
                            {{ number_format($item->subtotal, 0, ',', '.') }} đ
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection


@extends('frontend.layouts.app')

@section('title', 'Thanh toán thất bại')

@section('content')
    <section class="checkout-failed" style="padding: 80px 0; background: #f9f9f9;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="failed-card"
                        style="background: #fff; border-radius: 12px; padding: 40px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                        <!-- Icon failed -->
                        <div class="failed-icon" style="margin-bottom: 25px;">
                            <div
                                style="width: 80px; height: 80px; margin: 0 auto; background: #dc3545; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-times" style="font-size: 40px; color: #fff;"></i>
                            </div>
                        </div>

                        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 15px; color: #111;">
                            Thanh toán thất bại!
                        </h2>

                        @if (session('error'))
                            <div class="alert alert-danger" style="border-radius: 8px; margin-bottom: 25px;">
                                {{ session('error') }}
                            </div>
                        @elseif(isset($message))
                            <div class="alert alert-danger" style="border-radius: 8px; margin-bottom: 25px;">
                                {{ $message }}
                            </div>
                        @else
                            <p style="font-size: 15px; color: #666; margin-bottom: 30px;">
                                Đã có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.
                            </p>
                        @endif

                        <!-- Buttons -->
                        <div class="action-buttons"
                            style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary"
                                style="padding: 12px 30px; border-radius: 25px; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-redo me-2"></i>Thử lại
                            </a>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary"
                                style="padding: 12px 30px; border-radius: 25px; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-shopping-cart me-2"></i>Quay lại giỏ hàng
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary"
                                style="padding: 12px 30px; border-radius: 25px; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-home me-2"></i>Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

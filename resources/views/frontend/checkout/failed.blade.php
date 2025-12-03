@extends('frontend.layouts.app')

@section('title', 'Thanh toán thất bại')

<style>
    .failed-container {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px 0;
    }

    .failed-card {
        background: #fff;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        max-width: 500px;
    }

    .failed-icon {
        width: 80px;
        height: 80px;
        background: #fee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }

    .failed-icon svg {
        width: 40px;
        height: 40px;
        color: #e53637;
    }

    .failed-card h2 {
        font-size: 24px;
        font-weight: 600;
        color: #e53637;
        margin-bottom: 15px;
    }

    .failed-card p {
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-custom {
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #111;
        color: #fff;
    }

    .btn-primary:hover {
        background: #e53637;
        color: #fff;
    }

    .btn-outline {
        border: 1px solid #111;
        color: #111;
        background: transparent;
    }

    .btn-outline:hover {
        background: #111;
        color: #fff;
    }
</style>

@section('content')
<div class="failed-container">
    <div class="container">
        <div class="failed-card">
            <div class="failed-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            
            <h2>Thanh toán không thành công</h2>
            
            <p>{{ $message ?? 'Đã có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại sau.' }}</p>
            
            @if(isset($vnp_ResponseCode))
                <p class="text-muted small">Mã lỗi: {{ $vnp_ResponseCode }}</p>
            @endif
            
            <div class="btn-group">
                <a href="{{ route('cart.index') }}" class="btn-custom btn-primary">
                    Quay lại giỏ hàng
                </a>
                <a href="{{ route('home') }}" class="btn-custom btn-outline">
                    Về trang chủ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
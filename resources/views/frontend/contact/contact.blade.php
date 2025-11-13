@extends('frontend.layouts.app')

@section('title', 'Liên hệ')

@push('styles')
    <style>
        .contact-page-wrapper {
            padding-top: 40px;
            padding-bottom: 60px;
            background-color: #fff;
        }

        .contact-title {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .contact-info-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 24px;
            font-size: 14px;
            color: #444;
        }

        .contact-info-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .contact-info-icon {
            width: 20px;
            margin-right: 8px;
            color: #000;
            font-size: 15px;
            line-height: 1.4;
        }

        .contact-section-title {
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .contact-form .form-control {
            border-radius: 0;
            border-color: #e5e5e5;
            padding: 10px 12px;
            font-size: 14px;
        }

        .contact-form .form-control:focus {
            box-shadow: none;
            border-color: #000;
        }

        .contact-form label {
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .contact-submit-btn {
            border-radius: 0;
            width: 100%;
            padding: 12px 18px;
            font-size: 14px;

            background: #000;
            border-color: #000;
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.03em;

            transition: all 0.5s ease;
        }

        .contact-submit-btn:hover {
            background: #a3940c;
            color: #000;
            border-color: #000;
        }


        .contact-map iframe {
            border: 0;
            width: 100%;
            min-height: 420px;
            border-radius: 4px;
        }

        @media (max-width: 992px) {
            .contact-page-wrapper {
                padding-top: 24px;
            }

            .contact-map {
                margin-top: 24px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="contact-page-wrapper">
        <div class="container">

            {{-- Breadcrumb nhỏ --}}
            <nav aria-label="breadcrumb" class="mb-3 small">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
                </ol>
            </nav>

            <div class="row">
                {{-- Cột trái: thông tin công ty + form --}}
                <div class="col-lg-6">
                    <h1 class="contact-title">
                        SHOP THỜI TRANG EGA {{ strtoupper(config('', '')) }}
                    </h1>

                    <ul class="contact-info-list">
                        <li>
                            <span class="contact-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <span>
                                Địa chỉ: Tầng 8, tòa nhà Ford, số 313 Trường Chinh, quận Thanh Xuân, Hà Nội
                            </span>
                        </li>
                        <li>
                            <span class="contact-info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </span>
                            <span>
                                Số điện thoại: 0964 942 121
                            </span>
                        </li>
                        <li>
                            <span class="contact-info-icon">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <span>
                                Email: cskh@ega.vn
                            </span>
                        </li>
                    </ul>

                    <h5 class="contact-section-title">Liên hệ với chúng tôi</h5>

                    {{-- Flash message --}}
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Form liên hệ --}}
                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf

                        <div class="mb-3">
                            <label for="contact-name">Họ tên*</label>
                            <input id="contact-name" type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact-email">Email*</label>
                            <input id="contact-email" type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact-phone">Số điện thoại*</label>
                            <input id="contact-phone" type="text" name="phone"
                                class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="contact-message">Nhập nội dung*</label>
                            <textarea id="contact-message" name="message" rows="4" class="form-control @error('message') is-invalid @enderror"
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn contact-submit-btn">
                            Gửi liên hệ của bạn
                        </button>
                    </form>

                    {{-- FAQ (nếu có truyền $faqs từ controller) --}}
                    @if (isset($faqs) && $faqs->count())
                        <h5 class="contact-section-title mt-5">Câu hỏi thường gặp</h5>
                        <div class="accordion" id="faqAccordion">
                            @foreach ($faqs as $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#faqCollapse{{ $faq->id }}" aria-expanded="false"
                                            aria-controls="faqCollapse{{ $faq->id }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse"
                                        aria-labelledby="faqHeading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {!! nl2br(e($faq->answer)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Cột phải: bản đồ --}}
                <div class="col-lg-6 contact-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.644427634824!2d105.8229086760539!3d21.0070253885404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab74b4d0d3f7%3A0x0000000000000000!2s313%20Tr%C6%B0%E1%BB%9Dng%20Chinh!5e0!3m2!1svi!2svi!4v1700000000000!5m2!1svi!2svi"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

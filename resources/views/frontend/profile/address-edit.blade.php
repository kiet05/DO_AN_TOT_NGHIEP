@extends('frontend.layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <div class="container mx-auto py-10 px-4 sm:px-6 lg:px-8 max-w-3xl">

            {{-- HEADER --}}
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>

                    <h1 class="text-2xl font-bold text-slate-900 mt-1">
                        Sửa địa chỉ giao hàng
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Cập nhật thông tin địa chỉ để dùng nhanh khi đặt hàng.
                    </p>
                </div>

                <a href="{{ url('/profile') }}" class="btn-back">
                    ← Quay lại hồ sơ
                </a>


            </div>

            {{-- ALERTS --}}
            @if (session('success'))
                <div class="alert-box success">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-box error">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- FORM SỬA ĐỊA CHỈ --}}
            <div class="card">
                <h2 class="card-title-small mb-4">
                    Thông tin địa chỉ
                </h2>

                <form action="{{ route('profile.addresses.update', $address) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- HỌ TÊN --}}
                        <div>
                            <label class="label">Họ tên người nhận *</label>
                            <input type="text" name="receiver_name" class="input-field"
                                value="{{ old('receiver_name', $address->receiver_name) }}" required>
                            @error('receiver_name')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SĐT --}}
                        <div>
                            <label class="label">Số điện thoại *</label>
                            <input type="text" name="receiver_phone" class="input-field"
                                value="{{ old('receiver_phone', $address->receiver_phone) }}" required>
                            @error('receiver_phone')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ĐỊA CHỈ CHI TIẾT --}}
                        <div class="md:col-span-2">
                            <label class="label">Địa chỉ chi tiết *</label>
                            <input type="text" name="receiver_address_detail" class="input-field"
                                value="{{ old('receiver_address_detail', $address->receiver_address_detail) }}" required>
                            @error('receiver_address_detail')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        

                        {{-- QUẬN / HUYỆN --}}
                        <div>
                            <label class="label">Quận / Huyện *</label>
                            <input type="text" name="receiver_district" class="input-field"
                                value="{{ old('receiver_district', $address->receiver_district) }}" required>
                            @error('receiver_district')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- TỈNH / THÀNH PHỐ --}}
                        <div>
                            <label class="label">Tỉnh / Thành phố *</label>
                            <input type="text" name="receiver_city" class="input-field"
                                value="{{ old('receiver_city', $address->receiver_city) }}" required>
                            @error('receiver_city')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- MẶC ĐỊNH --}}
                        <div class="flex items-center gap-2 mt-1 md:col-span-2">
                            <input type="checkbox" name="is_default" id="is_default"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                            <label for="is_default" class="text-xs text-gray-600">
                                Đặt làm địa chỉ mặc định
                            </label>
                        </div>
                    </div>

                    <div
                        class="mt-4 border-t border-dashed border-gray-200 pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-gray-400">
                            Đảm bảo địa chỉ chính xác để đơn hàng được giao đúng nơi, đúng người.
                        </p>
                        <div class="flex items-center gap-2">
                            <a href="{{ url('/profile') }}" class="btn-cancel">
                                Huỷ
                            </a>

                            <button type="submit" class="btn-indigo-lg">
                                Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @push('styles')
        <style>
            /* CARD CHUNG */
            .card {
                background: #ffffff;
                border-radius: 1rem;
                border: 1px solid #e5e7eb;
                padding: 1.5rem 1.75rem;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            }

            .card-title-small {
                font-size: 0.9rem;
                font-weight: 600;
                color: #111827;
                margin-bottom: 0.75rem;
            }

            /* LABEL + INPUT */
            .label {
                display: block;
                font-size: 0.78rem;
                font-weight: 600;
                color: #4b5563;
                margin-bottom: 0.25rem;
                letter-spacing: 0.02em;
                text-transform: none;
            }

            .input-field {
                width: 100%;
                border-radius: 999px;
                border: 1px solid #e5e7eb;
                padding: 0.55rem 0.9rem;
                font-size: 0.85rem;
                color: #111827;
                outline: none;
                background-color: #ffffff;
                transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            }

            .input-field:focus {
                border-color: #4f46e5;
                box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.12);
                background-color: #f9fafb;
            }

            .input-field::placeholder {
                color: #9ca3af;
            }

            /* BUTTON CHÍNH */
            .btn-indigo-lg {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                border: 1px solid #4f46e5;
                background: #4f46e5;
                color: #ffffff;
                font-size: 0.78rem;
                font-weight: 600;
                padding: 0.55rem 1.5rem;
                letter-spacing: 0.02em;
                cursor: pointer;
                transition: background 0.15s ease, border-color 0.15s ease,
                    box-shadow 0.15s ease, transform 0.08s ease;
                text-decoration: none;
                white-space: nowrap;
            }

            .btn-indigo-lg:hover {
                background: #4338ca;
                border-color: #4338ca;
                box-shadow: 0 12px 24px rgba(79, 70, 229, 0.25);
                transform: translateY(-1px);
            }

            .btn-indigo-lg:active {
                transform: translateY(0);
                box-shadow: none;
            }

            /* ALERT BOX */
            .alert-box {
                border-radius: 0.75rem;
                padding: 0.6rem 0.9rem;
                font-size: 0.8rem;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.45rem;
            }

            .alert-box.success {
                background: #ecfdf3;
                border: 1px solid #bbf7d0;
                color: #166534;
            }

            .alert-box.error {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #b91c1c;
            }

            /* TUNE NHẸ PHẦN WRAPPER */
            .min-h-screen.bg-slate-50 {
                padding-bottom: 3rem;
            }

            @media (max-width: 640px) {
                .card {
                    padding: 1.25rem 1.1rem;
                    border-radius: 0.9rem;
                }
            }

            /* NÚT QUAY LẠI HỒ SƠ */
            .btn-back {
                display: inline-flex;
                align-items: center;
                padding: 6px 14px;
                font-size: 12px;
                font-weight: 600;
                border-radius: 999px;
                border: 1px solid #d1d5db;
                color: #374151;
                background: #ffffff;
                transition: all 0.15s ease;
                text-decoration: none;
            }

            .btn-back:hover {
                background: #f3f4f6;
                border-color: #cbd5e1;
                color: #1f2937;
            }

            /* NÚT HUỶ (trong footer form) */
            .btn-cancel {
                padding: 8px 20px;
                font-size: 12px;
                font-weight: 600;
                border-radius: 999px;
                border: 1px solid #d1d5db;
                background: #ffffff;
                color: #374151;
                transition: all 0.15s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .btn-cancel:hover {
                background: #f3f4f6;
                color: #1f2937;
                border-color: #cbd5e1;
            }
        </style>
    @endpush
@endsection

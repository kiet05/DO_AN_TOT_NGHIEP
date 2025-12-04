@extends('frontend.layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto py-10 px-4 sm:px-6 lg:px-8 max-w-5xl">

            {{-- HERO HEADER --}}
            <div class="profile-hero mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="profile-hero-eyebrow">Tài khoản khách hàng</p>
                        <h1 class="profile-hero-title">
                            Hồ sơ cá nhân
                        </h1>
                        <p class="profile-hero-desc">
                            Quản lý thông tin, bảo mật tài khoản và các cài đặt cá nhân của bạn tại một nơi.
                        </p>
                    </div>

                    <div class="profile-hero-summary">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Xin chào</p>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500">Khách hàng</span>
                            <span class="font-medium text-gray-900 truncate max-w-[60%] text-right">
                                {{ $user->name }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium text-gray-900 truncate max-w-[60%] text-right">
                                {{ $user->email }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-500">Số điện thoại</span>
                            <span class="font-medium text-gray-900">
                                {{ $user->phone ?: 'Chưa cập nhật' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-indigo-100/70 mt-1">
                            Thành viên từ {{ optional($user->created_at)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ALERT SUCCESS --}}
            @if (session('success'))
                <div class="alert-box success">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- ALERT ERROR --}}
            @if (session('error'))
                <div class="alert-box error">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- LAYOUT --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">

                    {{-- SECTION 1: THÔNG TIN CÁ NHÂN --}}
                    <div class="card profile-section" data-section="info">
                        <button type="button" class="section-header" data-section-toggle="info">
                            <div>
                                <h2 class="card-title-small">
                                    Thông tin cá nhân
                                </h2>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Dùng cho giao hàng và xuất hóa đơn.
                                </p>
                            </div>
                            <div class="section-chevron">
                                ▶
                            </div>
                        </button>

                        <div class="section-body">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- Name --}}
                                    <div>
                                        <label class="label">Họ và tên *</label>
                                        <input type="text" name="name" class="input-field"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div>
                                        <label class="label">Email đăng nhập</label>
                                        <input type="email" value="{{ $user->email }}" disabled
                                            class="input-field input-field-disabled">
                                        <p class="text-xs text-gray-400 mt-1">
                                            Email dùng để đăng nhập và nhận thông báo hệ thống.
                                        </p>
                                    </div>

                                    {{-- Phone --}}
                                    <div>
                                        <label class="label">Số điện thoại</label>
                                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                            class="input-field" placeholder="Ví dụ: 0987xxxxxx">
                                        @error('phone')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Display name --}}
                                    <div>
                                        <label class="label">Hiển thị tên</label>
                                        <input type="text" value="{{ $user->name }}"
                                            class="input-field input-field-muted" disabled>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Tên này sẽ hiển thị trên hóa đơn và trong lịch sử đơn hàng.
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="mt-6 border-t border-dashed border-gray-200 pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                                    <p class="text-xs text-gray-400">
                                        Kiểm tra kỹ thông tin trước khi lưu để tránh sai sót khi giao hàng.
                                    </p>
                                    <button class="btn-indigo-lg" type="submit">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- SECTION 2: SỔ ĐỊA CHỈ GIAO HÀNG --}}
                    <div class="card profile-section" data-section="address">
                        <button type="button" class="section-header" data-section-toggle="address">
                            <div>
                                <h2 class="card-title-small">
                                    Địa chỉ giao hàng
                                </h2>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Lưu nhiều địa chỉ để khi đặt hàng chỉ cần chọn, không phải nhập lại.
                                </p>
                            </div>
                            <div class="section-chevron">
                                ▶
                            </div>
                        </button>

                        <div class="section-body">

                            {{-- DANH SÁCH ĐỊA CHỈ --}}
                            @if (isset($addresses) && $addresses->count())
                                <div class="space-y-3 mb-5">
                                    @foreach ($addresses as $address)
                                        <div class="address-card">
                                            <div class="address-card-main">
                                                <p class="address-card-name">
                                                    {{ $address->receiver_name }}
                                                    @if ($address->is_default)
                                                        <span class="address-tag-default">
                                                            Mặc định
                                                        </span>
                                                    @endif
                                                </p>

                                                <p class="address-card-phone">
                                                    {{ $address->phone }}
                                                </p>

                                                <p class="address-card-address">
                                                    {{ $address->address_line }},
                                                    {{ $address->district }},
                                                    {{ $address->province }}
                                                </p>
                                            </div>

                                            <div class="address-card-actions">
                                                {{-- Đặt mặc định --}}
                                                @unless ($address->is_default)
                                                    <form action="{{ route('profile.addresses.set-default', $address) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn-chip btn-chip-primary">
                                                            Đặt mặc định
                                                        </button>
                                                    </form>
                                                @endunless

                                                {{-- Sửa --}}
                                                <a href="{{ route('profile.addresses.edit', $address) }}"
                                                    class="btn-chip btn-chip-edit">
                                                    Sửa
                                                </a>

                                                {{-- Xoá --}}
                                                <form action="{{ route('profile.addresses.destroy', $address) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn xoá địa chỉ này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-chip btn-chip-danger">
                                                        Xoá
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 mb-5">
                                    Bạn chưa lưu địa chỉ nào. Hãy thêm địa chỉ đầu tiên để khi đặt hàng có thể chọn nhanh.
                                </p>
                            @endif

                            {{-- FORM THÊM ĐỊA CHỈ MỚI --}}
                            <div class="border-t border-dashed border-gray-200 pt-4 mt-2">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">
                                    Thêm địa chỉ mới
                                </h3>

                                <form action="{{ route('profile.addresses.store') }}" method="POST">
                                    @csrf

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="label">Họ tên người nhận *</label>
                                            <input type="text" name="receiver_name" class="input-field"
                                                value="{{ old('receiver_name', $user->name) }}" required>
                                            @error('receiver_name')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="label">Số điện thoại *</label>
                                            <input type="text" name="phone" class="input-field"
                                                value="{{ old('phone', $user->phone) }}" required>
                                            @error('phone')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="label">Địa chỉ chi tiết *</label>
                                            <input type="text" name="address_line" class="input-field"
                                                placeholder="Số nhà, tên đường..." value="{{ old('address_line') }}"
                                                required>
                                            @error('address_line')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>



                                        <div>
                                            <label class="label">Quận / Huyện *</label>
                                            <input type="text" name="district" class="input-field"
                                                placeholder="VD tên Quận: Cầu Giấy..." value="{{ old('district') }}"
                                                required>
                                            @error('district')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="label">Tỉnh / Thành phố *</label>
                                            <input type="text" name="province" class="input-field"
                                                placeholder="VD tên Thành phố: Hà Nội..." value="{{ old('province') }}"
                                                required>
                                            @error('province')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>


                                    </div>

                                    <div class="flex items-center justify-end">
                                        <button type="submit" class="btn-address-submit">
                                            + Lưu địa chỉ
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: BẢO MẬT & MẬT KHẨU --}}
                    <div class="card card-soft border-red-100 profile-section" data-section="password">
                        <button type="button" class="section-header" data-section-toggle="password">
                            <div>
                                <h2 class="card-title-small text-red-600">
                                    Bảo mật & mật khẩu
                                </h2>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Nên đổi mật khẩu định kỳ 3–6 tháng một lần.
                                </p>
                            </div>
                            <div class="section-chevron">
                                ▶
                            </div>
                        </button>

                        <div class="section-body">
                            <div class="alert-warning mb-6">
                                <strong class="block text-sm font-semibold text-red-800">Yêu cầu xác thực</strong>
                                <p class="text-sm text-red-700">
                                    Bạn cần nhập chính xác mật khẩu hiện tại để đặt mật khẩu mới.
                                </p>
                            </div>

                            <form action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    {{-- Current Password --}}
                                    <div>
                                        <label class="label">Mật khẩu hiện tại *</label>
                                        <input type="password" class="input-field" name="current_password" required>
                                        @error('current_password')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- New Password --}}
                                    <div>
                                        <label class="label">Mật khẩu mới *</label>
                                        <input type="password" class="input-field" name="password" required>
                                        @error('password')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-400 mt-1">
                                            Tối thiểu 8 ký tự, nên có chữ hoa, chữ thường, số và ký tự đặc biệt.
                                        </p>
                                    </div>

                                    {{-- Confirm --}}
                                    <div>
                                        <label class="label">Xác nhận mật khẩu *</label>
                                        <input type="password" class="input-field" name="password_confirmation" required>
                                        @error('password_confirmation')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>

                                <div
                                    class="mt-6 border-t border-dashed border-gray-200 pt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                                    <p class="text-xs text-gray-400">
                                        Sau khi đổi mật khẩu, bạn nên đăng xuất khỏi các thiết bị không sử dụng chung.
                                    </p>
                                    <button class="btn-red-lg" type="submit">
                                        Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- CSS + JS --}}
    <style>
        /* HERO */
        .profile-hero {
            background: radial-gradient(circle at top left, #4f46e5 0%, #111827 55%, #020617 100%);
            border-radius: 1.5rem;
            padding: 1.9rem 2rem;
            color: #e5e7eb;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.35);
            position: relative;
            overflow: hidden;
        }

        .profile-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 100% -20%, rgba(129, 140, 248, 0.25), transparent 55%);
            opacity: .9;
            pointer-events: none;
        }

        .profile-hero>* {
            position: relative;
            z-index: 1;
        }

        .profile-hero-eyebrow {
            font-size: .7rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(199, 210, 254, 0.85);
            font-weight: 600;
            margin-bottom: .3rem;
        }

        .profile-hero-title {
            font-size: 1.9rem;
            line-height: 1.15;
            font-weight: 800;
            color: #f9fafb;
            margin-bottom: .25rem;
        }

        @media (min-width: 640px) {
            .profile-hero-title {
                font-size: 2.1rem;
            }
        }

        .profile-hero-desc {
            font-size: .9rem;
            color: rgba(226, 232, 240, 0.9);
            max-width: 36rem;
        }

        .profile-hero-summary {
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(16px);
            border-radius: 1rem;
            padding: .9rem 1.1rem;
            border: 1px solid rgba(148, 163, 184, 0.45);
            min-width: 210px;
            max-width: 260px;
            font-size: .85rem;
        }

        /* CARD */
        .card {
            background: white;
            padding: 1.4rem 1.5rem;
            border-radius: 1.25rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease, background-color .18s ease;
        }

        .card:hover {
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.13);
            transform: translateY(-1px);
            border-color: #e0e7ff;
        }

        .card-soft {
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .card-title-small {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .label {
            font-weight: 600;
            font-size: .85rem;
            color: #374151;
            margin-bottom: .3rem;
            display: block;
        }

        .input-field {
            width: 100%;
            border: 1px solid #d1d5db;
            padding: .7rem .75rem;
            border-radius: .6rem;
            font-size: .9rem;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease, transform .12s ease;
            background-color: #fff;
        }

        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.18);
            outline: none;
            background-color: #fff;
            transform: translateY(-0.5px);
        }

        .input-field-disabled {
            background-color: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }

        .input-field-muted {
            background-color: #f9fafb;
            color: #6b7280;
        }

        .btn-indigo-lg {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            padding: .75rem 2.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .9rem;
            border: none;
            cursor: pointer;
            transition: opacity .18s ease, transform .12s ease, box-shadow .18s ease;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.35);
        }

        .btn-indigo-lg:hover {
            opacity: .95;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(79, 70, 229, 0.4);
        }

        .btn-red-lg {
            background: linear-gradient(135deg, #dc2626, #f97373);
            color: white;
            padding: .75rem 2.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .9rem;
            border: none;
            cursor: pointer;
            transition: opacity .18s ease, transform .12s ease, box-shadow .18s ease;
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.35);
        }

        .btn-red-lg:hover {
            opacity: .96;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(220, 38, 38, 0.4);
        }

        .alert-box {
            padding: 1rem 1.1rem;
            border-left: 4px solid;
            border-radius: .75rem;
            margin-bottom: 1.5rem;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .alert-box.success {
            background: #ecfdf5;
            color: #065f46;
            border-color: #34d399;
        }

        .alert-box.error {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #f87171;
        }

        .alert-warning {
            background: #fef2f2;
            padding: 1rem;
            border-left: 4px solid #dc2626;
            border-radius: .75rem;
        }

        /* ACCORDION */
        .section-header {
            width: 100%;
            padding: .15rem 0 .2rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: transparent;
            border: none;
            text-align: left;
            cursor: pointer;
        }

        .section-body {
            display: none;
            margin-top: 1rem;
        }

        .profile-section.active .section-body {
            display: block;
        }

        .section-chevron {
            font-size: .8rem;
            color: #9ca3af;
            transition: transform .18s ease, color .18s ease;
        }

        .profile-section.active .section-chevron {
            transform: rotate(90deg);
            color: #4f46e5;
        }

        /* BUTTON CHO SỔ ĐỊA CHỈ */
        .btn-chip {
            border-radius: 999px;
            padding: .4rem .9rem;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            font-weight: 500;
            font-size: .75rem;
            color: #374151;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, transform .1s ease;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
        }

        .btn-chip:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            transform: translateY(-0.5px);
        }

        .btn-chip-primary {
            border-color: #c7d2fe;
            color: #4338ca;
            background: #eef2ff;
        }

        .btn-chip-primary:hover {
            background: #e0e7ff;
            border-color: #a5b4fc;
        }

        .btn-chip-danger {
            border-color: #fecaca;
            color: #b91c1c;
            background: #fef2f2;
        }

        .btn-chip-danger:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .btn-address-submit {
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ecfeff;
            padding: .65rem 1.8rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .85rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(15, 118, 110, 0.35);
            transition: opacity .18s ease, transform .12s ease, box-shadow .18s ease;
        }

        .btn-address-submit:hover {
            opacity: .96;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 118, 110, 0.42);
        }

        /* CARD ĐỊA CHỈ GIAO HÀNG */
        .address-card {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.9rem 1.1rem;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            background: radial-gradient(circle at top left, #f9fafb 0%, #ffffff 45%, #f1f5f9 100%);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.12s ease,
                background-color 0.15s ease;
        }

        .address-card:hover {
            border-color: #c7d2fe;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
        }

        .address-card-main {
            flex: 1;
            min-width: 0;
        }

        .address-card-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .address-tag-default {
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .address-card-phone {
            margin-top: 0.2rem;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .address-card-address {
            margin-top: 0.35rem;
            font-size: 0.86rem;
            color: #374151;
        }

        /* KHỐI NÚT BÊN PHẢI */
        .address-card-actions {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            align-items: flex-end;
        }

        /* Re-style chung cho chip, bỏ gạch chân – nếu chưa có thì thêm, có rồi thì chỉ cần thêm text-decoration */
        .btn-chip {
            border-radius: 999px;
            padding: 0.4rem 0.9rem;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            font-weight: 500;
            font-size: 0.75rem;
            color: #374151;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
            /* 👈 bỏ gạch chân */
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease,
                color 0.15s ease, transform 0.1s ease;
        }

        .btn-chip:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            transform: translateY(-0.5px);
        }

        /* Biến thể: Đặt mặc định */
        .btn-chip-primary {
            border-color: #c7d2fe;
            background: #eef2ff;
            color: #4338ca;
        }

        .btn-chip-primary:hover {
            background: #e0e7ff;
            border-color: #a5b4fc;
        }

        /* Biến thể: Xoá */
        .btn-chip-danger {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .btn-chip-danger:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        /* Biến thể: Sửa – trắng, hover xám, click vàng */
        .btn-chip-edit:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        .btn-chip-edit:active,
        .btn-chip-edit:focus-visible {
            background-color: #fef9c3;
            border-color: #facc15;
            color: #92400e;
            outline: none;
        }

        /* RESPONSIVE: MOBILE XUỐNG HÀNG ĐẸP HƠN */
        @media (max-width: 640px) {
            .address-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .address-card-actions {
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-start;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.profile-section');
            const toggles = document.querySelectorAll('[data-section-toggle]');

            function openSection(key) {
                sections.forEach(sec => {
                    if (sec.dataset.section === key) {
                        sec.classList.add('active');
                    } else {
                        sec.classList.remove('active');
                    }
                });
            }

            toggles.forEach(btn => {
                btn.addEventListener('click', function() {
                    const key = this.dataset.sectionToggle;
                    const current = document.querySelector('.profile-section.active');

                    if (current && current.dataset.section === key) {
                        // Nếu đang mở thì đóng lại hết
                        sections.forEach(sec => sec.classList.remove('active'));
                    } else {
                        openSection(key);
                    }
                });
            });

            // Mặc định mở phần Thông tin cá nhân
            openSection('info');
        });
    </script>
@endsection

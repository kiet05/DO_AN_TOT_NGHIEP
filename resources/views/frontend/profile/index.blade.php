@extends('frontend.layouts.app')

@section('content')

<div class="container mx-auto py-10 px-4 sm:px-6 lg:px-8 max-w-4xl">

    {{-- MAIN TITLE --}}
    <div class="mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 pb-2 border-b-2 border-indigo-200">
            Hồ sơ cá nhân
        </h1>
        <p class="text-gray-500 text-lg">Quản lý thông tin, ảnh đại diện và bảo mật tài khoản của bạn.</p>
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

    <div class="space-y-10">

        {{-- CARD 1 – THÔNG TIN CƠ BẢN --}}
        <div class="card">
            <h2 class="card-title indigo">Thông tin cá nhân</h2>

            {{-- AVATAR + INFO --}}
<div class="flex flex-col items-center text-center space-y-6 pb-6 border-b border-gray-100 mb-6">

                {{-- Avatar --}}
    <div class="flex-shrink-0 relative mx-auto sm:mx-0">
                   <img src="{{ asset('storage/' . $user->avatar_path) }}"  alt="Avatar" class="rounded-circle" style="width:120px; height:120px;">

                </div>

                {{-- User Info --}}
                <div class="text-center sm:text-left">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-indigo-600 font-medium">{{ $user->email }}</p>

                    <span class="badge">
                        <i class="fas fa-user mr-1"></i> Thành viên
                    </span>
                </div>
            </div>

            {{-- FORM ĐỔI AVATAR --}}
            <div class="mt-6">
                <h3 class="section-title">Đổi ảnh đại diện</h3>

                <form action="{{ route('profile.avatar.update') }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    <input type="file" name="avatar">
    <button type="submit">Tải lên</button>
</form>

            </div>

            {{-- FORM THÔNG TIN LIÊN HỆ --}}
            <div class="mt-10 pt-6 border-t border-gray-100">
                <h3 class="section-title">Cập nhật thông tin</h3>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Name --}}
                        <div>
                            <label class="label">Tên *</label>
                            <input type="text" name="name" class="input-field"
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="label">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                   class="input-field bg-gray-100 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Không thể thay đổi email.</p>
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="label">Số điện thoại</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   class="input-field" placeholder="Ví dụ: 0987xxxxxx">
                        </div>

                    </div>

                    <div class="mt-8 text-right">
                        <button class="btn-indigo-lg">Lưu thay đổi</button>
                    </div>
                </form>
            </div>

        </div>

        {{-- CARD 2 – ĐỔI MẬT KHẨU --}}
        <div class="card border-red-200">
            <h2 class="card-title red">Bảo mật tài khoản</h2>

            <div class="alert-warning mb-6">
                <strong class="block">Yêu cầu xác thực</strong>
                <p>Bạn phải nhập mật khẩu hiện tại để đổi mật khẩu mới.</p>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Current Password --}}
                    <div>
                        <label class="label">Mật khẩu hiện tại *</label>
                        <input type="password" class="input-field" name="current_password" required>
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="label">Mật khẩu mới *</label>
                        <input type="password" class="input-field" name="password" required>
                    </div>

                    {{-- Confirm --}}
                    <div>
                        <label class="label">Xác nhận mật khẩu *</label>
                        <input type="password" class="input-field" name="password_confirmation" required>
                    </div>

                </div>

                <div class="mt-8 text-right">
                    <button class="btn-red-lg">Đổi mật khẩu</button>
                </div>
            </form>
        </div>

    </div>

</div>

{{-- CSS TỐI GIẢN – MÀU MÈ ĐẸP --}}
<style>
    .card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transition: .2s;
    }
    .card:hover { box-shadow: 0 8px 22px rgba(0,0,0,0.10); }

    .card-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: .75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .card-title.indigo { color: #4f46e5; border-color: #e0e7ff; }
    .card-title.red { color: #dc2626; border-color: #fecaca; }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #374151;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        background: #e0e7ff;
        padding: .3rem .75rem;
        border-radius: 9999px;
        font-size: .85rem;
        color: #4338ca;
        font-weight: 600;
        margin-top: .4rem;
    }

    .label { font-weight: 600; font-size: .9rem; color: #374151; margin-bottom: .3rem; display: block; }

    .input-field {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: .75rem;
        border-radius: .6rem;
        transition: .15s ease;
    }
    .input-field:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.25);
        outline: none;
    }

    .file-input {
        border: 1px solid #d1d5db;
        padding: .6rem;
        border-radius: .5rem;
        background: #f9fafb;
        cursor: pointer;
    }

    .btn-indigo,
    .btn-indigo-lg {
        background: #4f46e5;
        color: white;
        padding: .6rem 1.4rem;
        border-radius: 999px;
        font-weight: 600;
        transition: .2s;
    }
    .btn-indigo:hover { background: #4338ca; }
    .btn-indigo-lg { padding: .8rem 2.2rem; }

    .btn-red-lg {
        background: #dc2626;
        color: white;
        padding: .8rem 2.2rem;
        border-radius: 999px;
        font-weight: 600;
        transition: .2s;
    }
    .btn-red-lg:hover { background: #b91c1c; }

    .alert-box {
        padding: 1rem;
        border-left: 4px solid;
        border-radius: .75rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    .alert-box.success { background: #ecfdf5; color: #065f46; border-color: #34d399; }
    .alert-box.error { background: #fef2f2; color: #b91c1c; border-color: #f87171; }

    .alert-warning {
        background: #fef2f2;
        padding: 1rem;
        border-left: 4px solid #dc2626;
        border-radius: .75rem;
    }
</style>

@endsection

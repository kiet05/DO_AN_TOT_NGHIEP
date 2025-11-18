@extends('layouts.admin.master') {{-- Sử dụng layout 'layouts.admin.master' như bạn đã cung cấp --}}

@section('title', 'Cập nhật Hồ sơ cá nhân')

@section('content')
    <style>
        /* Tái sử dụng style giới hạn chiều rộng và căn giữa */
        .profile-card {
            max-width: 980px;
            margin-inline: auto;
            padding-block: 20px; 
        }

        /* Footer dính để luôn thấy nút Lưu */
        .profile-actions-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px 16px;
            border-top: 1px solid #eee;
            margin: 0px -20px -20px -20px; 
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
            z-index: 10;
        }

        .form-label {
            font-weight: 500;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback.d-block {
            display: block !important;
        }
        
        /* Style cho ảnh đại diện (Đã thêm border-radius 50% để ảnh tròn) */
        .avatar-display {
            width: 100px; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 50%; /* 🌟 Làm ảnh tròn */
            border: 3px solid #007bff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>

    <div class="container-fluid">
        <div class="profile-card">
            {{-- Header/Breadcrumb --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h3 class="mb-1"><i class="bi bi-person-circle me-2"></i>Hồ Sơ Cá Nhân</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Hồ Sơ</li>
                        </ol>
                    </nav>
                </div>
            </div>

            {{-- Thông báo lỗi và thành công --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-1">Vui lòng kiểm tra các trường sau:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🌟 THÊM enctype="multipart/form-data" VÀO FORM --}}
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-4">
                            
                            {{-- PHẦN AVATAR VÀ CHỨC VỤ --}}
                            <div class="col-12 text-center mb-4">
                                @php
                                    // Nếu người dùng có tên file avatar, kiểm tra file thực tế trong public/storage/avatars
                                    $avatarPath = null;
                                    if (!empty($user->avatar)) {
                                        $publicAvatar = public_path('storage/avatars/' . $user->avatar);
                                        if (file_exists($publicAvatar)) {
                                            $avatarPath = asset('storage/avatars/' . $user->avatar) . '?v=' . filemtime($publicAvatar);
                                        }
                                    }

                                    // Nếu không có file hợp lệ, dùng SVG inline placeholder
                                    if (!$avatarPath) {
                                        $avatarPath = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22150%22%3E%3Crect fill=%22%23e9ecef%22 width=%22150%22 height=%22150%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2220%22 fill=%22%23999%22%3ENo Avatar%3C/text%3E%3C/svg%3E';
                                    }
                                @endphp

                                <img src="{{ $avatarPath }}" alt="Avatar" class="rounded-circle mx-auto mb-3 avatar-display" id="avatar-preview" loading="lazy">
                                    
                                <h3 class="h4 mb-1">{{ $user->name }}</h3>
                                
                                {{-- HIỂN THỊ CHỨC VỤ --}}
                                @if ($user->role)
                                    <span class="badge bg-primary text-white">
                                        {{ $user->role->name ?? 'Không rõ' }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Chưa gán chức vụ</span>
                                @endif
                            </div>
                            
                            <hr class="mb-4">

                            {{-- UPLOAD ẢNH ĐẠI DIỆN --}}
                            <div class="col-12">
                                <h5 class="mb-3 border-bottom pb-2">Cập nhật Ảnh đại diện</h5>
                                <div class="col-md-6">
                                    <label class="form-label">Chọn ảnh mới</label>
                                    {{-- 🌟 THÊM ID CHO CHỨC NĂNG PREVIEW --}}
                                    <input type="file" name="avatar" id="avatar-input"
                                        class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                                    @error('avatar')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF.</small>
                                </div>
                            </div>

                            {{-- THÔNG TIN CƠ BẢN --}}
                            <div class="col-12 mt-5">
                                <h5 class="mb-3 border-bottom pb-2">Thông tin tài khoản</h5>
                            </div>

                            {{-- Name Field --}}
                            <div class="col-md-6">
                                <label class="form-label">Tên của bạn <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email Field --}}
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ĐỔI MẬT KHẨU --}}
                            <div class="col-12 mt-5">
                                <h5 class="mb-3 border-bottom pb-2">Đổi Mật Khẩu</h5>
                                <div class="alert alert-info py-2">Bỏ trống 3 trường này nếu bạn không muốn đổi mật khẩu.</div>
                            </div>
                            
                            {{-- Current Password Field --}}
                            <div class="col-md-4">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- New Password Field --}}
                            <div class="col-md-4">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password Confirmation Field --}}
                            <div class="col-md-4">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control">
                            </div>

                        </div>
                    </div> {{-- End Card Body --}}
                    
                    {{-- Footer actions --}}
                    <div class="profile-actions-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2 me-1"></i> Cập nhật
                        </button>
                    </div>
                </div> {{-- End Card --}}

            </form>
        </div>
    </div>

    {{-- 🌟 SCRIPT ĐỂ PREVIEW AVATAR --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar-input');
            const avatarPreview = document.getElementById('avatar-preview');

            if (avatarInput) {
                avatarInput.addEventListener('change', function(event) {
                    // Kiểm tra xem đã có file được chọn chưa
                    if (event.target.files && event.target.files[0]) {
                        const reader = new FileReader();
                        
                        // Khi file được đọc xong, cập nhật src của ảnh preview
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                        };
                        
                        // Đọc file dưới dạng URL Data
                        reader.readAsDataURL(event.target.files[0]);
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
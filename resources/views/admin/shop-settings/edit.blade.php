
@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Cài đặt thông tin shop</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                            <li class="active"><a href="#">Cài đặt shop</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success mg-top-20">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger mg-top-20">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <form action="{{ route('admin.shop-settings.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="logo" class="form-label">Logo Shop</label>
                                                <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                                @if ($setting->logo)
                                                    <div class="mt-3">
                                                        <p class="mb-2">Logo hiện tại:</p>
                                                        <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                                    </div>
                                                @endif
                                                <small class="form-text text-muted">Định dạng: JPEG, PNG, JPG, GIF, WEBP. Kích thước tối đa: 2MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="hotline" class="form-label">Hotline</label>
                                                <input type="text" name="hotline" id="hotline" class="form-control" 
                                                    value="{{ old('hotline', $setting->hotline) }}" 
                                                    placeholder="VD: 0123456789 hoặc 1900xxxx">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" name="email" id="email" class="form-control" 
                                                    value="{{ old('email', $setting->email) }}" 
                                                    placeholder="VD: contact@shop.com">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-4">
                                                <label for="address" class="form-label">Địa chỉ</label>
                                                <textarea name="address" id="address" class="form-control" rows="3" 
                                                    placeholder="VD: 123 Đường ABC, Phường XYZ, Quận 1, TP.HCM">{{ old('address', $setting->address) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="mb-3 mt-4">Mạng xã hội</h5>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="facebook" class="form-label">
                                                    <i class="fab fa-facebook"></i> Facebook
                                                </label>
                                                <input type="url" name="facebook" id="facebook" class="form-control" 
                                                    value="{{ old('facebook', $setting->facebook) }}" 
                                                    placeholder="VD: https://facebook.com/yourpage">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="zalo" class="form-label">Zalo</label>
                                                <input type="text" name="zalo" id="zalo" class="form-control" 
                                                    value="{{ old('zalo', $setting->zalo) }}" 
                                                    placeholder="VD: 0337077804 hoặc link Zalo">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="tiktok" class="form-label">
                                                    <i class="fab fa-tiktok"></i> TikTok
                                                </label>
                                                <input type="url" name="tiktok" id="tiktok" class="form-control" 
                                                    value="{{ old('tiktok', $setting->tiktok) }}" 
                                                    placeholder="VD: https://tiktok.com/@yourpage">
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="sherah-btn sherah-gbcolor">Lưu thay đổi</button>
                                                <a href="{{ route('admin.dashboard') }}" class="sherah-btn" style="background-color: #6c757d; color: white;">Quay lại</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


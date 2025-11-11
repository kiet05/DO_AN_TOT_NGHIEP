@extends('layouts.admin.master')

@section('title', 'Sửa phương thức thanh toán')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="sherah-breadcrumb mg-top-30">
                                        <h2 class="sherah-breadcrumb__title">Sửa phương thức thanh toán</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="#">Sửa phương thức thanh toán</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="sherah-page-inner sherah-border sherah-basic-page sherah-default-bg mg-top-25 p-0">
                                <form class="sherah-wc__form-main"
                                    action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="product-form-box sherah-border mg-top-30">
                                                <h4 class="form-title m-0">Thông tin phương thức thanh toán</h4>
                                                <div class="row">
                                                    <!-- Tên -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Tên <span class="text-danger">*</span></label>
                                                            <div class="form-group__input">
                                                                <input type="text" name="name"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="VD: COD, VNPay"
                                                                    value="{{ old('name', $paymentMethod->name) }}"
                                                                    required>
                                                            </div>
                                                            @error('name')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Slug -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Slug</label>
                                                            <div class="form-group__input">
                                                                <input type="text" name="slug"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="VD: cod, vnpay"
                                                                    value="{{ old('slug', $paymentMethod->slug) }}">
                                                            </div>
                                                            @error('slug')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Tên hiển thị -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Tên hiển thị <span class="text-danger">*</span></label>
                                                            <div class="form-group__input">
                                                                <input type="text" name="display_name"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="VD: Thanh toán khi nhận hàng (COD)"
                                                                    value="{{ old('display_name', $paymentMethod->display_name) }}"
                                                                    required>
                                                            </div>
                                                            @error('display_name')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Mô tả -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Mô tả</label>
                                                            <div class="form-group__input">
                                                                <textarea name="description" class="sherah-wc__form-input" rows="3"
                                                                    placeholder="Mô tả về phương thức thanh toán">{{ old('description', $paymentMethod->description) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Icon -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Icon (URL hoặc class)</label>
                                                            <div class="form-group__input">
                                                                <input type="text" name="icon"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="VD: fas fa-money-bill hoặc URL ảnh"
                                                                    value="{{ old('icon', $paymentMethod->icon) }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Trạng thái -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Trạng thái</label>
                                                            <select name="is_active" class="form-group__input">
                                                                <option value="1" {{ old('is_active', $paymentMethod->is_active) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                                <option value="0" {{ old('is_active', $paymentMethod->is_active) == 0 ? 'selected' : '' }}>Tắt</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Thứ tự -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Thứ tự hiển thị</label>
                                                            <div class="form-group__input">
                                                                <input type="number" name="sort_order"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="Số càng nhỏ càng hiển thị trước"
                                                                    value="{{ old('sort_order', $paymentMethod->sort_order) }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Cấu hình VNPay -->
                                                    @if($paymentMethod->slug === 'vnpay')
                                                        <div class="col-12">
                                                            <div class="product-form-box sherah-border mg-top-30">
                                                                <h4 class="form-title m-0">Cấu hình VNPay</h4>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="sherah-wc__form-label">TMN Code (Terminal ID)</label>
                                                                            <div class="form-group__input">
                                                                                <input type="text" name="config[merchant_id]"
                                                                                    class="sherah-wc__form-input"
                                                                                    placeholder="Nhập TMN Code từ VNPay"
                                                                                    value="{{ old('config.merchant_id', $paymentMethod->config['merchant_id'] ?? '') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="sherah-wc__form-label">Hash Secret</label>
                                                                            <div class="form-group__input">
                                                                                <input type="text" name="config[secret_key]"
                                                                                    class="sherah-wc__form-input"
                                                                                    placeholder="Nhập Hash Secret từ VNPay"
                                                                                    value="{{ old('config.secret_key', $paymentMethod->config['secret_key'] ?? '') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="sherah-wc__form-label">URL thanh toán</label>
                                                                            <div class="form-group__input">
                                                                                <input type="text" name="config[url]"
                                                                                    class="sherah-wc__form-input"
                                                                                    placeholder="https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"
                                                                                    value="{{ old('config.url', $paymentMethod->config['url'] ?? 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="sherah-wc__form-label">Return URL</label>
                                                                            <div class="form-group__input">
                                                                                <input type="text" name="config[return_url]"
                                                                                    class="sherah-wc__form-input"
                                                                                    placeholder="http://yourdomain.com/payment/vnpay/return"
                                                                                    value="{{ old('config.return_url', $paymentMethod->config['return_url'] ?? url('/payment/vnpay/return')) }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mg-top-40 sherah-dflex sherah-dflex-gap-30 justify-content-end">
                                                <button type="submit" class="sherah-btn sherah-btn__primary">
                                                    Cập nhật
                                                </button>
                                                <a href="{{ route('admin.payment-methods.index') }}"
                                                    class="sherah-btn sherah-btn__third">Hủy</a>
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


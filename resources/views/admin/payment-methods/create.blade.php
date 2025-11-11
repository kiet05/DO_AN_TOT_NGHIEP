@extends('layouts.admin.master')

@section('title', 'Thêm phương thức thanh toán')

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
                                        <h2 class="sherah-breadcrumb__title">Thêm phương thức thanh toán</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="#">Thêm phương thức thanh toán</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="sherah-page-inner sherah-border sherah-basic-page sherah-default-bg mg-top-25 p-0">
                                <form class="sherah-wc__form-main"
                                    action="{{ route('admin.payment-methods.store') }}"
                                    method="POST">
                                    @csrf

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
                                                                    value="{{ old('name') }}"
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
                                                            <label class="sherah-wc__form-label">Slug (để trống sẽ tự động tạo)</label>
                                                            <div class="form-group__input">
                                                                <input type="text" name="slug"
                                                                    class="sherah-wc__form-input"
                                                                    placeholder="VD: cod, vnpay"
                                                                    value="{{ old('slug') }}">
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
                                                                    value="{{ old('display_name') }}"
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
                                                                    placeholder="Mô tả về phương thức thanh toán">{{ old('description') }}</textarea>
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
                                                                    value="{{ old('icon') }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Trạng thái -->
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Trạng thái</label>
                                                            <select name="is_active" class="form-group__input">
                                                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Tắt</option>
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
                                                                    value="{{ old('sort_order', 0) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mg-top-40 sherah-dflex sherah-dflex-gap-30 justify-content-end">
                                                <button type="submit" class="sherah-btn sherah-btn__primary">
                                                    Thêm phương thức
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

    @push('scripts')
    <script>
        // Prevent JavaScript errors from breaking the page
        try {
            $(document).ready(function() {
                console.log('Payment method create page loaded');
            });
        } catch(e) {
            console.error('Error in payment method create script:', e);
        }
    </script>
    @endpush
@endsection


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
                                    <h2 class="sherah-breadcrumb__title">Thêm banner mới</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="{{ route('admin.banners.index') }}">Banner</a></li>
                                        <li class="active"><a href="#">Thêm mới</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.banners.index') }}" class="sherah-btn sherah-gbcolor">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
                                </a>
                            </div>
                        </div>

                        {{-- ===== FORM THÊM BANNER ===== --}}
                        <div class="sherah-form sherah-page-inner sherah-border sherah-default-bg mg-top-25 p-4 rounded-3 shadow-sm">
                            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ảnh <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Dung lượng tối đa 2MB, định dạng JPG, PNG, WEBP.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Đường dẫn (Link)</label>
                                    <input type="text" name="link" class="form-control" placeholder="https://...">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vị trí hiển thị</label>
                                    <select name="position" class="form-select">
                                        <option value="top">Top</option>
                                        <option value="middle">Middle</option>
                                        <option value="bottom">Bottom</option>
                                    </select>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                    <label class="form-check-label" for="status">Kích hoạt (Bật)</label>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Lưu banner
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div> {{-- /.sherah-dsinner --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

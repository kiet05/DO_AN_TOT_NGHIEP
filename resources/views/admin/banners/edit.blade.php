@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">

                        {{-- ===== Breadcrumb + Tiêu đề ===== --}}
                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Chỉnh sửa banner</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="{{ route('admin.banners.index') }}">Banner</a></li>
                                        <li class="active"><a href="#">Chỉnh sửa</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.banners.index') }}" class="sherah-btn sherah-gbcolor">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
                                </a>
                            </div>
                        </div>

                        {{-- ===== Form chỉnh sửa ===== --}}
                        <div class="sherah-form sherah-page-inner sherah-border sherah-default-bg mg-top-25 p-4 rounded-3 shadow-sm">
                            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="title" class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control"
                                           value="{{ old('title', $banner->title) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label fw-semibold">Ảnh banner</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*">

                                    @if ($banner->image)
                                        <div class="mt-3">
                                            <p class="fw-semibold mb-1">Ảnh hiện tại:</p>
                                            <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner hiện tại"
                                                 class="border rounded-3" style="width: 280px; height: auto;">
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="link" class="form-label fw-semibold">Liên kết (Link)</label>
                                    <input type="text" name="link" id="link" class="form-control"
                                           placeholder="https://example.com"
                                           value="{{ old('link', $banner->link) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="position" class="form-label fw-semibold">Vị trí hiển thị</label>
                                    <select name="position" id="position" class="form-select">
                                        <option value="top" {{ $banner->position == 'top' ? 'selected' : '' }}>Top</option>
                                        <option value="middle" {{ $banner->position == 'middle' ? 'selected' : '' }}>Middle</option>
                                        <option value="bottom" {{ $banner->position == 'bottom' ? 'selected' : '' }}>Bottom</option>
                                    </select>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                           value="1" {{ $banner->status ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="status">Kích hoạt (Bật)</label>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Cập nhật banner
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

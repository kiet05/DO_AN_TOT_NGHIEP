@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">

                        {{-- ===== Breadcrumb ===== --}}
                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Thêm bài viết mới</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="{{ route('admin.posts.index') }}">Bài viết</a></li>
                                        <li class="active"><a href="#">Thêm mới</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.posts.index') }}" class="sherah-btn sherah-gbcolor">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
                                </a>
                            </div>
                        </div>

                        {{-- ===== Form thêm bài viết ===== --}}
                        <div class="sherah-form sherah-page-inner sherah-border sherah-default-bg mg-top-25 p-4 rounded-3 shadow-sm">
                            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Tiêu đề --}}
                                <div class="mb-3">
                                    <label for="title" class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Nhập tiêu đề bài viết" required>
                                </div>

                                {{-- Nội dung --}}
                                <div class="mb-3">
                                    <label for="content" class="form-label fw-semibold">Nội dung <span class="text-danger">*</span></label>
                                    <textarea name="content" id="content" class="form-control" rows="6" placeholder="Nhập nội dung bài viết..." required></textarea>
                                </div>

                                {{-- Hình ảnh --}}
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-semibold">Hình ảnh</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Định dạng JPG, PNG hoặc WEBP. Tối đa 2MB.</small>
                                </div>

                                {{-- Trạng thái xuất bản --}}
                                <div class="form-check form-switch mb-4">
                                    <input type="hidden" name="is_published" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1">
                                    <label class="form-check-label fw-semibold" for="is_published">Xuất bản ngay</label>
                                </div>

                                {{-- Nút hành động --}}
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Lưu bài viết
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

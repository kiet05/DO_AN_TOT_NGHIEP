@extends('layouts.admin.master')

@section('title', 'Sửa bài viết')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">

                        {{-- ===== Breadcrumb + Header ===== --}}
                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Chỉnh sửa bài viết</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="{{ route('admin.posts.index') }}">Bài viết</a></li>
                                        <li class="active"><a href="#">Chỉnh sửa</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.posts.index') }}" class="sherah-btn sherah-gbcolor">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
                                </a>
                            </div>
                        </div>

                        {{-- ===== Hiển thị lỗi ===== --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <div class="fw-semibold mb-1">Vui lòng kiểm tra lỗi bên dưới:</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ===== Form chỉnh sửa ===== --}}
                        <div class="sherah-form sherah-page-inner sherah-border sherah-default-bg mg-top-25 p-4 rounded-3 shadow-sm">
                            <form method="POST" action="{{ route('admin.posts.update', $post->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control"
                                           value="{{ old('title', $post->title) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nội dung <span class="text-danger">*</span></label>
                                    <textarea name="content" rows="6" class="form-control" required>{{ old('content', $post->content) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ảnh minh họa</label>
                                    @if ($post->image)
                                        <div class="mb-2">
                                            <p class="fw-semibold mb-1">Ảnh hiện tại:</p>
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh hiện tại"
                                                 class="border rounded-3" style="width:280px; height:auto;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Bỏ trống nếu không muốn thay ảnh.</small>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published"
                                           {{ old('is_published', !empty($post->published_at)) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_published">Xuất bản</label>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-1"></i> Lưu thay đổi
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

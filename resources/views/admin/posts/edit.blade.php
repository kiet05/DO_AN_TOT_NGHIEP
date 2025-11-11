@extends('layouts.admin.master')

@section('title', 'Sửa bài viết')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Sản phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="/">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.products.index') }}">Sản phẩm</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="mb-0">
                                    <i class="bi bi-pencil-square me-2"></i>Sửa bài viết
                                </h3>
                                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                                </a>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="fw-semibold mb-1">Vui lòng kiểm tra lỗi bên dưới:</div>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.posts.update', $post->id) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-3">
                                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control"
                                                value="{{ old('title', $post->title) }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                            <textarea name="content" rows="6" class="form-control" required>{{ old('content', $post->content) }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Ảnh minh họa</label>
                                            @if ($post->image)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $post->image) }}" alt="current"
                                                        style="height:90px;object-fit:cover;border-radius:6px">
                                                </div>
                                            @endif
                                            <input type="file" name="image" class="form-control">
                                            <div class="form-text">Bỏ trống nếu không muốn thay ảnh.</div>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_published"
                                                id="is_published"
                                                {{ old('is_published', !empty($post->published_at)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_published">Xuất bản</label>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-1"></i>Lưu thay đổi
                                            </button>
                                            <a href="{{ route('admin.posts.index') }}"
                                                class="btn btn-outline-secondary">Hủy</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.admin.master')

@section('title', 'Sửa bài viết')

@section('content')
    <style>
        .post-card {
            max-width: 980px;
            margin-inline: auto
        }

        .form-text {
            font-size: .875rem
        }

        .is-invalid {
            border-color: #dc3545
        }

        /* Khung preview ảnh 16:9 */
        .post-preview {
            width: 100%;
            max-width: 720px;
            aspect-ratio: 16/9;
            border: 1px dashed #e5e7eb;
            border-radius: 12px;
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden
        }

        .post-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        /* Footer dính */
        .post-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px 16px;
            border-top: 1px solid #eee
        }
    </style>

    <div class="container-fluid">
        <div class="post-card">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h3 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Sửa bài viết</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}">Bài viết</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit #{{ $post->id }}</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.posts.index', ['status' => request('status')]) }}" class="btn btn-light border">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>

            {{-- Lỗi --}}
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

            <form method="POST" action="{{ route('admin.posts.update', $post->id) }}" enctype="multipart/form-data"
                novalidate>
                @csrf
                @method('PUT')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Tiêu đề --}}
                            <div class="col-12">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title', $post->title) }}"
                                    class="form-control @error('title') is-invalid @enderror" required
                                    placeholder="Nhập tiêu đề" maxlength="200" id="post-title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text"><span id="title-count">0</span>/200 ký tự.</div>
                            </div>

                            {{-- Nội dung --}}
                            <div class="col-12">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="content" rows="8" required class="form-control @error('content') is-invalid @enderror"
                                    placeholder="Nhập nội dung bài viết...">{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ảnh + preview --}}
                            <div class="col-12">
                                <label class="form-label">Ảnh minh họa</label>
                                <input type="file" name="image" id="post-image"
                                    class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Bỏ trống nếu giữ ảnh hiện tại. Gợi ý tỉ lệ <strong>16:9</strong>, dung lượng &lt; 5MB.
                                </div>

                                <div class="mt-3">
                                    <div class="post-preview" id="post-preview">
                                        @if ($post->image)
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="current">
                                        @else
                                            <span class="text-muted small">Chưa có ảnh – chọn tệp để xem trước</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            id="btn-remove-image" disabled>
                                            <i class="bi bi-x-circle me-1"></i> Xoá ảnh chọn
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Xuất bản --}}
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                                    value="1" {{ old('is_published', $post->is_published ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Xuất bản bài viết này
                                </label>
                            </div>


                            {{-- Footer actions --}}
                            <div class="post-actions d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.posts.index', ['status' => request('status')]) }}"
                                    class="btn btn-light border">Hủy</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i> Lưu thay đổi
                                </button>
                            </div>
                        </div>
            </form>
        </div>
    </div>

    {{-- JS: preview ảnh + đếm ký tự --}}
    <script>
        (function() {
            const title = document.getElementById('post-title');
            const titleCount = document.getElementById('title-count');
            const input = document.getElementById('post-image');
            const preview = document.getElementById('post-preview');
            const btnRemove = document.getElementById('btn-remove-image');
            const MAX = 5 * 1024 * 1024; // 5MB

            function updCount() {
                titleCount.textContent = (title?.value || '').length;
            }
            title && (title.addEventListener('input', updCount), updCount());

            function clearPreviewToCurrent() {
                preview.innerHTML = '<span class="text-muted small">Chưa có ảnh – chọn tệp để xem trước</span>';
                btnRemove.disabled = true;
                input.value = '';
            }

            input.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    clearPreviewToCurrent();
                    return;
                }
                if (file.size > MAX) {
                    alert('Ảnh vượt quá 5MB. Vui lòng chọn ảnh nhẹ hơn.');
                    clearPreviewToCurrent();
                    return;
                }
                const url = URL.createObjectURL(file);
                preview.innerHTML = '<img src="' + url + '" alt="preview">';
                btnRemove.disabled = false;
            });

            btnRemove.addEventListener('click', clearPreviewToCurrent);
        })();
    </script>
@endsection

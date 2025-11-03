@extends('layouts.admin.master')

@section('title', 'Sửa Banner')

@section('content')
    <style>
        .bn-card {
            max-width: 980px;
            margin-inline: auto
        }

        .form-text {
            font-size: .875rem
        }

        /* Khung preview ảnh 16:9, không vỡ layout */
        .bn-preview {
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

        .bn-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .bn-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px 16px;
            border-top: 1px solid #eee
        }

        .is-invalid {
            border-color: #dc3545
        }

        /* FIX tràn/đè trên màn nhỏ */
        @media (max-width: 576px) {
            .bn-actions {
                position: static;
            }
        }

        /* chừa đáy để nội dung không bị nút sticky che mất khi màn lớn */
        .card-body {
            padding-bottom: 72px;
        }
    </style>

    <div class="container-fluid">
        <div class="bn-card">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h3 class="mb-1"><i class="bi bi-image me-2"></i>Sửa banner</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.banners.index', ['status' => request('status', 'active')]) }}"
                    class="btn btn-light border">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>

            {{-- Alert lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại các trường sau:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data"
                novalidate>
                @csrf
                @method('PUT')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Tiêu đề --}}
                            <div class="col-12">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title', $banner->title) }}"
                                    class="form-control @error('title') is-invalid @enderror" required
                                    placeholder="VD: Summer Sale 2025">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Hiển thị kèm ảnh để mô tả nội dung banner.</div>
                            </div>

                            {{-- Link --}}
                            <div class="col-md-6">
                                <label class="form-label">Link</label>
                                <input type="url" name="link" value="{{ old('link', $banner->link) }}"
                                    class="form-control @error('link') is-invalid @enderror"
                                    placeholder="https://example.com/sale">
                                @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Đường dẫn khi người dùng click vào banner (có thể bỏ trống).</div>
                            </div>

                            {{-- Vị trí --}}
                            <div class="col-md-6">
                                <label class="form-label">Vị trí</label>
                                <select name="position" class="form-select @error('position') is-invalid @enderror">
                                    @php $pos = old('position', $banner->position); @endphp
                                    <option value="top" {{ $pos === 'top' ? 'selected' : '' }}>Top</option>
                                    <option value="middle" {{ $pos === 'middle' ? 'selected' : '' }}>Middle</option>
                                    <option value="bottom" {{ $pos === 'bottom' ? 'selected' : '' }}>Bottom</option>
                                </select>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ảnh + preview --}}
                            <div class="col-12">
                                <label class="form-label">Ảnh {{ $banner->image ? '' : ' (bắt buộc)' }}</label>
                                <input type="file" name="image" id="bn-image"
                                    class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Khuyến nghị tỉ lệ <strong>16:9</strong> (ví dụ 1600×900). Dung lượng &lt; 5MB. Ảnh sẽ tự
                                    căn vừa khung.
                                </div>

                                <div class="mt-3">
                                    <div class="bn-preview" id="bn-preview">
                                        @if ($banner->image)
                                            <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}">
                                        @else
                                            <span class="text-muted small">Chưa có ảnh – chọn tệp để xem trước</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="bn-btn-remove"
                                            {{ $banner->image ? '' : 'disabled' }}>
                                            <i class="bi bi-x-circle me-1"></i>Xoá ảnh vừa chọn
                                        </button>
                                        @if ($banner->image)
                                            <a href="{{ asset('storage/' . $banner->image) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Xem ảnh hiện tại
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="1" {{ old('status', (int) $banner->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Bật (hiển thị)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer actions --}}
                    <div class="bn-actions d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.banners.index', ['status' => request('status', 'active')]) }}"
                            class="btn btn-light border">
                            Hủy
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2 me-1"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JS: preview ảnh + kiểm tra size + clear --}}
    <script>
        (function() {
            const input = document.getElementById('bn-image');
            const preview = document.getElementById('bn-preview');
            const btnRemove = document.getElementById('bn-btn-remove');
            const MAX = 5 * 1024 * 1024; // 5MB

            function clearPreview() {
                preview.innerHTML = '<span class="text-muted small">Chưa có ảnh – chọn tệp để xem trước</span>';
                btnRemove.disabled = true;
                input.value = '';
            }

            input.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    return;
                }
                if (file.size > MAX) {
                    alert('Ảnh vượt quá 5MB. Vui lòng chọn ảnh nhẹ hơn.');
                    clearPreview();
                    return;
                }
                const url = URL.createObjectURL(file);
                preview.innerHTML = '<img src="' + url + '" alt="preview">';
                btnRemove.disabled = false;
            });

            btnRemove && btnRemove.addEventListener('click', clearPreview);
        })();
    </script>
@endsection

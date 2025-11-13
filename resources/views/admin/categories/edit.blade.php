@extends('layouts.admin.master')

@section('title', 'Sửa danh mục')

@section('content')
    <div class="container py-4">
        <h3 class="fw-bold mb-4 text-primary">
            <i class="bi bi-pencil-square me-2"></i> Sửa danh mục
        </h3>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="needs-validation"
            novalidate>
            @csrf
            @method('PUT')

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white fw-semibold"
                    style="background: linear-gradient(90deg, #007bff, #00a8ff);">
                    <i class="bi bi-folder2-open me-2"></i> Thông tin danh mục
                </div>

                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên danh mục</label>
                        <input type="text" name="name" class="form-control shadow-sm"
                            value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug</label>
                        <input type="text" name="slug" class="form-control shadow-sm"
                            value="{{ old('slug', $category->slug) }}">
                        <small class="text-muted">Tự động tạo nếu để trống.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control shadow-sm"
                            placeholder="Nhập mô tả ngắn cho danh mục...">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-lg btn-primary px-4 shadow">
                    <i class="bi bi-save me-2"></i> Lưu thay đổi
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-lg btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
@endsection

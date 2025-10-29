@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
      <i class="bi bi-pencil-square me-2"></i> Sửa trang: {{ $page->title }}
      <span class="text-muted">({{ strtoupper($page->key) }})</span>
    </h4>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Vui lòng kiểm tra lỗi:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.pages.update', $page->key) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Tiêu đề</label>
          <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Nội dung</label>
          <textarea name="content" rows="10" class="form-control">{{ old('content', $page->content) }}</textarea>
        </div>

        <div class="form-check form-switch mb-3">
          <input type="hidden" name="published" value="0">
          <input class="form-check-input" type="checkbox" id="published" name="published" value="1"
                 {{ old('published', $page->published) ? 'checked' : '' }}>
          <label class="form-check-label" for="published">Hiển thị</label>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Lưu
          </button>
          <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

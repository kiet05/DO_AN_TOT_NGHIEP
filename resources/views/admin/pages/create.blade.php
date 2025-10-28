@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-plus"></i> Thêm trang mới</h4>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Slug (để trống sẽ tự tạo)</label>
                <input type="text" name="slug" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="content" rows="6" class="form-control"></textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="published" class="form-check-input" id="published">
                <label class="form-check-label" for="published">Hiển thị trang</label>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-save"></i> Lưu
            </button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection

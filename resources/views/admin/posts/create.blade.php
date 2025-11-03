@extends('layouts.admin.master')

@section('content')
    <div class="container">
        <h1>Thêm bài viết mới</h1>
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="content">Nội dung</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Hình ảnh</label>
                <input type="file" name="image" class="form-control">
            </div>
            <label class="form-check">
                <input type="checkbox" name="is_published" class="form-check-input"
                    {{ old('is_published', !empty($post?->published_at)) ? 'checked' : '' }}>
                Xuất bản
            </label>

            <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
    </div>
@endsection

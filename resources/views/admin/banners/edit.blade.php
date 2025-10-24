@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Sửa Banner</h1>

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $banner->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh</label>
            <input type="file" name="image" id="image" class="form-control">
            @if($banner->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $banner->image) }}" width="200" alt="Banner hiện tại">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Liên kết (link)</label>
            <input type="text" name="link" id="link" class="form-control" value="{{ old('link', $banner->link) }}">
        </div>

        <div class="mb-3">
            <label for="position" class="form-label">Vị trí hiển thị</label>
            <select name="position" id="position" class="form-control">
                <option value="top" {{ $banner->position == 'top' ? 'selected' : '' }}>Top</option>
                <option value="middle" {{ $banner->position == 'middle' ? 'selected' : '' }}>Middle</option>
                <option value="bottom" {{ $banner->position == 'bottom' ? 'selected' : '' }}>Bottom</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select name="status" id="status" class="form-control">
                <option value="1" {{ $banner->status ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$banner->status ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

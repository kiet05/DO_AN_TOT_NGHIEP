@extends('layouts.admin.master')

@section('content')
    <h1>Thêm banner mới</h1>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Ảnh</label>
            <input type="file" name="image" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Link</label>
            <input type="text" name="link" class="form-control">
        </div>

        <div class="mb-3">
            <label>Vị trí</label>
            <select name="position" class="form-control">
                <option value="top">Top</option>
                <option value="middle">Middle</option>
                <option value="bottom">Bottom</option>
            </select>
        </div>

        <div class="form-check form-switch mb-3">
            <input type="hidden" name="status" value="0">
            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
            <label class="form-check-label" for="status">Bật</label>
        </div>

        <button class="btn btn-success">Lưu</button>
    </form>
@endsection

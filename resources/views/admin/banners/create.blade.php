@extends('layouts.app')

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

    <div class="mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            <option value="1">Hiển thị</option>
            <option value="0">Ẩn</option>
        </select>
    </div>

    <button class="btn btn-success">Lưu</button>
</form>
@endsection

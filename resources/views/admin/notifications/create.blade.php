@extends('layouts.app')

@section('content')
<h1>Thêm thông báo</h1>
<form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-4">
    @csrf
    <div class="mb-3">
        <label>Tiêu đề</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Nội dung</label>
        <textarea name="content" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Lưu</button>
</form>
@endsection

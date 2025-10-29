@extends('layouts.app')

@section('content')
<h1>Sửa thông báo</h1>
<form method="POST" action="{{ route('admin.notifications.update', $notification) }}" class="space-y-4">
    @csrf
    @method('PUT')
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="{{ $notification->title }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Nội dung</label>
        <textarea name="content" class="form-control" required>{{ $notification->content }}</textarea>
    </div>
    <button type="submit" class="btn btn-success">Cập nhật</button>
</form>
@endsection

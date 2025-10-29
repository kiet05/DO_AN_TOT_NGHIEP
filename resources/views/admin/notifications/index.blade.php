@extends('layouts.app')

@section('content')
<h1>Danh sách thông báo</h1>
<a href="{{ route('admin.notifications.create') }}" class="btn btn-primary mb-3">+ Thêm thông báo</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($notifications as $n)
        <tr>
            <td>{{ $n->id }}</td>
            <td>{{ $n->title }}</td>
            <td>{{ $n->content }}</td>
            <td>{{ $n->status ? 'Hiển thị' : 'Ẩn' }}</td>
            <td>
<a href="{{ route('admin.notifications.edit', $n) }}" class="px-2 py-1 text-blue-600">Sửa</a>
               <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" class="inline">
    @csrf @method('DELETE')
    <button class="px-2 py-1 text-red-600" onclick="return confirm('Xóa thông báo này?')">Xóa</button>
</form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

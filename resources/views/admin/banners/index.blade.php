@extends('layouts.app')

@section('content')
<h1>Danh sách Banner</h1>

<a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">+ Thêm mới</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Hình ảnh</th>
            <th>Vị trí</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($banners as $banner)
        <tr>
            <td>{{ $banner->id }}</td>
            <td>{{ $banner->title }}</td>
          <td>
     @if($banner->image)
        <img src="{{ asset('storage/' . $banner->image) }}" width="120" alt="Banner">
    @else
        <span>Không có ảnh</span>
    @endif
</td>
            <td>{{ $banner->position }}</td>
            <td>{{ $banner->status ? 'Hiển thị' : 'Ẩn' }}</td>
            <td>
                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa banner này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

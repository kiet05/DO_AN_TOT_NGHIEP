@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="fa-solid fa-file-lines"></i> Quản lý trang tĩnh</h4>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Thêm trang
            </a>
        </div>

        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th><i class="fa-solid fa-key"></i> Mã</th>
                        <th><i class="fa-solid fa-heading"></i> Tiêu đề</th>
                        <th><i class="fa-solid fa-calendar-days"></i> Cập nhật</th>
                        <th><i class="fa-solid fa-eye"></i> Trạng thái</th>
                        <th><i class="fa-solid fa-gears"></i> Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td><strong>{{ strtoupper($page->key) }}</strong></td>
                            <td>{{ $page->title }}</td>
                            <td>{{ $page->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($page->published)
                                    <span class="badge bg-success">Hiển thị</span>
                                @else
                                    <span class="badge bg-secondary">Ẩn</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pages.show', $page->id) }}" class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>

                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>

                                <!-- Delete form -->
                                <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Quản lý Bài viết')

@section('content')
    <div class="container-fluid">

        {{-- Header + nút hành động (giống trang Banner) --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="bi bi-journal-text me-2"></i>
                Danh sách bài viết
            </h3>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.posts.index') }}"
                    class="btn btn-outline-secondary {{ request('status') ? '' : 'active' }}">
                    Tất cả <span class="badge bg-secondary ms-1">{{ $total ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.posts.index', ['status' => 'published']) }}"
                    class="btn btn-outline-success {{ request('status') === 'published' ? 'active' : '' }}">
                    Xuất bản <span class="badge bg-success ms-1">{{ $published ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}"
                    class="btn btn-outline-warning {{ request('status') === 'draft' ? 'active' : '' }}">
                    Nháp <span class="badge bg-warning text-dark ms-1">{{ $draft ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.posts.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                </a>
            </div>

        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Bảng dữ liệu --}}
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px">ID</th>
                            <th style="width:120px">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th style="width:120px">Trạng thái</th>
                            <th style="width:130px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>
                                    @if ($post->image)
                                        <img src="{{ asset('storage/' . $post->image) }}" alt="thumb"
                                            style="width:110px;height:62px;object-fit:cover;border-radius:6px">
                                    @else
                                        <span class="text-muted">Không ảnh</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $post->title }}</td>
                                <td class="text-muted">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 80) }}
                                </td>
                                <td>
                                    @if (!empty($post->published_at))
                                        <span class="badge bg-success">Ấn</span>
                                    @else
                                        <span class="badge bg-secondary">Nháp</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Hành động
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.posts.edit', $post->id) }}">
                                                    <i class="bi bi-pencil-square me-2"></i>Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.posts.destroy', $post->id) }}"
                                                    onsubmit="return confirm('Xóa bài viết này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash3 me-2"></i>Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Chưa có bài viết nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Chút CSS nhẹ để ảnh & badge đồng bộ, không ảnh hưởng trang khác --}}
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endsection

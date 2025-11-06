@extends('layouts.admin.master')

@section('title', 'Quản lý Bài viết')

@section('content')
    <div class="container-fluid">

        {{-- Header + nút hành động --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h3 class="mb-0 d-flex align-items-center">
                <i class="bi bi-journal-text me-2"></i>
                <strong>Danh sách bài viết</strong>
            </h3>

            <div class="d-flex flex-wrap gap-2">
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
                <a href="{{ route('admin.posts.index', ['status' => 'trash']) }}"
                    class="btn btn-outline-dark {{ request('status') === 'trash' ? 'active' : '' }}">
                    Thùng rác <span class="badge bg-dark ms-1">{{ $trash ?? 0 }}</span>
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
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 posts-table">
                        <colgroup>
                            <col style="width:80px;"> {{-- ID --}}
                            <col style="width:130px;"> {{-- Ảnh --}}
                            <col style="width:320px;"> {{-- Tiêu đề --}}
                            <col> {{-- Nội dung (co giãn) --}}
                            <col style="width:150px;"> {{-- Trạng thái --}}
                            <col style="width:140px;"> {{-- Hành động --}}
                        </colgroup>

                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($posts as $post)
                                <tr>
                                    <td class="text-nowrap">{{ $post->id }}</td>
                                    <td>
                                        @if ($post->image)
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="thumb"
                                                class="rounded thumb">
                                        @else
                                            <span class="text-muted">Không ảnh</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold cell-title" title="{{ $post->title }}">
                                        {{ $post->title }}
                                    </td>
                                    <td class="text-muted cell-content" title="{{ strip_tags($post->content) }}">
                                        {{ Str::limit(strip_tags($post->content), 160) }}
                                    </td>
                                    <td>
                                        @if (request('status') === 'trash')
                                            <span class="badge bg-warning text-dark">Trong thùng rác</span>
                                        @else
                                            @if (!empty($post->published_at))
                                                <span class="badge bg-success d-inline-flex align-items-center px-3 py-2">
                                                    <i class="bi bi-check2-circle me-1"></i> Xuất bản
                                                </span>
                                            @else
                                                <span class="badge bg-secondary d-inline-flex align-items-center px-3 py-2">
                                                    <i class="bi bi-file-earmark-text me-1"></i> Nháp
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group dropdown">
                                            <button type="button"
                                                class="btn btn-sm btn-light border dropdown-toggle d-inline-flex align-items-center"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-gear-fill me-1 text-secondary"></i>
                                                Hành động
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 py-2"
                                                style="min-width: 160px;">
                                                @if (request('status') === 'trash')
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.posts.restore', $post->id) }}"
                                                            class="m-0">
                                                            @csrf @method('PATCH')
                                                            <button type="submit"
                                                                class="dropdown-item py-2 d-flex align-items-center gap-2">
                                                                <i
                                                                    class="bi bi-arrow-counterclockwise text-success"></i>Khôi
                                                                phục
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.posts.forceDelete', $post->id) }}"
                                                            onsubmit="return confirm('Xóa vĩnh viễn bài viết này?');"
                                                            class="m-0">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                                                <i class="bi bi-x-octagon"></i>Xóa vĩnh viễn
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a href="{{ route('admin.posts.edit', $post->id) }}"
                                                            class="dropdown-item py-2 d-flex align-items-center gap-2">
                                                            <i class="bi bi-pencil-square text-primary"></i>Sửa
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.posts.destroy', $post->id) }}"
                                                            onsubmit="return confirm('Chuyển bài viết vào thùng rác?');"
                                                            class="m-0">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                                                <i class="bi bi-trash3"></i>Xóa
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Chưa có bài viết nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if (method_exists($posts, 'links'))
                <div class="card-footer bg-white d-flex justify-content-end">
                    {{ $posts->appends(request()->all())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- CSS override – KHẮC PHỤC ẨN CỘT CỦA THEME --}}
    <style>
        .posts-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }

        .posts-table th,
        .posts-table td {
            vertical-align: middle;
        }

        /* Ảnh và text truncation */
        .thumb {
            width: 112px;
            height: 64px;
            object-fit: cover;
        }

        .cell-title {
            max-width: 320px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cell-content {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* >>> Quan trọng: ép hiển thị tất cả cột, ghi đè CSS responsive của theme Sherah */
        .posts-table thead {
            display: table-header-group !important;
        }

        .posts-table tbody {
            display: table-row-group !important;
        }

        .posts-table tr {
            display: table-row !important;
        }

        .posts-table thead th,
        .posts-table tbody td {
            display: table-cell !important;
            visibility: visible !important;
        }

        /* Nếu theme dùng nth-child để ẩn cột, bật lại toàn bộ */
        .posts-table th:nth-child(n),
        .posts-table td:nth-child(n) {
            display: table-cell !important;
        }

        /* Dropdown form button style */
        .dropdown-menu form button.dropdown-item {
            border: none !important;
            background: transparent !important;
            padding-left: 1.5rem;
        }

        .dropdown-menu form button.dropdown-item:hover {
            background: transparent !important;
            color: #dc3545 !important;
            text-decoration: underline;
        }
    </style>
@endsection

@extends('layouts.admin.master')

@section('title', 'Quản lý Bài viết')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">

                        {{-- ===== HEADER + ACTION BUTTON ===== --}}
                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Danh sách bài viết</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="#">Home</a></li>
                                        <li class="active"><a href="{{ route('admin.posts.index') }}">Bài viết</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.posts.create') }}" class="sherah-btn sherah-gbcolor">
                                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                </a>
                            </div>
                        </div>

                        {{-- ===== FILTER BAR ===== --}}
                        <div class="mt-4 d-flex flex-wrap gap-2">
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
                           
                        </div>

                        {{-- ===== ALERT ===== --}}
                        @if (session('success'))
                            <div class="alert alert-success mt-3">{{ session('success') }}</div>
                        @endif

                        {{-- ===== TABLE ===== --}}
                        <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                            <div class="table-responsive">
                                <table class="sherah-table__main posts-table align-middle">
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
                                                                          action="{{ route('admin.posts.restore', $post->id) }}" class="m-0">
                                                                        @csrf @method('PATCH')
                                                                        <button type="submit"
                                                                                class="dropdown-item py-2 d-flex align-items-center gap-2">
                                                                            <i class="bi bi-arrow-counterclockwise text-success"></i>Khôi phục
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

                            {{-- Pagination --}}
                            @if (method_exists($posts, 'links'))
                                <div class="mt-3 d-flex justify-content-end">
                                    {{ $posts->appends(request()->all())->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>

                    </div> {{-- /.sherah-dsinner --}}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Custom CSS giữ bảng hiển thị đúng --}}
<style>
    .posts-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .posts-table th, .posts-table td { vertical-align: middle; }
    .thumb { width: 112px; height: 64px; object-fit: cover; }
    .cell-title, .cell-content {
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .posts-table thead { display: table-header-group !important; }
    .posts-table tbody { display: table-row-group !important; }
    .dropdown-menu form button.dropdown-item {
        border: none !important; background: transparent !important; padding-left: 1.5rem;
    }
    .dropdown-menu form button.dropdown-item:hover {
        background: transparent !important; color: #dc3545 !important; text-decoration: underline;
    }
/* Căn giữa tất cả các ô theo chiều dọc */
.posts-table td,
.posts-table th {
    vertical-align: middle !important;
}

/* Cột ảnh - đảm bảo thẳng hàng với tiêu đề */
.posts-table td:nth-child(2) {
    text-align: left !important;
    padding-left: 24px !important;
    vertical-align: middle !important;
}

/* Căn ảnh đúng baseline của hàng */
.posts-table td img.thumb {
    display: inline-block;
    vertical-align: middle;
    width: 120px;
    height: 70px;
    object-fit: cover;
    border-radius: 6px;
    position: relative;
    top: 2px; /* tinh chỉnh vi mô để khớp hoàn toàn baseline */
}

/* Giữ chiều cao hàng ổn định */
.posts-table tr {
    height: 80px;
    line-height: 1.2;
}
/* Dịch chữ "Ảnh" sang phải nhẹ cho thẳng với hình */
.posts-table th:nth-child(2) {
    padding-left: 70px !important;  /* dịch phải 32px (tùy chỉnh 28–36px nếu cần) */
    text-align: left !important;
}

/* Giữ ảnh canh giữa cột */
.posts-table td:nth-child(2) {
    text-align: left !important;
    padding-left: 24px !important; /* khớp với tiêu đề */
}

</style>
@endsection

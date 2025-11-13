@extends('layouts.admin.master')

@section('title', 'Quản lý Bài viết')

@section('content')
    <style>
        .sherah-table__main {
            table-layout: fixed;
            border-collapse: collapse;
            width: 100%;
        }

        .sherah-table__main th,
        .sherah-table__main td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        /* Ép lại thead/tbody không bị lệch */
        .sherah-table__head {
            display: table-header-group !important;
        }

        .sherah-table__body {
            display: table-row-group !important;
        }

        /* Ảnh bài viết */
        .post-thumb {
            width: 160px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
        }

        .post-title {
            max-width: 420px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .post-excerpt {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-col {
            width: 160px;
        }

        .action-col {
            width: 160px;
        }
    </style>

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">

                            {{-- Header + filter --}}
                            <div class="row mg-top-30">
                                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">
                                            <i class="bi bi-journal-text me-2"></i>
                                            @php $st = request('status'); @endphp
                                            @switch($st)
                                                @case('published')
                                                    Bài viết đã xuất bản
                                                @break

                                                @case('draft')
                                                    Bài viết nháp
                                                @break

                                                @default
                                                    Tất cả bài viết
                                            @endswitch
                                        </h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="{{ route('admin.posts.index') }}">Posts</a></li>
                                        </ul>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.posts.index') }}"
                                            class="btn btn-outline-secondary {{ request('status') ? '' : 'active' }}">
                                            Tất cả <span class="badge bg-light text-dark ms-1">{{ $total ?? 0 }}</span>
                                        </a>
                                        <a href="{{ route('admin.posts.index', ['status' => 'published']) }}"
                                            class="btn btn-outline-success {{ request('status') === 'published' ? 'active' : '' }}">
                                            Xuất bản <span
                                                class="badge bg-light text-dark ms-1">{{ $published ?? 0 }}</span>
                                        </a>
                                        <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}"
                                            class="btn btn-outline-warning {{ request('status') === 'draft' ? 'active' : '' }}">
                                            Nháp <span class="badge bg-light text-dark ms-1">{{ $draft ?? 0 }}</span>
                                        </a>

                                        @if (request('status') !== 'trash')
                                            <a href="{{ route('admin.posts.create') }}" class="btn btn-success">
                                                <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif

                            {{-- BẢNG --}}
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <div class="table-responsive">
                                    <table class="sherah-table__main align-middle">
                                        <colgroup>
                                            <col style="width:90px;"> {{-- ID --}}
                                            <col style="width:180px;"> {{-- Ảnh --}}
                                            <col> {{-- Tiêu đề --}}
                                            <col> {{-- Nội dung --}}
                                            <col style="width:160px;"> {{-- Trạng thái --}}
                                            <col style="width:160px;"> {{-- Hành động --}}
                                        </colgroup>

                                        <thead class="sherah-table__head">
                                            <tr>
                                                <th>ID</th>
                                                <th>Ảnh</th>
                                                <th>Tiêu đề</th>
                                                <th>Nội dung</th>
                                                <th class="status-col">Trạng thái</th>
                                                <th class="action-col">Hành động</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sherah-table__body">
                                            @forelse ($posts as $post)
                                                <tr>
                                                    <td class="text-nowrap">{{ $post->id }}</td>

                                                    <td>
                                                        @if ($post->image)
                                                            <img class="post-thumb"
                                                                src="{{ asset('storage/' . $post->image) }}"
                                                                alt="Ảnh bài viết">
                                                        @else
                                                            <span class="text-muted">Không có ảnh</span>
                                                        @endif
                                                    </td>

                                                    <td class="post-title" title="{{ $post->title }}">{{ $post->title }}
                                                    </td>

                                                    <td class="post-excerpt" title="{{ strip_tags($post->content) }}">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 160) }}
                                                    </td>

                                                    <td>
                                                        @if (!empty($post->published_at))
                                                            <span
                                                                class="btn btn-success btn-sm px-3 rounded-pill d-inline-flex align-items-center">
                                                                <i class="bi bi-check2-circle me-1"></i> Xuất bản
                                                            </span>
                                                        @else
                                                            <span
                                                                class="btn btn-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center">
                                                                <i class="bi bi-file-earmark-text me-1"></i> Nháp
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            {{-- Nút sửa --}}
                                                            <a href="{{ route('admin.posts.edit', $post->id) }}"
                                                                class="btn btn-sm btn-warning text-white d-flex align-items-center gap-1">
                                                                <i class="bi bi-pencil-square"></i> Sửa
                                                            </a>

                                                            {{-- Nút xóa --}}
                                                            <form method="POST"
                                                                action="{{ route('admin.posts.destroy', $post->id) }}"
                                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')"
                                                                class="m-0">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                                                                    <i class="bi bi-trash3"></i> Xóa
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        Không có bài viết nào.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if (is_object($posts) && method_exists($posts, 'links'))
                                    <div class="mt-3 d-flex justify-content-end">
                                        {{ $posts->appends(request()->all())->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

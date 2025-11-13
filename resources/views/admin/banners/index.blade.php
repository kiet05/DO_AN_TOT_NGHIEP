@extends('layouts.admin.master')

@section('content')
    <style>
        .sherah-table__main {
            table-layout: fixed;
            border-collapse: collapse;
            width: 100%
        }

        .sherah-table__main th,
        .sherah-table__main td {
            padding: 12px 16px;
            vertical-align: middle
        }

        /* Nếu theme set block cho thead/tbody -> ép về mặc định để không lệch cột */
        .sherah-table__head {
            display: table-header-group !important
        }

        .sherah-table__body {
            display: table-row-group !important
        }

        .bn-title {
            max-width: 420px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .bn-img {
            width: 160px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px
        }

        .bn-status-col {
            width: 160px
        }

        .bn-action-col {
            width: 160px
        }

        /* Căn trái các nút hành động */
        .action-buttons {
            display: flex;
            justify-content: flex-start;
            /* ← thay vì center */
            align-items: center;
            gap: 8px;
            height: 100%;
        }

        /* Giữ kích thước đồng nhất và thẳng hàng */
        .action-buttons .btn {
            min-width: 60px;
            height: 45px;
            font-weight: 500;
            border-radius: 6px;
            padding: 0 10px;
        }

        /* Giữ ô "Hành động" canh giữa theo trục dọc */
        td.action-col {
            vertical-align: middle !important;
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
                                        <h2 class="sherah-breadcrumb__title">Danh sách Banner</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                                        </ul>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.banners.index', ['status' => 'all']) }}"
                                            class="btn btn-outline-secondary {{ request('status', 'active') === 'all' ? 'active' : '' }}">
                                            Tất cả <span class="badge bg-light text-dark ms-1">{{ $countAll ?? 0 }}</span>
                                        </a>
                                        <a href="{{ route('admin.banners.index', ['status' => 'active']) }}"
                                            class="btn btn-outline-success {{ request('status', 'active') === 'active' ? 'active' : '' }}">
                                            Bật <span class="badge bg-light text-dark ms-1">{{ $countActive ?? 0 }}</span>
                                        </a>
                                        <a href="{{ route('admin.banners.index', ['status' => 'inactive']) }}"
                                            class="btn btn-outline-warning {{ request('status') === 'inactive' ? 'active' : '' }}">
                                            Tắt <span class="badge bg-light text-dark ms-1">{{ $countInactive ?? 0 }}</span>
                                        </a>
                                        <a href="{{ route('admin.banners.index', ['status' => 'trash']) }}"
                                            class="btn btn-outline-warning {{ request('status') === 'trash' ? 'active' : '' }}">
                                            Thùng rác <span
                                                class="badge bg-light text-dark ms-1">{{ $countTrash ?? 0 }}</span>
                                        </a>

                                        @if (request('status') !== 'trash')
                                            <a href="{{ route('admin.banners.create') }}" class="btn btn-success">
                                                <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif

                            {{-- Bảng --}}
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <div class="table-responsive">
                                    <table class="sherah-table__main align-middle">
                                        <colgroup>
                                            <col style="width:90px;"> {{-- ID --}}
                                            <col> {{-- Tên --}}
                                            <col style="width:180px;"> {{-- Ảnh --}}
                                            <col style="width:160px;"> {{-- Trạng thái --}}
                                            <col style="width:160px;"> {{-- Hành động --}}
                                        </colgroup>

                                        <thead class="sherah-table__head">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên banner</th>
                                                <th>Ảnh</th>
                                                <th class="bn-status-col">Trạng thái</th>
                                                <th class="bn-action-col">Hành động</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sherah-table__body">
                                            @forelse($banners as $banner)
                                                <tr>
                                                    <td class="text-nowrap">{{ $banner->id }}</td>

                                                    <td class="bn-title" title="{{ $banner->title }}">{{ $banner->title }}
                                                    </td>

                                                    <td>
                                                        @if ($banner->image)
                                                            <img class="bn-img"
                                                                src="{{ asset('storage/' . $banner->image) }}"
                                                                alt="Banner">
                                                        @else
                                                            <span class="text-muted">Không có ảnh</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if (request('status') === 'trash')
                                                            <span class="badge bg-warning text-dark">Trong thùng rác</span>
                                                        @else
                                                            @if ($banner->status)
                                                                <span
                                                                    class="btn btn-success btn-sm px-3 rounded-pill d-inline-flex align-items-center">
                                                                    <i class="bi bi-toggle-on me-1 fs-5"></i> Bật
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="btn btn-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center">
                                                                    <i class="bi bi-toggle-off me-1 fs-5"></i> Tắt
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>

                                                    <td class="action-col">
                                                        <div class="action-buttons">
                                                            @if (request('status') === 'trash')
                                                                {{-- 🔄 Khôi phục --}}
                                                                <form method="POST"
                                                                    action="{{ route('admin.banners.restore', $banner->id) }}"
                                                                    class="m-0">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi
                                                                        phục
                                                                    </button>
                                                                </form>

                                                                {{-- ❌ Xóa vĩnh viễn --}}
                                                                <form method="POST"
                                                                    action="{{ route('admin.banners.force', $banner->id) }}"
                                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn banner này?')"
                                                                    class="m-0">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                                                                        <i class="bi bi-trash3"></i> Xóa vĩnh viễn
                                                                    </button>
                                                                </form>
                                                            @else
                                                                {{-- ✏️ Sửa --}}
                                                                <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                                                    class="btn btn-warning btn-sm text-white d-flex align-items-center gap-1">
                                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                                </a>

                                                                {{-- 🗑️ Xóa --}}
                                                                <form method="POST"
                                                                    action="{{ route('admin.banners.destroy', $banner->id) }}"
                                                                    onsubmit="return confirm('Chuyển banner vào thùng rác?')"
                                                                    class="m-0">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                                                                        <i class="bi bi-trash3"></i> Xóa
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>



                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        @if (request('status') === 'trash')
                                                            Chưa có banner nào trong thùng rác.
                                                        @elseif(request('status') === 'inactive')
                                                            Không có banner đang tắt.
                                                        @elseif(request('status') === 'all')
                                                            Chưa có banner nào.
                                                        @else
                                                            Không có banner đang hoạt động.
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

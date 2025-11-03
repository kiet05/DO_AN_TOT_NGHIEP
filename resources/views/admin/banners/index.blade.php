@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sherah-body">
                    <div class="sherah-dsinner">
                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Danh sách Banner </h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="#">Home</a></li>
                                        <li class="active"><a href="{{ route('admin.banners.index') }}">Banner</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.banners.create') }}" class="sherah-btn sherah-gbcolor">
                                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                </a>
                            </div>
                        </div>

                        {{-- ====== Bộ lọc trạng thái ====== --}}
                        <div class="mt-4 d-flex flex-wrap gap-2">
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
                               class="btn btn-outline-danger {{ request('status') === 'trash' ? 'active' : '' }}">
                                Thùng rác <span class="badge bg-light text-dark ms-1">{{ $countTrash ?? 0 }}</span>
                            </a>
                        </div>

                        {{-- ====== Thông báo ====== --}}
                        @if (session('success'))
                            <div class="alert alert-success mt-3">{{ session('success') }}</div>
                        @endif

                        {{-- ====== Bảng Banner ====== --}}
                        <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                            <div class="table-responsive">
                                <table class="sherah-table__main align-middle">
                                    <thead class="sherah-table__head">
                                        <tr>
                                            <th style="width:50px">ID</th>
                                            <th>Tên banner</th>
                                            <th style="width:160px">Ảnh</th>
                                            <th style="width:130px">Trạng thái</th>
                                            <th style="width:160px">Hành động</th>
                                        </tr>
                                    </thead>

                                    <tbody class="sherah-table__body sherah-table__body-v3">
                                        @forelse ($banners as $banner)
                                            <tr>
                                                <td class="text-center">{{ $banner->id }}</td>
                                                <td class="fw-semibold">{{ $banner->title }}</td>
                                                <td>
                                                    @if ($banner->image)
                                                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner"
                                                             style="width:140px;height:78px;object-fit:cover;border-radius:6px">
                                                    @else
                                                        <span class="text-muted">Không có ảnh</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (request('status') === 'trash')
                                                        <span class="badge bg-warning text-dark">Trong thùng rác</span>
                                                    @else
                                                        @if ($banner->status)
                                                            <span class="badge bg-success"><i class="bi bi-toggle-on me-1"></i>Bật</span>
                                                        @else
                                                            <span class="badge bg-secondary"><i class="bi bi-toggle-off me-1"></i>Tắt</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-light border btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-gear"></i> Hành động
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 py-2">
                                                            @if (request('status') === 'trash')
                                                                <li>
                                                                    <form method="POST"
                                                                        action="{{ route('admin.banners.restore', $banner->id) }}">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                                            <i class="bi bi-arrow-counterclockwise text-success"></i>Khôi phục
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form method="POST"
                                                                        action="{{ route('admin.banners.force', $banner->id) }}"
                                                                        onsubmit="return confirm('Xóa vĩnh viễn banner này?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="dropdown-item text-danger d-flex align-items-center gap-2">
                                                                            <i class="bi bi-trash3"></i>Xóa vĩnh viễn
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                                                       class="dropdown-item d-flex align-items-center gap-2">
                                                                        <i class="bi bi-pencil-square text-primary"></i>Sửa
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <form method="POST"
                                                                          action="{{ route('admin.banners.destroy', $banner->id) }}"
                                                                          onsubmit="return confirm('Chuyển banner vào thùng rác?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="dropdown-item text-danger d-flex align-items-center gap-2">
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

                    </div> {{-- /.sherah-dsinner --}}
                </div>
            </div>
        </div>
    </div>
    <style>
    /* Căn giữa ID và thu hẹp độ rộng cột */
    table th:first-child,
    table td:first-child {
        text-align: center !important;
        width: 80px !important; /* Tùy chỉnh: bạn có thể giảm xuống 60 nếu muốn */
        vertical-align: middle !important;
        font-weight: 600;
    }

    /* Đảm bảo chiều cao hàng đồng nhất */
    table td {
        vertical-align: middle !important;
    }
</style>

</section>
@endsection

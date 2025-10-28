@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="bi bi-image me-2"></i>
                @if (request('status') === 'trash')
                    Danh sách banner trong thùng rác
                @elseif(request('status') === 'inactive')
                    Danh sách banner đang tắt
                @elseif(request('status') === 'all')
                    Danh sách tất cả banner
                @else
                    Danh sách banner đang hoạt động
                @endif
            </h4>

            {{-- Nút lọc + Thêm mới --}}
            <div class="d-flex gap-2">
                <a href="{{ route('admin.banners.index', ['status' => 'all']) }}"
                    class="btn btn-outline-secondary {{ request('status', 'active') === 'all' ? 'active' : '' }}">
                    Tất cả
                    <span class="badge bg-light text-dark ms-1">{{ $countAll ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.banners.index', ['status' => 'active']) }}"
                    class="btn btn-outline-success {{ request('status', 'active') === 'active' ? 'active' : '' }}">
                    Bật
                    <span class="badge bg-light text-dark ms-1">{{ $countActive ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.banners.index', ['status' => 'inactive']) }}"
                    class="btn btn-outline-warning {{ request('status') === 'inactive' ? 'active' : '' }}">
                    Tắt
                    <span class="badge bg-light text-dark ms-1">{{ $countInactive ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.banners.index', ['status' => 'trash']) }}"
                    class="btn btn-outline-warning {{ request('status') === 'trash' ? 'active' : '' }}">
                    Thùng rác
                    <span class="badge bg-light text-dark ms-1">{{ $countTrash ?? 0 }}</span>
                </a>

                {{-- Ẩn nút thêm mới khi đang ở thùng rác (tuỳ ý) --}}
                @if (request('status') !== 'trash')
                    <a href="{{ route('admin.banners.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Thêm mới
                    </a>
                @endif
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Bảng --}}
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px">ID</th>
                            <th>Tên banner</th>
                            <th style="width:160px">Ảnh</th>
                            <th style="width:120px">Trạng thái</th>
                            <th style="width:160px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
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
                                    @if (!request()->routeIs('admin.banners.index') || request('status') !== 'trash')
                                        @if ($banner->status)
                                            <span class="badge bg-success">Bật</span>
                                        @else
                                            <span class="badge bg-secondary">Tắt</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">Đang ở thùng rác</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            Hành động
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if (request('status') === 'trash')
                                                {{-- Thùng rác: Khôi phục & Xóa vĩnh viễn --}}
                                                <li>
                                                    <form method="POST"
                                                        action="{{ route('admin.banners.restore', $banner->id) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-arrow-counterclockwise me-2"></i>Khôi phục
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST"
                                                        action="{{ route('admin.banners.force', $banner->id) }}"
                                                        onsubmit="return confirm('Xóa vĩnh viễn banner này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash3 me-2"></i>Xóa vĩnh viễn
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                {{-- Danh sách thường: Sửa & Xóa mềm --}}
                                                <li>
                                                    <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                                        class="dropdown-item">
                                                        <i class="bi bi-pencil-square me-2"></i>Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST"
                                                        action="{{ route('admin.banners.destroy', $banner->id) }}"
                                                        onsubmit="return confirm('Chuyển banner vào thùng rác?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash3 me-2"></i>Xóa
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
    </div>

    {{-- style nhẹ để căn giữa hàng --}}
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endsection

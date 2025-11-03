@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="bi bi-image me-2"></i>
                @if (request('status') === 'trash')
                    <strong>Danh sách banner trong thùng rác</strong>
                @elseif(request('status') === 'inactive')
                    <strong>Danh sách banner đang tắt</strong>
                @elseif(request('status') === 'all')
                   <strong> Danh sách tất cả banner</strong>
                @else
                    <strong>Danh sách banner đang hoạt động</strong>
                @endif
            </h3>

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
            <div class="card-body p-0 table-wrap">
                <div style="max-height:500px; overflow-y:auto;">
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
                                        @if (request('status') === 'trash')
                                            <span class="badge bg-warning text-dark">Đang ở thùng rác</span>
                                        @else
                                            @if ($banner->status)
                                                <span
                                                    class="btn btn-success btn-sm px-3 rounded-pill d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-toggle-on me-1 fs-5"></i> Bật
                                                </span>
                                            @else
                                                <span
                                                    class="btn btn-secondary btn-sm px-3 rounded-pill d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-toggle-off me-1 fs-5"></i> Tắt
                                                </span>
                                            @endif
                                        @endif
                                    </td>


                                    <td>
                                        {{-- Dropdown Hành động (đã vá chặn lan truyền + ép toggle) --}}
                                        <div class="btn-group dropdown">
                                            <button type="button"
                                                class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                onclick="event.preventDefault(); event.stopPropagation();">
                                                <i class="bi bi-gear-fill me-1 text-secondary"></i>
                                                <span>Hành động</span>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 py-2"
                                                style="min-width: 140px;" onclick="event.stopPropagation();">
                                                @if (request('status') === 'trash')
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.banners.restore', $banner->id) }}"
                                                            class="m-0">
                                                            @csrf
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
                                                            action="{{ route('admin.banners.force', $banner->id) }}"
                                                            onsubmit="return confirm('Xóa vĩnh viễn banner này?');"
                                                            class="m-0">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                                                <i class="bi bi-trash3"></i>Xóa vĩnh viễn
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                                            class="dropdown-item py-2 d-flex align-items-center gap-2">
                                                            <i class="bi bi-pencil-square text-primary"></i>Sửa
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.banners.destroy', $banner->id) }}"
                                                            onsubmit="return confirm('Chuyển banner vào thùng rác?');"
                                                            class="m-0">
                                                            @csrf
                                                            @method('DELETE')
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

    {{-- CSS vá dropdown --}}
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        /* Không bị cắt khi container cuộn */
        .table-wrap {
            overflow: visible !important;
        }

        .card-body {
            position: relative;
        }

        /* Nổi lên trên các lớp khác */
        .dropdown-menu {
            z-index: 1060 !important;
        }

        /* Bỏ viền và nền cho nút Xóa trong dropdown */
        .dropdown-menu form button.dropdown-item {
            border: none !important;
            background: transparent !important;
            padding-left: 1.5rem;
            /* căn ngang với icon */
        }

        /* Khi hover: chỉ đổi màu chữ, không có nền */
        .dropdown-menu form button.dropdown-item:hover {
            background: transparent !important;
            color: #dc3545 !important;
            text-decoration: underline;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ép khởi tạo và điều khiển dropdown theo Bootstrap 5
            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(btn) {
                const inst = bootstrap.Dropdown.getOrCreateInstance(btn);
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    inst.toggle();
                });
            });

            // Chặn lan truyền bên trong menu, tránh bị hàng <tr> hoặc container cha "nuốt" click
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>
@endpush

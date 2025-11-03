@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <!-- Dashboard Inner -->
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Quản lý tài khoản Admin</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="#">Tài khoản Admin</a></li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                    <a href="{{ route('admin.accounts.create') }}" class="sherah-btn sherah-gbcolor">+ Thêm mới</a>
                                </div>
                            </div>

                            <!-- Success Message -->
                            @if (session('success'))
                                <div class="alert alert-success mg-top-20">{{ session('success') }}</div>
                            @endif

                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <table id="sherah-table__vendor" class="sherah-table__main sherah-table__main-v3">
                                    <!-- Table Head -->
                                    <thead class="sherah-table__head">
                                        <tr>
                                            <th class="sherah-table__column-1 sherah-table__h1">ID</th>
                                            <th class="sherah-table__column-2 sherah-table__h2">Tên</th>
                                            <th class="sherah-table__column-3 sherah-table__h3">Email</th>
                                            <th class="sherah-table__column-4 sherah-table__h4">Vai trò</th>
                                            <th class="sherah-table__column-5 sherah-table__h5">Trạng thái</th>
                                            <th class="sherah-table__column-6 sherah-table__h6">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sherah-table__body">
                                        @forelse ($admins as $admin)
                                            <tr>
                                                <!-- ID -->
                                                <td class="sherah-table__column-1 sherah-table__data-1">
                                                    <p class="crany-table__product--number"><a href="#" class="sherah-color1">#{{ $admin->id }}</a></p>
                                                </td>

                                                <!-- Tên -->
                                                <td class="sherah-table__column-2 sherah-table__data-2">
                                                    <div class="sherah-table__product-content">
                                                        <p class="sherah-table__product-desc">{{ $admin->name }}</p>
                                                    </div>
                                                </td>

                                                <!-- Email -->
                                                <td class="sherah-table__column-3 sherah-table__data-3">
                                                    <p class="sherah-table__product-desc">{{ $admin->email }}</p>
                                                </td>

                                                <!-- Vai trò -->
                                                <td class="sherah-table__column-4 sherah-table__data-4">
                                                    <p class="sherah-table__product-desc">{{ $admin->role->name ?? '—' }}</p>
                                                </td>

                                                <!-- Trạng thái -->
                                                <td class="sherah-table__column-5 sherah-table__data-5">
                                                    <div class="sherah-table__status {{ $admin->status ? 'sherah-color3' : 'sherah-color2' }}">
                                                        {{ $admin->status ? 'Hoạt động' : 'Khóa' }}
                                                    </div>
                                                </td>

                                                <!-- Hành động -->
                                                <td class="sherah-table__column-6 sherah-table__data-6">
                                                    <div class="sherah-table__status__group d-flex gap-1">
                                                        <a href="{{ route('admin.accounts.edit', $admin->id) }}"
                                                            class="sherah-table__action"
                                                            style="background-color: #3b82f6; color: white; border-radius: 4px; padding: 4px 8px;">
                                                            Sửa
                                                        </a>

                                                        <form action="{{ route('admin.accounts.destroy', $admin->id) }}"
                                                              method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="sherah-table__action"
                                                                style="background-color: #ef4444; color: white; border-radius: 4px; padding: 4px 8px;"
                                                                onclick="return confirm('Xóa tài khoản này?')">
                                                                Xóa
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Không có tài khoản admin nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Pagination -->
<div class="row mg-top-40">
    <div class="sherah-pagination">
        <ul class="sherah-pagination__list d-flex flex-wrap justify-content-center gap-1">
            <!-- Previous -->
            <li class="sherah-pagination__button {{ $admins->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $admins->previousPageUrl() }}" class="page-link">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>

            <!-- Page Numbers (giới hạn hiển thị 7 trang) -->
            @php
                $start = max(1, $admins->currentPage() - 3);
                $end = min($admins->lastPage(), $admins->currentPage() + 3);
                if ($end - $start < 6) {
                    if ($start == 1) $end = min($admins->lastPage(), 7);
                    if ($end == $admins->lastPage()) $start = max(1, $admins->lastPage() - 6);
                }
            @endphp

            @if($start > 1)
                <li><a href="{{ $admins->url(1) }}" class="page-link">01</a></li>
                @if($start > 2)<li class="disabled"><span class="page-link">...</span></li>@endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li class="{{ $admins->currentPage() == $i ? 'active' : '' }}">
                    <a href="{{ $admins->url($i) }}" class="page-link">
                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                    </a>
                </li>
            @endfor

            @if($end < $admins->lastPage())
                @if($end < $admins->lastPage() - 1)<li class="disabled"><span class="page-link">...</span></li>@endif
                <li><a href="{{ $admins->url($admins->lastPage()) }}" class="page-link">
                    {{ str_pad($admins->lastPage(), 2, '0', STR_PAD_LEFT) }}
                </a></li>
            @endif

            <!-- Next -->
            <li class="sherah-pagination__button {{ $admins->currentPage() == $admins->lastPage() ? 'disabled' : '' }}">
                <a href="{{ $admins->nextPageUrl() }}" class="page-link">
                    <i class="fas fa-angle-right"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.admin.master')

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

        .sherah-table__head {
            display: table-header-group !important;
        }

        .sherah-table__body {
            display: table-row-group !important;
        }

        .brand-name {
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 6px;
            background: #f8f9fa;
            padding: 4px;
        }

        .brand-status-col {
            width: 140px;
        }

        .brand-action-col {
            width: 160px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            /* căn giữa cả cụm */
            align-items: center;
            gap: 10px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            min-width: 90px;
            /* 🔥 QUAN TRỌNG: 2 nút bằng nhau */
            height: 40px;

            font-weight: 500;
            border-radius: 6px;
            padding: 0 12px;
        }
    </style>

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">

                            {{-- Header --}}
                            <div class="row mg-top-30">
                                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Quản lý Thương hiệu</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('admin.brands.create') }}" class="btn btn-success">
                                        <i class="bi bi-plus-lg me-1"></i> Thêm brand
                                    </a>
                                </div>
                            </div>



                            {{-- Table --}}
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <div class="table-responsive">
                                    <table class="sherah-table__main align-middle">
                                        <colgroup>
                                            <col style="width:80px;">
                                            <col>
                                            <col style="width:140px;">
                                            <col style="width:140px;">
                                            <col style="width:160px;">
                                        </colgroup>

                                        <thead class="sherah-table__head">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên thương hiệu</th>
                                                <th>Ảnh thương hiệu</th>
                                                <th class="brand-status-col">Trạng thái</th>
                                                <th class="brand-action-col">Hành động</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sherah-table__body">
                                            @forelse($brands as $brand)
                                                <tr>
                                                    <td class="text-nowrap">{{ $brand->id }}</td>

                                                    <td class="brand-name" title="{{ $brand->name }}">
                                                        {{ $brand->name }}
                                                    </td>

                                                    <td>
                                                        @if ($brand->logo)
                                                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                                                class="brand-logo" alt="{{ $brand->name }}">
                                                        @else
                                                            <span class="text-muted">Không có logo</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if ($brand->status)
                                                            <span class="badge bg-success">Hoạt động</span>
                                                        @else
                                                            <span class="badge bg-secondary">Tạm tắt</span>
                                                        @endif
                                                    </td>

                                                    <td class="action-col">
                                                        <div class="action-buttons">
                                                            <a href="{{ route('admin.brands.edit', $brand) }}"
                                                                class="btn btn-warning btn-sm action-btn text-white">
                                                                <i class="bi bi-pencil-square"></i>
                                                                <span>Sửa</span>
                                                            </a>

                                                            <form action="{{ route('admin.brands.destroy', $brand) }}"
                                                                method="POST" onsubmit="return confirm('Xóa brand này?')"
                                                                class="m-0">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger btn-sm action-btn">
                                                                    <i class="bi bi-trash3"></i>
                                                                    <span>Xóa</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        Chưa có brand nào
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    {{ $brands->links() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

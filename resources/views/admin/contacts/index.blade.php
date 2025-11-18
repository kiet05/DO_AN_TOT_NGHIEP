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

        /* Đảm bảo head/body hiển thị đúng dạng table để không lệch cột */
        .sherah-table__head {
            display: table-header-group !important;
        }

        .sherah-table__body {
            display: table-row-group !important;
        }

        /* Cột subject / email dài thì cắt bớt và hiển thị ... */
        .ct-subject,
        .ct-email {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ct-subject {
            max-width: 260px;
        }

        .ct-email {
            max-width: 220px;
        }

        .ct-status-col {
            width: 130px;
        }

        .ct-action-col {
            width: 140px;
        }

        /* Căn trái các nút hành động giống banner */
        .action-buttons {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 8px;
            height: 100%;
        }

        .action-buttons .btn {
            min-width: 70px;
            height: 40px;
            font-weight: 500;
            border-radius: 6px;
            padding: 0 12px;
        }

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
                                        <h2 class="sherah-breadcrumb__title">Danh sách liên hệ</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                            <li class="active"><a href="{{ route('admin.contacts.index') }}">Liên hệ</a>
                                            </li>
                                        </ul>
                                    </div>

                                    @php
                                        $status = request('status');
                                    @endphp

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.contacts.index') }}"
                                            class="btn btn-outline-secondary {{ $status === null || $status === '' ? 'active' : '' }}">
                                            Tất cả
                                        </a>
                                        <a href="{{ route('admin.contacts.index', ['status' => 'new']) }}"
                                            class="btn btn-outline-danger {{ $status === 'new' ? 'active' : '' }}">
                                            Mới
                                        </a>
                                        <a href="{{ route('admin.contacts.index', ['status' => 'read']) }}"
                                            class="btn btn-outline-success {{ $status === 'read' ? 'active' : '' }}">
                                            Đã đọc
                                        </a>
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
                                            <col style="width:70px;"> {{-- ID --}}
                                            <col style="width:180px;"> {{-- Họ tên --}}
                                            <col style="width:220px;"> {{-- Email --}}
                                            <col style="width:130px;"> {{-- SĐT --}}
                                            <col style="width:130px;"> {{-- Trạng thái --}}
                                            <col style="width:160px;"> {{-- Thời gian --}}
                                            <col style="width:140px;"> {{-- Hành động --}}
                                        </colgroup>

                                        <thead class="sherah-table__head">
                                            <tr>
                                                <th>ID</th>
                                                <th>Họ tên</th>
                                                <th>Email</th>
                                                <th>SĐT</th>
                                                <th class="ct-status-col">Trạng thái</th>
                                                <th>Thời gian</th>
                                                <th class="ct-action-col">Hành động</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sherah-table__body">
                                            @forelse($contacts as $contact)
                                                <tr>
                                                    <td>{{ $contact->id }}</td>
                                                    <td>{{ $contact->name }}</td>
                                                    <td class="ct-email" title="{{ $contact->email }}">
                                                        {{ $contact->email }}
                                                    </td>
                                                    <td>{{ $contact->phone }}</td>
                                                   
                                                    <td>
                                                        @if ($contact->status === 'new')
                                                            <span class="badge bg-danger">Mới</span>
                                                        @else
                                                            <span class="badge bg-secondary">Đã đọc</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $contact->created_at?->format('d/m/Y H:i') }}</td>
                                                    <td class="action-col">
                                                        <div class="action-buttons">
                                                            <a href="{{ route('admin.contacts.show', $contact) }}"
                                                                class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                                                <i class="bi bi-eye"></i> Xem
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        Chưa có yêu cầu hỗ trợ nào.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-3">
                                    {{ $contacts->withQueryString()->links() }}
                                </div>
                            </div>

                        </div> {{-- .sherah-dsinner --}}
                    </div> {{-- .sherah-body --}}
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.admin.master')

@section('title', 'Danh sách đánh giá')

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
                                        <h2 class="sherah-breadcrumb__title">Danh Sách Đánh Giá</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.reviews.index') }}" class="mb-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <select name="status" onchange="this.form.submit()" class="form-select w-auto">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="0" @selected(request('status') == '0')>Chờ</option>
                                        <option value="1" @selected(request('status') == '1')>Duyệt</option>
                                        <option value="2" @selected(request('status') == '2')>Từ chối</option>
                                    </select>

                                    <input type="text" name="product_id" class="form-control w-auto" placeholder="ID sản phẩm"
                                        value="{{ request('product_id') }}" />

                                    <button type="submit" class="btn btn-primary">Tìm</button>

                                    @if (request('status') || request('product_id'))
                                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                                    @endif
                                </div>
                            </form>

                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Sản phẩm</th>
                                                <th>Người đánh giá</th>
                                                <th>Đánh giá</th>
                                                <th>Bình luận</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reviews as $r)
                                                <tr>
                                                    <td>{{ $r->id }}</td>
                                                    <td class="text-nowrap">{{ optional($r->product)->name ?? '(đã xóa)' }}</td>
                                                    <td>{{ optional($r->user)->name ?? 'Khách' }}</td>
                                                    <td>{{ $r->rating }}/5</td>
                                                    <td class="text-truncate" style="max-width:300px">{{ $r->comment }}</td>
                                                    <td>
                                                        @php
                                                            $statusLabels = [0 => 'Chờ', 1 => 'Duyệt', 2 => 'Từ chối'];
                                                            $statusClasses = [0 => 'badge bg-secondary', 1 => 'badge bg-success', 2 => 'badge bg-danger'];
                                                        @endphp
                                                        <span class="{{ $statusClasses[$r->status] ?? 'badge bg-secondary' }}">
                                                            {{ $statusLabels[$r->status] ?? $r->status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ optional($r->created_at)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.reviews.show', $r->id) }}" class="btn btn-outline-primary btn-sm">Chi tiết</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Không có đánh giá nào</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    {{ $reviews->appends(request()->all())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

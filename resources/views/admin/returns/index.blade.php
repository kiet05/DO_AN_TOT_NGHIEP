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
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Sản phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="/">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.products.index') }}">Sản phẩm</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header text-dark">
                                    <form class="row g-2" method="get">
                                        <div class="col-auto">
                                            <select name="status" class="form-select" onchange="this.form.submit()">
                                                <option value="">Tất cả trạng thái</option>
                                                <option value="0" @selected(request('status') === '0')>Chờ duyệt</option>
                                                <option value="1" @selected(request('status') === '1')>Đã duyệt</option>
                                                <option value="3" @selected(request('status') === '3')>Đang hoàn tiền</option>
                                                <option value="4" @selected(request('status') === '4')>Hoàn tất</option>
                                                <option value="2" @selected(request('status') === '2')>Từ chối</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <input type="number" name="order_id" class="form-control"
                                                placeholder="Đơn hàng #" value="{{ request('order_id') }}">
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-primary">Lọc</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Đơn hàng</th>
                                                <th>Khách</th>
                                                <th>Số tiền hoàn</th>
                                                <th>PT hoàn</th>
                                                <th>Trạng thái</th>
                                                <th>Thời gian</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($returns as $r)
                                                @php
                                                    $labels = [
                                                        0 => 'Chờ duyệt',
                                                        1 => 'Đã duyệt',
                                                        2 => 'Từ chối',
                                                        3 => 'Đang hoàn tiền',
                                                        4 => 'Hoàn tất',
                                                    ];
                                                    $badges = [
                                                        0 => 'secondary',
                                                        1 => 'primary',
                                                        2 => 'danger',
                                                        3 => 'warning',
                                                        4 => 'success',
                                                    ];
                                                @endphp
                                                <tr>
                                                    <td>#{{ $r->id }}</td>
                                                    <td>#{{ $r->order_id }}</td>
                                                    <td>{{ $r->user->full_name ?? ($r->user->name ?? 'User ' . $r->user_id) }}
                                                    </td>
                                                    <td>{{ number_format($r->refund_amount, 0, ',', '.') }} đ</td>
                                                    <td>{{ $r->refund_method ?? '-' }}</td>
                                                    <td><span
                                                            class="badge text-bg-{{ $badges[$r->status] ?? 'secondary' }}">{{ $labels[$r->status] ?? $r->status }}</span>
                                                    </td>
                                                    <td>{{ optional($r->created_at)->format('d/m/Y H:i') }}</td>
                                                    <td><a href="{{ route('admin.returns.show', $r->id) }}"
                                                            class="btn btn-sm btn-outline-primary">Chi tiết</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $returns->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

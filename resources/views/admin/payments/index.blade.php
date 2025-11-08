@extends('layouts.admin.master')

@section('title', 'Danh sách thanh toán')

@section('content')
    <section class="sherah-adashboard sherah-show" id="payment-index">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Breadcrumb + quick actions --}}
                    <div class="sherah-flex-between mg-top-20 mg-bottom-10">
                        <div class="sherah-breadcrumb">
                            <h2 class="sherah-breadcrumb__title">Danh sách thanh toán</h2>
                            <ul class="sherah-breadcrumb__list">
                                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="active">Payments</li>
                            </ul>
                        </div>

                        {{-- Quick filters --}}
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('admin.payments.index') }}"
                                class="sherah-btn sherah-light btn-sm px-3 py-2 fs-5 {{ request('status') ? '' : 'active' }}">
                                Tất cả
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
                                class="sherah-btn sherah-border btn-sm px-3 py-2 fs-5 {{ request('status') === 'pending' ? 'active' : '' }}">
                                Pending
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'success']) }}"
                                class="sherah-btn sherah-gbcolor btn-sm px-3 py-2 fs-5 {{ request('status') === 'success' ? 'active' : '' }}">
                                Success
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'failed']) }}"
                                class="sherah-btn sherah-color btn-sm px-3 py-2 fs-5 {{ request('status') === 'failed' ? 'active' : '' }}">
                                Failed
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'canceled']) }}"
                                class="sherah-btn sherah-border-warning btn-sm px-3 py-2 fs-5 {{ request('status') === 'canceled' ? 'active' : '' }}">
                                Canceled
                            </a>
                        </div>



                    </div>

                    {{-- Alerts --}}
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Bộ lọc chi tiết --}}
                    <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-15">
                        <div class="sherah-page-title">
                            <h4>Bộ lọc thanh toán</h4>
                        </div>
                        <div class="sherah-page-content">
                            <form method="GET" class="row g-2 align-items-center">
                                <div class="col-lg-3 col-md-6">
                                    <input name="app_trans_id" value="{{ request('app_trans_id') }}"
                                        class="form-control form-control-sm" placeholder="Tìm mã giao dịch...">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <select name="gateway" class="form-select form-select-sm">
                                        <option value="">-- Cổng thanh toán --</option>
                                        @foreach (['zalopay' => 'ZALOPAY', 'cod' => 'COD', 'bank' => 'BANK'] as $val => $label)
                                            <option value="{{ $val }}" @selected(request('gateway') === $val)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">-- Trạng thái --</option>
                                        @foreach (['pending', 'success', 'failed', 'canceled'] as $st)
                                            <option value="{{ $st }}" @selected(request('status') === $st)>
                                                {{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                                        class="form-control form-control-sm" placeholder="Từ ngày">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                                        class="form-control form-control-sm" placeholder="Đến ngày">
                                </div>
                                <div class="col-lg-1 col-md-4 d-grid">
                                    <button class="sherah-btn sherah-gbcolor btn-sm">Lọc</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Bảng danh sách --}}
                    <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-20">
                        <div class="sherah-page-title">
                            <h4>Danh sách giao dịch</h4>
                        </div>
                        <div class="sherah-page-content">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 payment-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:90px">Đơn hàng</th>
                                            <th style="width:150px">Cổng thanh toán</th>
                                            <th>Mã giao dịch</th>
                                            <th class="text-end" style="width:140px">Số tiền</th>
                                            <th style="width:120px">Trạng thái</th>
                                            <th style="width:160px">Thời gian thanh toán</th>
                                            <th class="text-end" style="width:140px">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                            @php $map=['pending'=>'secondary','success'=>'success','failed'=>'danger','canceled'=>'warning']; @endphp
                                            <tr>
                                                <td>#{{ $payment->order_id }}</td>
                                                <td class="text-uppercase">{{ $payment->gateway }}</td>
                                                <td class="font-monospace text-nowrap">{{ $payment->app_trans_id }}</td>
                                                <td class="text-end text-nowrap">{{ number_format($payment->amount) }}đ
                                                </td>
                                                <td><span
                                                        class="badge bg-{{ $map[$payment->status] ?? 'secondary' }}">{{ ucfirst($payment->status) }}</span>
                                                </td>
                                                <td class="text-nowrap">
                                                    {{ optional($payment->paid_at)->format('d/m/Y H:i') }}</td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a class="btn btn-outline-primary"
                                                            href="{{ route('admin.payments.show', $payment) }}">Xem</a>
                                                        @if (strtolower($payment->gateway) === 'zalopay')
                                                            <form method="POST"
                                                                action="{{ route('admin.payments.query', $payment) }}">
                                                                @csrf
                                                                <button class="btn btn-outline-info">Query</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Chưa có giao dịch.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="sherah-page-footer d-flex justify-content-end p-3">
                            {{ $payments->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

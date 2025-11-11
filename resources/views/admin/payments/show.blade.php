@extends('layouts.admin.master')

@section('title', 'Chi tiết giao dịch')

@section('content')
    <section class="sherah-adashboard sherah-show" id="payment-show">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Breadcrumb + actions --}}
                    <div class="sherah-flex-between mg-top-20 mg-bottom-20">
                        <div class="sherah-breadcrumb">
                            <h2 class="sherah-breadcrumb__title">Giao dịch #{{ $payment->id }}</h2>
                            <ul class="sherah-breadcrumb__list">
                                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li><a href="{{ route('admin.payments.index') }}">Payments</a></li>
                                <li class="active">Chi tiết</li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.payments.index') }}" class="sherah-btn sherah-light btn-sm">← Về danh
                                sách</a>
                        </div>
                    </div>

                    {{-- Thông tin giao dịch (rút gọn) --}}
                    <div class="sherah-page-inner sherah-border sherah-default-bg mb-4 p-4">
                        <div class="sherah-page-title mb-3">
                            <h4>Thông tin giao dịch</h4>
                        </div>
                        <form method="POST" action="{{ route('admin.payments.updateStatus', $payment) }}"
                            class="mt-3 d-flex gap-2 align-items-center">
                            @csrf
                            <label class="text-muted">Cập nhật trạng thái:</label>
                            <select name="status" class="form-select" style="max-width:200px;">
                                @foreach ($allowed as $next)
                                    <option value="{{ $next }}">{{ ucfirst($next) }}</option>
                                @endforeach
                            </select>
                            <button class="sherah-btn sherah-border btn-sm">Cập nhật</button>
                        </form>

                        <div class="sherah-page-content mt-4">
                            @php
                                $statusText =
                                    [
                                        'pending' => 'Đang xử lý',
                                        'success' => 'Thành công',
                                        'failed' => 'Thất bại',
                                        'canceled' => 'Đã hủy',
                                        'refunded' => 'Đã hoàn tiền',
                                    ][$payment->status] ?? $payment->status;

                                $badge =
                                    [
                                        'pending' => 'secondary',
                                        'success' => 'success',
                                        'failed' => 'danger',
                                        'canceled' => 'warning',
                                        'refunded' => 'info',
                                    ][$payment->status] ?? 'secondary';

                                $rows = [
                                    'Đơn hàng' => $payment->order_id ? '#' . $payment->order_id : null,
                                    'Cổng thanh toán' => strtoupper($payment->gateway), 
                                    'Mã giao dịch App' => $payment->app_trans_id,
                                    'Mã giao dịch Gateway' => $payment->vnp_trans_id ?? $payment->gateway_trans_id, 
                                    'Số tiền' => number_format($payment->amount) . 'đ',
                                    'Trạng thái' => '<span class="badge bg-' . $badge . '">' . $statusText . '</span>',
                                    'Thanh toán lúc' => optional($payment->paid_at)->format('d/m/Y H:i'),
                                    'Tạo lúc' => $payment->created_at
                                        ? $payment->created_at->format('d/m/Y H:i')
                                        : null,
                                    'Cập nhật' => $payment->updated_at
                                        ? $payment->updated_at->format('d/m/Y H:i')
                                        : null,
                                ];

                                $rows = array_filter($rows, fn($v) => filled($v));
                            @endphp

                            <div class="row gy-2">
                                @foreach ($rows as $label => $value)
                                    <div class="col-sm-5 col-md-4 text-muted"><strong>{{ $label }}:</strong></div>
                                    <div class="col-sm-7 col-md-8">{!! $label === 'Đơn hàng' && $payment->order_id
                                        ? '<a href="' . route('admin.orders.show', $payment->order_id) . '">#' . $payment->order_id . '</a>'
                                        : $value !!}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Nhật ký giao dịch --}}
                    <div class="sherah-page-inner sherah-border sherah-default-bg p-4">
                        <div class="sherah-page-title mb-3">
                            <h4>Nhật ký giao dịch</h4>
                        </div>
                        <div class="sherah-page-content">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width:160px">Thời gian</th>
                                            <th style="min-width:140px">Loại</th>
                                            <th>Nội dung</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payment->logs as $log)
                                            @php
                                                $msg =
                                                    $log->message ??
                                                    (is_array($log->data ?? null) || is_object($log->data ?? null)
                                                        ? json_encode($log->data, JSON_UNESCAPED_UNICODE)
                                                        : $log->data ?? '');
                                            @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                                                </td>
                                                <td class="text-uppercase">{{ $log->type }}</td>
                                                <td><span class="d-inline-block text-truncate" style="max-width:680px"
                                                        title="{{ $msg }}">{{ $msg }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">Chưa có nhật ký.</td>
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
    </section>
@endsection
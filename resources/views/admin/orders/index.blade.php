@extends('layouts.admin.master')

@section('title', 'Order List')

@section('content')
    <style>
        .badge-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            text-transform: capitalize;
            color: #fff;
            font-weight: 600;
            display: inline-block;
            min-width: 85px;
            text-align: center;
        }

        .pending {
            background: #6c757d
        }

        .shipping {
            background: #17a2b8
        }

        .completed {
            background: #28a745
        }

        .cancelled {
            background: #dc3545
        }

        .payment-paid {
            color: #28a745;
            font-weight: 700
        }

        .payment-unpaid {
            color: #dc3545;
            font-weight: 700
        }

        .td-address {
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }
    </style>

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">

                            {{-- Breadcrumb --}}
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Order List</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Bộ lọc --}}
                            <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-3">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <select name="status" onchange="this.form.submit()" class="form-select w-auto">
                                        <option value="">-- Tất cả trạng thái --</option>
                                        <option value="pending" @selected(request('status') == 'pending')>Chờ xử lý</option>
                                        <option value="shipping" @selected(request('status') == 'shipping')>Đang giao</option>
                                        <option value="completed" @selected(request('status') == 'completed')>Đã giao</option>
                                        <option value="cancelled" @selected(request('status') == 'cancelled')>Đã hủy</option>
                                    </select>

                                    {{-- Tìm theo ID (chấp nhận 87 hoặc #00087) --}}
                                    <input type="text" name="id"
                                        class="form-control w-auto border border-1 border-secondary"
                                        placeholder="  Tìm theo ID..." value="{{ request('id') }}" />


                                    <button type="submit" class="btn btn-primary">Lọc</button>

                                    @if (request('status') || request('id'))
                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Xóa
                                            lọc</a>
                                    @endif
                                </div>
                            </form>


                            {{-- Bảng đơn hàng --}}
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Người nhận</th>
                                                <th>SĐT</th>
                                                <th>Địa chỉ</th>
                                                <th class="text-end">Phí ship</th>
                                                <th class="text-end">Tổng tiền</th>
                                                <th>Thanh toán</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                                <th width="120">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                                @php
                                                    $statusClass = $order->order_status; // pending/shipping/completed/cancelled
                                                    $paymentClass =
                                                        $order->payment_status === 'paid'
                                                            ? 'payment-paid'
                                                            : 'payment-unpaid';
                                                @endphp
                                                <tr>
                                                    <td class="text-nowrap">#{{ $order->id }}</td>
                                                    <td class="text-nowrap fw-bold">{{ $order->receiver_name }}</td>
                                                    <td class="text-nowrap">{{ $order->receiver_phone }}</td>
                                                    <td class="td-address" title="{{ $order->receiver_address }}">
                                                        {{ $order->receiver_address }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                                                    <td class="text-end text-primary fw-bold">
                                                        {{ number_format($order->final_amount, 0, ',', '.') }}đ</td>
                                                    <td class="{{ $paymentClass }}">{{ $order->payment_status }}</td>
                                                    <td><span
                                                            class="badge-status {{ $statusClass }}">{{ $order->order_status }}</span>
                                                    </td>
                                                    <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                                            class="btn btn-outline-primary btn-sm">Chi tiết</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted">Không có đơn hàng nào
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    {{ $orders->appends(request()->all())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

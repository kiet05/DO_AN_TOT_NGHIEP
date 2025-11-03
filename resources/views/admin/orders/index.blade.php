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

        .td-address {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-select {
            min-width: 150px;
        }

        /* Nếu theme set display:block cho thead/tbody, ép về mặc định để khỏi lệch cột */
        .sherah-table__head {
            display: table-header-group !important;
        }

        .sherah-table__body,
        .sherah-table__body-v3 {
            display: table-row-group !important;
        }
    </style>

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Order list</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="{{ route('admin.orders.index') }}">Order List</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="" class="sherah-btn sherah-gbcolor">Add New Order</a>
                                </div>
                            </div>

                            <!-- Bộ lọc trạng thái -->
                            <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
                                <select name="status" onchange="this.form.submit()" class="form-select w-auto d-inline">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử
                                        lý</option>
                                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang
                                        giao</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã
                                        giao</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã
                                        hủy</option>
                                </select>
                            </form>

                            <!-- Bảng đơn hàng -->
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <div class="table-responsive">
                                    <table class="sherah-table__main align-middle">
                                        <colgroup>
                                            <col style="width:90px;">
                                            <col style="width:170px;">
                                            <col style="width:140px;">
                                            <col style="width:300px;"> {{-- Địa chỉ --}}
                                            <col style="width:120px;">
                                            <col style="width:140px;">
                                            <col style="width:120px;">
                                            <col style="width:160px;"> {{-- Trạng thái --}}
                                            <col style="width:160px;">
                                            <col style="width:110px;">
                                        </colgroup>

                                        <thead class="sherah-table__head align-middle">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Người nhận</th>
                                                <th>Điện thoại</th>
                                                <th>Địa chỉ</th>
                                                <th class="text-end">Phí ship</th>
                                                <th class="text-end">Tổng tiền</th>
                                                <th>Thanh toán</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                                <th>Cập nhật</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sherah-table__body sherah-table__body-v3">
                                            @forelse($orders as $order)
                                                <tr>
                                                    <td class="text-nowrap">#{{ $order->id }}</td>
                                                    <td class="text-nowrap">{{ $order->receiver_name }}</td>
                                                    <td class="text-nowrap">{{ $order->receiver_phone }}</td>

                                                    <td class="td-address" title="{{ $order->receiver_address }}">
                                                        {{ $order->receiver_address }}
                                                    </td>

                                                    <td class="text-end text-nowrap">
                                                        {{ number_format($order->shipping_fee, 0, ',', '.') }}đ
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        {{ number_format($order->final_amount, 0, ',', '.') }}đ
                                                    </td>

                                                    <td class="text-capitalize text-nowrap">{{ $order->payment_status }}
                                                    </td>
                                                    <td>
                                                        <form method="POST"
                                                            action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                                            @csrf
                                                            <select name="status"
                                                                class="form-select form-select-sm status-select"
                                                                onchange="this.form.submit()">
                                                                <option value="pending"
                                                                    {{ $order->order_status == 'pending' ? 'selected' : '' }}>
                                                                    Chờ xử lý</option>
                                                                <option value="shipping"
                                                                    {{ $order->order_status == 'shipping' ? 'selected' : '' }}>
                                                                    Đang giao</option>
                                                                <option value="completed"
                                                                    {{ $order->order_status == 'completed' ? 'selected' : '' }}>
                                                                    Đã giao</option>
                                                                <option value="cancelled"
                                                                    {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>
                                                                    Đã hủy</option>
                                                            </select>
                                                        </form>
                                                    </td>

                                                    <td class="text-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-outline-secondary btn-sm">In hóa
                                                            đơn</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">Không có đơn hàng nào</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
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

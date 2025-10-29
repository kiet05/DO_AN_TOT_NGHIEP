@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📦 Quản lý đơn hàng</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Bộ lọc trạng thái -->
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
        <select name="status" onchange="this.form.submit()" class="form-select w-auto d-inline">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ xử lý</option>
            <option value="shipping" {{ request('status')=='shipping'?'selected':'' }}>Đang giao</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Đã giao</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Đã hủy</option>
        </select>
    </form>

    <!-- Bảng danh sách -->
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Người nhận</th>
                <th>Điện thoại</th>
                <th>Địa chỉ</th>
                <th>Phí ship</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Cập nhật</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->receiver_name }}</td>
                    <td>{{ $order->receiver_phone }}</td>
                    <td>{{ $order->receiver_address }}</td>
                    <td>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                    <td>{{ number_format($order->final_amount, 0, ',', '.') }}đ</td>
                    <td>{{ $order->payment_status }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="form-select">
                                <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="shipping" {{ $order->order_status == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Đã giao</option>
                                <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </form>
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td><a href="#" class="btn btn-outline-secondary btn-sm">In hóa đơn</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Không có đơn hàng nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>

{{ $orders->appends(request()->all())->links('pagination::bootstrap-5') }}


</div>
@endsection

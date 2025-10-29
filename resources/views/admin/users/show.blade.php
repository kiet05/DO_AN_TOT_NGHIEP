@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">👤 Thông tin khách hàng #{{ $user->id }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Họ tên:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa có' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <h4>🧾 Danh sách đơn hàng</h4>
    <table class="table table-hover align-middle">
        <thead class="table-secondary">
            <tr>
                <th>ID</th>
                <th>Địa chỉ</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($user->orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->receiver_address }}</td>
                    <td>{{ number_format($order->final_amount, 0, ',', '.') }}đ</td>
                    <td>{{ ucfirst($order->order_status) }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Không có đơn hàng nào</td></tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">⬅ Quay lại</a>
</div>
@endsection

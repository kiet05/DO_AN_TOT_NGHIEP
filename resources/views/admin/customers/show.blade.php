@extends('admin.layouts.master')
@section('title', 'Thông tin khách hàng')

@section('content')
<div class="p-6">

    <h1 class="text-xl font-bold mb-4">Thông tin khách hàng</h1>

    <div class="bg-white p-5 rounded-lg shadow mb-6">
        <p><strong>Họ tên:</strong> {{ $customer->name }}</p>
        <p><strong>Email:</strong> {{ $customer->email }}</p>
        <p><strong>Số điện thoại:</strong> {{ $customer->phone ?? '—' }}</p>
        <p><strong>Địa chỉ:</strong> {{ $customer->address ?? '—' }}</p>

        <p class="mt-2">
            <strong>Trạng thái:</strong>
            @if($customer->status)
                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Hoạt động</span>
            @else
                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Đã khóa</span>
            @endif
        </p>

        <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST" class="mt-4">
            @csrf
            <button class="px-4 py-2 border rounded hover:bg-gray-100">
                {{ $customer->status ? 'Khóa tài khoản' : 'Mở tài khoản' }}
            </button>
        </form>
    </div>

    {{-- Lịch sử mua hàng --}}
    <h2 class="text-lg font-semibold mb-3">Lịch sử mua hàng</h2>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y text-sm">
            <thead class="bg-gray-100 font-medium">
                <tr>
                    <th class="p-3 text-left">Mã đơn</th>
                    <th class="p-3 text-right">Tổng tiền</th>
                    <th class="p-3 text-center">Trạng thái</th>
                    <th class="p-3 text-right">Ngày tạo</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($customer->orders as $o)
                <tr>
                    <td class="p-3">#{{ $o->id }}</td>
                    <td class="p-3 text-right">{{ number_format($o->total,0,',','.') }}đ</td>
                    <td class="p-3 text-center">{{ $o->status ? 'Hoàn tất' : 'Chờ xử lý' }}</td>
                    <td class="p-3 text-right">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        Không có đơn hàng nào
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
@endsection

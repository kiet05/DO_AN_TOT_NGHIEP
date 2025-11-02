@extends('admin.layouts.master')
@section('title', 'Quản lý khách hàng')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold flex items-center gap-2 mb-6">
        👤 Quản lý khách hàng
    </h1>

    {{-- Form tìm kiếm --}}
    <form action="" method="GET" class="flex gap-3 mb-6 max-w-md">
        <input type="text" name="q" value="{{ $kw ?? '' }}"
               placeholder="Tìm theo tên, email hoặc SĐT"
               class="border rounded-lg px-3 py-2 w-full">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Tìm kiếm
        </button>
    </form>

    {{-- Bảng danh sách --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-100 text-sm font-medium">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Họ tên</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Số điện thoại</th>
                    <th class="p-3 text-center">Ngày tạo</th>
                    <th class="p-3 text-center">Tổng đơn hàng</th>
                    <th class="p-3 text-center">Trạng thái</th>
                    <th class="p-3 text-center">Hành động</th>
                </tr>
            </thead>

            <tbody class="divide-y text-sm">
                @foreach ($customers as $u)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 text-gray-600">#{{ $u->id }}</td>
                    <td class="p-3 font-medium">{{ $u->name }}</td>
                    <td class="p-3">{{ $u->email }}</td>
                    <td class="p-3">{{ $u->phone ?? '—' }}</td>
                    <td class="p-3 text-center">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-3 text-center">{{ $u->orders_count ?? 0 }}</td>

                    {{-- ✅ Trạng thái --}}
                    <td class="p-3 text-center">
                        @if($u->status)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Hoạt động</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Đã khóa</span>
                        @endif
                    </td>

                    {{-- ✅ Nút hành động --}}
                    <td class="p-3 text-right space-x-2">

                        {{-- Chi tiết --}}
                        <a href="{{ route('admin.customers.show',$u->id) }}"
                           class="px-3 py-1.5 text-sm border rounded hover:bg-gray-100">
                           Chi tiết
                        </a>

                        {{-- Khóa / Mở --}}
                        <form action="{{ route('admin.customers.toggleStatus',$u->id) }}"
                              method="POST" class="inline">
                            @csrf
                            <button class="px-3 py-1.5 text-sm border rounded hover:bg-gray-100"
                                onclick="return confirm('Bạn có chắc muốn {{ $u->status ? 'KHÓA' : 'MỞ'} } tài khoản này?')">
                                {{ $u->status ? 'Khóa' : 'Mở' }}
                            </button>
                        </form>

                        {{-- Xóa --}}
                        <form action="{{ route('admin.users.destroy',$u->id) }}"
                              method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600"
                                onclick="return confirm('Bạn có chắc muốn XÓA khách hàng này?')">
                                Xóa
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="mt-4">
        {{ $customers->links() }}
    </div>

</div>
@endsection

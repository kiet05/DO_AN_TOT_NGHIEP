@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">👤 Quản lý khách hàng</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Thanh tìm kiếm -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 d-flex gap-2 align-items-center">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control w-auto" placeholder="🔍 Tìm theo tên hoặc email">
        <button class="btn btn-primary">Tìm kiếm</button>
    </form>

    <!-- Bảng danh sách -->
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày tạo</th>
                <th>Tổng đơn hàng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $user->orders()->count() }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Xác nhận xóa khách hàng này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Không có khách hàng nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->appends(request()->all())->links('pagination::bootstrap-5') }}
</div>
@endsection

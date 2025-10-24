@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Quản lý tài khoản Admin</h1>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
    ← Quay lại Dashboard
</a>

    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary mb-3">Thêm Admin</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
            <tr>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->phone ?? 'N/A' }}</td>
                <td>{{ $admin->address ?? 'N/A' }}</td>
                <td>{{ $admin->role->name }}</td>
                <td>{{ $admin->status ? 'Hoạt động' : 'Khóa' }}</td>
                <td>
                    <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa tài khoản này?')">Xóa</button>
                    </form>
                    <form action="{{ route('admin.admins.toggle-status', $admin) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm {{ $admin->status ? 'btn-secondary' : 'btn-success' }}"
                            onclick="return confirm('{{ $admin->status ? 'Khóa' : 'Mở khóa' }} tài khoản này?')">
                            {{ $admin->status ? 'Khóa' : 'Mở khóa' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $admins->links() }}
</div>
@endsection
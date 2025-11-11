@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Sản phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="/">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.products.index') }}">Sản phẩm</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>
                            <h2>Quản lý tài khoản Admin</h2>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary mb-3">+ Thêm mới</a>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($admins as $admin)
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ $admin->role->name ?? '—' }}</td>
                                            <td>
                                                <span class="badge {{ $admin->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $admin->status ? 'Hoạt động' : 'Khóa' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.accounts.edit', $admin->id) }}"
                                                    class="btn btn-warning btn-sm">Sửa</a>
                                                <form action="{{ route('admin.accounts.destroy', $admin->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Xóa tài khoản này?')">Xóa</button>
                                                </form>
                                                {{-- <a href="{{ route('admin.accounts.toggleStatus', $admin->id) }}" class="btn btn-secondary btn-sm">
                                        {{ $admin->status ? 'Khóa' : 'Mở' }}
                                        </a> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                @if (method_exists($admins, 'links'))
                                    <div class="card-footer bg-white d-flex justify-content-end">
                                        {{ $admins->appends(request()->all())->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

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
                                <!-- Breadcrumb -->
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Sản phẩm</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="/">Dashboard</a></li>
                                        <li class="active"><a href="{{ route('admin.accounts.index') }}">Admin</a></li>
                                        <li class="active">Thêm Admin mới</li>
                                    </ul>
                                </div>
                                <!-- End Breadcrumb -->
                            </div>
                        </div>

                        <div class="card p-4 shadow-sm mt-4">
                            <h3 class="mb-4">Thêm Admin mới</h3>

                            <form action="{{ route('admin.accounts.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Tên</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" class="form-control">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
    <label class="form-label">Vai trò</label>
    <select name="role_id" class="form-select">
        {{-- <option value="1" {{ old('role_id', $user->role_id ?? '') == 1 ? 'selected' : '' }}>
            Quản trị viên
        </option> --}}
        <option value="2" {{ old('role_id', $user->role_id ?? '') == 2 ? 'selected' : '' }}>
            Nhân viên
        </option>
        <option value="6" {{ old('role_id', $user->role_id ?? '') == 4 ? 'selected' : '' }}>
            Biên tập viên
        </option>
    </select>

    @error('role_id')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>


                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="0" {{ old('status') === 0 ? 'selected' : '' }}>Khóa</option>
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success">Lưu</button>
                                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">Hủy</a>
                                </div>
                            </form>

                        </div> {{-- end card --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

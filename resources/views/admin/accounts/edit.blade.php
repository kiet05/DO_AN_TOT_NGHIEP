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
                            <h3>Chỉnh sửa Admin</h3>

                            <form action="{{ route('admin.accounts.update', $user->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-3">
                                    <label>Tên</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}">
                                    @error('email')
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
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $user->status ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="0" {{ !$user->status ? 'selected' : '' }}>Khóa</option>
                                    </select>
                                </div>

                                <button class="btn btn-primary">Cập nhật</button>
                                <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

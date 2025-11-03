@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <!-- Dashboard Inner -->
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Thêm Admin mới</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li><a href="{{ route('admin.accounts.index') }}">Tài khoản Admin</a></li>
                                            <li class="active"><a href="#">Thêm mới</a></li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                    <a href="{{ route('admin.accounts.index') }}" class="sherah-btn sherah-btn--secondary">Quay lại</a>
                                </div>
                            </div>

                            <!-- Form -->
                            <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <form action="{{ route('admin.accounts.store') }}" method="POST" class="sherah-form">
                                    @csrf

                                    <!-- Tên -->
                                    <div class="row mg-top-20">
                                        <div class="col-md-6">
                                            <label class="sherah-form__label">Tên <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="sherah-form__input" 
                                                   value="{{ old('name') }}" placeholder="Nhập tên admin">
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="row mg-top-20">
                                        <div class="col-md-6">
                                            <label class="sherah-form__label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="sherah-form__input" 
                                                   value="{{ old('email') }}" placeholder="example@gmail.com">
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Mật khẩu -->
                                    <div class="row mg-top-20">
                                        <div class="col-md-6">
                                            <label class="sherah-form__label">Mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="sherah-form__input" 
                                                   placeholder="Ít nhất 6 ký tự">
                                            @error('password')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Vai trò -->
                                    <div class="row mg-top-20">
                                        <div class="col-md-6">
                                            <label class="sherah-form__label">Vai trò <span class="text-danger">*</span></label>
                                            <select name="role_id" class="sherah-form__input sherah-form__select">
                                                <option value="">-- Chọn vai trò --</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Nút hành động -->
                                    <div class="row mg-top-30">
                                        <div class="col-12">
                                            <button type="submit" class="sherah-btn sherah-gbcolor">
                                                Lưu thông tin
                                            </button>
                                            <a href="{{ route('admin.accounts.index') }}" class="sherah-btn sherah-btn--secondary mg-left-10">
                                                Hủy
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
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
                <div class="sherah-breadcrumb">
                  <h2 class="sherah-breadcrumb__title">Thêm khách hàng</h2>
                  <ul class="sherah-breadcrumb__list">
                    <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
                    <li class="active"><a href="#">Thêm mới</a></li>
                  </ul>
                </div>
                <a href="{{ route('admin.customers.index') }}" class="sherah-btn sherah-default">← Quay lại</a>
              </div>
            </div>

            <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-25 p-20">
              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                  </ul>
                </div>
              @endif

              <form method="POST" action="{{ route('admin.customers.store') }}" class="row g-3">
                @csrf

                <div class="col-md-6">
                  <label class="form-label">Tên khách hàng *</label>
                  <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email *</label>
                  <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Số điện thoại</label>
                  <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Địa chỉ</label>
                  <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Mật khẩu *</label>
                  <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Nhập lại mật khẩu *</label>
                  <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Trạng thái *</label>
                  <select name="status" class="form-select" required>
                    <option value="1" {{ old('status','1')=='1'?'selected':'' }}>Hoạt động</option>
                    <option value="0" {{ old('status')==='0'?'selected':'' }}>Bị khóa</option>
                  </select>
                </div>

                <div class="col-12">
                  <button type="submit" class="sherah-btn sherah-gbcolor">Tạo khách hàng</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection


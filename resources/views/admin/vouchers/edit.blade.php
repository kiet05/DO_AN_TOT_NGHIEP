@extends('layouts.admin.master')

@section('content')
    {{-- <h4>Sửa mã khuyến mãi</h4>

    <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
        @method('PUT')
        @include('admin.vouchers._form', ['voucher' => $voucher])
        <button class="btn btn-primary mt-3">Cập nhật</button>
    </form> --}}

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12 mg-top-30">
                    <div class="sherah-body">
                        <div class="row mg-top-30">
                            <div class="col-12 sherah-flex-between">
                                <div class="sherah-breadcrumb">
                                    <h2 class="sherah-breadcrumb__title">Sửa mã khuyến mãi</h2>
                                    <ul class="sherah-breadcrumb__list">
                                        <li><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                        <li class="active"><a href="{{ route('admin.vouchers.index') }}">Danh sách mã
                                                khuyến mãi</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="sherah-dsinner mg-top-30">
                            <h4>Sửa mã khuyến mãi</h4>

                            <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
                                @method('PUT')
                                @include('admin.vouchers._form', ['voucher' => $voucher])
                                <button class="btn btn-primary mt-3">Cập nhật</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

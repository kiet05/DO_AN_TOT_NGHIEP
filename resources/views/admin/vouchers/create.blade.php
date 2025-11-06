@extends('admin.layouts.app')

@section('content')
<h4>Thêm mã khuyến mãi</h4>

<form action="{{ route('admin.vouchers.store') }}" method="POST">
    @include('admin.vouchers._form', ['voucher' => null])
    <button class="btn btn-primary mt-3">Lưu</button>
</form>
@endsection

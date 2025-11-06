@extends('admin.layouts.app')

@section('content')
<h4>Sửa mã khuyến mãi</h4>

<form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
    @method('PUT')
    @include('admin.vouchers._form', ['voucher' => $voucher])
    <button class="btn btn-primary mt-3">Cập nhật</button>
</form>
@endsection

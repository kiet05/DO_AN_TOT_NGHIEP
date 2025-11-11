@extends('layouts.admin.master')

@section('content')
<div class="container" style="padding: 20px;">
    <h1>Phương thức thanh toán</h1>
    <p>Total: {{ $paymentMethods->total() }}</p>
    <p>Count: {{ $paymentMethods->count() }}</p>
    
    <table border="1" cellpadding="10" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentMethods as $method)
                <tr>
                    <td>{{ $method->id }}</td>
                    <td>{{ $method->display_name }}</td>
                    <td>{{ $method->slug }}</td>
                    <td>{{ $method->is_active ? 'Hoạt động' : 'Tắt' }}</td>
                    <td>
                        <a href="{{ route('admin.payment-methods.edit', $method->id) }}">Sửa</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


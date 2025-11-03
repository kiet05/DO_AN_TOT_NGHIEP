@extends('layouts.app')
@section('title', 'Khách hàng mua nhiều nhất')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">👥 Top 10 khách hàng mua nhiều nhất</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên khách hàng</th>
                <th>Email</th>
                <th>Số đơn hàng</th>
                <th>Tổng chi tiêu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $c)
            <tr>
                <td>{{ $c->name }}</td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->total_orders }}</td>
                <td>{{ number_format($c->total_spent, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

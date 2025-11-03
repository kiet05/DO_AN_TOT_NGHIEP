@extends('layouts.app')
@section('title', 'Sản phẩm bán chạy')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🔥 Top 10 sản phẩm bán chạy</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng bán</th>
                <th>Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->total_sold }}</td>
                <td>{{ number_format($p->revenue, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

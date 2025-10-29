@extends('layouts.app')
@section('title', 'Báo cáo & Thống kê')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📊 Báo cáo & Thống kê</h2>
    <ul>
        <li><a href="{{ route('admin.reports.revenue') }}">Thống kê doanh thu theo ngày / tuần / tháng</a></li>
        <li><a href="{{ route('admin.reports.topProducts') }}">Sản phẩm bán chạy</a></li>
        <li><a href="{{ route('admin.reports.topCustomers') }}">Khách hàng mua nhiều nhất</a></li>
    </ul>
</div>
@endsection

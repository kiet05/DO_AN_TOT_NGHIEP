@extends('layouts.app')
@section('title', 'Thống kê doanh thu')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📈 Thống kê doanh thu</h2>

    <form action="{{ route('admin.reports.revenue') }}" method="GET" class="mb-4">
        <label>Chọn khoảng thời gian: </label>
        <select name="period" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Theo ngày</option>
            <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Theo tuần</option>
            <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Theo tháng</option>
        </select>
    </form>

    <canvas id="revenueChart" height="100"></canvas>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($query as $item)
            <tr>
                <td>{{ $item->date }}</td>
                <td>{{ number_format($item->revenue, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: {!! json_encode($data) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            fill: true,
            tension: 0.3
        }]
    }
});
</script>
@endsection

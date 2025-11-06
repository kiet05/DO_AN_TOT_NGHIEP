  @extends('layouts.admin.master')
@section('title', 'Thống kê doanh thu')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">📈 Thống kê doanh thu</h2>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">⬅ Tổng quan</a>
  </div>

  <form action="{{ route('admin.reports.revenue') }}" method="GET" class="mb-4 d-flex gap-2 align-items-center">
    <label class="me-2">Khoảng thời gian:</label>
    <select name="period" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
      <option value="day"   {{ ($period ?? 'day') == 'day' ? 'selected' : '' }}>Theo ngày</option>
      <option value="week"  {{ ($period ?? 'day') == 'week' ? 'selected' : '' }}>Theo tuần</option>
      <option value="month" {{ ($period ?? 'day') == 'month' ? 'selected' : '' }}>Theo tháng</option>
    </select>

    <input type="date" name="from" class="form-control w-auto" value="{{ $from ?? '' }}">
    <input type="date" name="to"   class="form-control w-auto" value="{{ $to ?? '' }}">
    <button class="btn btn-primary">Áp dụng</button>
    @if(request()->hasAny(['period','from','to']))
      <a href="{{ route('admin.reports.revenue') }}" class="btn btn-default">Xóa lọc</a>
    @endif
  </form>

  <div class="sherah-page-inner sherah-border sherah-default-bg">
    <div class="sherah-page-title"><h4>Biểu đồ doanh thu</h4></div>
    <div class="sherah-page-content">
      <canvas id="revenueChart" height="120"></canvas>
    </div>
  </div>

  <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mt-3">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Thời gian</th>
            <th class="text-end">Doanh thu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($query as $item)
            <tr>
              <td>{{ $item->date }}</td>
              <td class="text-end">{{ number_format($item->revenue, 0, ',', '.') }} đ</td>
            </tr>
          @empty
            <tr><td colspan="2" class="text-center text-muted">Không có dữ liệu</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');
if (ctx) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: @json($labels ?? []),
      datasets: [{
        label: 'Doanh thu (VNĐ)',
        data: @json($data ?? []),
        borderColor: '#007bff',
        backgroundColor: 'rgba(0, 123, 255, 0.2)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  });
}
</script>
@endpush

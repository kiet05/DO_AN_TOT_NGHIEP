@extends('layouts.admin.master')

@section('title','Thống kê & Báo cáo')

@section('content')
<section class="sherah-adashboard sherah-show">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sherah-body">
          <div class="sherah-dsinner">

            {{-- Breadcrumb --}}
            <div class="row mg-top-30">
              <div class="col-12 sherah-flex-between">
                <div class="sherah-breadcrumb">
                  <h2 class="sherah-breadcrumb__title">Thống kê & Báo cáo</h2>
                  <ul class="sherah-breadcrumb__list">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="active">Reports</li>
                  </ul>
                </div>
              </div>
            </div>

            {{-- Filter --}}
            <form class="d-flex gap-2 mb-3" method="GET" action="{{ route('admin.reports.index') }}">
              <input type="date" name="from" class="form-control"
                     value="{{ request('from', optional($from)->toDateString()) }}">
              <input type="date" name="to" class="form-control"
                     value="{{ request('to', optional($to)->toDateString()) }}">
              <button class="btn btn-primary">Áp dụng</button>
              @if(request()->hasAny(['from','to']))
                <a href="{{ route('admin.reports.index') }}" class="btn btn-default">Xóa lọc</a>
              @endif
            </form>

            {{-- KPI --}}
            <div class="row">
              <div class="col-md-6 col-lg-3 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Doanh thu</h4></div>
                  <div class="sherah-page-content">
                    <div class="h4 text-primary mb-0">
                      {{ number_format($totals->revenue ?? 0, 0, ',', '.') }}đ
                    </div>
                    <small class="text-muted">
                      {{ optional($from)->toDateString() }} → {{ optional($to)->toDateString() }}
                    </small>
                    @if(!is_null($revenueChangePercent))
                      <div class="small mt-1 {{ $revenueChangePercent >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $revenueChangePercent >= 0 ? '+' : '' }}{{ $revenueChangePercent }}%
                        so với kỳ trước
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-3 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Tổng đơn</h4></div>
                  <div class="sherah-page-content">
                    <a href="{{ route('admin.orders.index', request()->only(['from','to'])) }}">
                      <div class="h4 mb-0">{{ $totals->orders_count ?? 0 }}</div>
                    </a>
                    <small class="text-muted">
                      {{ optional($from)->toDateString() }} → {{ optional($to)->toDateString() }}
                    </small>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-3 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Đang giao</h4></div>
                  <div class="sherah-page-content">
                    <a href="{{ route('admin.orders.index', array_merge(request()->only(['from','to']), ['status' => 'shipping'])) }}">
                      <div class="h4 mb-0">{{ $ordersByStatus['shipping'] ?? 0 }}</div>
                    </a>
                    <small class="text-muted">Đơn hàng</small>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-3 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Chờ xử lý</h4></div>
                  <div class="sherah-page-content">
                    <a href="{{ route('admin.orders.index', array_merge(request()->only(['from','to']), ['status' => 'pending'])) }}">
                      <div class="h4 mb-0">{{ $ordersByStatus['pending'] ?? 0 }}</div>
                    </a>
                    <small class="text-muted">Đơn hàng</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- KPI row 2 --}}
            <div class="row mg-top-10">
              <div class="col-md-6 col-lg-4 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Đơn hoàn thành</h4></div>
                  <div class="sherah-page-content">
                    <a href="{{ route('admin.orders.index', array_merge(request()->only(['from','to']), ['status' => 'completed'])) }}">
                      <div class="h4 mb-0">{{ $totals->completed_orders ?? 0 }}</div>
                    </a>
                    <small class="text-muted">Trong khoảng thời gian lọc</small>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-4 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Đơn bị huỷ</h4></div>
                  <div class="sherah-page-content">
                    <a href="{{ route('admin.orders.index', array_merge(request()->only(['from','to']), ['status' => 'cancelled'])) }}">
                      <div class="h4 mb-0">{{ $totals->cancelled_orders ?? 0 }}</div>
                    </a>
                    <small class="text-muted">Bao gồm đơn khách huỷ / admin huỷ</small>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-lg-4 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Giá trị đơn trung bình</h4></div>
                  <div class="sherah-page-content">
                    <div class="h4 mb-0">
                      {{ number_format($avgOrderValue ?? 0, 0, ',', '.') }}đ
                    </div>
                    <small class="text-muted">Chỉ tính đơn hoàn thành</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- Order processing performance --}}
            @if($orderProcessingStats && $orderProcessingStats->total_orders > 0)
            <div class="row mg-top-10">
              <div class="col-lg-12">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title">
                    <h4>Hiệu suất xử lý đơn hàng</h4>
                  </div>
                  <div class="sherah-page-content">
                    <div class="row">
                      <div class="col-md-4">
                        <strong>Tổng đơn hoàn thành:</strong><br>
                        {{ $orderProcessingStats->total_orders }}
                      </div>
                      <div class="col-md-4">
                        <strong>Thời gian xử lý TB:</strong><br>
                        {{ round($orderProcessingStats->avg_hours_to_complete, 1) }} giờ
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endif

            
            {{-- Charts --}}
            <div class="row mg-top-20">
              <div class="col-lg-8 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Doanh thu theo ngày</h4></div>
                  <div class="sherah-page-content">
                    @if(!empty($chartLabels))
                      <canvas id="revChart" height="120"></canvas>
                    @else
                      <div class="text-center text-muted">Không có dữ liệu</div>
                    @endif
                  </div>
                </div>
              </div>

              <div class="col-lg-4 mg-top-10">
                <div class="sherah-page-inner sherah-border sherah-default-bg">
                  <div class="sherah-page-title"><h4>Đơn theo trạng thái</h4></div>
                  <div class="sherah-page-content">
                    <canvas id="statusChart" height="120"></canvas>
                  </div>
                </div>
              </div>
            </div>

            {{-- Revenue by payment --}}
            @if($revenueByPayment && $revenueByPayment->count())
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
              <div class="sherah-page-title"><h4>Doanh thu theo phương thức thanh toán</h4></div>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th>Phương thức</th>
                      <th class="text-end">Số đơn</th>
                      <th class="text-end">Doanh thu</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($revenueByPayment as $row)
                      <tr>
                        <td>{{ $row->payment_method }}</td>
                        <td class="text-end">{{ $row->orders_count }}</td>
                        <td class="text-end">{{ number_format($row->revenue, 0, ',', '.') }}đ</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif

            {{-- Top products --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
              <div class="sherah-page-title"><h4>Top sản phẩm bán chạy</h4></div>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Sản phẩm</th>
                      <th class="text-end">SL</th>
                      <th class="text-end">Doanh thu</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($topProducts as $i => $p)
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                          <a href="{{ route('admin.orders.index', array_merge(request()->only(['from','to']), ['product_id' => $p->id])) }}">
                            {{ $p->name }}
                          </a>
                        </td>
                        <td class="text-end">{{ $p->qty }}</td>
                        <td class="text-end">{{ number_format($p->amount, 0, ',', '.') }}đ</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            {{-- Product full report --}}
            @if($productReport && $productReport->count())
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
              <div class="sherah-page-title">
                <h4>Báo cáo sản phẩm (chi tiết)</h4>
              </div>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Sản phẩm</th>
                      <th class="text-end">SL bán</th>
                      <th class="text-end">Doanh thu</th>
                      <th class="text-end">% tổng DT</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($productReport as $i => $p)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td>{{ $p->name }}</td>
                      <td class="text-end">{{ $p->qty }}</td>
                      <td class="text-end">{{ number_format($p->revenue, 0, ',', '.') }}đ</td>
                      <td class="text-end">{{ $p->percent }}%</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif


            {{-- Low stock --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
              <div class="sherah-page-title"><h4>Sản phẩm tồn kho thấp (≤ 5)</h4></div>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Sản phẩm</th>
                      <th>SKU</th>
                      <th class="text-end">Tồn</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($lowStock as $i => $r)
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->sku }}</td>
                        <td class="text-end">{{ $r->quantity }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            {{-- Slow moving products --}}
          @if($slowMovingProducts && $slowMovingProducts->count())
          <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
            <div class="sherah-page-title">
              <h4>Sản phẩm tồn kho cao nhưng bán chậm</h4>
            </div>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>SKU</th>
                    <th class="text-end">Tồn kho</th>
                    <th class="text-end">Đã bán</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($slowMovingProducts as $i => $r)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->sku }}</td>
                    <td class="text-end">{{ $r->quantity }}</td>
                    <td class="text-end">{{ $r->sold_qty }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          @endif


          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const revCtx = document.getElementById('revChart')?.getContext('2d');
  if (revCtx) {
    new Chart(revCtx, {
      type: 'line',
      data: {
        labels: @json($chartLabels),
        datasets: [{
          label: 'Doanh thu (đ)',
          data: @json($chartData),
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      }
    });
  }

  const statusCtx = document.getElementById('statusChart')?.getContext('2d');
  if (statusCtx) {
    new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Đơn chờ xử lý', 'Đơn đang giao', 'Đơn hoàn thành', 'Đơn hủy'],
        datasets: [{
          data: [
            {{ $ordersByStatus['pending'] ?? 0 }},
            {{ $ordersByStatus['shipping'] ?? 0 }},
            {{ $ordersByStatus['completed'] ?? 0 }},
            {{ $ordersByStatus['cancelled'] ?? 0 }}
          ]
        }]
      }
    });
  }
</script>
@endsection
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

              {{-- Tabs Reports --}}
          <div class="mg-top-10 mg-bottom-10">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}"
                  href="{{ route('admin.reports.index') }}">
                  Tổng quan
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}"
                  href="{{ route('admin.reports.revenue') }}">
                  Doanh thu (Revenue)
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.topCustomers') ? 'active' : '' }}"
                  href="{{ route('admin.reports.topCustomers') }}">
                  Top khách hàng
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.topProducts') ? 'active' : '' }}"
                  href="{{ route('admin.reports.topProducts') }}">
                  Top sản phẩm
                </a>
              </li>
            </ul>
          </div>


              {{-- Bộ lọc thời gian --}}
              <form class="d-flex gap-2 mb-3" method="GET" action="{{ route('admin.reports.index') }}">
                <input type="date" name="from" class="form-control" value="{{ $from }}">
                <input type="date" name="to" class="form-control" value="{{ $to }}">
                <button class="btn btn-primary">Áp dụng</button>
                @if(request()->hasAny(['from','to']))
                  <a href="{{ route('admin.reports.index') }}" class="btn btn-default">Xóa lọc</a>
                @endif
              </form>

              {{-- Cards tổng quan --}}
              <div class="row">
                <div class="col-md-6 col-lg-3 mg-top-10">
                  <div class="sherah-page-inner sherah-border sherah-default-bg">
                    <div class="sherah-page-title"><h4>Doanh thu</h4></div>
                    <div class="sherah-page-content">
                      <div class="h4 text-primary mb-0">{{ number_format($totals->revenue ?? 0, 0, ',', '.') }}đ</div>
                      <small class="text-muted">({{ $from }} → {{ $to }})</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3 mg-top-10">
                  <div class="sherah-page-inner sherah-border sherah-default-bg">
                    <div class="sherah-page-title"><h4>Tổng đơn</h4></div>
                    <div class="sherah-page-content">
                      <div class="h4 mb-0">{{ $totals->orders_count ?? 0 }}</div>
                      <small class="text-muted">({{ $from }} → {{ $to }})</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3 mg-top-10">
                  <div class="sherah-page-inner sherah-border sherah-default-bg">
                    <div class="sherah-page-title"><h4>Đang giao</h4></div>
                    <div class="sherah-page-content">
                      <div class="h4 mb-0">{{ $ordersByStatus['shipping'] ?? 0 }}</div>
                      <small class="text-muted">Đơn hàng</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3 mg-top-10">
                  <div class="sherah-page-inner sherah-border sherah-default-bg">
                    <div class="sherah-page-title"><h4>Chờ xử lý</h4></div>
                    <div class="sherah-page-content">
                      <div class="h4 mb-0">{{ $ordersByStatus['pending'] ?? 0 }}</div>
                      <small class="text-muted">Đơn hàng</small>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Charts --}}
              <div class="row mg-top-20">
                <div class="col-lg-8 mg-top-10">
                  <div class="sherah-page-inner sherah-border sherah-default-bg">
                    <div class="sherah-page-title"><h4>Doanh thu theo ngày</h4></div>
                    <div class="sherah-page-content">
                      <canvas id="revChart" height="120"></canvas>
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

              {{-- Bảng: Top sản phẩm --}}
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
                          <td>{{ $i+1 }}</td>
                          <td>{{ $p->name }}</td>
                          <td class="text-end">{{ $p->qty }}</td>
                          <td class="text-end">{{ number_format($p->amount,0,',','.') }}đ</td>
                        </tr>
                      @empty
                        <tr><td colspan="4" class="text-center text-muted">Không có dữ liệu</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

              {{-- Bảng: Tồn kho thấp --}}
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
                          <td>{{ $i+1 }}</td>
                          <td>{{ $r->name }}</td>
                          <td>{{ $r->sku }}</td>
                          <td class="text-end">{{ $r->quantity }}</td>
                        </tr>
                      @empty
                        <tr><td colspan="4" class="text-center text-muted">Không có dữ liệu</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

              {{-- Bảng: Mã giảm giá --}}
              @if($topCoupons && $topCoupons->count())
              <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
                <div class="sherah-page-title"><h4>Mã giảm giá dùng nhiều</h4></div>
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Mã</th>
                        <th class="text-end">Lượt dùng</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($topCoupons as $i => $c)
                        <tr>
                          <td>{{ $i+1 }}</td>
                          <td>{{ $c->code }}</td>
                          <td class="text-end">{{ $c->used }}</td>
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

  {{-- Chart.js CDN (nhẹ, đủ dùng) --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const revCtx = document.getElementById('revChart')?.getContext('2d');
    if (revCtx) {
      new Chart(revCtx, {
        type: 'line',
        data: {
          labels: @json($chartLabels),
          datasets: [{ label: 'Doanh thu (đ)', data: @json($chartData), tension: 0.3 }]
        },
        options: {
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } }
        }
      });
    }

    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    if (statusCtx) {
      const data = {
        labels: ['pending','shipping','completed','cancelled'],
        datasets: [{
          data: [
            {{ $ordersByStatus['pending'] ?? 0 }},
            {{ $ordersByStatus['shipping'] ?? 0 }},
            {{ $ordersByStatus['completed'] ?? 0 }},
            {{ $ordersByStatus['cancelled'] ?? 0 }},
          ]
        }]
      };
      new Chart(statusCtx, { type: 'doughnut', data });
    }
  </script>
  @endsection

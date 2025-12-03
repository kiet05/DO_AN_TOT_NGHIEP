@extends('layouts.admin.master')
@section('title', 'Khách hàng mua nhiều nhất')

@section('content')
<section class="sherah-adashboard sherah-show">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sherah-body">
          <div class="sherah-dsinner">

            <div class="row mg-top-30">
              <div class="col-12 sherah-flex-between">
                <div class="sherah-breadcrumb">
                  <h2 class="sherah-breadcrumb__title">Top 10 khách hàng mua nhiều nhất</h2>
                </div>
              </div>
            </div>

            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
              <div class="table-responsive">
                <table class="table table-striped align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tên khách hàng</th>
                      <th>Email</th>
                      <th class="text-end">Số đơn hàng</th>
                      <th class="text-end">Tổng chi tiêu</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($customers as $i => $c)
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->email }}</td>
                        <td class="text-end">{{ $c->total_orders }}</td>
                        <td class="text-end">{{ number_format($c->total_spent, 0, ',', '.') }} đ</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

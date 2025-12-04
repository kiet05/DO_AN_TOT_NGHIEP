@extends('layouts.admin.master')
@section('title', 'Sản phẩm bán chạy')

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
                  <h2 class="sherah-breadcrumb__title">Top 10 sản phẩm bán chạy</h2>
                </div>
              </div>
            </div>

            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
              <div class="table-responsive">
                <table class="table table-striped align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tên sản phẩm</th>
                      <th class="text-end">Số lượng bán</th>
                      <th class="text-end">Doanh thu</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($products as $i => $p)
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->name }}</td>
                        <td class="text-end">{{ $p->total_sold }}</td>
                        <td class="text-end">{{ number_format($p->revenue, 0, ',', '.') }} đ</td>
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

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@extends('layouts.admin.master')

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
                  <h2 class="sherah-breadcrumb__title">Chi tiết khách hàng</h2>
                  <ul class="sherah-breadcrumb__list">
                    <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
                    <li class="active"><a href="#">Chi tiết</a></li>
                  </ul>
                </div>

                {{-- Action buttons --}}
                <div class="d-flex gap-2">
                  <a href="{{ route('admin.customers.index') }}" class="sherah-btn sherah-default">← Quay lại</a>

                  <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST"
                        onsubmit="return confirm('Xác nhận {{ $customer->status ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản?')">
                    @csrf @method('PATCH')
                    <button type="submit"
                      class="sherah-btn {{ $customer->status ? 'sherah-gbcolor2' : 'sherah-gbcolor' }}">
                      {{ $customer->status ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}
                    </button>
                  </form>
                </div>
              </div>
            </div>

            {{-- Thông tin khách hàng --}}
            <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-25 p-20">
              <div class="row g-3">
                <div class="col-md-3">
                  <p class="text-muted m-0">Tên khách hàng</p>
                  <h5 class="m-0">{{ $customer->name }}</h5>
                </div>

                <div class="col-md-3">
                  <p class="text-muted m-0">Email</p>
                  <h6 class="m-0">{{ $customer->email }}</h6>
                </div>

                <div class="col-md-3">
                  <p class="text-muted m-0">Số điện thoại</p>
                  <h6 class="m-0">{{ $customer->phone ?? '—' }}</h6>
                </div>

                <div class="col-md-3">
                  <p class="text-muted m-0">Trạng thái</p>
                  <div class="sherah-table__status {{ $customer->status ? 'sherah-color3' : 'sherah-color2' }}">
                    {{ $customer->status ? 'Hoạt động' : 'Bị khóa' }}
                  </div>
                </div>
              </div>
            </div>

            {{-- Lịch sử mua hàng --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
              <h4 class="mb-3">Lịch sử mua hàng</h4>

              <table class="sherah-table__main sherah-table__main-v3">
                <thead class="sherah-table__head">
                  <tr>
                    <th>Mã đơn</th>
                    <th>Ngày tạo</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Số sản phẩm</th>
                  </tr>
                </thead>

                <tbody class="sherah-table__body">
                  @forelse ($orders as $o)
                  <tr>
                    <td><a class="sherah-color1" href="#">#{{ $o->id }}</a></td>
                    <td>{{ optional($o->created_at)->format('d M, Y H:i') }}</td>
                    <td>{{ number_format($o->final_amount ?? 0) }} đ</td>
                    <td><div class="sherah-table__status sherah-color3">{{ $o->order_status }}</div></td>
                    <td>{{ optional($o->orderItems)->sum('quantity') ?? 0 }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="5" class="text-center">Chưa có đơn hàng</td></tr>
                  @endforelse
                </tbody>
              </table>

              {{-- Pagination orders --}}
              <div class="row mg-top-40">
                <div class="sherah-pagination">
                  <ul class="sherah-pagination__list">
                    <li class="sherah-pagination__button {{ $orders->onFirstPage() ? 'disabled' : '' }}">
                      <a href="{{ $orders->previousPageUrl() }}"><i class="fas fa-angle-left"></i></a>
                    </li>

                    @for ($i=1;$i <= $orders->lastPage();$i++)
                      <li class="{{ $orders->currentPage() == $i ? 'active' : '' }}">
                        <a href="{{ $orders->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                      </li>
                    @endfor

                    <li class="sherah-pagination__button {{ $orders->onLastPage() ? 'disabled' : '' }}">
                      <a href="{{ $orders->nextPageUrl() }}"><i class="fas fa-angle-right"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            {{-- Lịch sử hoạt động --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
              <h4 class="mb-3">Lịch sử hoạt động</h4>

              <table class="sherah-table__main sherah-table__main-v3">
                <thead class="sherah-table__head">
                  <tr>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                    <th>IP</th>
                    <th>Chi tiết</th>
                  </tr>
                </thead>
                <tbody class="sherah-table__body">
                  @forelse ($logs as $log)
                  <tr>
                    <td>{{ $log->at }}</td>
                    <td>{{ $log->data['action'] ?? '-' }}</td>
                    <td>{{ $log->data['ip'] ?? '-' }}</td>
                    <td>
                      <code style="white-space: pre-wrap; font-size: 12px;">
                        {{ json_encode($log->data['payload'] ?? [], JSON_UNESCAPED_UNICODE) }}
                      </code>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="4" class="text-center">Chưa có hoạt động nào</td></tr>
                  @endforelse
                </tbody>
              </table>

              {{-- Pagination logs --}}
              <div class="row mg-top-40">
                <div class="sherah-pagination">
                  <ul class="sherah-pagination__list">
                    <li class="{{ $logs->onFirstPage() ? 'disabled' : '' }}">
                      <a href="{{ $logs->previousPageUrl() }}"><i class="fas fa-angle-left"></i></a>
                    </li>

                    @for ($i=1;$i <= $logs->lastPage();$i++)
                      <li class="{{ $logs->currentPage() == $i ? 'active' : '' }}">
                        <a href="{{ $logs->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                      </li>
                    @endfor

                    <li class="{{ $logs->currentPage() == $logs->lastPage() ? 'disabled' : '' }}">
                      <a href="{{ $logs->nextPageUrl() }}"><i class="fas fa-angle-right"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

          </div> {{-- /inner --}}
        </div>
      </div>
    </div>
  </div>
</section>

@endsection


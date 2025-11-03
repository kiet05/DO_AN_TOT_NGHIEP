@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sherah-body">
          <div class="sherah-dsinner">

            {{-- Breadcrumb + tiêu đề --}}
            <div class="row mg-top-30">
              <div class="col-12 sherah-flex-between">
                <div class="sherah-breadcrumb">
                  <h2 class="sherah-breadcrumb__title">Lịch sử mua hàng</h2>
                  <ul class="sherah-breadcrumb__list">
                    <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
                    <li class="active"><a href="#">Lịch sử</a></li>
                  </ul>
                </div>

                {{-- Nút quay lại + khoá/mở nhanh --}}
                <div class="d-flex gap-2">
                  <a href="{{ route('admin.customers.index') }}" class="sherah-btn sherah-default">← Quay lại</a>

                  <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}"
                        method="POST"
                        onsubmit="return confirm('Xác nhận {{ $customer->status ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="sherah-btn {{ $customer->status ? 'sherah-gbcolor2' : 'sherah-gbcolor' }}">
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
                  <div class="sherah-table__product-content">
                    <p class="mb-1 text-muted">Tên khách hàng</p>
                    <h5 class="m-0">{{ $customer->name }}</h5>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="sherah-table__product-content">
                    <p class="mb-1 text-muted">Email</p>
                    <h6 class="m-0">{{ $customer->email }}</h6>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="sherah-table__product-content">
                    <p class="mb-1 text-muted">Số điện thoại</p>
                    <h6 class="m-0">{{ $customer->phone ?? '—' }}</h6>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="sherah-table__product-content">
                    <p class="mb-1 text-muted">Trạng thái</p>
                    <div class="sherah-table__status {{ $customer->status ? 'sherah-color3' : 'sherah-color2' }}">
                      {{ $customer->status ? 'Hoạt động' : 'Bị khóa' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Bảng đơn hàng --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
              <table class="sherah-table__main sherah-table__main-v3">
                <thead class="sherah-table__head">
                  <tr>
                    <th class="sherah-table__column-1 sherah-table__h1">Mã đơn</th>
                    <th class="sherah-table__column-2 sherah-table__h2">Ngày tạo</th>
                    <th class="sherah-table__column-3 sherah-table__h3">Tổng tiền</th>
                    <th class="sherah-table__column-4 sherah-table__h4">Trạng thái</th>
                    <th class="sherah-table__column-5 sherah-table__h5">Số sản phẩm</th>
                  </tr>
                </thead>

                <tbody class="sherah-table__body">
                  @forelse ($orders as $o)
                    <tr>
                      {{-- ID --}}
                      <td class="sherah-table__column-1 sherah-table__data-1">
                        <p class="crany-table__product--number">
                          <a href="#" class="sherah-color1">#{{ $o->id }}</a>
                        </p>
                      </td>

                      {{-- Ngày tạo --}}
                      <td class="sherah-table__column-2 sherah-table__data-2">
                        <div class="sherah-table__product-content">
                          <p class="sherah-table__product-desc">
                            {{ optional($o->created_at)->format('d M, Y H:i') }}
                          </p>
                        </div>
                      </td>

                      {{-- Tổng tiền --}}
                      <td class="sherah-table__column-3 sherah-table__data-3">
                        <p class="sherah-table__product-desc">
                          {{ number_format($o->final_amount ?? 0) }} đ
                        </p>
                      </td>

                      {{-- Trạng thái đơn --}}
                      <td class="sherah-table__column-4 sherah-table__data-4">
                        <div class="sherah-table__product-content">
                          <div class="sherah-table__status sherah-color3">
                            {{ $o->order_status ?? '—' }}
                          </div>
                        </div>
                      </td>

                      {{-- Số SP --}}
                      <td class="sherah-table__column-5 sherah-table__data-5">
                        <p class="sherah-table__product-desc">
                          {{ optional($o->orderItems)->sum('quantity') ?? ($o->items_count ?? 0) }}
                        </p>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center">Khách hàng chưa có đơn hàng</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>

              {{-- Phân trang giống categories --}}
              <div class="row mg-top-40">
                <div class="sherah-pagination">
                  <ul class="sherah-pagination__list">
                    <li class="sherah-pagination__button {{ $orders->onFirstPage() ? 'disabled' : '' }}">
                      <a href="{{ $orders->previousPageUrl() }}"><i class="fas fa-angle-left"></i></a>
                    </li>

                    @for ($i = 1; $i <= $orders->lastPage(); $i++)
                      <li class="{{ $orders->currentPage() == $i ? 'active' : '' }}">
                        <a href="{{ $orders->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                      </li>
                    @endfor

                    <li class="sherah-pagination__button {{ $orders->currentPage() == $orders->lastPage() ? 'disabled' : '' }}">
                      <a href="{{ $orders->nextPageUrl() }}"><i class="fas fa-angle-right"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

          </div> {{-- /sherah-dsinner --}}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

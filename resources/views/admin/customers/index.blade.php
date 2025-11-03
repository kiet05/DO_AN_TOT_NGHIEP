@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sherah-body">
          <!-- Dashboard Inner -->
          <div class="sherah-dsinner">

            {{-- Header + breadcrumb + bộ lọc --}}
            <div class="row mg-top-30">
              <div class="col-12 sherah-flex-between">
                <!-- Sherah Breadcrumb -->
                <div class="sherah-breadcrumb">
                  <h2 class="sherah-breadcrumb__title">Danh sách khách hàng</h2>
                  <ul class="sherah-breadcrumb__list">
                    <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="active"><a href="#">Khách hàng</a></li>
                  </ul>
                </div>
                <!-- End Sherah Breadcrumb -->

                {{-- Bộ lọc nhanh (search + status) --}}
                <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex gap-2">
                  <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                         placeholder="Tìm tên / email / SĐT" style="min-width: 260px">
                  <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active"  @selected(request('status')==='active')>Hoạt động</option>
                    <option value="inactive"@selected(request('status')==='inactive')>Bị khóa</option>
                  </select>
                  <button class="sherah-btn sherah-gbcolor">Lọc</button>
                </form>
              </div>
            </div>

            {{-- Bảng danh sách --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
              <table id="sherah-table__vendor" class="sherah-table__main sherah-table__main-v3">
                <!-- Head -->
                <thead class="sherah-table__head">
                  <tr>
                    <th class="sherah-table__column-1 sherah-table__h1">#</th>
                    <th class="sherah-table__column-2 sherah-table__h2">Tên khách hàng</th>
                    <th class="sherah-table__column-3 sherah-table__h3">Email</th>
                    <th class="sherah-table__column-4 sherah-table__h4">SĐT</th>
                    <th class="sherah-table__column-5 sherah-table__h5">Số đơn hàng</th>
                    <th class="sherah-table__column-6 sherah-table__h6">Trạng thái</th>
                    <th class="sherah-table__column-7 sherah-table__h7">Hành động</th>
                  </tr>
                </thead>

                <!-- Body -->
                <tbody class="sherah-table__body">
                @forelse ($customers as $idx => $u)
                  <tr>
                    {{-- STT --}}
                    <td class="sherah-table__column-1 sherah-table__data-1">
                      <p class="crany-table__product--number">
                        <a href="#" class="sherah-color1">
                          #{{ $customers->firstItem() + $idx }}
                        </a>
                      </p>
                    </td>

                    {{-- Tên --}}
                    <td class="sherah-table__column-2 sherah-table__data-2">
                      <div class="sherah-table__product-content">
                        <p class="sherah-table__product-desc">{{ $u->name }}</p>
                      </div>
                    </td>

                    {{-- Email --}}
                    <td class="sherah-table__column-3 sherah-table__data-3">
                      <p class="sherah-table__product-desc">{{ $u->email }}</p>
                    </td>

                    {{-- SĐT --}}
                    <td class="sherah-table__column-4 sherah-table__data-4">
                      <p class="sherah-table__product-desc">{{ $u->phone ?? '—' }}</p>
                    </td>

                    {{-- Số đơn --}}
                    <td class="sherah-table__column-5 sherah-table__data-5">
                      <p class="sherah-table__product-desc">{{ $u->orders_count }}</p>
                    </td>

                    {{-- Trạng thái --}}
                    <td class="sherah-table__column-6 sherah-table__data-6">
                      <div class="sherah-table__product-content">
                        <div class="sherah-table__status {{ $u->status ? 'sherah-color3' : 'sherah-color2' }}">
                          {{ $u->status ? 'Hoạt động' : 'Bị khóa' }}
                        </div>
                      </div>
                    </td>

                    {{-- Hành động --}}
                    <td class="sherah-table__column-7 sherah-table__data-7">
                      <div class="sherah-table__status__group d-flex gap-1">
                        <a href="{{ route('admin.customers.show', $u->id) }}"
                           class="sherah-table__action"
                           style="background-color:#0ea5e9;color:#fff;border-radius:4px;padding:4px 8px;">
                          Lịch sử
                        </a>

                        <form action="{{ route('admin.customers.toggleStatus', $u->id) }}"
                              method="POST" onsubmit="return confirm('Xác nhận {{ $u->status ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản?')">
                          @csrf @method('PATCH')
                          <button type="submit" class="sherah-table__action"
                                  style="background-color:{{ $u->status ? '#f59e0b' : '#10b981' }};color:#fff;border-radius:4px;padding:4px 8px;">
                            {{ $u->status ? 'Khóa' : 'Mở khóa' }}
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">Không có khách hàng nào</td>
                  </tr>
                @endforelse
                </tbody>
              </table>

              {{-- Pagination giống categories --}}
              <div class="row mg-top-40">
                <div class="sherah-pagination">
                  <ul class="sherah-pagination__list">
                    <li class="sherah-pagination__button {{ $customers->onFirstPage() ? 'disabled' : '' }}">
                      <a href="{{ $customers->previousPageUrl() }}"><i class="fas fa-angle-left"></i></a>
                    </li>

                    @for ($i = 1; $i <= $customers->lastPage(); $i++)
                      <li class="{{ $customers->currentPage() == $i ? 'active' : '' }}">
                        <a href="{{ $customers->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                      </li>
                    @endfor

                    <li class="sherah-pagination__button {{ $customers->currentPage() == $customers->lastPage() ? 'disabled' : '' }}">
                      <a href="{{ $customers->nextPageUrl() }}"><i class="fas fa-angle-right"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

          </div>
          <!-- End Dashboard Inner -->
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

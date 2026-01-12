
@extends('layouts.admin.master')

@section('content')
<section class="sherah-adashboard sherah-show">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sherah-body">
          <div class="sherah-dsinner">

            {{-- Header + breadcrumb + nút thêm --}}
            <div class="row mg-top-30">
              <div class="col-12 sherah-flex-between">

                <div class="sherah-breadcrumb">
                  <h2 class="sherah-breadcrumb__title">Danh sách khách hàng</h2>
                  <ul class="sherah-breadcrumb__list">
                    <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="active"><a href="#">Khách hàng</a></li>
                  </ul>
                </div>

                <div class="d-flex gap-2">
                  {{-- Ô tìm kiếm --}}
                  <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Tìm tên / email / SĐT" style="min-width: 240px">

                    <select name="status" class="form-select">
                      <option value="">Tất cả</option>
                      <option value="active" @selected(request('status')=='active')>Hoạt động</option>
                      <option value="inactive" @selected(request('status')=='inactive')>Bị khóa</option>
                    </select>

                    <button class="sherah-btn sherah-gbcolor">Lọc</button>
                  </form>

                  {{-- nút thêm mới --}}
                  {{-- <a href="{{ route('admin.customers.create') }}" class="sherah-btn sherah-gbcolor">
                    + Thêm khách hàng
                  </a> --}}
                </div>
              </div>
            </div>

            {{-- Bảng danh sách --}}
            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
              <table class="sherah-table__main sherah-table__main-v3">
                <thead class="sherah-table__head">
                  <tr>
                    <th>#</th>
                    <th>Tên khách hàng</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Số đơn hàng</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Hành động</th>
                  </tr>
                </thead>

                <tbody class="sherah-table__body">
                @forelse ($customers as $index => $u)
                  <tr>
                    <td>{{ $customers->firstItem() + $index }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone ?? '—' }}</td>
                    <td>{{ $u->orders_count }}</td>

                    <td>
                      <span class="sherah-table__status {{ $u->status ? 'sherah-color3' : 'sherah-color2' }}">
                        {{ $u->status ? 'Hoạt động' : 'Bị khóa' }}
                      </span>
                    </td>

                    <td class="text-center">
                      <div class="d-flex gap-1 justify-content-center">

                        {{-- LỊCH SỬ HOẠT ĐỘNG --}}
                        <form action="{{ route('admin.customers.show', $u->id) }}" method="GET" class="mb-0">
                            <button type="submit"
                                    class="sherah-table__action"
                                    style="white-space: nowrap;background:#3b82f6;color:#fff;padding:4px 10px;border-radius:4px;">
                                Lịch sử
                            </button>
                        </form>


                        {{-- SỬA --}}
                        {{-- <form action="{{ route('admin.customers.edit', $u->id) }}" method="GET" class="mb-0">
                            <button type="submit"
                                    class="sherah-table__action"
                                    style="background:#10b981;color:#fff;padding:4px 10px;border-radius:4px;">
                                Sửa
                            </button>
                        </form> --}}


                        {{-- KHÓA/MỞ --}}
                        <form method="POST"
                              action="{{ route('admin.customers.toggleStatus', $u->id) }}"
                              onsubmit="return confirm('Xác nhận {{ $u->status ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản?')">
                          @csrf @method('PATCH')
                          <button type="submit"
                                  class="sherah-table__action"
                                  style="background:{{ $u->status ? '#f59e0b' : '#06b6d4' }};color:#fff;padding:4px 10px;border-radius:4px;">
                            {{ $u->status ? 'Khóa' : 'Mở' }}
                          </button>
                        </form>

                        {{-- XOÁ --}}
                        {{-- <form method="POST"
                              action="{{ route('admin.customers.destroy', $u->id) }}"
                              onsubmit="return confirm('Xác nhận xoá khách hàng này?')">
                          @csrf @method('DELETE')
                          <button type="submit"
                                  class="sherah-table__action"
                                  style="background:#ef4444;color:#fff;padding:4px 10px;border-radius:4px;">
                            Xoá
                          </button>
                        </form> --}}

                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-center">Không có khách hàng nào</td></tr>
                @endforelse
                </tbody>
              </table>

              {{-- Pagination --}}
              <div class="row mg-top-40">
                <div class="sherah-pagination">
                  <ul class="sherah-pagination__list">
                    <li class="{{ $customers->onFirstPage() ? 'disabled' : '' }}">
                      <a href="{{ $customers->previousPageUrl() }}"><i class="fas fa-angle-left"></i></a>
                    </li>

                    @for ($i = 1; $i <= $customers->lastPage(); $i++)
                      <li class="{{ $customers->currentPage() == $i ? 'active' : '' }}">
                        <a href="{{ $customers->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                      </li>
                    @endfor

                    <li class="{{ $customers->currentPage() == $customers->lastPage() ? 'disabled' : '' }}">
                      <a href="{{ $customers->nextPageUrl() }}"><i class="fas fa-angle-right"></i></a>
                    </li>

                  </ul>
                </div>
              </div>

            </div> <!-- end table -->

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@extends('layouts.admin.master')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<section class="sherah-adashboard sherah-show">
  <div class="container">
    <div class="row">
      <div class="col-12">

        {{-- Header + actions --}}
        <div class="sherah-flex-between mg-top-20 mg-bottom-10">
          <div class="sherah-breadcrumb">
            <h2 class="sherah-breadcrumb__title">Đơn hàng {{ $order->code ?? ('#'.str_pad($order->id,5,'0',STR_PAD_LEFT)) }}</h2>
            <ul class="sherah-breadcrumb__list">
              <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
              <li><a href="{{ route('admin.orders.index') }}">Orders</a></li>
              <li class="active">Chi tiết</li>
            </ul>
          </div>

          <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="sherah-btn sherah-light">← Về danh sách</a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="sherah-btn sherah-border">
              <i class="fa fa-file-text-o me-1"></i> Hóa đơn
            </a>
            <a href="{{ route('admin.orders.invoice.pdf', $order->id) }}" class="sherah-btn sherah-gbcolor">
              <i class="fa fa-download me-1"></i> PDF
            </a>
            <button class="sherah-btn sherah-color" onclick="window.print()">
              <i class="fa fa-print me-1"></i> In
            </button>
          </div>
        </div>

        {{-- Info cards --}}
        <div class="row">
          <div class="col-lg-6">
            <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-15">
              <div class="sherah-page-title"><h4>Thông tin khách hàng</h4></div>
              <div class="sherah-page-content">
                <p><strong>Họ tên:</strong> {{ $order->receiver_name }}</p>
                <p><strong>Điện thoại:</strong> {{ $order->receiver_phone }}</p>
                <p class="mb-0"><strong>Địa chỉ:</strong> {{ $order->receiver_address }}</p>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-15">
              <div class="sherah-page-title"><h4>Thông tin đơn hàng</h4></div>
              <div class="sherah-page-content">
                @php
                  $statusMap = ['pending'=>'label-default','shipping'=>'label-info','completed'=>'label-success','cancelled'=>'label-danger'];
                  $statusCls = $statusMap[$order->order_status] ?? 'label-default';
                  $payCls    = ($order->payment_status === 'paid') ? 'label-success' : 'label-danger';
                @endphp
                <p><strong>Trạng thái:</strong> <span class="label {{ $statusCls }}">{{ $order->order_status }}</span></p>
                <p><strong>Thanh toán:</strong> <span class="label {{ $payCls }}">{{ $order->payment_status }}</span></p>
                <p><strong>Phí ship:</strong> {{ number_format($order->shipping_fee,0,',','.') }}đ</p>
                <p class="mb-2"><strong>Tổng tiền:</strong> <span class="fw-bold text-primary">{{ number_format($order->final_amount,0,',','.') }}đ</span></p>

                {{-- Cập nhật trạng thái nhanh --}}
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-flex gap-2">
                  @csrf
                  <select name="status" class="form-select" style="max-width:220px">
                    <option value="pending"   @selected($order->order_status=='pending')>Chờ xử lý</option>
                    <option value="shipping"  @selected($order->order_status=='shipping')>Đang giao</option>
                    <option value="completed" @selected($order->order_status=='completed')>Đã giao</option>
                    <option value="cancelled" @selected($order->order_status=='cancelled')>Đã hủy</option>
                  </select>
                  <button class="sherah-btn sherah-border">Cập nhật</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- Items table --}}
        <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-20">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th width="64">Ảnh</th>
                  <th>Sản phẩm</th>
                  <th>SKU/Variant</th>
                  <th class="text-end">Giá</th>
                  <th class="text-end">SL</th>
                  <th class="text-end">Thành tiền</th>
                </tr>
              </thead>
              <tbody>
                @php $subTotal = 0; @endphp
                @forelse(($order->items ?? []) as $it)
                  @php
                    $price = (float)($it->price ?? 0);
                    $qty   = (int)($it->quantity ?? 0);
                    $line  = $price * $qty;   $subTotal += $line;
                    $product = $it->product ?? null;
                    $img = $product && $product->image_main ? asset($product->image_main) : 'https://placehold.co/300x300?text=IMG';
                  @endphp
                  <tr>
                    <td><img src="{{ $img }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px"></td>
                    <td>
                      <div class="fw-bold">{{ $product->name ?? 'Sản phẩm đã xoá' }}</div>
                      @if(!empty($it->options)) <small class="text-muted">{{ $it->options }}</small> @endif
                    </td>
                    <td>{{ $it->sku ?? ($product->sku ?? '—') }}</td>
                    <td class="text-end">{{ number_format($price,0,',','.') }}đ</td>
                    <td class="text-end">{{ $qty }}</td>
                    <td class="text-end">{{ number_format($line,0,',','.') }}đ</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted">Đơn hàng không có sản phẩm.</td></tr>
                @endforelse
              </tbody>

              @if(($order->items ?? null) && count($order->items))
                <tfoot>
                  <tr>
                    <th colspan="5" class="text-end">Tạm tính</th>
                    <th class="text-end">{{ number_format($subTotal,0,',','.') }}đ</th>
                  </tr>
                  <tr>
                    <th colspan="5" class="text-end">Phí ship</th>
                    <th class="text-end">{{ number_format($order->shipping_fee,0,',','.') }}đ</th>
                  </tr>
                  <tr>
                    <th colspan="5" class="text-end">Tổng thanh toán</th>
                    <th class="text-end text-primary">{{ number_format($order->final_amount,0,',','.') }}đ</th>
                  </tr>
                </tfoot>
              @endif
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection

@push('styles')
<style>
  @media print {
    .sherah-sidebar, .sherah-header, .sherah-btn, .sherah-breadcrumb, .sherah-footer { display:none !important; }
    .sherah-page-inner, .sherah-table { border:0 !important; box-shadow:none !important; }
    body { background:#fff !important; }
  }
</style>
@endpush
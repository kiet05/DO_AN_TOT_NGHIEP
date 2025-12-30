@extends('layouts.admin.master')

@section('title', 'Yêu cầu hoàn hàng #' . $ret->id)

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
                                        <h2 class="sherah-breadcrumb__title">
                                            Yêu cầu #{{ $ret->id }} - Đơn #{{ $ret->order->id ?? 'N/A' }}
                                        </h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.returns.index') }}">Yêu cầu hoàn
                                                    hàng</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="sherah-default-bg sherah-border mg-top-30">
                                <div class="row">
                                    {{-- CỘT TRÁI: THÔNG TIN YÊU CẦU + SẢN PHẨM --}}
                                    <div class="col-lg-8 col-12 p-4">

                                        {{-- Thông tin khách & lý do --}}
                                        <div class="mb-3">
                                            <p class="mb-1">
                                                <strong>Khách:</strong>
                                                {{ $ret->user->name ?? 'N/A' }}
                                                - {{ $ret->user->email ?? '' }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Đơn hàng:</strong>
                                                @if ($ret->order)
                                                    #{{ $ret->order->id }}
                                                    - {{ $ret->order->receiver_name }} |
                                                    {{ $ret->order->receiver_phone }} |
                                                    {{ $ret->order->receiver_address }}
                                                @else
                                                    Không tìm thấy đơn hàng
                                                @endif
                                            </p>
                                            <p class="mb-1">
                                                <strong>Hình thức yêu cầu:</strong>
                                                @php
                                                    $actionLabel =
                                                        [
                                                            'refund_full' => 'Hoàn tiền toàn bộ đơn hàng',
                                                            'refund_partial' => 'Hoàn tiền một phần (một vài sản phẩm)',
                                                            'exchange_product' => 'Đổi sang sản phẩm khác',
                                                            'exchange_variant' => 'Đổi size / màu',
                                                        ][$ret->action_type] ?? $ret->action_type;
                                                @endphp
                                                {{ $actionLabel }}
                                            </p>
                                            <p class="mt-2">
                                                <strong>Lý do:</strong><br>
                                                {!! nl2br(e($ret->reason ?? ($ret->order->return_reason ?? '(Không có)'))) !!}
                                            </p>
                                        </div>

                                        {{-- Ảnh minh chứng --}}
                                        <div class="mb-4">
                                            <h5 class="mb-2">Ảnh minh chứng</h5>

                                            @php
                                                $proof = $ret->proof_image ?: $ret->order->return_image_path ?? null;
                                            @endphp

                                            @if ($proof)
                                                <a href="{{ asset('storage/' . $proof) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary mb-2">
                                                    Xem ảnh gốc
                                                </a>

                                                <div
                                                    style="max-width:260px; border:1px solid #eee; padding:6px; border-radius:8px;">
                                                    <img src="{{ asset('storage/' . $proof) }}" alt="Ảnh minh chứng"
                                                        style="width:100%; object-fit:contain;">
                                                </div>
                                            @else
                                                <p class="text-muted">Chưa có ảnh minh chứng.</p>
                                            @endif

                                            @if (is_array($ret->evidence_urls))
                                                <div class="mt-2 d-flex flex-wrap gap-2">
                                                    @foreach ($ret->evidence_urls as $url)
                                                        <a href="{{ $url }}" target="_blank"
                                                            class="badge bg-light text-primary border">
                                                            Link bổ sung
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Sản phẩm trong yêu cầu --}}
                                        <div class="mt-4">
                                            <h5 class="mb-2">Sản phẩm liên quan</h5>

                                            <div class="sherah-table p-0">
                                                <table class="product-overview-table">
                                                    <thead>
                                                        <tr style="text-center;">
                                                            <th style="width: 70px; text-align: center;">Ảnh</th>
                                                            <th style="width: 200px; text-align: center;">Sản phẩm</th>
                                                            <th style="width: 120px; text-align: center;">Thuộc tính</th>
                                                            <th style="width: 90px; text-align: center;">SL hóa đơn</th>
                                                            <th style="width: 90px; text-align: center;">SL yêu cầu hoàn
                                                            </th>
                                                            <th style="width: 140px; text-align: center;">Đơn giá (sau giảm)
                                                            </th>
                                                            <th style="width: 140px; text-align: center;">Tiền hoàn (dòng)
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            // Tổng gốc của order (dùng để phân bổ voucher nếu voucher áp dụng toàn đơn)
                                                            $order = $ret->order;
                                                            $order_original_total = 0;
                                                            $order_final_total = 0;
                                                            if ($order && $order->orderItems) {
                                                                foreach ($order->orderItems as $oi) {
                                                                    $order_original_total +=
                                                                        ($oi->price ?? 0) * ($oi->quantity ?? 0);
                                                                    // try several possible fields for final line total
                                                                    $order_final_total +=
                                                                        $oi->final_amount ??
                                                                        ($oi->total_price ??
                                                                            ($oi->price ?? 0) * ($oi->quantity ?? 0));
                                                                }
                                                            }

                                                            $order_discount_total = max(
                                                                0,
                                                                $order_original_total - $order_final_total,
                                                            );
                                                            $total_refund_sum = 0;
                                                        @endphp

                                                        @forelse ($ret->items as $item)
                                                            @php
                                                                $orderItem = $item->orderItem;
                                                                $product = $orderItem?->product;
                                                                $variant = $orderItem?->productVariant;

                                                                // bảo vệ null
                                                                $oi_price = $orderItem->price ?? 0;
                                                                $oi_qty = $orderItem->quantity ?? 1;

                                                                // line original total (giá gốc * qty)
                                                                $line_original_total = $oi_price * $oi_qty;

                                                                // Nếu orderItem đã lưu giá sau giảm (final_amount or final_price or total_price)
                                                                $line_final_total_raw =
                                                                    $orderItem->final_amount ??
                                                                    ($orderItem->final_price ??
                                                                        ($orderItem->total_price ?? null));

                                                                if ($line_final_total_raw !== null) {
                                                                    // final total cho toàn dòng đã có sẵn
                                                                    $line_final_total = (float) $line_final_total_raw;
                                                                } else {
                                                                    // chia voucher theo tỉ lệ nếu order có giảm chung
                                                                    if (
                                                                        $order_original_total > 0 &&
                                                                        $order_discount_total > 0
                                                                    ) {
                                                                        $proportional_discount =
                                                                            ($line_original_total /
                                                                                max(1, $order_original_total)) *
                                                                            $order_discount_total;
                                                                    } else {
                                                                        $proportional_discount = 0;
                                                                    }
                                                                    $line_final_total =
                                                                        $line_original_total - $proportional_discount;
                                                                }

                                                                // đơn giá sau giảm (cho 1 sản phẩm)
                                                                $unit_price_after =
                                                                    $oi_qty > 0 ? $line_final_total / $oi_qty : 0;

                                                                // số lượng khách yêu cầu hoàn (trong return_item)
                                                                $requested_qty = $item->quantity ?? 0;

                                                                // tổng tiền hoàn cho dòng này = đơn giá sau giảm * số lượng trả
                                                                $refund_total_line = $unit_price_after * $requested_qty;

                                                                // cộng vào tổng trả chung
                                                                $total_refund_sum += $refund_total_line;
                                                            @endphp

                                                            <tr>
                                                                <td>
                                                                    @if ($variant?->image_url)
                                                                        <img src="{{ asset('storage/' . $variant->image_url) }}"
                                                                            style="width:50px;height:50px;object-fit:cover;">
                                                                    @elseif ($product?->image_main)
                                                                        <img src="{{ asset('storage/' . $product->image_main) }}"
                                                                            style="width:50px;height:50px;object-fit:cover;">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $product?->name ?? 'Sản phẩm #' . ($orderItem->product_id ?? '') }}
                                                                    @if ($variant)
                                                                        <div class="text-muted small">
                                                                            {{ $variant->sku ?? '' }}
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($variant)
                                                                        {{ $variant->attribute_summary ?? 'N/A' }}
                                                                    @else
                                                                        <span class="text-muted">- Không có -</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    {{ $oi_qty }}
                                                                </td>
                                                                <td class="text-center">
                                                                    {{ $requested_qty }}
                                                                </td>
                                                                <td style="text-align: right;">
                                                                    {{-- Hiện đơn giá SAU khi đã chia voucher --}}
                                                                    <strong>{{ number_format($unit_price_after, 0, ',', '.') }}
                                                                        đ</strong>
                                                                    <div class="text-muted small">(x{{ $oi_qty }}
                                                                        tổng dòng)</div>
                                                                </td>
                                                                <td style="text-align: right;">
                                                                    <strong>{{ number_format($refund_total_line, 0, ',', '.') }}
                                                                        đ</strong>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-muted text-center">
                                                                    Không có dòng sản phẩm nào trong yêu cầu hoàn hàng.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="6" style="text-align:right;"><strong>Tổng tiền
                                                                    hoàn (tạm tính):</strong></td>
                                                            <td style="text-align:right;">
                                                                <strong>{{ number_format($total_refund_sum, 0, ',', '.') }}
                                                                    đ</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- CỘT PHẢI: XỬ LÝ & THÔNG TIN HOÀN TIỀN --}}
                                    <div class="col-lg-4 col-12 sherah-border-left p-4">

                                        {{-- FORM XỬ LÝ (Duyệt / Từ chối) --}}
                                        <div class="mb-4">
                                            <h5 class="mb-3">Xử lý</h5>

                                            <form action="{{ route('admin.returns.approve', $ret->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="form-label">Số tiền hoàn</label>
                                                    <input type="number" step="1000" min="0" name="refund_amount"
                                                        value="{{ old('refund_amount', $ret->refund_amount ?? round($total_refund_sum)) }}"
                                                        class="form-control">
                                                    <small class="text-muted">Gợi ý: tổng tiền hoàn tạm tính đã hiện ở dưới
                                                        bảng.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Phương thức</label>
                                                    <select name="refund_method" class="form-select">
                                                        <option value="manual"
                                                            {{ old('refund_method', $ret->refund_method) === 'manual' ? 'selected' : '' }}>
                                                            Hoàn thủ công (chuyển khoản / tiền mặt)
                                                        </option>
                                                        <option value="wallet"
                                                            {{ old('refund_method', $ret->refund_method) === 'wallet' ? 'selected' : '' }}>
                                                            Hoàn vào ví (nội bộ)
                                                        </option>
                                                    </select>
                                                </div>

                                                @if ($ret->status === \App\Models\ReturnModel::PENDING)
                                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                                        Duyệt
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-secondary w-100 mb-2" disabled>
                                                        Đã xử lý
                                                    </button>
                                                @endif
                                            </form>

                                            <form action="{{ route('admin.returns.reject', $ret->id) }}" method="POST"
                                                class="mt-2">
                                                @csrf
                                                @if ($ret->status === \App\Models\ReturnModel::PENDING)
                                                    <button type="submit" class="btn btn-outline-danger w-100">
                                                        Từ chối
                                                    </button>
                                                @endif
                                            </form>
                                        </div>

                                        {{-- THÔNG TIN HOÀN TIỀN --}}
                                        <div class="sherah-default-bg sherah-border p-3 mb-3">
                                            <h6 class="mb-3">Thông tin hoàn tiền</h6>

                                            <p class="mb-1">
                                                <strong>Số tiền hoàn (gợi ý):</strong>
                                                {{ number_format($total_refund_sum, 0, ',', '.') }} đ
                                            </p>

                                            <p class="mb-1">
                                                <strong>Phương thức:</strong>
                                                @php
                                                    $methodLabel = match ($ret->refund_method) {
                                                        'wallet' => 'Hoàn về ví',
                                                        'manual' => 'Hoàn thủ công',
                                                        default => $ret->refund_method ? $ret->refund_method : '-',
                                                    };
                                                @endphp
                                                {{ $methodLabel }}
                                            </p>
{{-- Số tài khoản nhận tiền hoàn --}}
                                            <p class="mb-1">
                                                <strong>Số tài khoản nhận tiền hoàn:</strong>
                                                {{ $ret->refund_account_number ?? '-' }}
   

                                            <p class="mb-1">
                                                <strong>Người duyệt:</strong>
                                                {{ $ret->approver->name ?? '-' }}
                                            </p>

                                            <p class="mb-1">
                                                <strong>Thời điểm:</strong>
                                                {{ $ret->decided_at ? $ret->decided_at->format('d/m/Y H:i') : '-' }}
                                            </p>

                                            <p class="mb-0">
                                                <strong>Trạng thái yêu cầu:</strong>
                                                @php
                                                    $statusText =
                                                        [
                                                            0 => 'Chờ xử lý',
                                                            1 => 'Đã duyệt',
                                                            2 => 'Đã từ chối',
                                                            3 => 'Đang hoàn tiền',
                                                            4 => 'Hoàn tất',
                                                            5 => 'Chờ khách xác nhận',
                                                        ][$ret->status] ?? $ret->status;
                                                @endphp
                                                {{ $statusText }}
                                            </p>
                                        </div>

                                        {{-- THÔNG TIN ĐƠN HÀNG --}}
                                        @if ($ret->order)
                                            <div class="sherah-default-bg sherah-border p-3">
                                                <h6 class="mb-3">Thông tin đơn hàng</h6>
                                                <p class="mb-1">
                                                    <strong>Mã đơn:</strong> #{{ $ret->order->id }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Ngày đặt:</strong>
                                                    {{ $ret->order->created_at?->format('d/m/Y H:i') }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Giá trị đơn (sau giảm):</strong>
                                                    {{ number_format($ret->order->final_amount ?? ($order_final_total ?? 0), 0, ',', '.') }}₫
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Trạng thái đơn:</strong>
                                                    {{ $ret->order->status_label ?? $ret->order->order_status }}
                                                </p>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        </div> {{-- /.sherah-dsinner --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

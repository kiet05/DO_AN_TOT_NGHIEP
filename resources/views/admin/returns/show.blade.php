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
                                    <div class="col-lg-8 col-12 p-4">

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
                                                            $order = $ret->order;
                                                            $order_original_total = 0;
                                                            $order_final_total = 0;

                                                            if ($order && $order->orderItems) {
                                                                foreach ($order->orderItems as $oi) {
                                                                    $order_original_total +=
                                                                        ($oi->price ?? 0) * ($oi->quantity ?? 0);
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

                                                                $oi_price = $orderItem->price ?? 0;
                                                                $oi_qty = $orderItem->quantity ?? 1;

                                                                $line_original_total = $oi_price * $oi_qty;

                                                                $line_final_total_raw =
                                                                    $orderItem->final_amount ??
                                                                    ($orderItem->final_price ??
                                                                        ($orderItem->total_price ?? null));

                                                                if ($line_final_total_raw !== null) {
                                                                    $line_final_total = (float) $line_final_total_raw;
                                                                } else {
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

                                                                $unit_price_after =
                                                                    $oi_qty > 0 ? $line_final_total / $oi_qty : 0;

                                                                $requested_qty = $item->quantity ?? 0;

                                                                $refund_total_line = $unit_price_after * $requested_qty;

                                                                $total_refund_sum += $refund_total_line;
                                                            @endphp

                                                            <tr>
                                                                <td style="text-align:center;">
                                                                    <div class="return-product-thumb">
                                                                        @if ($variant?->image_url)
                                                                            <img
                                                                                src="{{ asset('storage/' . $variant->image_url) }}">
                                                                        @elseif ($product?->image_main)
                                                                            <img
                                                                                src="{{ asset('storage/' . $product->image_main) }}">
                                                                        @else
                                                                            <span class="text-muted small">No image</span>
                                                                        @endif
                                                                    </div>
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
                                                                    <strong>{{ number_format($unit_price_after, 0, ',', '.') }}
                                                                        đ</strong>
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
                                                                    đ</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-12 sherah-border-left p-4">

                                        <div class="mb-4">
                                            <h5 class="mb-3">Xử lý</h5>

                                            <form action="{{ route('admin.returns.approve', $ret->id) }}" method="POST">
                                                @csrf

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
                                                        Duyệt (tự tính tiền hoàn)
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

                                            @if ($ret->status === \App\Models\ReturnModel::APPROVED)
                                                <form action="{{ route('admin.returns.refundAuto', $ret->id) }}"
                                                    method="POST" class="mt-2">
                                                    @csrf
                                                    <button class="btn btn-success w-100">Hoàn tiền vào ví</button>
                                                </form>

                                                <form action="{{ route('admin.returns.refundManual', $ret->id) }}"
                                                    method="POST" enctype="multipart/form-data" class="mt-2">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label small">
                                                            Ảnh chứng minh đã hoàn tiền
                                                        </label>
                                                        <input type="file" name="refund_proof_image" class="form-control"
                                                            accept="image/*">
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label small">
                                                            Người duyệt
                                                        </label>
                                                        <input type="text" name="approved_by_name"
                                                            class="form-control" placeholder="Nhập tên người duyệt"
                                                            value="{{ old('approved_by_name', auth()->user()->name ?? '') }}">
                                                    </div>

                                                    <button class="btn btn-warning w-100">
                                                        Xác nhận đã hoàn thủ công
                                                    </button>

                                                    {{-- <button class="btn btn-warning w-100">Đánh dấu đã hoàn thủ công</button> --}}
                                                </form>
                                            @endif
                                        </div>

                                        <div class="sherah-default-bg sherah-border p-3 mb-3">
                                            <h6 class="mb-3">Thông tin hoàn tiền</h6>

                                            <p class="mb-1">
                                                <strong>Số tiền hoàn (tạm tính):</strong>
                                                {{ number_format($total_refund_sum, 0, ',', '.') }} đ
                                            </p>

                                            <p class="mb-1">
                                                <strong>Số tiền hoàn (đã lưu):</strong>
                                                {{ number_format($ret->refund_amount ?? 0, 0, ',', '.') }} đ
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
                                            @if ($ret->refund_method === 'manual')
                                                <p class="mb-1">
                                                    <strong>Số tài khoản hoàn tiền:</strong>
                                                    @if ($ret->refund_account_number)
                                                        <span class="text-dark">
                                                            {{ $ret->refund_account_number }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Khách chưa cung cấp</span>
                                                    @endif
                                                </p>
                                            @endif

                                            @if ($ret->refund_proof_image)
                                                <p class="mb-1">
                                                    <strong>Ảnh chứng minh hoàn tiền:</strong>
                                                </p>

                                                <a href="{{ asset('storage/' . $ret->refund_proof_image) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                                                    Xem ảnh gốc
                                                </a>

                                                <div
                                                    style="max-width:240px;
                border:1px solid #eee;
                padding:6px;
                border-radius:8px;">
                                                    <img src="{{ asset('storage/' . $ret->refund_proof_image) }}"
                                                        style="width:100%; object-fit:contain;">
                                                </div>
                                            @endif

                                            <p class="mb-1">
                                                <strong>Người duyệt:</strong>
                                                {{ $ret->approved_by_name ?? '-' }}
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
                                                    {{ number_format($ret->order->final_amount ?? 0, 0, ',', '.') }}₫
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .return-product-thumb {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .return-product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* ❗ quan trọng */
        }
    </style>

@endsection

{{-- resources/views/frontend/order/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    @php
        // Map trạng thái => label + màu badge
        $statusMeta = [
            'pending' => ['label' => 'Chờ xử lý', 'badge' => 'bg-secondary'],
            'confirmed' => ['label' => 'Đã xác nhận', 'badge' => 'bg-info'],
            'processing' => ['label' => 'Đang chuẩn bị', 'badge' => 'bg-info'],
            'shipping' => ['label' => 'Đang giao', 'badge' => 'bg-primary'],
            'shipped' => ['label' => 'Đã giao', 'badge' => 'bg-success'],
            'return_pending' => ['label' => 'Đang chờ hoàn', 'badge' => 'bg-warning text-dark'],
            'returned' => ['label' => 'Hoàn hàng', 'badge' => 'bg-success'],
            'cancelled' => ['label' => 'Đã hủy', 'badge' => 'bg-dark'],
        ];

        $currentStatus = $order->order_status;
        $currentStatusMeta = $statusMeta[$currentStatus] ?? [
            'label' => ucfirst($currentStatus),
            'badge' => 'bg-secondary',
        ];

        // Thứ tự các cột trạng thái trong dòng flow (giống admin)
        $statusRow = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Xác nhận',
            'processing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao',
            'shipped' => 'Đã giao',
            'return_pending' => 'Đang chờ hoàn',
            'returned' => 'Hoàn hàng',
            'cancelled' => 'Đã hủy',
        ];

        $statusOrder = array_keys($statusRow);
        $statusIndex = array_flip($statusOrder);
        $currentIndex = $statusIndex[$currentStatus] ?? null;

        // Map thời gian theo status từ lịch sử
        $historyByStatus = [];
        if (!empty($order->statusHistories)) {
            foreach ($order->statusHistories as $history) {
                $st = $history->status;
                if (!isset($historyByStatus[$st]) || $history->created_at < $historyByStatus[$st]->created_at) {
                    $historyByStatus[$st] = $history;
                }
            }
        }

        // Địa chỉ nhận
        $fullAddress = trim(
            ($order->receiver_address ?? '') .
                ', ' .
                ($order->receiver_ward ?? '') .
                ', ' .
                ($order->receiver_district ?? '') .
                ', ' .
                ($order->receiver_province ?? ''),
            ' ,',
        );

        // Tiền
        $subtotal = $order->subtotal ?? $order->items->sum(fn($i) => (float) $i->price * (int) $i->quantity);
        $shippingFee = $order->shipping_fee ?? 0;
        $discountTotal = $order->discount_total ?? 0;

        $grandTotal = $subtotal + $shippingFee - $discountTotal;
    @endphp

    <div class="order-detail-wrapper">
        <div class="container">

            {{-- HEADER --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h1 class="h4 mb-1">
                        Đơn hàng
                        {{ $order->code ?? 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </h1>
                    <div class="small text-muted">
                        Đặt lúc
                        <strong>{{ $order->created_at?->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>

                <div class="text-end">
                    <span class="badge {{ $currentStatusMeta['badge'] }} px-3 py-2 mb-1">
                        {{ $currentStatusMeta['label'] }}
                    </span>
                    <div class="small text-muted">
                        Mã đơn:
                        <strong>{{ $order->code ?? 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                </div>
            </div>

            {{-- DÒNG TRẠNG THÁI GIỐNG ADMIN --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Trạng thái đơn hàng</h6>
                        <span class="small text-muted">
                            Cập nhật gần nhất:
                            <strong>{{ $order->updated_at?->format('d/m/Y H:i') }}</strong>
                        </span>
                    </div>

                    <div class="status-flow-grid">
                        @foreach ($statusRow as $key => $label)
                            @php
                                $idx = $statusIndex[$key];
                                $isDone = $currentIndex !== null && $idx <= $currentIndex;
                                $isCurrent = $currentStatus === $key;
                                $history = $historyByStatus[$key] ?? null;
                            @endphp
                            <div
                                class="status-flow-cell
                                    {{ $isDone ? 'status-flow-cell-done' : '' }}
                                    {{ $isCurrent ? 'status-flow-cell-current' : '' }}">
                                <div class="status-flow-label">
                                    <span class="status-flow-icon {{ $isDone ? 'status-flow-icon-done' : '' }}">
                                        @if ($isDone)
                                            ✓
                                        @endif
                                    </span>
                                    <span>{{ $label }}</span>
                                </div>
                                <div class="status-flow-time">
                                    @if ($history?->created_at)
                                        {{ $history->created_at->format('H:i d/m/Y') }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($currentStatus === 'return_pending')
                        <div class="alert alert-warning small mt-3 mb-0">
                            Yêu cầu trả hàng / hoàn tiền của bạn đang được xử lý. Bộ phận CSKH sẽ liên hệ lại sớm nhất.
                        </div>
                    @elseif($currentStatus === 'returned')
                        <div class="alert alert-success small mt-3 mb-0">
                            Đơn hàng đã được xử lý hoàn hàng hoặc hoàn tiền theo thỏa thuận với bạn.
                        </div>
                    @elseif($currentStatus === 'cancelled')
                        <div class="alert alert-secondary small mt-3 mb-0">
                            Đơn hàng đã bị hủy. Nếu đây không phải thao tác của bạn, vui lòng liên hệ CSKH.
                        </div>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                {{-- CỘT TRÁI: THÔNG TIN NHẬN HÀNG + THANH TOÁN --}}
                <div class="col-lg-5">
                    {{-- Thông tin nhận hàng --}}
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            Thông tin nhận hàng
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="text-muted small d-block">Người nhận</span>
                                <strong>{{ $order->receiver_name ?? ($order->customer_name ?? (auth()->user()->name ?? 'Khách hàng')) }}</strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Số điện thoại</span>
                                <strong>{{ $order->receiver_phone ?? ($order->phone ?? ($order->customer_phone ?? '')) }}</strong>
                            </div>
                            @if ($fullAddress)
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Địa chỉ nhận hàng</span>
                                    <span>{{ $fullAddress }}</span>
                                </div>
                            @endif

                            @if (!empty($order->shipping_method))
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Hình thức giao hàng</span>
                                    <span>{{ $order->shipping_method }}</span>
                                </div>
                            @endif

                            @if (!empty($order->note))
                                <div class="mb-0">
                                    <span class="text-muted small d-block">Ghi chú của bạn</span>
                                    <span>{{ $order->note }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Thanh toán --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            Thanh toán
                        </div>
                        <div class="card-body">
                            <dl class="row mb-2 small">
                                @if ($subtotal !== null)
                                    <dt class="col-6 text-muted">Tạm tính</dt>
                                    <dd class="col-6 text-end">
                                        {{ number_format($subtotal, 0, ',', '.') }}₫
                                    </dd>
                                @endif

                                @if ($discountTotal && $discountTotal > 0)
                                    <dt class="col-6 text-muted">Giảm giá</dt>
                                    <dd class="col-6 text-end text-success">
                                        − {{ number_format($discountTotal, 0, ',', '.') }}₫
                                    </dd>
                                @endif

                                @if ($shippingFee !== null)
                                    <dt class="col-6 text-muted">Phí vận chuyển</dt>
                                    <dd class="col-6 text-end">
                                        {{ number_format($shippingFee, 0, ',', '.') }}₫
                                    </dd>
                                @endif

                                @if (!empty($order->coupon_code))
                                    <dt class="col-6 text-muted">Mã giảm giá</dt>
                                    <dd class="col-6 text-end">
                                        <span class="badge bg-light text-dark border">
                                            {{ $order->coupon_code }}
                                        </span>
                                    </dd>
                                @endif
                            </dl>

                            <hr class="my-2">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Tổng thanh toán</span>
                                <span class="fw-bold fs-5 text-primary">
                                    {{ number_format($grandTotal ?? 0, 0, ',', '.') }}₫
                                </span>
                            </div>

                            <div class="small text-muted mb-0">
                                Phương thức thanh toán:
                                <strong>{{ $order->payment_method_label ?? ($order->payment_method ?? 'Thanh toán khi nhận hàng') }}</strong><br>
                                Trạng thái thanh toán:
                                @php
                                    $paymentStatus = $order->payment_status;

                                    // Tự động chuyển trạng thái COD sang 'paid' khi đơn đã giao
                                    if (
                                        in_array($currentStatus, ['shipped', 'returned']) &&
                                        $paymentStatus !== 'paid'
                                    ) {
                                        $paymentStatus = 'paid';
                                    }

                                    $paymentLabel = $paymentStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                                    $paymentBadge = $paymentStatus === 'paid' ? 'bg-success' : 'bg-warning text-dark';
                                @endphp

                                <strong class="badge {{ $paymentBadge }}">
                                    {{ $paymentLabel }}
                                </strong>


                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: SẢN PHẨM + LỊCH SỬ TRẠNG THÁI --}}
                <div class="col-lg-7">
                    {{-- Sản phẩm --}}
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            Sản phẩm trong đơn
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle order-items-table">
                                    <thead class="small text-muted">
                                        <tr>
                                            <th>Ảnh</th>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            @php
                                                // Giống logic bên index
                                                $product = $item->product ?? null;
                                                $variant = $item->productVariant ?? null;

                                                if ($variant && $variant->image_url) {
                                                    // ảnh biến thể (ưu tiên)
                                                    $thumb = asset('storage/' . $variant->image_url);
                                                } elseif ($product && $product->image_main) {
                                                    // fallback: ảnh chính của sản phẩm
                                                    $thumb = asset('storage/' . $product->image_main);
                                                } else {
                                                    $thumb = null;
                                                }

                                                $variantText =
                                                    $item->variant_name ?? ($item->variant ?? ($item->options ?? ''));
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if ($thumb)
                                                        <div class="order-item-thumb">
                                                            <img src="{{ $thumb }}"
                                                                alt="{{ $product->name ?? 'Sản phẩm' }}">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="small fw-semibold">
                                                        {{ $item->product_name ?? ($product->name ?? 'Sản phẩm') }}
                                                    </div>
                                                    @if ($variantText)
                                                        <div class="small text-muted">
                                                            Phân loại: {{ $variantText }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($item->price, 0, ',', '.') }}₫
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    </tbody>
                                    <tfoot class="small">
                                        <tr>
                                            <td colspan="4" class="text-end text-muted">Tạm tính</td>
                                            <td class="text-end fw-semibold">
                                                {{ number_format($subtotal, 0, ',', '.') }}₫
                                            </td>
                                        </tr>

                                        @if ($discountTotal > 0)
                                            <tr>
                                                <td colspan="4" class="text-end text-muted">Giảm giá</td>
                                                <td class="text-end text-success fw-semibold">
                                                    − {{ number_format($discountTotal, 0, ',', '.') }}₫
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($shippingFee > 0)
                                            <tr>
                                                <td colspan="4" class="text-end text-muted">Phí vận chuyển</td>
                                                <td class="text-end fw-semibold">
                                                    {{ number_format($shippingFee, 0, ',', '.') }}₫
                                                </td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td colspan="4" class="text-end fw-semibold">Tổng thanh toán</td>
                                            <td class="text-end fw-bold text-primary">
                                                {{ number_format($grandTotal, 0, ',', '.') }}₫
                                            </td>
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Lịch sử trạng thái chi tiết (nếu bạn vẫn muốn giữ) --}}
                    @if (!empty($order->statusHistories) && $order->statusHistories->count())
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold">
                                Lịch sử cập nhật trạng thái
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 small">
                                    @foreach ($order->statusHistories->sortByDesc('created_at') as $history)
                                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                                            <div>
                                                <strong>{{ $statusMeta[$history->status]['label'] ?? $history->status }}</strong>
                                                @if ($history->note)
                                                    <span class="text-muted">
                                                        ({{ $history->note }})
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-muted">
                                                {{ $history->created_at?->format('d/m/Y H:i') }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- HÀNH ĐỘNG NHANH --}}
            <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Nếu cần hỗ trợ thêm, bạn có thể liên hệ CSKH qua hotline
                    hoặc gửi tin nhắn cho fanpage của shop.
                </div>
                <div class="d-flex flex-wrap gap-2">
                    {{-- Yêu cầu trả hàng / hoàn tiền --}}
                    @if (method_exists($order, 'canBeReturnedByCustomer')
                            ? $order->canBeReturnedByCustomer()
                            : in_array($currentStatus, ['completed']))
                        <a href="{{ route('order.return.form', $order) }}" class="btn btn-sm btn-outline-warning">
                            Yêu cầu trả hàng / hoàn tiền
                        </a>
                    @endif

                    {{-- Hủy đơn --}}
                    @if (method_exists($order, 'canBeCancelledByCustomer')
                            ? $order->canBeCancelledByCustomer()
                            : in_array($currentStatus, ['pending', 'confirmed', 'processing']))
                        <a href="{{ route('order.cancel.form', $order) }}" class="btn btn-sm btn-outline-danger">
                            Hủy đơn hàng
                        </a>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- CSS --}}
    <style>
        .order-detail-wrapper {
            background-color: #f7f7f9;
            padding-top: 24px;
            padding-bottom: 80px;
            /* thừa khoảng cách với footer */
        }

        /* Dòng trạng thái */
        .status-flow-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e2e3e8;
            background-color: #f9fafb;
        }

        .status-flow-cell {
            padding: 0.45rem 0.75rem;
            border-right: 1px solid #e2e3e8;
            background-color: #f9fafb;
        }

        .status-flow-cell:last-child {
            border-right: none;
        }

        .status-flow-cell-done {
            background-color: #e3f4ea;
            color: #145c32;
        }

        .status-flow-cell-current {
            background-color: #cfe8ff;
            color: #0b4f8a;
            font-weight: 600;
        }

        .status-flow-label {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.85rem;
        }

        .status-flow-icon {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 1px solid #c0c4cc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            line-height: 1;
            flex-shrink: 0;
        }

        .status-flow-icon-done {
            border-color: currentColor;
            background-color: #fff;
            font-weight: 700;
        }

        .status-flow-time {
            margin-left: 1.6rem;
            margin-top: 0.15rem;
            font-size: 0.75rem;
            color: #6c757d;
            min-height: 1.1rem;
        }

        /* Sản phẩm */
        .order-items-table tbody tr td {
            vertical-align: middle;
        }

        .order-item-thumb {
            width: 52px;
            height: 66px;
            border-radius: 0.25rem;
            overflow: hidden;
            background-color: #f3f3f7;
        }

        .order-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .status-flow-cell-done span {
            color: inherit;
        }


        @media (max-width: 767.98px) {
            .status-flow-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
@endsection

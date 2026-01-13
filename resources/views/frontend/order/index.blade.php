{{-- resources/views/frontend/order/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
    <style>
        .orders-page {
            padding: 32px 0 40px;
            background-color: #f3f4f6;
        }

        .orders-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .orders-header {
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .orders-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: #111827;
        }

        /* ========== SEARCH BAR ========== */
        .orders-search {
            margin-bottom: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .orders-search-input {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            font-size: 13px;
            min-width: 230px;
            outline: none;
        }

        .orders-search-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.15);
        }

        .orders-search-btn {
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            border: 1px solid #2563eb;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
        }

        .orders-search-btn-reset {
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            cursor: pointer;
            text-decoration: none;
        }

        .orders-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .orders-tab {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #374151;
            text-decoration: none;
            transition: all 0.15s;
        }

        .orders-tab:hover {
            background-color: #f3f4ff;
            color: #1d4ed8;
            border-color: #c7d2fe;
        }

        .orders-tab.active {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
            font-weight: 600;
        }

        .order-card {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .order-card-header {
            padding: 10px 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            background: #f9fafb;
        }

        .order-code {
            font-weight: 600;
            color: #111827;
        }

        .order-date {
            color: #6b7280;
            margin-left: 8px;
        }

        .order-header-right {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .badge-payment {
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-payment-paid {
            background-color: #ecfdf3;
            color: #15803d;
        }

        .badge-payment-unpaid {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        .badge-status {
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-status-processing {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .badge-status-shipped {
            background-color: #ecfeff;
            color: #0891b2;
        }

        .badge-status-completed {
            background-color: #ecfdf3;
            color: #15803d;
        }

        .badge-status-cancelled {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        .badge-status-default {
            background-color: #e5e7eb;
            color: #374151;
        }

        .order-card-body {
            padding: 12px 16px 10px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .order-main {
            display: flex;
            gap: 10px;
            flex: 1;
        }

        .order-thumb {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .order-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .order-thumb-placeholder {
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
            padding: 4px;
        }

        .order-info {
            flex: 1;
        }

        .order-product-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .order-product-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .order-more-items {
            font-size: 12px;
            color: #4b5563;
            margin-top: 4px;
        }

        .order-footer {
            padding: 8px 16px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed #e5e7eb;
            font-size: 13px;
        }

        .order-total-label {
            color: #6b7280;
            margin-right: 4px;
        }

        .order-total-value {
            font-weight: 700;
            color: #b91c1c;
            font-size: 15px;
        }

        .order-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .btn-order-outline,
        .btn-order-primary {
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 12px;
            min-width: 110px;
            text-align: center;
            white-space: nowrap;
        }

        .review-wrapper {
            width: 100%;
            margin-top: 8px;
        }

        .review-toggle-btn {
            border-radius: 4px;
            padding: 6px 14px;
            font-size: 12px;
            min-width: 110px;
            text-align: center;
            white-space: nowrap;
        }


        .btn-order-outline {
            border-color: #2563eb;
            color: #2563eb;
        }

        .btn-order-outline:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .btn-order-primary {
            background-color: #ef4444;
            border-color: #ef4444;
            color: #ffffff;
        }

        .btn-order-primary:hover {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        .orders-empty {
            text-align: center;
            padding: 40px 0;
            color: #6b7280;
            font-size: 14px;
        }

        .orders-empty-icon {
            font-size: 40px;
            margin-bottom: 8px;
        }

        /* PHÂN TRANG */
        .orders-pagination {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
        }

        .orders-pagination nav {
            margin: 0;
        }

        .orders-pagination .pagination {
            margin-bottom: 0;
        }

        .orders-pagination .page-link {
            border-radius: 999px;
            margin: 0 2px;
            padding: 4px 10px;
            font-size: 13px;
        }

        .orders-pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }
    </style>

    <div class="orders-page">
        <div class="orders-container">
            <div class="orders-header">
                <h1 class="orders-title">Đơn hàng của tôi</h1>
            </div>

            {{-- THANH TÌM KIẾM THEO TÊN / ID SẢN PHẨM --}}
            <form method="GET" action="{{ route('order.index') }}" class="orders-search">
                {{-- giữ trạng thái đang chọn trên tab --}}
                @if ($status !== 'all')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif

                <input type="text" name="q" class="orders-search-input"
                    placeholder="Tìm theo tên hoặc ID sản phẩm..." value="{{ request('q') }}">

                <button type="submit" class="orders-search-btn">
                    Tìm kiếm
                </button>

                @if (request('q'))
                    <a href="{{ route('order.index', ['status' => $status !== 'all' ? $status : null]) }}"
                        class="orders-search-btn-reset">
                        Xóa lọc
                    </a>
                @endif
            </form>

            {{-- Tabs trạng thái --}}
            <div class="orders-tabs">
                @foreach ($statusTabs as $key => $label)
                    <a href="{{ route('order.index', ['status' => $key !== 'all' ? $key : null, 'q' => request('q')]) }}"
                        class="orders-tab {{ $status === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($orders->isEmpty())
                <div class="orders-empty">
                    <div class="orders-empty-icon">🧾</div>
                    <p>Bạn chưa có đơn hàng nào.</p>
                </div>
            @else
                @foreach ($orders as $order)
                    @php
                        $firstItem = $order->items->first();

                        // Trạng thái thô từ DB
                        $orderStatus = $order->order_status;

                        // Chuẩn hoá alias cũ
                        $aliases = [
                            'success' => 'shipped', // hoặc 'completed' tuỳ DB
                            'canceled' => 'cancelled',
                        ];
                        $canon = $aliases[$orderStatus] ?? $orderStatus;

                        // Mặc định
                        $statusClass = 'badge-status badge-status-default';
                        $statusLabel = $canon;

                        switch ($canon) {
                            case 'pending':
                                $statusClass = 'badge-status badge-status-processing';
                                $statusLabel = 'Chờ xác nhận';
                                break;
                            case 'confirmed':
                            case 'processing':
                                $statusClass = 'badge-status badge-status-processing';
                                $statusLabel = 'Đang chuẩn bị';
                                break;
                            case 'shipping':
                                $statusClass = 'badge-status badge-status-shipped';
                                $statusLabel = 'Đang giao';
                                break;
                            case 'shipped':
                                $statusClass = 'badge-status badge-status-shipped';
                                $statusLabel = 'Đã giao hàng';
                                break;
                            case 'completed':
                                $statusClass = 'badge-status badge-status-completed';
                                $statusLabel = 'Hoàn thành';
                                break;
                            case 'return_pending':
                                $statusClass = 'badge-status badge-status-completed';
                                $statusLabel = 'Chờ xác nhận trả hàng';
                                break;
                            case 'returned':
                                $statusClass = 'badge-status badge-status-completed';
                                $statusLabel = 'Hoàn / Trả hàng';
                                break;
                            case 'return_waiting_customer':
                                $statusClass = 'badge-status badge-status-completed';
                                $statusLabel = 'Vui lòng xác nhận đã được hoàn tiền';
                                break;
                            //  case 'returned_completed':
                            //    $statusClass = 'badge-status badge-status-completed';
                            //   $statusLabel = 'Đã hoàn thành hoàn hàng';
                            //   break;
                            case 'cancelled':
                                $statusClass = 'badge-status badge-status-cancelled';
                                $statusLabel = 'Đã hủy';
                                break;
                        }

                        // payment_status: 'paid' / 'unpaid'
                        $paymentStatus = $order->payment_status ?? null;
                        $paymentLabel = $paymentStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        $paymentClass =
                            $paymentStatus === 'paid'
                                ? 'badge-payment badge-payment-paid'
                                : 'badge-payment badge-payment-unpaid';
                        $paymentMethod = $order->payment_method ?? 'cod';

                        $paymentMethodLabel =
                            [
                                'cod' => 'Thanh toán khi nhận hàng (COD)',
                                'bank' => 'Chuyển khoản ngân hàng',
                                'vnpay' => 'Thanh toán VNPay',
                                'momo' => 'Thanh toán MoMo',
                                'wallet' => 'Ví điện tử',
                            ][$paymentMethod] ?? ucfirst($paymentMethod);

                        $total =
                            $order->grand_total ??
                            ($order->final_amount ?? ($order->total_price ?? ($order->total ?? 0)));
                    @endphp

                    <div class="order-card">
                        <div class="order-card-header">
                            <div>
                                <span class="order-code">
                                    Mã đơn: {{ $order->code ?? 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="order-date">
                                    • Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="order-header-right">
                                {{-- ⭐ HIỂN THỊ PHƯƠNG THỨC THANH TOÁN ⭐ --}}
                                <span class="badge-payment" style="background:#e0f2fe; color:#0369a1;">
                                    {{ $paymentMethodLabel }}
                                </span>
                                <span class="{{ $paymentClass }}">{{ $paymentLabel }}</span>
                                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>
                        </div>

                        <div class="order-card-body">
                            <div class="order-main">
                                <div class="order-thumb">
                                    @php
                                        $product = $firstItem->product ?? null;
                                        $variant = $firstItem->productVariant ?? null;

                                        if ($variant && $variant->image_url) {
                                            $thumb = asset('storage/' . $variant->image_url);
                                        } elseif ($product && $product->image_main) {
                                            $thumb = asset('storage/' . $product->image_main);
                                        } else {
                                            $thumb = null;
                                        }
                                    @endphp

                                    @if ($thumb)
                                        <img src="{{ $thumb }}" alt="{{ $product->name ?? 'Sản phẩm' }}">
                                    @else
                                        <div class="order-thumb-placeholder">
                                            Không có ảnh
                                        </div>
                                    @endif
                                </div>

                                <div class="order-info">
                                    <div class="order-product-name">
                                        {{ $firstItem->product_name ?? 'Sản phẩm trong đơn hàng' }}
                                    </div>
                                    <div class="order-product-meta">
                                        Số lượng: x{{ $firstItem->quantity ?? 1 }}
                                    </div>
                                    @if ($order->items->count() > 1)
                                        <div class="order-more-items">
                                            + {{ $order->items->count() - 1 }} sản phẩm khác
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div>
                                <span class="order-total-label">Thành tiền:</span>
                                <span class="order-total-value">
                                    {{ number_format($total, 0, ',', '.') }}₫
                                </span>
                            </div>

                            <div class="order-actions">

                                <a href="{{ route('order.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    Chi tiết đơn
                                </a>
                                @php
                                    $latestReturn = optional($order->returns ?? collect())
                                        ->where('user_id', auth()->id())
                                        ->sortByDesc('id')
                                        ->first();
                                @endphp

                                @if ($latestReturn)
                                    <a href="{{ route('order.return.track', $order) }}"
                                        class="btn btn-sm btn-outline-info ms-2">
                                        Theo dõi hoàn
                                    </a>
                                @endif

                                @if ($canon === 'shipped')
                                    <form action="{{ route('order.complete', $order) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Xác nhận đơn hàng đã hoàn tất?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success ms-2">
                                            Hoàn thành đơn
                                        </button>
                                    </form>
                                @endif

                                @if (!in_array($canon, ['completed']))
                                    @if ($order->canBeCancelledByCustomer())
                                        <a href="{{ route('order.cancel.form', $order) }}"
                                            class="btn btn-sm btn-outline-danger ms-2"
                                            onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                            Hủy đơn
                                        </a>
                                    @elseif ($order->canBeConfirmedReceivedByCustomer())
                                        <form action="{{ route('order.received', $order) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Xác nhận bạn đã nhận được hàng?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success ms-2">
                                                Đã nhận hàng
                                            </button>
                                        </form>
                                    @elseif ($order->canRequestReturnByCustomer())
                                        <a href="{{ route('order.return.form', $order) }}"
                                            class="btn btn-sm btn-outline-warning ms-2"
                                            onclick="return confirm('Bạn có chắc chắn muốn hoàn/trả đơn hàng này không?');">
                                            Trả hàng / Hoàn tiền
                                        </a>
                                    @endif
                                @endif
                                {{-- 👉 NÚT: KHÁCH XÁC NHẬN ĐÃ NHẬN TIỀN HOÀN --}}
                                @php
                                    $returnNeedConfirm = optional($order->returns ?? collect())
                                        ->where('user_id', auth()->id())
                                        ->where('status', \App\Models\ReturnModel::WAITING_CUSTOMER_CONFIRM)
                                        ->sortByDesc('id')
                                        ->first();
                                @endphp

                                @if ($returnNeedConfirm)
                                    <form action="{{ route('order.return.confirmReceived', $returnNeedConfirm->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn đã nhận đủ số tiền hoàn chưa?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success ms-2">
                                            Tôi đã nhận tiền hoàn
                                        </button>
                                    </form>
                                @endif
                                {{-- ⭐ FORM ĐÁNH GIÁ SẢN PHẨM SAU KHI NHẬN HÀNG ⭐ --}}
                                @if (in_array($canon, ['shipped', 'completed']) && !$order->is_reviewed)
                                    @php
                                        // Lọc ra những sản phẩm chưa đánh giá
                                        $itemsToReview = $order->items->filter(function ($item) {
                                            return !\App\Models\Review::where('user_id', auth()->id())
                                                ->where('product_id', $item->product_id)
                                                ->where('order_item_id', $item->id)
                                                ->exists();
                                        });
                                    @endphp

                                    @if ($itemsToReview->isNotEmpty())
                                        <!-- Nút đánh giá tổng -->
                                        @if ($itemsToReview->isNotEmpty())
                                            <!-- Nút đánh giá tổng -->
                                            <button class="btn btn-sm btn-outline-warning review-toggle-btn" type="button"
                                                data-order="{{ $order->id }}">
                                                Đánh giá
                                            </button>
                                        @else
                                            <span class="text-success" style="font-size: 12px;">✓ Đã đánh giá</span>
                                        @endif

                                        <!-- Wrapper chứa form từng sản phẩm (ẩn ban đầu) -->
                                        <div class="review-wrapper" style="display:none; margin-top:8px;">
                                            @foreach ($itemsToReview as $item)
                                                @php
                                                    $variant = $item->productVariant ?? null;
                                                    $product = $item->product ?? null;

                                                    // Ảnh hiển thị (ưu tiên biến thể, fallback ảnh chính sản phẩm)
                                                    if ($variant && $variant->image_url) {
                                                        $thumb = asset('storage/' . $variant->image_url);
                                                    } elseif ($product && $product->image_main) {
                                                        $thumb = asset('storage/' . $product->image_main);
                                                    } else {
                                                        $thumb = null;
                                                    }

                                                    // Xử lý text biến thể
                                                    if ($variant) {
                                                        $variantText = $variant->name ?? null;

                                                        if (
                                                            !$variantText &&
                                                            $variant->attributes &&
                                                            $variant->attributes->count()
                                                        ) {
                                                            // Ví dụ: "Đen / Size L / Cotton"
                                                            $variantText = $variant->attributes
                                                                ->pluck('value')
                                                                ->join(' / ');
                                                        }
                                                    } else {
                                                        $variantText =
                                                            $item->variant_name ??
                                                            ($item->variant ?? ($item->options ?? null));
                                                    }
                                                @endphp

                                                <div class="mb-3">
                                                    <!-- Hiển thị tên sản phẩm + biến thể + ảnh -->
                                                    <div class="d-flex align-items-center mb-2 gap-2">
                                                        @if ($thumb)
                                                            <img src="{{ $thumb }}" alt="{{ $item->product_name }}"
                                                                style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                                                        @endif
                                                        <div>
                                                            <div style="font-weight:600;">
                                                                {{ $item->product_name }}
                                                                @if ($variantText)
                                                                    - {{ $variantText }}
                                                                @endif
                                                                (x{{ $item->quantity }})
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Form đánh giá -->
                                                    <div id="reviewForm-{{ $item->id }}" class="card p-3 shadow-sm"
                                                        style="max-width:500px; flex-basis:100%;">
                                                        <form
                                                            action="{{ route('products.reviews.store', $item->product_id) }}"
                                                            method="POST" enctype="multipart/form-data"
                                                            onsubmit="submitReview(event, {{ $item->id }}, '{{ $item->product_name }}')">
                                                            @csrf
                                                            <input type="hidden" name="order_item_id"
                                                                value="{{ $item->id }}">

                                                            {{-- Sao --}}
                                                            <div class="mb-2 d-flex gap-1 stars-wrapper"
                                                                data-item="{{ $item->id }}">
                                                                <input type="hidden" name="rating"
                                                                    id="ratingInput-{{ $item->id }}" value="0">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <span class="star" data-value="{{ $i }}"
                                                                        style="cursor:pointer; font-size:22px; color:#ccc;">★</span>
                                                                @endfor
                                                            </div>

                                                            {{-- Nội dung --}}
                                                            <div class="mb-2">
                                                                <textarea name="comment" class="form-control" rows="2" placeholder="Viết nhận xét..." required></textarea>
                                                            </div>

                                                            {{-- Ảnh --}}
                                                            <div class="mb-2">
                                                                <input type="file" name="image" class="form-control"
                                                                    accept="image/*">
                                                            </div>

                                                            <button type="submit" class="btn btn-dark btn-sm"
                                                                id="reviewBtn-{{ $item->id }}">
                                                                Gửi
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                @if ($order->canBeReorderedByCustomer())
                                    <form action="{{ route('order.reorder', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                            Mua lại
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($orders->hasPages())
                    <div class="orders-pagination">
                        {{ $orders->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle form tổng - SỬA ĐỂ HỖ TRỢ NHIỀU ĐƠN HÀNG
        document.querySelectorAll('.review-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = btn.dataset.order;
                const wrapper = btn.closest('.order-actions').querySelector('.review-wrapper');

                if (wrapper) {
                    wrapper.style.display = wrapper.style.display === 'none' ? 'block' : 'none';
                    btn.textContent = wrapper.style.display === 'none' ? 'Đánh giá' :
                        'Ẩn đánh giá';
                }
            });
        });

        // JS chọn sao vẫn giữ nguyên cho từng sản phẩm
        document.querySelectorAll('.stars-wrapper').forEach(wrapper => {
            const itemId = wrapper.dataset.item;
            const stars = wrapper.querySelectorAll('.star');
            const input = document.getElementById('ratingInput-' + itemId);

            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    input.value = star.dataset.value;
                    stars.forEach(s => s.style.color = '#ccc');
                    for (let i = 0; i <= index; i++) {
                        stars[i].style.color = '#f0ad4e';
                    }
                });
            });
        });
    });


    function submitReview(event, itemId) {
        event.preventDefault();

        const form = event.target;
        const submitBtn = document.getElementById('reviewBtn-' + itemId);
        const wrapper = form.closest('.review-wrapper');
        const toggleBtn = wrapper.parentElement.querySelector('.review-toggle-btn');

        submitBtn.disabled = true;
        submitBtn.innerText = 'Đang gửi...';

        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Lỗi đánh giá');
                }

                alert('✅ ' + data.message);

                /* ✅ XOÁ HẲN FORM SẢN PHẨM */
                const reviewBlock = form.closest('.mb-3');
                if (reviewBlock) {
                    reviewBlock.remove();
                }

                /* ✅ NẾU HẾT SẢN PHẨM */
                if (wrapper.querySelectorAll('.mb-3').length === 0) {
                    wrapper.remove();

                    if (toggleBtn) {
                        toggleBtn.outerHTML =
                            '<span class="text-success" style="font-size:12px;">✓ Đã đánh giá</span>';
                    }
                }
            })
            .catch(err => {
                alert(err.message || 'Có lỗi xảy ra!');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Gửi';
            });
    }
</script>

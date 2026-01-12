@extends('layouts.admin.master')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Header + actions --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                        <div>
                            <h2 class="mb-1">
                                Đơn hàng {{ $order->code ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb small mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                                </ol>
                            </nav>
                        </div>

                        <div class="btn-group">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Về danh sách
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-outline-secondary">
                                <i class="fa fa-file-text-o me-1"></i> Hóa đơn
                            </a>
                            <a href="{{ route('admin.orders.invoice.pdf', $order->id) }}" class="btn btn-primary">
                                <i class="fa fa-download me-1"></i> PDF
                            </a>
                            {{-- <button class="btn btn-dark" onclick="window.print()">
                                <i class="fa fa-print me-1"></i> In
                            </button> --}}
                        </div>
                    </div>
                    {{-- 🔔 Thông báo hệ thống --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="fa fa-check-circle me-1"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                            <i class="fa fa-exclamation-circle me-1"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @php
                        // Chuẩn hoá trạng thái từ dữ liệu cũ
                        $aliases = [
                            'success' => 'completed',
                            'canceled' => 'cancelled',
                        ];
                        $canon = $aliases[$order->order_status] ?? $order->order_status;

                        // Badge trạng thái
                        $statusBadge = [
                            'pending' => ['txt' => 'Chờ xử lý', 'cls' => 'bg-secondary'],
                            'confirmed' => ['txt' => 'Xác nhận', 'cls' => 'bg-primary'],
                            'processing' => ['txt' => 'Chuẩn bị', 'cls' => 'bg-warning text-dark'],
                            'shipping' => ['txt' => 'Đang giao', 'cls' => 'bg-info'],
                            'shipped' => ['txt' => 'Đã giao', 'cls' => 'bg-success'],
                            'completed' => ['txt' => 'Hoàn thành', 'cls' => 'bg-success'],
                            'cancelled' => ['txt' => 'Đã hủy', 'cls' => 'bg-danger'],
                            'return_pending' => ['txt' => 'Yêu cầu hoàn hàng', 'cls' => 'bg-warning text-dark'], // 👈 thêm

                            'returned' => ['txt' => 'Hoàn hàng', 'cls' => 'bg-warning text-dark'],
                        ];

                        // Thanh toán
                        $payTxt = $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        $payCls = $order->payment_status === 'paid' ? 'bg-success' : 'bg-danger';
                        // Loại thanh toán
                        $payTypeTxt = match ($order->payment_method ?? '') {
                            'cod' => 'Thanh toán khi nhận hàng (COD)',
                            'online' => 'Thanh toán online',
                            default => 'VNPay',
                        };
                        $payTypeCls =
                            $order->payment_type === 'cod'
                                ? 'bg-info'
                                : ($order->payment_type === 'online'
                                    ? 'bg-primary'
                                    : 'bg-secondary');
                        // Chuỗi bước (8 cột, gồm cả Hoàn hàng & Đã hủy)
                        $steps = [
                            ['key' => 'pending', 'label' => 'Chờ xử lý'],
                            ['key' => 'confirmed', 'label' => 'Xác nhận'],
                            ['key' => 'processing', 'label' => 'Chuẩn bị'],
                            ['key' => 'shipping', 'label' => 'Đang giao'],
                            ['key' => 'shipped', 'label' => 'Đã giao'],
                            ['key' => 'completed', 'label' => 'Hoàn thành'],
                            ['key' => 'returned', 'label' => 'Hoàn hàng'],
                            ['key' => 'cancelled', 'label' => 'Đã hủy'],
                        ];

                        // Luồng chính (không gồm Hoàn hàng / Đã hủy)
                        $pipelineKeys = ['pending', 'confirmed', 'processing', 'shipping', 'shipped', 'completed'];
                        $pipelineIndexMap = array_flip($pipelineKeys);

                        $canonInPipeline = isset($pipelineIndexMap[$canon]);
                        $currentIndex = $canonInPipeline ? $pipelineIndexMap[$canon] : -1;
                        $shippedIndex = $pipelineIndexMap['shipped'];

                        // Nhãn tiếng Việt cho dropdown cập nhật
                        $labelStatus = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Xác nhận',
                            'processing' => 'Chuẩn bị',
                            'shipping' => 'Đang giao',
                            'shipped' => 'Đã giao',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Hủy',
                            'return_pending' => 'Yêu cầu trả hàng',

                            'returned' => 'Hoàn hàng',
                        ];

                        // allowedNext được truyền từ controller (theo statusMatrix)
                        $allowedNext = $allowedNext ?? [];
                        $isLocked = empty($allowedNext);
                        $statusTimes = $statusTimes ?? []; // mảng ['pending' => Carbon|string, ...]
                    @endphp

                    <div class="row g-3">
                        {{-- Tài khoản đặt hàng --}}
                        @if ($order->user)
                            <div class="card shadow-sm border-0 h-100 mb-3">
                                <div class="card-header bg-light fw-semibold">Thông tin tài khoản</div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="text-muted">Tên tài khoản:</span>
                                        <strong>{{ $order->user->name }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted">Email:</span>
                                        <strong>{{ $order->user->email }}</strong>
                                    </div>
                                    <div class="mb-0">
                                        <span class="text-muted">Số điện thoại:</span>
                                        <strong>{{ $order->user->phone ?? $order->receiver_phone }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Khách hàng --}}
                        <div class="card shadow-sm border-0 h-100">
                            <div class="">
                                <div class="card-header bg-light fw-semibold">Thông tin người nhận</div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="text-muted">Họ tên:</span>
                                        <strong>{{ $order->receiver_name }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted">Điện thoại:</span>
                                        <strong>{{ $order->receiver_phone }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted">Địa chỉ nhận hàng:</span>
                                        <strong>{{ $order->receiver_address }}</strong>
                                    </div>
                                    @if ($order->note)
                                        <div class="mb-2">
                                            <span class="text-muted">Ghi chú đơn hàng:</span>
                                            <strong>{{ $order->note }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Đơn hàng --}}
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light fw-semibold">Thông tin đơn hàng</div>
                            <div class="card-body">

                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="text-muted">Trạng thái:</span>
                                    <span class="badge {{ $statusBadge[$canon]['cls'] ?? 'bg-secondary' }}">
                                        {{ $statusBadge[$canon]['txt'] ?? $order->status_label }}
                                    </span>

                                    <span class="text-muted ms-3">Thanh toán:</span>
                                    <span class="badge {{ $payCls }}">{{ $payTxt }}</span>

                                    <span class="text-muted ms-3">Loại thanh toán:</span>
                                    <span class="badge bg-primary text-white">{{ $payTypeTxt }}</span>

                                    @if (in_array($canon, ['cancelled', 'returned'], true))
                                        <span class="badge bg-light text-danger ms-3">
                                            Trạng thái cuối: {{ $statusBadge[$canon]['txt'] ?? $order->status_label }}
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-muted mb-1">
                                    Thời gian đặt hàng:
                                    <strong>{{ $order->created_at?->format('H:i d/m/Y') }}</strong>
                                </div>

                                {{-- ⏳ Thông báo tự động hoàn thành --}}
                                @if ($canon === 'shipped' && $order->shipped_at)
                                    <div class="small text-warning mb-2">
                                        @php
                                            $end = \Carbon\Carbon::parse($order->shipped_at)->addDays(3);
                                            $diff = now()->diff($end);
                                        @endphp

                                        ⏳ Tự động hoàn thành sau:
                                        <strong>
                                            {{ $diff->d }} ngày {{ $diff->h }} giờ {{ $diff->i }} phút
                                        </strong>

                                    </div>
                                @endif


                                <div class="small text-muted mb-3">
                                    Phí ship:
                                    <strong>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</strong> ·
                                    Tổng tiền:
                                    <strong class="text-primary">
                                        {{ number_format($order->final_amount, 0, ',', '.') }}đ
                                    </strong>
                                </div>

                                {{-- Stepper 8 bước --}}
                                <ul class="list-group list-group-horizontal-sm mb-3">
                                    @foreach ($steps as $i => $s)
                                        @php
                                            $pipelineIndex = $pipelineIndexMap[$s['key']] ?? null;

                                            $isDone = false;
                                            $cls = '';
                                            $icon = 'fa-circle-o';

                                            if ($canonInPipeline) {
                                                // Đơn đang trên luồng chính: tô xanh tới bước hiện tại
                                                if ($pipelineIndex !== null && $pipelineIndex <= $currentIndex) {
                                                    $isDone = true;
                                                    $cls = 'list-group-item-success';
                                                }
                                            } elseif (in_array($canon, ['returned', 'return_pending'], true)) {
                                                // pending → shipped xanh
                                                if ($pipelineIndex !== null && $pipelineIndex <= $shippedIndex) {
                                                    $isDone = true;
                                                    $cls = 'list-group-item-success';
                                                }

                                                if ($s['key'] === 'returned') {
                                                    $isDone = true;
                                                    // nếu mới yêu cầu thì vàng, nếu đã xử lý xong thì xanh
                                                    $cls =
                                                        $canon === 'return_pending'
                                                            ? 'list-group-item-warning'
                                                            : 'list-group-item-success';
                                                }
                                            } elseif ($canon === 'cancelled') {
                                                // Đã hủy: chỉ cột Đã hủy đỏ
                                                if ($s['key'] === 'cancelled') {
                                                    $isDone = true;
                                                    $cls = 'list-group-item-danger';
                                                }
                                            }

                                            if ($isDone) {
                                                if ($canon === 'cancelled' && $s['key'] === 'cancelled') {
                                                    $icon = 'fa-times-circle';
                                                } elseif ($canon === 'returned' && $s['key'] === 'returned') {
                                                    $icon = 'fa-undo';
                                                } else {
                                                    $icon = 'fa-check-circle';
                                                }
                                            }
                                        @endphp

                                        <li
                                            class="list-group-item d-flex flex-column justify-content-center flex-fill {{ $cls }}">
                                            <div class="d-flex align-items-center">
                                                <i class="fa {{ $icon }} me-2"></i>
                                                <span class="small">{{ $s['label'] }}</span>
                                            </div>

                                            @php
                                                $time = $statusTimes[$s['key']] ?? null;
                                            @endphp

                                            @if ($time)
                                                <span class="small text-muted mt-1">
                                                    {{ \Carbon\Carbon::parse($time)->format('H:i d/m/Y') }}
                                                </span>
                                            @else
                                                {{-- Chưa tới trạng thái này --}}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Cập nhật trạng thái: ẩn nếu không còn bước tiếp --}}
                                @if (!$isLocked)
                                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}"
                                        class="d-flex align-items-center gap-2">
                                        @csrf
                                        <select name="status" id="order-status-select"
                                            class="form-select form-select-sm w-auto">
                                            @foreach ($allowedNext as $st)
                                                <option value="{{ $st }}">
                                                    {{ $labelStatus[$st] ?? ucfirst($st) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                            onclick="return confirmUpdateStatus()">Cập nhật</button>

                                    </form>
                                @else
                                    <div class="alert alert-light border d-flex align-items-center p-2 mb-0">
                                        <i
                                            class="fa {{ in_array($canon, ['cancelled', 'returned'], true)
                                                ? 'fa-times-circle text-danger'
                                                : 'fa-check-circle text-success' }} me-2"></i>
                                        <span class="small">
                                            Đơn đang ở trạng thái cuối:
                                            <strong>{{ $labelStatus[$canon] ?? $order->status_label }}</strong>.
                                            Không thể cập nhật thêm.
                                        </span>
                                    </div>
                                @endif

                                {{-- Lý do hủy / hoàn hàng --}}
                                @php
                                    $cancelReason = $canon === 'cancelled' ? $order->cancel_reason : null;
                                    $returnReason = in_array($canon, ['return_pending', 'returned'], true)
                                        ? $order->return_reason
                                        : null;
                                @endphp

                                {{-- Đơn bị hủy: giữ card chi tiết --}}
                                @if ($cancelReason)
                                    <div class="mt-3">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header bg-light fw-semibold">
                                                Lý do hủy đơn của khách
                                            </div>
                                            <div class="card-body">
                                                <div class="border rounded bg-light px-3 py-2"
                                                    style="white-space: pre-line; font-size: 14px;">
                                                    {{ $cancelReason }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Đơn hoàn / trả hàng: chỉ 1 dòng + link xem chi tiết --}}
                                @if ($returnReason)
                                    @php
                                        // Lấy câu lý do chính (trước dấu " | ", nếu có) và rút gọn độ dài
                                        $firstPart = preg_split('/\s*\|\s*/', $returnReason)[0] ?? $returnReason;
                                        $shortReason = \Illuminate\Support\Str::limit($firstPart, 80);
                                    @endphp
                                    <div
                                        class="alert alert-warning d-flex justify-content-between align-items-center mt-3">
                                        <div class="small">
                                            <strong>Lý do hoàn hàng:</strong>
                                            {{ $shortReason }}
                                        </div>
                                        <a href="{{ route('admin.returns.index') }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                @endif

                            </div>
                        </div>

                        {{-- Bảng sản phẩm --}}
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-header bg-light fw-semibold">Sản phẩm</div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="64">Ảnh</th>
                                            <th>Sản phẩm</th>
                                            <th>Phân loại</th>
                                            <th class="text-end">Giá</th>
                                            <th class="text-end">SL</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subTotal = 0; @endphp

                                        @forelse($order->orderItems as $it)
                                            @php
                                                $price = (float) ($it->price ?? 0);
                                                $qty = (int) ($it->quantity ?? 0);
                                                $line = $price * $qty;
                                                $subTotal += $line;

                                                $product = $it->product;
                                                $variant = $it->productVariant;

                                                $variantAttributes =
                                                    $variant && $variant->attributeValues
                                                        ? $variant->attributeValues->pluck('value')->join(', ')
                                                        : null;

                                                // Ảnh theo biến thể (ưu tiên), fallback về ảnh sản phẩm
                                                if ($variant && $variant->image_url) {
                                                    $img = asset('storage/' . $variant->image_url);
                                                } elseif ($product && $product->image_main) {
                                                    $img = asset('storage/' . $product->image_main);
                                                } elseif ($product && $product->images && $product->images->first()) {
                                                    $img = asset('storage/' . $product->images->first()->image_path);
                                                } else {
                                                    $img = 'https://placehold.co/300x300?text=IMG';
                                                }

                                            @endphp
                                            <tr>
                                                <td>
                                                    <img src="{{ $img }}" alt="img"
                                                        class="rounded img-thumbnail" width="48" height="48">
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ $product->name ?? 'Sản phẩm đã xoá' }}
                                                    </div>
                                                </td>
                                                <td class="text-muted">
                                                    @if ($variantAttributes)
                                                        {{ $variantAttributes }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($price, 0, ',', '.') }}đ
                                                </td>
                                                <td class="text-end">
                                                    {{ $qty }}
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    {{ number_format($line, 0, ',', '.') }}đ
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="alert alert-light text-muted mb-0 py-5">
                                                        <div class="mb-1">
                                                            <i class="fa fa-info-circle"></i>
                                                        </div>
                                                        Đơn hàng không có sản phẩm.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    @if ($order->orderItems && $order->orderItems->count())
                                        @php
                                            // Tính discount từ voucher
                                            $discountAmount = 0;

                                            if ($order->voucher_id) {
                                                // Cách 1: Lấy từ VoucherUsage nếu có relationship
                                                if ($order->relationLoaded('voucherUsage') && $order->voucherUsage) {
                                                    $discountAmount = $order->voucherUsage->discount_amount ?? 0;
                                                } else {
                                                    // Cách 2: Tính ngược từ final_amount
                                                    $totalBeforeDiscount = $subTotal + $order->shipping_fee;
                                                    $discountAmount = $totalBeforeDiscount - $order->final_amount;
                                                    $discountAmount = max(0, $discountAmount); // Đảm bảo không âm
                                                }
                                            }
                                        @endphp
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="5" class="text-end text-muted">Tạm tính</th>
                                                <th class="text-end">
                                                    {{ number_format($subTotal, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            {{-- ✅ THÊM DÒNG GIẢM GIÁ --}}
                                            @if ($discountAmount > 0)
                                                <tr>
                                                    <th colspan="5" class="text-end text-muted">
                                                        Giảm giá
                                                        @if ($order->voucher)
                                                            <span
                                                                class="badge bg-success ms-2">{{ $order->voucher->code }}</span>
                                                        @endif
                                                    </th>
                                                    <th class="text-end text-success">
                                                        − {{ number_format($discountAmount, 0, ',', '.') }}đ
                                                    </th>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th colspan="5" class="text-end text-muted">Phí ship</th>
                                                <th class="text-end">
                                                    {{ number_format($order->shipping_fee, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-end">Tổng thanh toán</th>
                                                <th class="text-end text-primary fw-bold">
                                                    {{ number_format($order->final_amount, 0, ',', '.') }}đ
                                                </th>
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

            .sherah-sidebar,
            .sherah-header,
            .sherah-btn,
            .sherah-breadcrumb,
            .sherah-footer {
                display: none !important;
            }

            .sherah-page-inner,
            .sherah-table {
                border: 0 !important;
                box-shadow: none !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
    <script>
        function confirmUpdateStatus() {
            let select = document.getElementById('order-status-select');
            let text = select.options[select.selectedIndex].text;
            return confirm("Bạn có chắc muốn cập nhật trạng thái thành: " + text + "  ?");
        }
    </script>
@endpush

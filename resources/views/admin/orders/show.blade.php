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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
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
                            <button class="btn btn-dark" onclick="window.print()">
                                <i class="fa fa-print me-1"></i> In
                            </button>
                        </div>
                    </div>

                    @php
                        // Chuẩn hoá trạng thái từ dữ liệu cũ
                        $aliases = [
                            'success'  => 'completed',
                            'canceled' => 'cancelled',
                        ];
                        $canon = $aliases[$order->order_status] ?? $order->order_status;

                        // Badge trạng thái
                        $statusBadge = [
                            'pending'    => ['txt' => 'Chờ xử lý',  'cls' => 'bg-secondary'],
                            'confirmed'  => ['txt' => 'Xác nhận',   'cls' => 'bg-primary'],
                            'processing' => ['txt' => 'Chuẩn bị',   'cls' => 'bg-warning text-dark'],
                            'shipping'   => ['txt' => 'Đang giao',  'cls' => 'bg-info'],
                            'shipped'    => ['txt' => 'Đã giao',    'cls' => 'bg-success'],
                            'completed'  => ['txt' => 'Hoàn thành', 'cls' => 'bg-success'],
                            'cancelled'  => ['txt' => 'Đã hủy',     'cls' => 'bg-danger'],
                            'returned'   => ['txt' => 'Hoàn hàng',  'cls' => 'bg-warning text-dark'],
                        ];

                        // Thanh toán
                        $payTxt = $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        $payCls = $order->payment_status === 'paid' ? 'bg-success' : 'bg-danger';

                        // Chuỗi bước (8 cột, gồm cả Hoàn hàng & Đã hủy)
                        $steps = [
                            ['key' => 'pending',    'label' => 'Chờ xử lý'],
                            ['key' => 'confirmed',  'label' => 'Xác nhận'],
                            ['key' => 'processing', 'label' => 'Chuẩn bị'],
                            ['key' => 'shipping',   'label' => 'Đang giao'],
                            ['key' => 'shipped',    'label' => 'Đã giao'],
                            ['key' => 'completed',  'label' => 'Hoàn thành'],
                            ['key' => 'returned',   'label' => 'Hoàn hàng'],
                            ['key' => 'cancelled',  'label' => 'Đã hủy'],
                        ];

                        // Luồng chính (không gồm Hoàn hàng / Đã hủy)
                        $pipelineKeys     = ['pending', 'confirmed', 'processing', 'shipping', 'shipped', 'completed'];
                        $pipelineIndexMap = array_flip($pipelineKeys);

                        $canonInPipeline = isset($pipelineIndexMap[$canon]);
                        $currentIndex    = $canonInPipeline ? $pipelineIndexMap[$canon] : -1;
                        $shippedIndex    = $pipelineIndexMap['shipped'];

                        // Nhãn tiếng Việt cho dropdown cập nhật
                        $labelStatus = [
                            'pending'    => 'Chờ xử lý',
                            'confirmed'  => 'Xác nhận',
                            'processing' => 'Chuẩn bị',
                            'shipping'   => 'Đang giao',
                            'shipped'    => 'Đã giao',
                            'completed'  => 'Hoàn thành',
                            'cancelled'  => 'Hủy',
                            'returned'   => 'Hoàn hàng',
                        ];

                        // allowedNext được truyền từ controller (theo statusMatrix)
                        $allowedNext = $allowedNext ?? [];
                        $isLocked    = empty($allowedNext);
                    @endphp

                    <div class="row g-3">
                        {{-- Khách hàng --}}
                        <div class="card shadow-sm border-0 h-100">
                            <div class="">
                                <div class="card-header bg-light fw-semibold">Thông tin khách hàng</div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="text-muted">Họ tên:</span>
                                        <strong>{{ $order->receiver_name }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted">Điện thoại:</span>
                                        <strong>{{ $order->receiver_phone }}</strong>
                                    </div>
                                    <div class="mb-0">
                                        <span class="text-muted">Địa chỉ nhận hàng:</span>
                                        <strong>{{ $order->receiver_address }}</strong>
                                    </div>
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

                                    @if (in_array($canon, ['cancelled', 'returned'], true))
                                        <span class="badge bg-light text-danger ms-3">
                                            Trạng thái cuối: {{ $statusBadge[$canon]['txt'] ?? $order->status_label }}
                                        </span>
                                    @endif
                                </div>

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
                                            $cls    = '';
                                            $icon   = 'fa-circle-o';

                                            if ($canonInPipeline) {
                                                // Đơn đang trên luồng chính: tô xanh tới bước hiện tại
                                                if ($pipelineIndex !== null && $pipelineIndex <= $currentIndex) {
                                                    $isDone = true;
                                                    $cls    = 'list-group-item-success';
                                                }
                                            } elseif ($canon === 'returned') {
                                                // Hoàn hàng: pending → shipped xanh, returned vàng
                                                if ($s['key'] === 'returned') {
                                                    $isDone = true;
                                                    $cls    = 'list-group-item-warning';
                                                } elseif ($pipelineIndex !== null && $pipelineIndex <= $shippedIndex) {
                                                    $isDone = true;
                                                    $cls    = 'list-group-item-success';
                                                }
                                            } elseif ($canon === 'cancelled') {
                                                // Đã hủy: chỉ cột Đã hủy đỏ
                                                if ($s['key'] === 'cancelled') {
                                                    $isDone = true;
                                                    $cls    = 'list-group-item-danger';
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

                                        <li class="list-group-item d-flex align-items-center flex-fill {{ $cls }}">
                                            <i class="fa {{ $icon }} me-2"></i>
                                            <span class="small">{{ $s['label'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Cập nhật trạng thái: ẩn nếu không còn bước tiếp --}}
                                @if (!$isLocked)
                                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}"
                                          class="d-flex align-items-center gap-2">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm w-auto">
                                            @foreach ($allowedNext as $st)
                                                <option value="{{ $st }}">
                                                    {{ $labelStatus[$st] ?? ucfirst($st) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Cập nhật
                                        </button>
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
                                                $qty   = (int) ($it->quantity ?? 0);
                                                $line  = $price * $qty;
                                                $subTotal += $line;

                                                $product = $it->product;
                                                $variant = $it->productVariant;

                                                $variantAttributes =
                                                    $variant && $variant->attributeValues
                                                        ? $variant->attributeValues->pluck('value')->join(', ')
                                                        : null;

                                                if ($product && $product->image_main) {
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
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="5" class="text-end text-muted">Tạm tính</th>
                                                <th class="text-end">
                                                    {{ number_format($subTotal, 0, ',', '.') }}đ
                                                </th>
                                            </tr>
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
@endpush

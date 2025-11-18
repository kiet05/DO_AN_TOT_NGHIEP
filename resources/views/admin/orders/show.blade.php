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
                            <h2 class="mb-1">Đơn hàng {{ $order->code ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb small mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
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
                        // Đồng bộ tên trạng thái để hiển thị
                        $canon =
                            ['success' => 'completed', 'canceled' => 'cancelled'][$order->order_status] ??
                            $order->order_status;

                        $statusBadge = [
                            'pending' => ['txt' => 'Chờ xử lý', 'cls' => 'bg-secondary'],
                            'shipping' => ['txt' => 'Đang giao', 'cls' => 'bg-info'],
                            'completed' => ['txt' => 'Đã giao', 'cls' => 'bg-success'],
                            'cancelled' => ['txt' => 'Đã hủy', 'cls' => 'bg-danger'],
                        ];

                        $payTxt = $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        $payCls = $order->payment_status === 'paid' ? 'bg-success' : 'bg-danger';

                        // Step list (không cần CSS riêng, dùng list-group ngang)
                        $steps = [
                            ['key' => 'pending', 'label' => 'Chờ xử lý'],
                            ['key' => 'shipping', 'label' => 'Đang giao'],
                            ['key' => 'completed', 'label' => 'Đã giao'],
                        ];
                        $currentIndex = collect($steps)->search(function ($s) use ($canon) {
                            return $s['key'] === $canon;
                        });
                        if ($currentIndex === false) {
                            $currentIndex = 0;
                        }

                        // Matrix cho cập nhật nhanh
                        $matrix = [
                            'pending' => ['shipping', 'cancelled'],
                            'shipping' => ['completed', 'cancelled'],
                            'completed' => [],
                            'cancelled' => [],
                        ];
                        $allowedNext = $allowedNext ?? ($matrix[$canon] ?? []);
                        $isLocked = empty($allowedNext);
                        $labelStatus = [
                            'pending' => 'Chờ xử lý',
                            'shipping' => 'Đang giao',
                            'completed' => 'Đã giao',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp

                    <div class="row g-3">
                        {{-- Khách hàng --}}
                        <div class="card shadow-sm border-0 h-100">
                            <div class="">
                                <div class="card-header bg-light fw-semibold">Thông tin khách hàng</div>
                                <div class="card-body">
                                    <div class="mb-2"><span class="text-muted">Họ tên:</span>
                                        <strong>{{ $order->receiver_name }}</strong>
                                    </div>
                                    <div class="mb-2"><span class="text-muted">Điện thoại:</span>
                                        <strong>{{ $order->receiver_phone }}</strong>
                                    </div>
                                    <div class="mb-0"><span class="text-muted">Địa chỉ nhận hàng:</span>
                                        <strong>{{ $order->receiver_address }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Đơn hàng --}}
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light fw-semibold">Thông tin đơn hàng</div>
                            <div class="card-body">
                                @php
                                    // Chuẩn hoá tên trạng thái
                                    $canon =
                                        ['success' => 'completed', 'canceled' => 'cancelled'][$order->order_status] ??
                                        $order->order_status;

                                    $statusBadge = [
                                        'pending' => ['txt' => 'Chờ xử lý', 'cls' => 'bg-secondary'],
                                        'shipping' => ['txt' => 'Đang giao', 'cls' => 'bg-info'],
                                        'completed' => ['txt' => 'Đã giao', 'cls' => 'bg-success'],
                                        'cancelled' => ['txt' => 'Đã hủy', 'cls' => 'bg-danger'],
                                    ];

                                    $payTxt = $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                                    $payCls = $order->payment_status === 'paid' ? 'bg-success' : 'bg-danger';

                                    // 4 bước gồm cả "Đã hủy"
                                    $steps = [
                                        ['key' => 'pending', 'label' => 'Chờ xử lý'],
                                        ['key' => 'shipping', 'label' => 'Đang giao'],
                                        ['key' => 'completed', 'label' => 'Đã giao'],
                                        ['key' => 'cancelled', 'label' => 'Đã hủy'],
                                    ];

                                    // Tính ô nào active + màu theo trạng thái hiện tại
                                    $isActive = function ($step) use ($canon) {
                                        if ($canon === 'cancelled') {
                                            return $step === 'cancelled';
                                        }
                                        if ($canon === 'completed') {
                                            return in_array($step, ['pending', 'shipping', 'completed'], true);
                                        }
                                        if ($canon === 'shipping') {
                                            return in_array($step, ['pending', 'shipping'], true);
                                        }
                                        return $step === 'pending';
                                    };
                                    $ctxClass = function ($step, $canon) {
                                        if ($canon === 'cancelled') {
                                            return $step === 'cancelled' ? 'list-group-item-danger' : '';
                                        }
                                        if ($canon === 'completed') {
                                            return in_array($step, ['pending', 'shipping', 'completed'], true)
                                                ? 'list-group-item-success'
                                                : '';
                                        }
                                        if ($canon === 'shipping') {
                                            return in_array($step, ['pending', 'shipping'], true)
                                                ? 'list-group-item-info'
                                                : '';
                                        }
                                        return $step === 'pending' ? 'list-group-item-secondary' : '';
                                    };

                                    $labelStatus = [
                                        'pending' => 'Chờ xử lý',
                                        'shipping' => 'Đang giao',
                                        'completed' => 'Đã giao',
                                        'cancelled' => 'Đã hủy',
                                    ];

                                    $matrix = [
                                        'pending' => ['shipping', 'cancelled'],
                                        'shipping' => ['completed', 'cancelled'],
                                        'completed' => [],
                                        'cancelled' => [],
                                    ];
                                    $allowedNext = $matrix[$canon] ?? [];
                                    $isLocked = in_array($canon, ['completed', 'cancelled'], true);
                                @endphp

                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="text-muted">Trạng thái:</span>
                                    <span class="badge {{ $statusBadge[$canon]['cls'] ?? 'bg-secondary' }}">
                                        {{ $statusBadge[$canon]['txt'] ?? ucfirst($canon) }}
                                    </span>
                                    <span class="text-muted ms-3">Thanh toán:</span>
                                    <span class="badge {{ $payCls }}">{{ $payTxt }}</span>
                                </div>

                                <div class="small text-muted mb-3">
                                    Phí ship: <strong>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</strong> ·
                                    Tổng tiền: <strong
                                        class="text-primary">{{ number_format($order->final_amount, 0, ',', '.') }}đ</strong>
                                </div>

                                {{-- Stepper 4 cột (Bootstrap list-group ngang) --}}
                                <ul class="list-group list-group-horizontal-sm mb-3">
                                    @foreach ($steps as $s)
                                        @php
                                            $active = $isActive($s['key']);
                                            $cls = $ctxClass($s['key'], $canon);
                                            $icon =
                                                $canon === 'cancelled' && $s['key'] === 'cancelled'
                                                    ? 'fa-times-circle'
                                                    : ($active
                                                        ? 'fa-check-circle'
                                                        : 'fa-circle-o');
                                        @endphp
                                        <li
                                            class="list-group-item d-flex align-items-center flex-fill {{ $cls }}">
                                            <i class="fa {{ $icon }} me-2"></i>
                                            <span class="small">{{ $s['label'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Cập nhật nhanh: ẩn hoàn toàn khi đã giao/đã hủy --}}
                                @if (!$isLocked)
                                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}"
                                        class="d-flex align-items-center gap-2">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm w-auto">
                                            @foreach ($allowedNext as $st)
                                                <option value="{{ $st }}" @selected(old('status') === $st)>
                                                    {{ $labelStatus[$st] ?? ucfirst($st) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Cập nhật</button>
                                    </form>
                                @else
                                    <div class="alert alert-light border d-flex align-items-center p-2 mb-0">
                                        <i
                                            class="fa {{ $canon === 'cancelled' ? 'fa-times-circle text-danger' : 'fa-check-circle text-success' }} me-2"></i>
                                        <span class="small">Đơn đang ở trạng thái cuối:
                                            <strong>{{ $labelStatus[$canon] }}</strong>. Không thể cập nhật thêm.</span>
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

                                                // Lấy chuỗi phân loại (nếu có)
                                                $variantAttributes =
                                                    $variant && $variant->attributeValues
                                                        ? $variant->attributeValues->pluck('value')->join(', ')
                                                        : null;

                                                // Lấy ảnh: ưu tiên image_main, sau đó tới ảnh phụ, cuối cùng là placeholder
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
                                                        <div class="mb-1"><i class="fa fa-info-circle"></i></div>
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
                                                <th class="text-end">{{ number_format($subTotal, 0, ',', '.') }}đ</th>
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

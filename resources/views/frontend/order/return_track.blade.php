@extends('frontend.layouts.app')

@section('title', 'Theo dõi hoàn tiền')

@section('content')
    <div class="container py-4" style="max-width: 820px;">
        <h3 class="mb-2">Theo dõi hoàn tiền</h3>
        <p class="text-muted mb-4">
            Đơn hàng: <strong>#{{ $order->code ?? $order->id }}</strong>
            <br>
            <small>
                Ngày đặt:
                {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}
            </small>
            <br>
            Trạng thái đơn:
            <span class="fw-semibold">
                {{ $order->status_label ?? $order->order_status }}
            </span>
        </p>

        @php
            $statusMap = [
                0 => ['label' => 'Chờ xử lý', 'class' => 'secondary'],
                1 => ['label' => 'Đã duyệt', 'class' => 'primary'],
                2 => ['label' => 'Đã từ chối', 'class' => 'danger'],
                3 => ['label' => 'Đang hoàn tiền', 'class' => 'warning'],
                4 => ['label' => 'Hoàn tất', 'class' => 'success'],
                5 => ['label' => 'Chờ bạn xác nhận', 'class' => 'info'],
            ];
            $status = $statusMap[$return->status] ?? ['label' => 'Không xác định', 'class' => 'dark'];
        @endphp

        <!-- CARD TRẠNG THÁI -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Trạng thái hoàn tiền</h5>
                    <span class="badge bg-{{ $status['class'] }} fs-6">
                        {{ $status['label'] }}
                    </span>
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="mb-1 text-muted">Số tiền hoàn</p>
                            <h5 class="text-success mb-0">
                                {{ number_format($return->refund_amount ?? 0, 0, ',', '.') }}₫
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="mb-1 text-muted">Giá trị đơn</p>
                            <h5 class="mb-0">
                                {{ number_format($order->final_amount ?? ($order->total_amount ?? 0), 0, ',', '.') }}₫
                            </h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="mb-1 text-muted">Phương thức hoàn</p>
                            <strong>
                                {{ $return->refund_method === 'wallet' ? 'Hoàn tiền vào ví nội bộ' : 'Hoàn tiền thủ công' }}
                            </strong>
                        </div>

                    </div>
                    @if ($return->refund_method === 'manual' && $return->refund_account_number)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="mb-1 text-muted">Số tài khoản nhận tiền hoàn</p>
                                <strong>{{ $return->refund_account_number }}</strong>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="mb-1 text-muted">Người xử lý</p>
                            <strong>{{ $return->approved_by_name ?? 'Đang cập nhật' }}</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="mb-1 text-muted">Thời điểm xử lý</p>
                            <strong>
                                {{ $return->decided_at ? $return->decided_at->format('d/m/Y H:i') : 'Chưa xử lý' }}
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- GHI CHÚ TRẠNG THÁI --}}
                @if ($return->status == 5)
                    <div class="alert alert-info mt-4 mb-0">
                        💡 Hệ thống đã hoàn tiền. Vui lòng kiểm tra và xác nhận bạn đã nhận được tiền.
                    </div>
                @endif

                @if ($return->status == 2)
                    <div class="alert alert-danger mt-4 mb-0">
                        ❌ Yêu cầu hoàn tiền đã bị từ chối. Nếu có thắc mắc, vui lòng liên hệ bộ phận hỗ trợ.
                    </div>
                @endif

            </div>
        </div>

        <!-- ẢNH CHỨNG MINH -->
        @if ($return->refund_proof_image)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Chứng từ hoàn tiền</h5>

                    <a href="{{ asset('storage/' . $return->refund_proof_image) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary mb-3">
                        Xem ảnh gốc
                    </a>

                    <div class="text-center">
                        <img src="{{ asset('storage/' . $return->refund_proof_image) }}" class="img-fluid rounded border"
                            style="max-height: 360px;">
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between">
            <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">
                ← Quay lại danh sách đơn hàng
            </a>
        </div>
    </div>
@endsection

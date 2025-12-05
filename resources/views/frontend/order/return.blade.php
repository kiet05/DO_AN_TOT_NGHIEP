@extends('frontend.layouts.app')

@section('title', 'Yêu cầu trả hàng / hoàn tiền #' . ($order->code ?? $order->id))

@section('content')
    <div class="return-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-3">Yêu cầu trả hàng / hoàn tiền</h3>

                            <p class="text-muted mb-4 small">
                                Mã đơn:
                                <strong>{{ $order->code ?? 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong><br>
                                Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}<br>
                            </p>

                            <form id="return-form" action="{{ route('order.return', $order) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                {{-- 1. Thông tin yêu cầu --}}
                                <div class="return-section mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="section-dot me-2"></span>
                                        <h6 class="mb-0">Thông tin yêu cầu</h6>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        Vui lòng chọn loại yêu cầu để shop xử lý đúng mong muốn của bạn.
                                    </p>

                                    @php
                                        $actions = [
                                            'refund_full' => 'Hoàn tiền toàn bộ đơn hàng',
                                            'refund_partial' => 'Hoàn tiền một phần (một vài sản phẩm)',
                                            'exchange_product' => 'Đổi sang sản phẩm khác',
                                            'exchange_variant' => 'Đổi size / màu',
                                        ];
                                    @endphp


                                    <div class="row g-2">
                                        @foreach ($actions as $key => $label)
                                            <div class="col-md-6">
                                                <div class="form-check action-pill">
                                                    <input class="form-check-input js-return-action" type="radio"
                                                        name="return_action" id="return_action_{{ $key }}"
                                                        value="{{ $key }}" data-label="{{ $label }}">
                                                    <label class="form-check-label" for="return_action_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- 2. Lý do trả hàng / hoàn tiền --}}
                                    <div class="return-section mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="section-dot me-2"></span>
                                            <h6 class="mb-0">
                                                Lý do trả hàng / hoàn tiền <span class="text-danger">*</span>
                                            </h6>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            Chọn một hoặc nhiều lý do bên dưới, hoặc ghi rõ hơn ở phần “Mô tả chi tiết”.
                                        </p>

                                        @php
                                            $reasons = [
                                                'Sản phẩm bị lỗi / hư hỏng',
                                                'Sản phẩm bị bể vỡ / móp méo khi vận chuyển',
                                                'Giao sai sản phẩm so với đơn đặt hàng',
                                                'Thiếu sản phẩm / thiếu phụ kiện',
                                                'Sản phẩm khác mô tả / hình ảnh',
                                                'Chất lượng không như mong đợi',
                                                'Nhầm size / màu / mẫu mã',
                                            ];
                                        @endphp

                                        <div class="reason-list mb-3">
                                            @foreach ($reasons as $idx => $reason)
                                                <div class="form-check reason-item">
                                                    <input class="form-check-input js-return-reason" type="checkbox"
                                                        value="{{ $reason }}" id="reason_{{ $idx }}">
                                                    <label class="form-check-label" for="reason_{{ $idx }}">
                                                        {{ $reason }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            <div class="form-check reason-item mt-2">
                                                <input class="form-check-input js-reason-other-toggle" type="checkbox"
                                                    id="reason_other_toggle">
                                                <label class="form-check-label" for="reason_other_toggle">
                                                    Lý do khác
                                                </label>
                                            </div>

                                            <div class="mt-2 other-reason-wrapper d-none">
                                                <textarea class="form-control form-control-sm js-other-reason" rows="3" placeholder="Nhập lý do khác của bạn..."></textarea>
                                            </div>
                                        </div>

                                        {{-- Mô tả chi tiết thêm --}}
                                        <label class="form-label small fw-semibold mb-1">
                                            Mô tả chi tiết tình trạng sản phẩm
                                        </label>
                                        <textarea class="form-control form-control-sm js-detail-note" rows="3"
                                            placeholder="Ví dụ: Hộp bị móp 1 góc, chai bên trong bị nứt nhẹ..."></textarea>

                                        {{-- input ẩn: backend vẫn nhận return_reason như cũ --}}
                                        <input type="hidden" name="return_reason" id="return_reason_input"
                                            value="{{ old('return_reason') }}">

                                        @error('return_reason')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- 3. Ảnh / chứng từ --}}
                                    <div class="return-section mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="section-dot me-2"></span>
                                            <h6 class="mb-0">
                                                Ảnh minh chứng <small class="text-muted fw-normal">(khuyến khích)</small>
                                            </h6>
                                        </div>
                                        <p class="text-muted small mb-2">
                                            Bạn vui lòng chụp rõ sản phẩm, lỗi gặp phải, bao bì bên ngoài hoặc hóa đơn để
                                            shop
                                            hỗ trợ nhanh hơn.
                                        </p>

                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-7">
                                                <input type="file" name="return_image"
                                                    class="form-control @error('return_image') is-invalid @enderror"
                                                    accept="image/*">
                                                <small class="text-muted d-block mt-1">
                                                    Dung lượng tối đa 2MB, định dạng jpg / png / webp.
                                                </small>
                                                @error('return_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-5">
                                                <div class="photo-hint small text-muted">
                                                    Gợi ý:
                                                    <ul class="mb-0 ps-3">
                                                        <li>Toàn cảnh sản phẩm</li>
                                                        <li>Vị trí lỗi cận cảnh</li>
                                                        <li>Vỏ hộp / bao bì bên ngoài</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 4. Thông tin nhận tiền hoàn (chỉ hiện nếu đơn COD) --}}
                                    @php
                                        // Tuỳ field thực tế của bạn: payment_method / payment_method_code / ...
                                        $methodRaw = strtolower(
                                            (string) ($order->payment_method ??
                                                ($order->payment_method_code ?? ($order->payment_method_slug ?? ''))),
                                        );
                                        $isCod = in_array($methodRaw, [
                                            'cod',
                                            'cash_on_delivery',
                                            'thanh_toan_khi_nhan_hang',
                                        ]);
                                    @endphp

                                    @if ($isCod)
                                        <div class="return-section mb-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="section-dot me-2"></span>
                                                <h6 class="mb-0">
                                                    Thông tin nhận tiền hoàn (COD)
                                                </h6>
                                            </div>
                                            <p class="text-muted small mb-2">
                                                Vui lòng nhập số tài khoản ngân hàng mà bạn muốn nhận tiền hoàn.
                                            </p>

                                            <input type="text" name="refund_account_number"
                                                class="form-control form-control-sm @error('refund_account_number') is-invalid @enderror"
                                                value="{{ old('refund_account_number') }}"
                                                placeholder="Ví dụ: 0123456789 - MB Bank - NGUYEN VAN A">

                                            @error('refund_account_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror

                                            <small class="text-muted d-block mt-1">
                                                Nếu bạn không cung cấp, CSKH sẽ liên hệ lại để xác nhận phương thức hoàn
                                                tiền.
                                            </small>
                                        </div>
                                    @endif

                                    {{-- 5. Ghi chú cho shop (không bắt buộc) --}}
                                    <div class="return-section mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="section-dot me-2"></span>
                                            <h6 class="mb-0">Ghi chú thêm cho shop</h6>
                                        </div>
                                        <textarea class="form-control form-control-sm js-extra-note" rows="2"
                                            placeholder="Ví dụ: Muốn đổi sang size M, xin hỗ trợ nhận hàng tại giờ hành chính..."></textarea>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <button type="submit" class="btn btn-warning px-4">
                                            Gửi yêu cầu
                                        </button>
                                        <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">
                                            Quay lại danh sách
                                        </a>
                                    </div>

                                    <p class="text-muted small mt-3">
                                        Sau khi gửi yêu cầu, bộ phận CSKH sẽ liên hệ lại để xác nhận thông tin và hướng dẫn
                                        bạn
                                        quy trình giao nhận sản phẩm trả hàng / hoàn tiền.
                                    </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS nhỏ cho form --}}
    <style>
        .return-page {
            background-color: #f7f7f9;
            min-height: 70vh;
        }

        .return-section {
            border-radius: 0.75rem;
            border: 1px solid #ececf1;
            background-color: #fff;
            padding: 1rem 1.1rem;
        }

        .section-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: linear-gradient(135deg, #ffb74d, #ff7043);
        }

        .reason-list {
            border-radius: 0.5rem;
            border: 1px dashed #dedee5;
            padding: 0.75rem 0.9rem;
            background-color: #fdfdfd;
        }

        .reason-item+.reason-item {
            margin-top: 0.35rem;
        }

        .reason-item .form-check-input,
        .reason-item .form-check-label {
            cursor: pointer;
            font-size: 0.92rem;
        }

        .action-pill {
            border-radius: 999px;
            border: 1px solid #e0e0e7;
            padding: 0.35rem 0.75rem;
            background-color: #fafafa;
            transition: all 0.15s ease;
            font-size: 0.9rem;
        }

        .action-pill .form-check-input {
            cursor: pointer;
        }

        .action-pill .form-check-label {
            cursor: pointer;
            width: 100%;
        }

        .action-pill:hover {
            border-color: #ffc107;
            background-color: #fff8e1;
        }

        .action-pill input:checked+label {
            font-weight: 600;
            color: #c47f00;
        }

        .other-reason-wrapper textarea,
        .js-detail-note,
        .js-extra-note {
            font-size: 0.9rem;
        }

        .photo-hint ul li {
            margin-bottom: 0.1rem;
        }
    </style>

    {{-- JS gom dữ liệu về return_reason --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const actionRadios = document.querySelectorAll('.js-return-action');
            const reasonCheckbox = document.querySelectorAll('.js-return-reason');
            const otherToggle = document.querySelector('.js-reason-other-toggle');
            const otherWrapper = document.querySelector('.other-reason-wrapper');
            const otherTextarea = document.querySelector('.js-other-reason');
            const detailNote = document.querySelector('.js-detail-note');
            const extraNote = document.querySelector('.js-extra-note');
            const hiddenInput = document.getElementById('return_reason_input');
            const form = document.getElementById('return-form');

            function buildReasonText() {
                const parts = [];

                const chosenAction = Array.from(actionRadios).find(r => r.checked);
                if (chosenAction) {
                    parts.push('Hình thức xử lý: ' + chosenAction.value);
                }

                const reasons = Array.from(reasonCheckbox)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                if (reasons.length > 0) {
                    parts.push('Lý do: ' + reasons.join('; '));
                }

                const otherText = otherTextarea && otherTextarea.value.trim();
                if (otherText) {
                    parts.push('Lý do khác: ' + otherText);
                }

                const detailText = detailNote && detailNote.value.trim();
                if (detailText) {
                    parts.push('Mô tả chi tiết: ' + detailText);
                }

                const extraText = extraNote && extraNote.value.trim();
                if (extraText) {
                    parts.push('Ghi chú cho shop: ' + extraText);
                }

                hiddenInput.value = parts.join(' | ');
            }

            reasonCheckbox.forEach(cb => cb.addEventListener('change', buildReasonText));
            actionRadios.forEach(r => r.addEventListener('change', buildReasonText));
            if (detailNote) detailNote.addEventListener('input', buildReasonText);
            if (extraNote) extraNote.addEventListener('input', buildReasonText);
            if (otherTextarea) otherTextarea.addEventListener('input', buildReasonText);

            if (otherToggle) {
                otherToggle.addEventListener('change', function() {
                    if (this.checked) {
                        otherWrapper.classList.remove('d-none');
                        otherTextarea && otherTextarea.focus();
                    } else {
                        otherWrapper.classList.add('d-none');
                        if (otherTextarea) {
                            otherTextarea.value = '';
                        }
                    }
                    buildReasonText();
                });
            }

            form.addEventListener('submit', function(e) {
                buildReasonText();

                if (!hiddenInput.value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một lý do hoặc nhập mô tả chi tiết để gửi yêu cầu.');
                }
            });
        });
    </script>
@endsection

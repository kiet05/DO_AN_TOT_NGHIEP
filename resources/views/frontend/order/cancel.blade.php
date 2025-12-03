@extends('frontend.layouts.app')

@section('title', 'Hủy đơn hàng #' . ($order->code ?? $order->id))

@section('content')
    <div class="cancel-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="mb-3">Hủy đơn hàng</h2>
                            <p class="text-muted mb-4">
                                Mã đơn:
                                <strong>{{ $order->code ?? 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong><br>
                                Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}
                            </p>

                            <form id="cancel-order-form" action="{{ route('order.cancel', $order) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label d-block">
                                        Lý do hủy đơn <span class="text-danger">*</span>
                                    </label>

                                    @php
                                        $reasons = [
                                            'Tôi không còn nhu cầu mua nữa',
                                            'Tôi đặt nhầm sản phẩm hoặc trùng đơn',
                                            'Muốn thay đổi địa chỉ hoặc thông tin nhận hàng',
                                            'Muốn thay đổi phương thức thanh toán',
                                            'Thời gian giao hàng dự kiến quá lâu',
                                            'Tìm được sản phẩm tương tự với mức giá tốt hơn',
                                        ];
                                    @endphp

                                    <div class="reason-list">
                                        @foreach ($reasons as $idx => $reason)
                                            <div class="form-check reason-item">
                                                <input class="form-check-input js-cancel-reason" type="checkbox"
                                                    value="{{ $reason }}" id="reason_{{ $idx }}">
                                                <label class="form-check-label" for="reason_{{ $idx }}">
                                                    {{ $reason }}
                                                </label>
                                            </div>
                                        @endforeach

                                        <div class="form-check reason-item mt-2">
                                            <input class="form-check-input js-cancel-reason-other-toggle" type="checkbox"
                                                id="reason_other_toggle">
                                            <label class="form-check-label" for="reason_other_toggle">
                                                Lý do khác
                                            </label>
                                        </div>

                                        <div class="mt-2 other-reason-wrapper d-none">
                                            <textarea class="form-control js-other-reason" rows="3" placeholder="Nhập lý do khác của bạn..."></textarea>
                                        </div>
                                    </div>

                                    {{-- input ẩn dùng để gửi lên server, giữ nguyên tên cancel_reason --}}
                                    <input type="hidden" name="cancel_reason" id="cancel_reason_input"
                                        value="{{ old('cancel_reason') }}">

                                    @error('cancel_reason')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-danger px-4">
                                        Xác nhận hủy đơn
                                    </button>
                                    <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">
                                        Quay lại danh sách
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <p class="text-muted small text-center mt-3">
                        Nếu bạn gặp khó khăn khi đặt hàng, hãy liên hệ bộ phận hỗ trợ để được tư vấn thêm.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cancel-page {
            background-color: #f7f7f9;
            min-height: 60vh;
        }

        .reason-list {
            border-radius: 0.5rem;
            border: 1px solid #e5e5ea;
            padding: 0.75rem 1rem;
            background-color: #fff;
        }

        .reason-item+.reason-item {
            margin-top: 0.35rem;
        }

        .reason-item .form-check-input {
            cursor: pointer;
        }

        .reason-item .form-check-label {
            cursor: pointer;
            font-size: 0.95rem;
        }

        .other-reason-wrapper textarea {
            font-size: 0.95rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reasonCheckboxes = document.querySelectorAll('.js-cancel-reason');
            const otherToggle = document.querySelector('.js-cancel-reason-other-toggle');
            const otherWrapper = document.querySelector('.other-reason-wrapper');
            const otherTextarea = document.querySelector('.js-other-reason');
            const hiddenInput = document.getElementById('cancel_reason_input');
            const form = document.getElementById('cancel-order-form');

            function updateHiddenInput() {
                const checkedValues = Array.from(reasonCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                const otherText = otherTextarea && otherTextarea.value.trim();

                if (otherText) {
                    checkedValues.push('Khác: ' + otherText);
                }

                hiddenInput.value = checkedValues.join('; ');
            }

            reasonCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateHiddenInput);
            });

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
                    updateHiddenInput();
                });
            }

            if (otherTextarea) {
                otherTextarea.addEventListener('input', updateHiddenInput);
            }

            form.addEventListener('submit', function(e) {
                updateHiddenInput();

                if (!hiddenInput.value.trim()) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một lý do hủy đơn hoặc nhập lý do khác.');
                }
            });
        });
    </script>
@endsection

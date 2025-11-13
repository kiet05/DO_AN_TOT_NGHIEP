@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">

                            {{-- Breadcrumb + nút quay lại --}}
                            <div class="row mg-top-30">
                                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">
                                            Yêu cầu hỗ trợ #{{ $contact->id }}
                                        </h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                            <li><a href="{{ route('admin.contacts.index') }}">Liên hệ</a></li>
                                            <li class="active"><a href="#">Chi tiết</a></li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">
                                        &larr; Quay lại danh sách
                                    </a>
                                </div>
                            </div>

                            <div class="sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">
                                                    Chi tiết yêu cầu hỗ trợ
                                                    <span class="text-muted">#{{ $contact->id }}</span>
                                                </h5>
                                                <span>
                                                    @if ($contact->status === 'new')
                                                        <span class="badge bg-danger">Mới</span>
                                                    @else
                                                        <span class="badge bg-secondary">Đã đọc</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <strong>Họ tên:</strong>
                                                    <div>{{ $contact->name }}</div>
                                                </div>

                                                <div class="mb-2">
                                                    <strong>Email:</strong>
                                                    <div>{{ $contact->email }}</div>
                                                </div>

                                                <div class="mb-2">
                                                    <strong>Số điện thoại:</strong>
                                                    <div>{{ $contact->phone ?: '—' }}</div>
                                                </div>

                                                <div class="mb-2">
                                                    <strong>Tiêu đề:</strong>
                                                    <div>{{ $contact->subject ?: 'Không có tiêu đề' }}</div>
                                                </div>

                                                <div class="mb-3">
                                                    <strong>Thời gian gửi:</strong>
                                                    <div>{{ $contact->created_at?->format('d/m/Y H:i') }}</div>
                                                </div>

                                                <hr>

                                                <div>
                                                    <strong>Nội dung:</strong>
                                                    <p class="mt-2" style="white-space: pre-line;">
                                                        {!! nl2br(e($contact->message)) !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Có thể thêm cột phải cho ghi chú nội bộ sau này --}}
                                    <div class="col-lg-4">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header">
                                                <h6 class="mb-0">Thông tin thêm</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-1">
                                                    <strong>ID:</strong> {{ $contact->id }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Trạng thái:</strong>
                                                    @if ($contact->status === 'new')
                                                        <span class="badge bg-danger">Mới</span>
                                                    @else
                                                        <span class="badge bg-secondary">Đã đọc</span>
                                                    @endif
                                                </p>
                                                <p class="mb-0 text-muted small">
                                                    Dùng khu vực này để thêm ghi chú nội bộ, lịch sử xử lý,
                                                    phân công nhân viên, v.v. (nếu phát triển thêm).
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> {{-- .sherah-page-inner --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

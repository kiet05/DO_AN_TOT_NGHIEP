@extends('layouts.admin.master')

@section('content')
    @php
        $labels = [0 => 'Chờ duyệt', 1 => 'Đã duyệt', 2 => 'Từ chối', 3 => 'Đang hoàn tiền', 4 => 'Hoàn tất'];
        $badges = [0 => 'secondary', 1 => 'primary', 2 => 'danger', 3 => 'warning', 4 => 'success'];
    @endphp
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Sản phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="/">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.products.index') }}">Sản phẩm</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            Yêu cầu #{{ $ret->id }} · Đơn #{{ $ret->order_id }}
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <span
                                                    class="badge text-bg-{{ $badges[$ret->status] ?? 'secondary' }}">{{ $labels[$ret->status] ?? $ret->status }}</span>
                                            </div>
                                            <div class="mb-2"><b>Khách:</b>
                                                {{ $ret->user->full_name ?? ($ret->user->name ?? 'User ' . $ret->user_id) }}
                                                ·
                                                {{ $ret->user->email ?? '' }}</div>
                                            <div class="mb-2"><b>Lý do:</b> {{ $ret->reason }}</div>
                                            @if ($ret->proof_image)
                                                <div class="mb-3">
                                                    <a href="{{ $ret->proof_image }}" target="_blank"
                                                        class="btn btn-sm btn-outline-secondary">Ảnh minh chứng</a>
                                                </div>
                                            @endif
                                            @if (is_array($ret->evidence_urls) && count($ret->evidence_urls))
                                                <div class="d-flex gap-2 flex-wrap mb-3">
                                                    @foreach ($ret->evidence_urls as $u)
                                                        <a href="{{ $u }}" target="_blank"
                                                            class="btn btn-sm btn-outline-secondary">Ảnh</a>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <h6 class="mt-3 mb-2">Sản phẩm</h6>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Order Item</th>
                                                        <th>Số lượng</th>
                                                        <th>Ghi chú</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($ret->items as $it)
                                                        <tr>
                                                            <td>#{{ $it->order_item_id }}</td>
                                                            <td>{{ $it->quantity }}</td>
                                                            <td>{{ $it->note }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-muted">Không có dòng sản phẩm
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <div class="form-control bg-light">
                                                        <div><b>Số tiền hoàn:</b>
                                                            {{ number_format($ret->refund_amount, 0, ',', '.') }} đ</div>
                                                        <div><b>Phương thức:</b> {{ $ret->refund_method ?? '-' }}</div>
                                                        <div><b>Người duyệt:</b>
                                                            {{ $ret->approver->full_name ?? ($ret->approver->name ?? '-') }}
                                                        </div>
                                                        <div><b>Thời điểm:</b>
                                                            {{ optional($ret->decided_at)->format('d/m/Y H:i') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">Xử lý</div>
                                        <div class="card-body d-grid gap-2">
                                            @if ($ret->status === 0)
                                                <form method="post"
                                                    action="{{ route('admin.returns.approve', $ret->id) }}"
                                                    class="d-grid gap-2">
                                                    @csrf
                                                    <input type="number" name="refund_amount" min="0" step="1000"
                                                        class="form-control" placeholder="Số tiền hoàn"
                                                        value="{{ $ret->refund_amount }}">
                                                    <select name="refund_method" class="form-select">
                                                        <option value="wallet" @selected($ret->refund_method === 'wallet')>Hoàn về ví
                                                        </option>
                                                        <option value="manual" @selected($ret->refund_method === 'manual')>Hoàn thủ công
                                                        </option>
                                                    </select>
                                                    <button class="btn btn-primary">Duyệt</button>
                                                </form>
                                                <form method="post"
                                                    action="{{ route('admin.returns.reject', $ret->id) }}">
                                                    @csrf
                                                    <button class="btn btn-outline-danger w-100 mt-2">Từ chối</button>
                                                </form>
                                            @endif

                                            @if (($ret->status === 1 || $ret->status === 3) && $ret->refund_method === 'wallet')
                                                <form method="post"
                                                    action="{{ route('admin.returns.refund.auto', $ret->id) }}">
                                                    @csrf
                                                    <button class="btn btn-success w-100">Hoàn tiền vào ví</button>
                                                </form>
                                            @endif

                                            @if ($ret->status === 1)
                                                <form method="post"
                                                    action="{{ route('admin.returns.refunding', $ret->id) }}">
                                                    @csrf
                                                    <button class="btn btn-warning w-100 mt-2">Đang hoàn tiền</button>
                                                </form>
                                            @endif

                                            @if (($ret->status === 1 || $ret->status === 3) && $ret->refund_method === 'manual')
                                                <form method="post"
                                                    action="{{ route('admin.returns.refund.manual', $ret->id) }}">
                                                    @csrf
                                                    <button class="btn btn-success w-100">Đánh dấu hoàn tất</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
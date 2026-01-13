@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Mã khuyến mãi</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="/">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.vouchers.index') }}">Mã khuyến
                                                    mãi</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Danh sách mã khuyến mãi</h4>
                                <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">+ Thêm mã</a>
                            </div>

                            <form class="mb-3" method="GET">
                                <div class="input-group" style="max-width: 360px;">
                                    <input type="text" name="keyword" class="form-control"
                                        value="{{ request('keyword') }}" placeholder="Tìm theo mã / tên...">
                                    <button class="btn btn-outline-secondary">Tìm</button>
                                </div>
                            </form>

                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Tên</th>
                                        <th>Kiểu</th>
                                        <th>Giá trị</th>
                                        <th>Áp dụng</th>
                                        <th>Trạng thái</th>

                                        <th>Thời gian</th>
                                        <th>Lượt dùng</th>
                                        <th width="140">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vouchers as $item)
                                        <tr>
                                            <td><strong>{{ $item->code }}</strong></td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->type == 'percent' ? 'Giảm %' : 'Giảm tiền' }}</td>
                                            <td>
                                                @if ($item->type === 'percent')
                                                    {{ rtrim(rtrim(number_format($item->value, 2), '0'), '.') }}%
                                                    @if ($item->max_discount)
                                                        <small class="text-muted d-block">
                                                            Tối đa: {{ number_format($item->max_discount) }}đ
                                                        </small>
                                                    @endif
                                                @elseif ($item->type === 'fixed')
                                                    {{ number_format($item->value) }}đ
                                                @endif
                                            </td>

                                            <td>
                                                @if ($item->apply_type === 'all')
                                                    Tất cả
                                                @endif
                                                @if ($item->apply_type === 'products')
                                                    Sản phẩm chọn
                                                @endif
                                                @if ($item->apply_type === 'categories')
                                                    Danh mục chọn
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->is_active)
                                                    <span class="badge bg-success">Còn hiệu lực</span>
                                                @else
                                                    <span class="badge bg-secondary">Vô hiệu</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->start_at)
                                                    {{ $item->start_at->format('d/m/Y H:i') }}
                                                @endif
                                                -
                                                @if ($item->end_at)
                                                    {{ $item->end_at->format('d/m/Y H:i') }}
                                                @else
                                                    Không giới hạn
                                                @endif
                                            </td>
                                            <td>
                                                {{ $item->used_count }}/{{ $item->usage_limit ?? '∞' }}
                                            </td>
                                            <td>

                                                @if ($item->used_count == 0)
                                                    <a href="{{ route('admin.vouchers.edit', $item) }}"
                                                        class="btn btn-sm btn-warning">Sửa</a>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled
                                                        title="Mã đã được áp dụng, không thể sửa">Sửa</button>
                                                @endif


                                                <a href="{{ route('admin.vouchers.report', $item) }}"
                                                    class="btn btn-sm btn-info">BC</a>

                                                <!-- Nút xóa -->
                                                <form action="{{ route('admin.vouchers.destroy', $item) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Xóa mã này?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Xóa</button>
                                                </form>
                                                <!-- Nút vô hiệu hóa / kích hoạt -->
                                                <form action="{{ route('admin.vouchers.toggle', $item) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $item->is_active ? 'btn-danger' : 'btn-secondary' }}">
                                                        {{ $item->is_active ? 'Vô hiệu' : 'Kích hoạt' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                @if (method_exists($vouchers, 'links'))
                                    <div class="card-footer bg-white d-flex justify-content-end">
                                        {{ $vouchers->appends(request()->all())->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

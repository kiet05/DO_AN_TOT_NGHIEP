@extends('layouts.admin.master')

@section('content')

    @styled
        <style>
            .sherah-table__action {
        display: flex;
        gap: 8px; /* Khoảng cách giữa các nút */
        justify-content: flex-start; /* Căn trái */
        align-items: center; /* Căn giữa theo chiều dọc */
    }

    .sherah-table__action .btn {
        margin: 0;
        flex-grow: 1; /* Các nút sẽ có kích thước đều nhau */
        text-align: center; /* Căn giữa văn bản trong nút */
    }
        </style>
    @endstyled

    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Danh sách mã khuyến mãi</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Trang chủ</a></li>
                                            <li class="active"><a href="{{ route('admin.vouchers.index') }}">Danh sách mã
                                                    khuyến mãi</a></li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('admin.vouchers.create') }}" class="sherah-btn sherah-gbcolor">Thêm mã
                                        mới</a>
                                </div>
                            </div>

                            <!-- Bộ lọc tìm kiếm -->
                            <form class="mb-4" method="GET">
                                <div class="input-group d-flex align-items-center" style="max-width: 400px;">
                                    <input type="text" name="keyword" class="form-control"
                                        value="{{ request('keyword') }}" placeholder="Tìm theo mã / tên...">
                                    <button class="btn btn-outline-secondary ml-2">Tìm</button>
                                </div>
                            </form>

                            <!-- Bảng mã khuyến mãi -->
                            <div class="sherah-table sherah-page-inner sherah-border sherah-default-bg mg-top-25">
                                <div class="table-responsive">
                                    <table class="sherah-table__main align-middle">
                                        <colgroup>
                                            <col style="width:100px;">
                                            <col style="width:180px;">
                                            <col style="width:120px;">
                                            <col style="width:120px;">
                                            <col style="width:120px;">
                                            <col style="width:150px;">
                                            <col style="width:150px;">
                                            <col style="width:160px;">
                                        </colgroup>

                                        <thead class="sherah-table__head align-middle">
                                            <tr>
                                                <th>Mã</th>
                                                <th>Tên</th>
                                                <th>Kiểu</th>
                                                <th>Giá trị</th>
                                                <th>Áp dụng</th>
                                                <th>Thời gian</th>
                                                <th>Lượt dùng</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sherah-table__body sherah-table__body-v3">
                                            @foreach ($vouchers as $item)
                                                <tr>
                                                    <td><strong>{{ $item->code }}</strong></td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->type == 'percent' ? 'Giảm %' : 'Giảm tiền' }}</td>
                                                    <td>
                                                        {{ $item->value }}{{ $item->type == 'percent' ? '%' : ' đ' }}
                                                        @if ($item->max_discount)
                                                            <small class="text-muted d-block">Tối đa:
                                                                {{ number_format($item->max_discount) }}đ</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($item->apply_type === 'all')
                                                            Tất cả
                                                        @elseif ($item->apply_type === 'products')
                                                            Sản phẩm chọn
                                                        @elseif ($item->apply_type === 'categories')
                                                            Danh mục chọn
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
                                                    <td
                                                        class="sherah-table__action d-flex justify-start items-center gap-2">
                                                        <a href="{{ route('admin.vouchers.edit', $item) }}"
                                                            class="btn btn-sm btn-warning"><i class="fa-solid fa-wrench"></i></a>
                                                        <a href="{{ route('admin.vouchers.report', $item) }}"
                                                            class="btn btn-sm btn-info"><i class="fa-solid fa-eye"></i></a>
                                                        <form action="{{ route('admin.vouchers.destroy', $item) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Xóa mã này?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Phân trang -->
                                <div class="mt-3">
                                    {{ $vouchers->appends(request()->all())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách mã khuyến mãi</h4>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">+ Thêm mã</a>
</div>

<form class="mb-3" method="GET">
    <div class="input-group" style="max-width: 360px;">
        <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Tìm theo mã / tên...">
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
                {{ $item->value }}{{ $item->type == 'percent' ? '%' : ' đ' }}
                @if($item->max_discount)
                    <small class="text-muted d-block">Tối đa: {{ number_format($item->max_discount) }}đ</small>
                @endif
            </td>
            <td>
                @if ($item->apply_type === 'all') Tất cả @endif
                @if ($item->apply_type === 'products') Sản phẩm chọn @endif
                @if ($item->apply_type === 'categories') Danh mục chọn @endif
            </td>
            <td>
                @if($item->start_at) {{ $item->start_at->format('d/m/Y H:i') }} @endif
                -
                @if($item->end_at) {{ $item->end_at->format('d/m/Y H:i') }} @else Không giới hạn @endif
            </td>
            <td>
                {{ $item->used_count }}/{{ $item->usage_limit ?? '∞' }}
            </td>
            <td>
                <a href="{{ route('admin.vouchers.edit', $item) }}" class="btn btn-sm btn-warning">Sửa</a>
                <a href="{{ route('admin.vouchers.report', $item) }}" class="btn btn-sm btn-info">BC</a>
                <form action="{{ route('admin.vouchers.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa mã này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $vouchers->links() }}
@endsection

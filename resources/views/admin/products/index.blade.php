@extends('layouts.admin.master')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold mb-0">Quản lý sản phẩm</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Thêm sản phẩm
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá cơ bản</th>
                            <th>Trạng thái</th>
                            <th>Biến thể</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    @php
                                        $image = $product->images->first()->path ?? null;
                                    @endphp
                                    @if ($image)
                                        <img src="{{ asset('storage/' . $image) }}" width="60" height="60"
                                            class="rounded">
                                    @else
                                        <img src="https://via.placeholder.com/60" class="rounded">
                                    @endif
                                </td>

                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ number_format($product->base_price, 0, ',', '.') }}₫</td>
                                <td>
                                    @php
                                        $badgeClass = match ($product->status) {
                                            'new' => 'bg-success-subtle text-success',
                                            'hot' => 'bg-danger-subtle text-danger',
                                            'sale' => 'bg-warning-subtle text-warning',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-uppercase">
                                        {{ $product->status }}
                                    </span>
                                </td>

                                <td>{{ $product->variants->count() }} biến thể</td>

                                <td class="text-center">
                                    <a href="" class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-cubes"></i>
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa sản phẩm này?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-box fs-4"></i> Chưa có sản phẩm nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection

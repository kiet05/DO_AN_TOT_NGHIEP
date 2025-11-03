@extends('layouts.admin.master')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold mb-0">Quản lý danh mục</h1>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Thêm danh mục
            </a>
        </div>

        {{-- Ô tìm kiếm danh mục --}}
        <form action="{{ route('admin.categories.index') }}" method="GET" class="mb-3 d-flex" style="max-width: 400px;">
            <input type="text" name="keyword" class="form-control me-2" placeholder="Tìm danh mục theo tên..."
                value="{{ request('keyword') }}">
            <button type="submit" class="btn btn-outline-primary">Tìm</button>
        </form>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tên danh mục</th>
                            <th>
                                
                            </th>
                            <th>Số sản phẩm</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="fw-semibold">{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->products_count ?? 0 }}</td>

                                <td class="text-center">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Xóa danh mục này? Các sản phẩm sẽ được chuyển sang “Trống”.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="ti ti-box fs-4"></i> Chưa có danh mục nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $categories->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection

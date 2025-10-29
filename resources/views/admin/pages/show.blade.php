@extends('layouts.app')

@section('title', 'Chi tiết trang - ' . $page->title)

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chi tiết trang: {{ $page->title }}</h5>
            <div>
                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                </a>

                <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger btn-delete">
                        <i class="fa-solid fa-trash"></i> Xóa
                    </button>
                </form>

                <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card-body">
            <p><strong>Mã:</strong> {{ $page->id }}</p>
            <p><strong>Key:</strong> {{ $page->key }}</p>
            <p><strong>Slug:</strong> {{ $page->slug }}</p>
            <p><strong>Trạng thái:</strong> 
                {!! $page->published 
                    ? '<span class="badge bg-success">Đang hiển thị</span>' 
                    : '<span class="badge bg-secondary">Ẩn</span>' !!}
            </p>
            <hr>
            <h6><strong>Nội dung:</strong></h6>
            <div class="border rounded p-3 bg-light">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (confirm('Bạn có chắc muốn xóa trang này không?')) {
                form.submit();
            }
        });
    });
});
</script>
@endpush

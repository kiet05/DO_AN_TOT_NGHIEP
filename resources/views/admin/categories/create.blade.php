@extends('layouts.admin.master')

@section('title', 'Thêm sản phẩm')

@section('content')

    <div class="container py-4">
        <h3 class="fw-bold mb-4 text-success">
            <i class="bi bi-plus-circle me-2"></i> Thêm sản phẩm mới
        </h3>

        ```
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation"
            novalidate>
            @csrf

            {{-- ===== Thông tin cơ bản ===== --}}
            <x-admin.card title="Thông tin sản phẩm" icon="bi bi-box-seam" color="success">
                <div class="row g-3">
                    <x-admin.input col="6" label="Tên sản phẩm" name="name" required
                        placeholder="Nhập tên sản phẩm" />
                    <x-admin.input col="6" label="Giá gốc" name="base_price" type="number" min="0"
                        placeholder="Nhập giá gốc" />
                    <x-admin.textarea col="12" label="Mô tả" name="description" placeholder="Mô tả sản phẩm..." />
                    <x-admin.select col="4" label="Danh mục" name="category_id" :options="$categories"
                        placeholder="-- Chọn danh mục --" />
                    <x-admin.select col="4" label="Thương hiệu" name="brand_id" :options="$brands"
                        placeholder="-- Chọn thương hiệu --" />
                    <x-admin.input col="4" label="Ảnh chính (URL)" name="image_main"
                        placeholder="Nhập URL ảnh chính" />
                </div>
            </x-admin.card>

            {{-- ===== Ảnh phụ ===== --}}
            <x-admin.card title="Ảnh phụ" icon="bi bi-images" color="info">
                <div id="image-list"></div>
                <button type="button" id="add-image" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Thêm ảnh
                </button>
            </x-admin.card>

            {{-- ===== Biến thể sản phẩm ===== --}}
            <x-admin.card title="Biến thể sản phẩm" icon="bi bi-diagram-3" color="success">
                <div class="variant-list" id="variant-list">
                    @include('admin.products.partials.variant', [
                        'index' => 0,
                        'attributes' => $attributes,
                    ])
                </div>
                <button type="button" id="add-variant" class="btn btn-outline-success btn-sm mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Thêm biến thể
                </button>
            </x-admin.card>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-lg btn-success px-4 shadow">
                    <i class="bi bi-save me-2"></i> Thêm sản phẩm
                </button>
            </div>
        </form>
        ```

    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('add-image').addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('d-flex', 'align-items-center', 'mb-2');
            div.innerHTML = `
        <input type="text" name="images[]" class="form-control shadow-sm" placeholder="URL ảnh phụ">
        <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-image">
            <i class="bi bi-trash"></i>
        </button>`;
            document.getElementById('image-list').appendChild(div);
        });

        document.addEventListener('click', e => {
            if (e.target.closest('.remove-image')) e.target.closest('.d-flex').remove();
        });
    </script>
@endpush

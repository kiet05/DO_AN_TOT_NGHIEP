@extends('layouts.admin.master')

@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="container py-4">
        <h3 class="fw-bold mb-4 text-success">
            <i class="bi bi-plus-circle me-2"></i> Thêm sản phẩm mới
        </h3>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation"
            novalidate>
            @csrf

            {{-- ===== Thông tin cơ bản ===== --}}
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header text-white fw-semibold" style="background: linear-gradient(90deg, #28a745, #00c853);">
                    <i class="bi bi-box-seam me-2"></i> Thông tin sản phẩm
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên sản phẩm</label>
                            <input type="text" name="name" class="form-control shadow-sm"
                                placeholder="Nhập tên sản phẩm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giá gốc</label>
                            <input type="number" name="base_price" class="form-control shadow-sm" min="0"
                                placeholder="Nhập giá gốc">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả</label>
                            <textarea name="description" rows="3" class="form-control shadow-sm" placeholder="Mô tả sản phẩm..."></textarea>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Danh mục</label>
                            <select name="category_id" class="form-select shadow-sm">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Thương hiệu</label>
                            <select name="brand_id" class="form-select shadow-sm">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ảnh chính (URL)</label>
                            <input type="text" name="image_main" class="form-control shadow-sm"
                                placeholder="Nhập URL ảnh chính">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Ảnh phụ ===== --}}
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-info text-white fw-semibold">
                    <i class="bi bi-images me-2"></i> Ảnh phụ
                </div>
                <div class="card-body p-4">
                    <div id="image-list"></div>
                    <button type="button" id="add-image" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="bi bi-plus-circle me-1"></i> Thêm ảnh
                    </button>
                </div>
            </div>

            {{-- ===== Biến thể ===== --}}
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-success text-white fw-semibold">
                    <i class="bi bi-diagram-3 me-2"></i> Biến thể sản phẩm
                </div>
                <div class="card-body p-4" id="variant-list">
                    <div class="border rounded p-3 mb-3 variant-item bg-light shadow-sm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" name="variants[0][sku]" class="form-control shadow-sm"
                                    placeholder="SKU">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Giá</label>
                                <input type="number" name="variants[0][price]" class="form-control shadow-sm"
                                    min="0" placeholder="Giá bán">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Số lượng</label>
                                <input type="number" name="variants[0][quantity]" class="form-control shadow-sm"
                                    min="0" placeholder="Số lượng">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="variants[0][status]" class="form-select shadow-sm">
                                    <option value="1" selected>Hiển thị</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>
                        </div>

                        {{-- Thuộc tính --}}
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Thuộc tính</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($attributes as $attribute)
                                    <div>
                                        <strong>{{ $attribute->name }}</strong>
                                        <select name="variants[0][attribute_value_ids][]" class="form-select shadow-sm">
                                            <option value="">-- Chọn --</option>
                                            @foreach ($attribute->values as $value)
                                                <option value="{{ $value->id }}">{{ $value->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-variant" class="btn btn-outline-success btn-sm mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Thêm biến thể
                </button>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-lg btn-success px-4 shadow">
                    <i class="bi bi-save me-2"></i> Thêm sản phẩm
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Thêm ảnh phụ
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

        // Xóa ảnh phụ
        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-image')) {
                e.target.closest('.d-flex').remove();
            }
        });
    </script>
@endpush

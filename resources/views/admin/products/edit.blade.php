@extends('layouts.admin.master')

@section('title', 'Thêm sản phẩm')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <!-- Dashboard Inner -->
                        <div class="sherah-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="sherah-breadcrumb mg-top-30">
                                        <h2 class="sherah-breadcrumb__title">Sửa Sản Phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                            <li><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                                            <li class="active"><a href="profile-info.html">Cập nhật sản phẩm</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="sherah-page-inner sherah-border sherah-basic-page sherah-default-bg mg-top-25 p-0">
                                <form class="sherah-wc__form-main"
                                    action="{{ route('admin.products.update', $product->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-lg-6 col-12">
                                            <!-- Thông tin cơ bản sản phẩm -->
                                            <div class="product-form-box sherah-border mg-top-30">
                                                <h4 class="form-title m-0">Thông tin cơ bản</h4>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Tên sản phẩm</label>
                                                            <div class="form-group__input">
                                                                <input class="sherah-wc__form-input"
                                                                    placeholder="Nhập tên sản phẩm" type="text"
                                                                    name="name" value="{{ old('name', $product->name) }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Giá gốc</label>
                                                            <div class="form-group__input">
                                                                <input class="sherah-wc__form-input"
                                                                    placeholder="Nhập giá gốc" type="number" step="0.01"
                                                                    name="base_price"
                                                                    value="{{ old('base_price', $product->base_price) }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Mô tả sản phẩm</label>
                                                            <div class="form-group__input">
                                                                <textarea class="sherah-wc__form-input" placeholder="Nhập mô tả" name="description">{{ old('description', $product->description) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Danh mục</label>
                                                            <select class="form-group__input" name="category_id" required>
                                                                <option value="">-- Chọn danh mục --</option>
                                                                @foreach ($categories as $category)
                                                                    <option value="{{ $category->id }}"
                                                                        {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Thương hiệu</label>
                                                            <select class="form-group__input" name="brand_id">
                                                                <option value="">-- Chọn thương hiệu --</option>
                                                                @foreach ($brands as $brand)
                                                                    <option value="{{ $brand->id }}"
                                                                        {{ $brand->id == $product->brand_id ? 'selected' : '' }}>
                                                                        {{ $brand->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Giảm giá</label>
                                                            <select class="form-group__input" name="is_on_sale">
                                                                <option value="0">-- không --</option>
                                                                <option value="1">-- giảm giá --</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Mặt hàng mới</label>
                                                            <select class="form-group__input" name="is_new">
                                                                <option value="0">-- không --</option>
                                                                <option value="1">-- Mặt hàng mới --</option>
                                                            </select>
                                                        </div>
                                                    </div> --}}

                                                </div>
                                            </div>
                                            <!-- End Thông tin cơ bản sản phẩm -->
                                        </div>

                                        <div class="col-lg-6 col-12">
                                            <!-- Specification -->
                                            <div class="product-form-box sherah-border mg-top-30">
                                                <h4 class="form-title m-0">Biến thể sản phẩm</h4>

                                                {{-- Chọn thuộc tính để tạo biến thể MỚI --}}
                                                <div class="mt-3">
                                                    <div class="row g-3">
                                                        {{-- KÍCH CỠ --}}
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Kích cỡ áp dụng</label>
                                                                <select id="matrix-sizes" name="matrix_sizes[]"
                                                                    class="form-control" multiple size="1">
                                                                    @foreach ($sizes as $size)
                                                                        <option value="{{ $size->id }}">
                                                                            {{ $size->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- MÀU SẮC --}}
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Màu sắc áp dụng</label>
                                                                <select id="matrix-colors" name="matrix_colors[]"
                                                                    class="form-control" multiple size="1">
                                                                    @foreach ($colors as $color)
                                                                        <option value="{{ $color->id }}">
                                                                            {{ $color->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- CHẤT LIỆU --}}
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Chất liệu áp
                                                                    dụng</label>
                                                                <select id="matrix-materials" name="matrix_materials[]"
                                                                    class="form-control" multiple size="1">
                                                                    @foreach ($materials as $material)
                                                                        <option value="{{ $material->id }}">
                                                                            {{ $material->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-2">
                                                        <button type="button" class="sherah-btn sherah-btn__secondary"
                                                            id="btn-generate-variants">
                                                            Tạo thêm biến thể từ thuộc tính
                                                        </button>
                                                    </div>

                                                    {{-- Bảng tất cả biến thể (cũ + mới) --}}
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered align-middle variants-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Size</th>
                                                                    <th>Màu sắc</th>
                                                                    <th>Chất liệu</th>
                                                                    <th>SKU</th>
                                                                    <th>Số lượng</th>
                                                                    <th>Giá</th>
                                                                    <th>Trạng thái</th>
                                                                    <th>Ảnh hiện tại</th>
                                                                    <th>Ảnh mới</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="variants-body">
                                                                {{-- Biến thể đang có --}}
                                                                @foreach ($product->variants as $index => $variant)
                                                                    @php
                                                                        // Lấy tên theo quan hệ đã load trong controller
                                                                        $sizeNames =
                                                                            $variant->sizes
                                                                                ?->pluck('value')
                                                                                ->join(', ') ?? '';
                                                                        $colorNames =
                                                                            $variant->colors
                                                                                ?->pluck('value')
                                                                                ->join(', ') ?? '';
                                                                        $materialNames =
                                                                            $variant->materials
                                                                                ?->pluck('value')
                                                                                ->join(', ') ?? '';

                                                                        // Lấy list id attribute_value hiện có để gửi lại cho controller khi update
                                                                        $attrIds =
                                                                            $variant->attributes
                                                                                ?->pluck('id')
                                                                                ->toArray() ?? [];
                                                                    @endphp

                                                                    <tr>
                                                                        <td class="variant-size-name">{{ $sizeNames }}
                                                                        </td>
                                                                        <td class="variant-color-name">{{ $colorNames }}
                                                                        </td>
                                                                        <td class="variant-material-name">
                                                                            {{ $materialNames }}</td>

                                                                        {{-- các ô SKU / Qty / Price / Status / Image giữ nguyên như bạn đang có --}}
                                                                        <td>
                                                                            <input type="text"
                                                                                name="variants[{{ $index }}][sku]"
                                                                                class="form-control"
                                                                                value="{{ $variant->sku }}">
                                                                        </td>

                                                                        <td>
                                                                            <input type="number"
                                                                                name="variants[{{ $index }}][quantity]"
                                                                                class="form-control" min="0"
                                                                                value="{{ $variant->quantity }}">
                                                                        </td>

                                                                        <td>
                                                                            <input type="number"
                                                                                name="variants[{{ $index }}][price]"
                                                                                class="form-control" min="0"
                                                                                step="0.01"
                                                                                value="{{ $variant->price }}">
                                                                        </td>

                                                                        <td>
                                                                            <select
                                                                                name="variants[{{ $index }}][status]"
                                                                                class="form-control">
                                                                                <option value="1"
                                                                                    {{ $variant->status ? 'selected' : '' }}>
                                                                                    Hiện</option>
                                                                                <option value="0"
                                                                                    {{ !$variant->status ? 'selected' : '' }}>
                                                                                    Ẩn</option>
                                                                            </select>
                                                                        </td>

                                                                        {{-- Ảnh hiện tại + ảnh mới… (đoạn này giữ nguyên code cũ của bạn) --}}
                                                                        <td>
                                                                            @if ($variant->image_url)
                                                                                <img src="{{ asset('storage/' . $variant->image_url) }}"
                                                                                    alt="Variant Image"
                                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                                            @else
                                                                                <span class="text-muted small">Chưa có
                                                                                    ảnh</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <input type="file"
                                                                                name="variants[{{ $index }}][image]"
                                                                                class="form-control" accept="image/*">
                                                                            {{-- giữ đường dẫn ảnh cũ để dùng nếu không upload ảnh mới --}}
                                                                            <input type="hidden"
                                                                                name="variants[{{ $index }}][old_image]"
                                                                                value="{{ $variant->image }}">
                                                                        </td>

                                                                        {{-- Hidden ID để controller biết đây là biến thể cũ --}}
                                                                        <input type="hidden"
                                                                            name="variants[{{ $index }}][id]"
                                                                            value="{{ $variant->id }}">

                                                                        {{-- Hidden attribute_value_ids để không mất liên kết size/màu/chất liệu --}}
                                                                        @foreach ($attrIds as $attrId)
                                                                            <input type="hidden"
                                                                                name="variants[{{ $index }}][attribute_value_ids][]"
                                                                                value="{{ $attrId }}">
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                {{-- Template cho biến thể MỚI (giống create) --}}
                                                <template id="variant-row-template">
                                                    <tr>
                                                        <td class="variant-size-name"></td>
                                                        <td class="variant-color-name"></td>
                                                        <td class="variant-material-name"></td>

                                                        <td>
                                                            <input type="text" class="form-control variant-sku"
                                                                placeholder="SKU">
                                                        </td>

                                                        <td>
                                                            <input type="number" class="form-control variant-qty"
                                                                min="0" value="0">
                                                        </td>

                                                        <td>
                                                            <input type="number" class="form-control variant-price"
                                                                min="0" step="0.01">
                                                        </td>

                                                        <td>
                                                            <select class="form-control variant-status">
                                                                <option value="1" selected>Hiện</option>
                                                                <option value="0">Ẩn</option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <span class="text-muted small">Mới</span>
                                                        </td>

                                                        <td>
                                                            <input type="file" class="form-control variant-image"
                                                                accept="image/*">
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger btn-remove-variant">
                                                                &times;
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </div>
                                            <!-- End Specification -->

                                        </div>

                                    </div>
                                    <!-- Ảnh chính -->
                                    <div class="product-form-box sherah-border mg-top-30">
                                        <div class="form-group">
                                            <div class="image-upload-group">
                                                <div class="image-upload-group__single image-upload-group__single--upload"
                                                    style="width: 200px; height: 200px; display: inline-block; margin-right: 10px; overflow: hidden;">
                                                    @if ($product->image_main)
                                                        <img src="{{ asset('storage/' . $product->image_main) }}"
                                                            alt="Ảnh chính"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    @endif
                                                </div>

                                                <div class="image-upload-group__single image-upload-group__single--upload"
                                                    style="width: 200px; height: 200px; display: inline-block; overflow: hidden;">
                                                    <input type="file" class="btn-check" name="image_main"
                                                        id="input-img-main" autocomplete="off">
                                                    <label class="image-upload-label" for="input-img-main"
                                                        style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; border: 2px dashed #ccc; cursor: pointer;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="40"
                                                            height="40" viewBox="0 0 91.787 84.116">
                                                            <!-- SVG content -->
                                                        </svg>
                                                        <span style="margin-left: 5px;">Upload ảnh</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ảnh phụ -->
                                    <div class="product-form-box sherah-border mg-top-30">
                                        <div class="form-group">
                                            <div class="image-upload-group">
                                                @foreach ($product->images as $img)
                                                    <div class="image-upload-group__single"
                                                        style="width: 200px; height: 200px; display: inline-block; margin-right: 10px; overflow: hidden;">
                                                        <img src="{{ asset('storage/' . $img->image_url) }}"
                                                            alt="Ảnh sản phẩm"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                @endforeach

                                                <div class="image-upload-group__single image-upload-group__single--upload"
                                                    style="width: 200px; height: 200px; display: inline-block; overflow: hidden;">
                                                    <input type="file" class="btn-check" name="images[]"
                                                        id="input-img-upload" autocomplete="off" multiple>
                                                    <label class="image-upload-label" for="input-img-upload"
                                                        style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; border: 2px dashed #ccc; cursor: pointer;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="40"
                                                            height="40" viewBox="0 0 91.787 84.116">
                                                            <!-- SVG content -->
                                                        </svg>
                                                        <span style="margin-left: 5px;">Upload ảnh</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class=" mg-top-40 sherah-dflex sherah-dflex-gap-30 justify-content-end">
                                        <button type="submit" class="sherah-btn sherah-btn__primary">Sửa sản phẩm
                                        </button>
                                        <button class="sherah-btn sherah-btn__third">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>


            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bắt đầu đếm index từ số biến thể hiện có
            let variantIndex = document.querySelectorAll('#variants-body tr').length;

            function getSelectedOptions(selectId) {
                const select = document.getElementById(selectId);
                if (!select) return [];
                return Array.from(select.selectedOptions).map(function(opt) {
                    return {
                        id: opt.value,
                        name: opt.textContent.trim()
                    };
                });
            }

            function addVariantRow(size, color, material) {
                const tbody = document.getElementById('variants-body');
                const template = document.getElementById('variant-row-template');
                if (!tbody || !template) return;

                const clone = template.content.cloneNode(true);
                const tr = clone.querySelector('tr');

                tr.querySelector('.variant-size-name').textContent = size.name;
                tr.querySelector('.variant-color-name').textContent = color.name;
                tr.querySelector('.variant-material-name').textContent = material.name;

                const skuInput = tr.querySelector('.variant-sku');
                const qtyInput = tr.querySelector('.variant-qty');
                const priceInput = tr.querySelector('.variant-price');
                const statusInput = tr.querySelector('.variant-status');
                const imageInput = tr.querySelector('.variant-image');

                skuInput.name = `variants[${variantIndex}][sku]`;
                qtyInput.name = `variants[${variantIndex}][quantity]`;
                priceInput.name = `variants[${variantIndex}][price]`;
                statusInput.name = `variants[${variantIndex}][status]`;
                imageInput.name = `variants[${variantIndex}][image]`;

                // Gắn attribute_value_ids (size, color, material) cho biến thể mới
                [size, color, material].forEach(function(attr) {
                    if (attr && attr.id) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `variants[${variantIndex}][attribute_value_ids][]`;
                        hidden.value = attr.id;
                        tr.appendChild(hidden);
                    }
                });

                tbody.appendChild(clone);
                variantIndex++;
            }

            const generateBtn = document.getElementById('btn-generate-variants');
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    const sizes = getSelectedOptions('matrix-sizes');
                    const colors = getSelectedOptions('matrix-colors');
                    const materials = getSelectedOptions('matrix-materials');

                    if (!sizes.length || !colors.length || !materials.length) {
                        alert('Vui lòng chọn ít nhất một kích cỡ, một màu sắc và một chất liệu.');
                        return;
                    }

                    sizes.forEach(function(size) {
                        colors.forEach(function(color) {
                            materials.forEach(function(material) {
                                addVariantRow(size, color, material);
                            });
                        });
                    });
                });
            }

            // Xóa dòng biến thể (cả cũ + mới)
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-variant');
                if (btn) {
                    const row = btn.closest('tr');
                    if (row) row.remove();
                }
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        #matrix-sizes,
        #matrix-colors,
        #matrix-materials {
            height: 38px;
            padding-right: 30px;
        }

        .variants-table {
            min-width: 1000px;
        }

        .variants-table th,
        .variants-table td {
            white-space: nowrap;
        }

        .variants-table input.form-control,
        .variants-table select.form-control {
            min-width: 110px;
            height: 32px;
            padding: 2px 6px;
            font-size: 0.85rem;
        }
    </style>
@endpush

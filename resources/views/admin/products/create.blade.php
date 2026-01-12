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
                                        <h2 class="sherah-breadcrumb__title">Thêm sản phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Dashboard</a></li>
                                            <li class="active"><a href="profile-info.html">Thêm sản phẩm</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="sherah-page-inner sherah-border sherah-basic-page sherah-default-bg mg-top-25 p-0">
                                <form class="sherah-wc__form-main" action="{{ route('admin.products.store') }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
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
                                                                    name="name" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Giá gốc</label>
                                                            <div class="form-group__input">
                                                                <input id="base-price" class="sherah-wc__form-input"
                                                                    placeholder="Nhập giá gốc" type="number" step="0.01"
                                                                    name="base_price" required>

                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Mô tả sản phẩm</label>
                                                            <div class="form-group__input">
                                                                <textarea class="sherah-wc__form-input" placeholder="Nhập mô tả" name="description"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label class="sherah-wc__form-label">Danh mục</label>
                                                            <select class="form-group__input" name="category_id" required>
                                                                <option value="">-- Chọn danh mục --</option>
                                                                @foreach ($categories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}</option>
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
                                                                    <option value="{{ $brand->id }}">{{ $brand->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Thông tin cơ bản sản phẩm -->
                                        </div>

                                        <div class="col-lg-6 col-12">
                                            <!-- Organization -->
                                            {{-- <div class="product-form-box sherah-border mg-top-30">
                                                <h4 class="form-title m-0">Tổ chức sản phẩm</h4>

                                                <div class="form-group">
                                                    <label class="sherah-wc__form-label">Thêm danh mục</label>
                                                    <div class="form-group__input d-flex">
                                                        <input class="sherah-wc__form-input" placeholder="Nhập danh mục"
                                                            type="text" name="new_category">
                                                        <button class="sherah-btn__add sherah-btn sherah-btn__secondary"
                                                            type="button">Thêm</button>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="sherah-wc__form-label">Thêm thương hiệu</label>
                                                    <div class="form-group__input d-flex">
                                                        <input class="sherah-wc__form-input" placeholder="Nhập thương hiệu"
                                                            type="text" name="new_brand">
                                                        <button class="sherah-btn__add sherah-btn sherah-btn__secondary"
                                                            type="button">Thêm</button>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="sherah-wc__form-label">Thêm màu sắc</label>
                                                    <div class="form-group__input d-flex">
                                                        <input class="sherah-wc__form-input" placeholder="Nhập màu sắc"
                                                            type="text" name="new_color">
                                                        <button class="sherah-btn__add sherah-btn sherah-btn__secondary"
                                                            type="button">Thêm</button>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="sherah-wc__form-label">Thêm kích thước</label>
                                                    <div class="form-group__input d-flex">
                                                        <input class="sherah-wc__form-input" placeholder="Nhập kích thước"
                                                            type="text" name="new_size">
                                                        <button class="sherah-btn__add sherah-btn sherah-btn__secondary"
                                                            type="button">Thêm</button>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <!-- End Organization -->
                                            <!-- Specification -->
                                            <div class="product-form-box sherah-border mg-top-30">
                                                <h4 class="form-title m-0">Biến thể sản phẩm</h4>

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
                                                                <small class="text-muted">Giữ Ctrl để chọn nhiều
                                                                    size.</small>
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
                                                                <small class="text-muted">Giữ Ctrl để chọn nhiều
                                                                    màu.</small>
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
                                                                <small class="text-muted">Giữ Ctrl để chọn nhiều
                                                                    chất liệu.</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-2">
                                                        <button type="button" class="sherah-btn sherah-btn__secondary"
                                                            id="btn-generate-variants">
                                                            Tạo biến thể từ thuộc tính
                                                        </button>
                                                    </div>

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
                                                                    <th>Ảnh biến thể</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="variants-body">
                                                                {{-- JS sẽ render các dòng ở đây --}}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                {{-- Template 1 dòng biến thể --}}
                                                <template id="variant-row-template">
                                                    <tr>
                                                        <td class="variant-size-name"></td>
                                                        <td class="variant-color-name"></td>
                                                        <td class="variant-material-name"></td>

                                                        <td>
                                                            <input type="text" class="form-control variant-sku"
                                                                placeholder="Để trống để tự sinh SKU">
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
                                                    <!-- Chưa có ảnh chính, chỉ hiển thị ô upload -->
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
                                                        <span style="margin-left: 5px;">Upload ảnh chính</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ảnh phụ -->
                                    <div class="product-form-box sherah-border mg-top-30">
                                        <div class="form-group">
                                            <div class="image-upload-group">
                                                <!-- Chưa có ảnh phụ, chỉ hiển thị ô upload -->
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
                                                        <span style="margin-left: 5px;">Upload ảnh phụ</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class=" mg-top-40 sherah-dflex sherah-dflex-gap-30 justify-content-end">
                                        <button type="submit" class="sherah-btn sherah-btn__primary">Thêm sản
                                            phẩm</button>
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
            let variantIndex = 0;

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
                // ✅ FILL GIÁ GỐC
                const basePriceInput = document.getElementById('base-price');
                if (basePriceInput && basePriceInput.value !== '') {
                    priceInput.value = basePriceInput.value;
                }

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

                    const tbody = document.getElementById('variants-body');
                    tbody.innerHTML = '';
                    variantIndex = 0;

                    sizes.forEach(function(size) {
                        colors.forEach(function(color) {
                            materials.forEach(function(material) {
                                addVariantRow(size, color, material);
                            });
                        });
                    });
                });
            }

            // Xóa 1 dòng biến thể
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-variant');
                if (btn) {
                    const row = btn.closest('tr');
                    if (row) row.remove();
                }
            });

            // PHẦN ẢNH PHỤ: giữ nguyên logic bạn đang dùng
            const addImageBtn = document.getElementById('add-image');
            if (addImageBtn) {
                addImageBtn.addEventListener('click', function() {
                    const div = document.createElement('div');
                    div.classList.add('d-flex', 'align-items-center', 'mb-2');
                    div.innerHTML = `
                <input type="text" name="images[]" class="form-control shadow-sm" placeholder="URL ảnh phụ">
                <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-image">
                    <i class="bi bi-trash"></i>
                </button>`;
                    const list = document.getElementById('image-list');
                    if (list) list.appendChild(div);
                });
            }

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-image')) {
                    const wrap = e.target.closest('.d-flex');
                    if (wrap) wrap.remove();
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Giữ hình dạng select multiple như 1 hàng */
        #matrix-sizes,
        #matrix-colors,
        #matrix-materials {
            height: 38px;
            padding-right: 30px;
        }

        /* Table biến thể: cho rộng để có thể cuộn ngang */
        .variants-table {
            min-width: 1000px;
            /* tùy bạn, 1000–1200px đều được */
        }

        .variants-table th,
        .variants-table td {
            white-space: nowrap;
            /* không xuống dòng, tránh bị vỡ layout */
        }

        .variants-table input.form-control,
        .variants-table select.form-control {
            min-width: 110px;
            /* để ô nhập không bị quá bé */
            height: 32px;
            padding: 2px 6px;
            font-size: 0.85rem;
        }
    </style>
@endpush

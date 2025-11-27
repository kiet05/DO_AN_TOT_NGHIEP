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

                                                <div id="variants-wrapper">
                                                    @php
                                                        $hasVariants = $product->variants->count() > 0;
                                                    @endphp

                                                    @if ($hasVariants)
                                                        @foreach ($product->variants as $index => $variant)
                                                            <div class="variant row mb-3 g-3">
                                                                <input type="hidden"
                                                                    name="variants[{{ $index }}][id]"
                                                                    value="{{ $variant->id }}">

                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <label>SKU</label>
                                                                    <input type="text"
                                                                        name="variants[{{ $index }}][sku]"
                                                                        class="form-control" value="{{ $variant->sku }}"
                                                                        required>
                                                                </div>

                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <label>Số lượng</label>
                                                                    <input type="number"
                                                                        name="variants[{{ $index }}][quantity]"
                                                                        class="form-control"
                                                                        value="{{ $variant->quantity }}" required>
                                                                </div>

                                                                <div class="col-lg-12 col-md-12 col-12">
                                                                    <label>Giá</label>
                                                                    <input type="number"
                                                                        name="variants[{{ $index }}][price]"
                                                                        class="form-control" value="{{ $variant->price }}"
                                                                        required>
                                                                </div>
                                                                <div class="col-lg-12 col-md-12 col-12">
                                                                    <div class="form-group">
                                                                        <label class="sherah-wc__form-label">Ảnh biến thể
                                                                        </label>

                                                                        @if (!empty($variant->image_url))
                                                                            <div class="variant-image-preview mb-2">
                                                                                <img src="{{ asset('storage/' . $variant->image_url) }}"
                                                                                    alt="Ảnh biến thể"
                                                                                    style="max-width: 120px; height:auto; border-radius:4px; border:1px solid #eee;">
                                                                            </div>
                                                                        @endif

                                                                        <input type="file"
                                                                            name="variants[{{ $index }}][image]"
                                                                            class="form-control" accept="image/*">
                                                                        <small class="text-muted">Nếu không chọn ảnh mới, hệ
                                                                            thống giữ ảnh cũ.</small>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-3 col-md-3 col-12">
                                                                    <label>Trạng thái</label>
                                                                    <select name="variants[{{ $index }}][status]"
                                                                        class="form-control">
                                                                        <option value="1"
                                                                            {{ $variant->status == 1 ? 'selected' : '' }}>
                                                                            Hiện
                                                                        </option>
                                                                        <option value="0"
                                                                            {{ $variant->status == 0 ? 'selected' : '' }}>
                                                                            Ẩn
                                                                        </option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-lg-3 col-md-3 col-12">
                                                                    <label>Kích thước</label>
                                                                    <select
                                                                        name="variants[{{ $index }}][attribute_value_ids][]"
                                                                        class="form-control">
                                                                        @foreach ($sizes as $size)
                                                                            <option value="{{ $size->id }}"
                                                                                {{ $variant->sizes->contains('id', $size->id) ? 'selected' : '' }}>
                                                                                {{ $size->value }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-lg-3 col-md-3 col-12">
                                                                    <label>Màu sắc</label>
                                                                    <select
                                                                        name="variants[{{ $index }}][attribute_value_ids][]"
                                                                        class="form-control">
                                                                        @foreach ($colors as $color)
                                                                            <option value="{{ $color->id }}"
                                                                                {{ $variant->colors->contains('id', $color->id) ? 'selected' : '' }}>
                                                                                {{ $color->value }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-lg-3 col-md-3 col-12">
                                                                    <label>Chất liệu</label>
                                                                    <select
                                                                        name="variants[{{ $index }}][attribute_value_ids][]"
                                                                        class="form-control">
                                                                        @foreach ($materials as $material)
                                                                            <option value="{{ $material->id }}"
                                                                                {{ $variant->materials->contains('id', $material->id) ? 'selected' : '' }}>
                                                                                {{ $material->value }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        {{-- Nếu chưa có biến thể nào, render 1 biến thể rỗng --}}
                                                        @php $index = 0; @endphp
                                                        <div class="variant row mb-3 g-3">
                                                            <input type="hidden" name="variants[0][id]" value="">

                                                            <div class="col-lg-6 col-md-6 col-12">
                                                                <label>SKU</label>
                                                                <input type="text" name="variants[0][sku]"
                                                                    class="form-control" value="" required>
                                                            </div>

                                                            <div class="col-lg-6 col-md-6 col-12">
                                                                <label>Số lượng</label>
                                                                <input type="number" name="variants[0][quantity]"
                                                                    class="form-control" value="" required>
                                                            </div>

                                                            <div class="col-lg-12 col-md-12 col-12">
                                                                <label>Giá</label>
                                                                <input type="number" name="variants[0][price]"
                                                                    class="form-control" value="" required>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-12">
                                                                <label>Ảnh biến thể</label>
                                                                <input type="file" name="variants[0][image]"
                                                                    class="form-control" accept="image/*">
                                                            </div>

                                                            <div class="col-lg-3 col-md-3 col-12">
                                                                <label>Trạng thái</label>
                                                                <select name="variants[0][status]" class="form-control">
                                                                    <option value="1">Hiện</option>
                                                                    <option value="0">Ẩn</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-lg-3 col-md-3 col-12">
                                                                <label>Kích thước</label>
                                                                <select name="variants[0][attribute_value_ids][]"
                                                                    class="form-control">
                                                                    @foreach ($sizes as $size)
                                                                        <option value="{{ $size->id }}">
                                                                            {{ $size->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-lg-3 col-md-3 col-12">
                                                                <label>Màu sắc</label>
                                                                <select name="variants[0][attribute_value_ids][]"
                                                                    class="form-control">
                                                                    @foreach ($colors as $color)
                                                                        <option value="{{ $color->id }}">
                                                                            {{ $color->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-lg-3 col-md-3 col-12">
                                                                <label>Chất liệu</label>
                                                                <select name="variants[0][attribute_value_ids][]"
                                                                    class="form-control">
                                                                    @foreach ($materials as $material)
                                                                        <option value="{{ $material->id }}">
                                                                            {{ $material->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <button type="button" class="sherah-btn sherah-btn__secondary"
                                                        id="add-variant">
                                                        Thêm biến thể
                                                    </button>
                                                </div>
                                                <!-- End Specification -->
                                            </div>
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
            let variantIndex = {{ $product->variants->count() }};

            const variantsWrapper = document.getElementById('variants-wrapper');
            const addVariantBtn = document.getElementById('add-variant');

            if (variantsWrapper && addVariantBtn) {
                addVariantBtn.addEventListener('click', function() {
                    const template = variantsWrapper.querySelector('.variant');
                    if (!template) return;

                    const newVariant = template.cloneNode(true);

                    // bỏ id cũ (nếu có) để không update nhầm
                    const hiddenId = newVariant.querySelector('input[type="hidden"][name^="variants"]');
                    if (hiddenId) hiddenId.remove();

                    // cập nhật lại name + reset value cho tất cả input (kể cả file input)
                    newVariant.querySelectorAll('input').forEach(input => {
                        const oldName = input.getAttribute('name');
                        if (!oldName) return;

                        input.value = '';
                        const newName = oldName.replace(/\[(\d+)\]/, '[' + variantIndex + ']');
                        input.setAttribute('name', newName);
                    });

                    // cập nhật lại name + reset cho select
                    newVariant.querySelectorAll('select').forEach(select => {
                        const oldName = select.getAttribute('name');
                        if (!oldName) return;

                        select.selectedIndex = 0;
                        const newName = oldName.replace(/\[(\d+)\]/, '[' + variantIndex + ']');
                        select.setAttribute('name', newName);
                    });

                    // ✅ xóa preview ảnh biến thể cũ trong bản clone (nếu có)
                    newVariant.querySelectorAll('.variant-image-preview').forEach(el => el.remove());

                    variantsWrapper.appendChild(newVariant);
                    variantIndex++;
                });
            }

            const addImageBtn = document.getElementById('add-image');
            if (addImageBtn) {
                addImageBtn.addEventListener('click', () => {
                    const div = document.createElement('div');
                    div.classList.add('d-flex', 'align-items-center', 'mb-2');
                    div.innerHTML = `
                    <input type="text" name="images[]" class="form-control shadow-sm" placeholder="URL ảnh phụ">
                    <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-image">
                        <i class="bi bi-trash"></i>
                    </button>`;
                    const imageList = document.getElementById('image-list');
                    if (imageList) {
                        imageList.appendChild(div);
                    }
                });
            }

            document.addEventListener('click', (e) => {
                if (e.target.closest('.remove-image')) {
                    const row = e.target.closest('.d-flex');
                    if (row) row.remove();
                }
            });
        });
    </script>
@endpush

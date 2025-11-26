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
                                                                <input class="sherah-wc__form-input"
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

                                                <div id="variants-wrapper">
                                                    <!-- Biến thể mẫu -->
                                                    <div class="variant row mb-3 g-3">
                                                        <input type="hidden" name="variants[0][id]" value="">

                                                        <div class="col-lg-6 col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">SKU</label>
                                                                <input type="text" name="variants[0][sku]"
                                                                    class="sherah-wc__form-input" placeholder="Mã sản phẩm"
                                                                    required>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Số lượng</label>
                                                                <input type="number" name="variants[0][quantity]"
                                                                    class="sherah-wc__form-input" placeholder="Số lượng"
                                                                    required>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Giá tiền</label>
                                                                <input type="number" name="variants[0][price]"
                                                                    class="sherah-wc__form-input" placeholder="Giá tiền"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Ảnh biến thể </label>
                                                                <input type="file" name="variants[0][image]"
                                                                    class="form-control" accept="image/*">
                                                            </div>
                                                        </div>


                                                        <div class="col-lg-3 col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Trạng thái</label>
                                                                <select name="variants[0][status]" class="form-control"
                                                                    required>
                                                                    <option value="1">Hiện</option>
                                                                    <option value="0">Ẩn</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Size -->
                                                        <div class="col-lg-3 col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Kích cỡ</label>
                                                                <select name="variants[0][attribute_value_ids][]"
                                                                    class="form-control" required>
                                                                    @foreach ($sizes as $size)
                                                                        <option value="{{ $size->id }}">
                                                                            {{ $size->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Color -->
                                                        <div class="col-lg-3 col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Màu sắc</label>
                                                                <select name="variants[0][attribute_value_ids][]"
                                                                    class="form-control" required>
                                                                    @foreach ($colors as $color)
                                                                        <option value="{{ $color->id }}">
                                                                            {{ $color->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Material -->
                                                        <div class="col-lg-3 col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="sherah-wc__form-label">Chất liệu</label>
                                                                <select name="variants[0][attribute_value_ids][]"
                                                                    class="form-control" required>
                                                                    @foreach ($materials as $material)
                                                                        <option value="{{ $material->id }}">
                                                                            {{ $material->value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" class="sherah-btn sherah-btn__secondary"
                                                    id="add-variant">
                                                    Thêm biến thể
                                                </button>
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
        let variantIndex = 1;
        document.getElementById('add-variant').addEventListener('click', function() {
            const wrapper = document.getElementById('variants-wrapper');
            const newVariant = wrapper.querySelector('.variant').cloneNode(true);

            // Reset input values
            newVariant.querySelectorAll('input').forEach(input => input.value = '');
            newVariant.querySelectorAll('select').forEach(select => {
                select.selectedIndex = -1;
                // Update name index
                const name = select.getAttribute('name');
                select.setAttribute('name', name.replace(/\d+/, variantIndex));
            });
            newVariant.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/\d+/, variantIndex));
            });

            wrapper.appendChild(newVariant);
            variantIndex++;
        });

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

@extends('layouts.admin.master')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <div class="sherah-dsinner">
                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Sản phẩm</h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="/">Dashboard</a></li>
                                            <li class="active"><a href="{{ route('admin.products.index') }}">Sản phẩm</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-4 col-12">
                                    <div class="sherah-product-sidebar sherah-default-bg mg-top-30">
                                        <h4 class="sherah-product-sidebar__title sherah-border-btm">Danh mục sản phẩm</h4>
                                        <ul class="sherah-product-sidebar__list">
                                            @foreach ($categories as $category)
                                                <li>
                                                    <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                                        class="{{ request('category_id') == $category->id ? 'fw-bold text-primary' : '' }}">
                                                        <span><i class="fa-solid fa-chevron-right"></i>
                                                            {{ $category->name }}</span>
                                                        <span class="count">{{ $category->products_count }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="sherah-product-sidebar sherah-default-bg mg-top-30">
                                        <h4 class="sherah-product-sidebar__title sherah-border-btm">Price Range</h4>
                                        <div class="price-filter">
                                            <div class="price-filter-inner">
                                                <div id="slider-range"></div>
                                                <div class="price_slider_amount">
                                                    <div class="label-input">
                                                        <span>Range:</span><input type="text" id="amount"
                                                            name="price" placeholder="Add Your Price" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Brands Sidebar -->
                                    <div class="sherah-product-sidebar sherah-default-bg mg-top-30">
                                        <h4 class="sherah-product-sidebar__title sherah-border-btm">Nhãn hàng</h4>
                                        <ul class="sherah-product-sidebar__list">
                                            @foreach ($brands as $brand)
                                                <li>
                                                    <a href="{{ route('admin.products.index', ['brand_id' => $brand->id]) }}"
                                                        class="{{ request('brand_id') == $brand->id ? 'fw-bold text-primary' : '' }}">
                                                        <span><i class="fa-solid fa-chevron-right"></i>
                                                            {{ $brand->name }}</span>
                                                        <span class="count">{{ $brand->products_count }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- Sizes Sidebar -->
                                    <div class="sherah-product-sidebar sherah-default-bg mg-top-30">
                                        <h4 class="sherah-product-sidebar__title sherah-border-btm">Size</h4>
                                        <ul class="sherah-product-sidebar__size">
                                            @foreach ($sizes as $size)
                                                <li>
                                                    <a class="sherah-border"
                                                        href="{{ route('admin.products.index', ['size_id' => $size->id]) }}">
                                                        {{ $size->value }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-xxl-9 col-lg-8 col-12">
                                    <div class="sherah-breadcrumb__right mg-top-30">
                                        <div class="sherah-breadcrumb__right--first">
                                            <div class="sherah-header__form sherah-header__form--product">
                                                <form class="sherah-header__form-inner"
                                                    action="{{ route('admin.products.index') }}" method="GET">
                                                    <button class="search-btn" type="submit">
                                                        <svg width="24" height="25" viewBox="0 0 24 25"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M15.6888 18.2542C10.5721 22.0645 4.46185 20.044 1.80873 16.2993C-0.984169 12.3585 -0.508523 7.01726 2.99926 3.64497C6.41228 0.362739 11.833 0.112279 15.5865 3.01273C19.3683 5.93475 20.8252 11.8651 17.3212 16.5826C17.431 16.6998 17.5417 16.8246 17.6599 16.9437C19.6263 18.9117 21.5973 20.8751 23.56 22.8468C24.3105 23.601 24.0666 24.7033 23.104 24.9575C22.573 25.0972 22.1724 24.8646 21.8075 24.4988C19.9218 22.6048 18.0276 20.7194 16.1429 18.8245C15.9674 18.65 15.8314 18.4361 15.6888 18.2542ZM2.39508 10.6363C2.38758 14.6352 5.61109 17.8742 9.62079 17.8977C13.6502 17.9212 16.9018 14.6914 16.9093 10.6597C16.9169 6.64673 13.7046 3.41609 9.69115 3.39921C5.66457 3.38232 2.40259 6.61672 2.39508 10.6363Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <input name="s" value="{{ request('s') }}" type="text"
                                                        placeholder="Search">
                                                </form>
                                            </div>
                                            {{-- <p>Showing 1–8 of 60 results</p> --}}
                                        </div>
                                        <div class="sherah-product__nav list-group" id="list-tab" role="tablist">
                                            <a class="list-group-item {{ request('sort') == 'new' ? 'active' : '' }}"
                                                href="{{ route('admin.products.index', array_merge(request()->all(), ['sort' => 'new'])) }}">
                                                <span>Sản phẩm mới</span>
                                            </a>
                                            <a class="list-group-item {{ request('sort') == 'sale' ? 'active' : '' }}"
                                                href="{{ route('admin.products.index', array_merge(request()->all(), ['sort' => 'sale'])) }}">
                                                <span>Sản phẩm giảm giá</span>
                                            </a>
                                            <a class="list-group-item {{ request('sort') == 'newest' ? 'active' : '' }}"
                                                href="{{ route('admin.products.index', array_merge(request()->all(), ['sort' => 'newest'])) }}">
                                                <span>Mặt hàng mới</span>
                                            </a>
                                        </div>

                                    </div>
                                    {{-- Sản phẩm --}}
                                    <div class="tab-content mt-4" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="tab_1" role="tabpanel"
                                            aria-labelledby="nav-home-tab">
                                            <div class="row g-4"> <!-- g-4: khoảng cách giữa các cột -->
                                                @forelse ($products as $product)
                                                    <div class="col-xxl-4 col-lg-6 col-md-6 col-12 d-flex">
                                                        <!-- d-flex để card full chiều cao -->
                                                        <div
                                                            class="sherah-product-card sherah-product-card__v2 sherah-default-bg sherah-border w-100 d-flex flex-column">

                                                            <!-- Ảnh sản phẩm -->
                                                            <div class="sherah-product-card__img" style="flex-shrink: 0;">
                                                                @if ($product->image_main)
                                                                    <img src="{{ asset('storage/' . $product->image_main) }}"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 300px; object-fit: cover;">
                                                                @else
                                                                    <img src="https://via.placeholder.com/300x300?text=No+Image"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 300px; object-fit: cover;">
                                                                @endif
                                                            </div>

                                                            <!-- Nội dung card -->
                                                            <div
                                                                class="sherah-product-card__content mt-3 d-flex flex-column flex-grow-1">
                                                                <h4 class="sherah-product-card__title mb-2">
                                                                    <a href=""
                                                                        class="sherah-pcolor">
                                                                        {{ $product->name }}
                                                                    </a>
                                                                </h4>

                                                                <div class="sherah-product__bottom--single mb-3">
                                                                    <h5 class="sherah-product-card__price">
                                                                        @if ($product->sale_price ?? false)
                                                                            <del>{{ number_format($product->base_price, 0, ',', '.') }}₫</del>
                                                                            {{ number_format($product->sale_price, 0, ',', '.') }}₫
                                                                        @else
                                                                            {{ number_format($product->base_price, 0, ',', '.') }}₫
                                                                        @endif
                                                                    </h5>
                                                                </div>

                                                                <div
                                                                    class="sherah-product__bottom mt-auto d-flex align-items-stretch gap-2">
                                                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                                                        class="sherah-btn default flex-fill d-flex align-items-center justify-content-center">
                                                                        Sửa chữa
                                                                    </a>

                                                                    <a href=""
                                                                        class="sherah-btn default flex-fill d-flex align-items-center justify-content-center">
                                                                        Xem chi tiết
                                                                    </a>

                                                                    <form
                                                                        action="{{ route('admin.products.destroy', $product->id) }}"
                                                                        method="POST" class="flex-fill m-0 p-0 d-flex">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="sherah-btn default w-100 d-flex align-items-center justify-content-center">
                                                                            Xóa
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-center py-5">
                                                        <p class="text-muted">Không có sản phẩm nào trong danh mục này.</p>
                                                    </div>
                                                @endforelse
                                            </div>

                                            <div class="row mg-top-40">
                                                <div class="sherah-pagination">
                                                    <ul class="sherah-pagination__list">
                                                        {{-- Trang trước --}}
                                                        <li
                                                            class="sherah-pagination__button {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                                            <a href="{{ $products->previousPageUrl() }}"><i
                                                                    class="fas fa-angle-left"></i></a>
                                                        </li>

                                                        {{-- Các số trang --}}
                                                        @for ($i = 1; $i <= $products->lastPage(); $i++)
                                                            <li
                                                                class="{{ $products->currentPage() == $i ? 'active' : '' }}">
                                                                <a
                                                                    href="{{ $products->url($i) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</a>
                                                            </li>
                                                        @endfor

                                                        {{-- Trang tiếp --}}
                                                        <li
                                                            class="sherah-pagination__button {{ $products->currentPage() == $products->lastPage() ? 'disabled' : '' }}">
                                                            <a href="{{ $products->nextPageUrl() }}"><i
                                                                    class="fas fa-angle-right"></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Release the $ symbol from DataTables
        var dt = $.noConflict(true);

        // Use a different symbol for jQuery UI
        var jq = $.noConflict();
        jq(function() {
            jq("#slider-range").slider({
                range: true,
                min: 0,
                max: 500,
                values: [100, 300],
                slide: function(event, ui) {
                    jq("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);

                }
            });
            jq("#amount").val("$" + jq("#slider-range").slider("values", 0) +
                " - $" + jq("#slider-range").slider("values", 1));

        });
    </script>
@endsection

@extends('layouts.admin.master')

@section('title', 'Xem chi tiết sản phẩm')

@section('content')
    <section class="sherah-adashboard sherah-show">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sherah-body">
                        <!-- Dashboard Inner -->
                        <div class="sherah-dsinner">

                            <div class="row mg-top-30">
                                <div class="col-12 sherah-flex-between">
                                    <!-- Sherah Breadcrumb -->
                                    <div class="sherah-breadcrumb">
                                        <h2 class="sherah-breadcrumb__title">Product Details</Details>
                                        </h2>
                                        <ul class="sherah-breadcrumb__list">
                                            <li><a href="#">Home</a></li>
                                            <li class="active"><a href="products.html">Products Details</a></li>
                                        </ul>
                                    </div>
                                    <!-- End Sherah Breadcrumb -->
                                </div>
                            </div>

                            <div class="product-detail-body sherah-default-bg sherah-border mg-top-30">
                                <div class="row">
                                    {{-- Ảnh --}}
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="product-gallery">
                                            <div class="product-details-image">

                                                <!-- Thumbnails (ảnh phụ) -->
                                                <ul class="nav-pills nav flex-nowrap product-thumbs" id="pills-tab"
                                                    role="tablist">
                                                    @forelse ($product->images as $index => $image)
                                                        <li class="single-thumbs" role="presentation"
                                                            style="width: 100px; height: 100px; margin-right: 5px;">
                                                            <a class="{{ $index == 0 ? 'active' : '' }}"
                                                                id="pills-{{ $index }}-tab" data-bs-toggle="pill"
                                                                href="#pills-{{ $index }}" role="tab"
                                                                aria-controls="pills-{{ $index }}"
                                                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                                                <img src="{{ asset('storage/' . $image->image_url) }}"
                                                                    alt="thumbs"
                                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li class="single-thumbs" role="presentation"
                                                            style="width: 60px; height: 60px;">
                                                            <a class="active">
                                                                <img src="{{ asset('img/no-image.png') }}" alt="no-image"
                                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                            </a>
                                                        </li>
                                                    @endforelse
                                                </ul>

                                                <!-- Ảnh chính -->
                                                <div class="main-preview-image"
                                                    style="max-width: 400px; max-height: 400px; margin: 0 auto; overflow: hidden;">
                                                    <div class="tab-content product-image" id="pills-tabContent">
                                                        <div class="tab-pane fade show active" id="pills-main"
                                                            role="tabpanel" aria-labelledby="pills-main-tab">
                                                            <div class="single-product-image"
                                                                style="width: 100%; height: 100%;">
                                                                @if ($product->image_main)
                                                                    <img src="{{ asset('storage/' . $product->image_main) }}"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100%; object-fit: contain;">
                                                                @else
                                                                    <img src="{{ asset('img/no-image.png') }}"
                                                                        alt="no-image"
                                                                        style="width: 100%; height: 100%; object-fit: contain;">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Thêm ảnh phụ khác nếu muốn hiển thị khi click -->
                                                        @foreach ($product->images as $index => $image)
                                                            <div class="tab-pane fade" id="pills-{{ $index }}"
                                                                role="tabpanel"
                                                                aria-labelledby="pills-{{ $index }}-tab">
                                                                <div class="single-product-image"
                                                                    style="max-width: 400px; max-height: 400px; margin: 0 auto;">
                                                                    <img src="{{ asset('storage/' . $image->image_url) }}"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100%; object-fit: contain;">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    {{-- Thông tin sản phẩm --}}
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="product-detail-body__content">

                                            {{-- Tên sản phẩm --}}
                                            <h2 class="product-detail-body__title">{{ $product->name }}</h2>

                                            {{-- Thông tin bán (có thể bỏ nếu chưa dùng) --}}
                                            {{-- <p class="product-detail-body__stats">
                                                Sold {{ rand(5, 50) }} products in last 10 hours
                                            </p> --}}

                                            {{-- Giá sản phẩm --}}
                                            <div class="product-detail-body__deal--rating">
                                                <h5 class="sherah-product-card__price">
                                                    @if ($product->sale_price ?? false)
                                                        <del>{{ number_format($product->base_price, 0, ',', '.') }}₫</del>
                                                        {{ number_format($product->sale_price, 0, ',', '.') }}₫
                                                    @else
                                                        {{ number_format($product->base_price, 0, ',', '.') }}₫
                                                    @endif
                                                </h5>

                                                {{-- Đánh giá (giả lập 5 sao) --}}
                                                {{-- <div class="sherah-product-card__meta sherah-dflex sherah-flex-gap-30">
                                                    <div
                                                        class="sherah-product-card__rating sherah-dflex sherah-flex-gap-5">
                                                        @for ($i = 0; $i < 5; $i++)
                                                            <span class="sherah-color4"><i class="fa fa-star"></i></span>
                                                        @endfor
                                                        ({{ rand(10, 100) }})
                                                    </div>
                                                </div> --}}
                                            </div>

                                            {{-- Tồn kho --}}
                                            <p class="product-detail-body__stock sherah-color3">
                                                {{ $product->variants->sum('quantity') }} Trong kho
                                            </p>

                                            {{-- Mô tả --}}
                                            <div class="product-detail-body__text">
                                                {{ $product->description }}
                                            </div>

                                            {{-- Nút đặt hàng --}}
                                            {{-- <div class="product-inside-button">
                                                <div class="sherah-button-group">
                                                    <div class="quantity">
                                                        <div class="input-group">
                                                            <div class="button minus">
                                                                <button type="button" class="btn btn-primary btn-number"
                                                                    disabled data-type="minus"
                                                                    data-field="quant[1]">-</button>
                                                            </div>
                                                            <input type="text" name="quant[1]" class="input-number"
                                                                data-min="1" data-max="10" value="1">
                                                            <div class="button plus">
                                                                <button type="button" class="btn btn-primary btn-number"
                                                                    data-type="plus" data-field="quant[1]">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="sherah-btn">Add to Cart</a>
                                                    <a href="#" class="sherah-btn default"><i
                                                            class="fa fa-heart"></i></a>
                                                    <a href="#" class="sherah-btn default"><i
                                                            class="fa fa-share-alt"></i></a>
                                                </div>
                                            </div> --}}

                                            <div class="sherah-border-btm pd-top-40 mg-btm-40"></div>

                                            {{-- Thông tin thêm --}}
                                            <div class="sherah-products-meta">
                                                <ul class="sherah-products-meta__list">
                                                    <li><span class="p-list-title">SKU :</span>
                                                        {{ $product->variants->first()->sku ?? 'N/A' }}
                                                    </li>

                                                    <li><span class="p-list-title">Category :</span>
                                                        {{ $product->category->name ?? 'Không có danh mục' }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-detail-body sherah-default-bg sherah-border mg-top-30">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="sherah-product-tabs mg-btm-30">
                                            <div class="sherah-product-tabs__list list-group " id="list-tab"
                                                role="tablist">
                                                <a class="list-group-item active" data-bs-toggle="list" href="#p_tab_1"
                                                    role="tab" href="#">Biến thể</a>
                                                {{-- <a class="list-group-item" data-bs-toggle="list" href="#p_tab_2"
                                                    role="tab">Tính năng</a> --}}
                                                <a class="list-group-item" data-bs-toggle="list" href="#p_tab_3"
                                                    role="tab">Đánh giá</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            <div class="tab-pane fade show active" id="p_tab_1" role="tabpanel"
                                                aria-labelledby="nav-home-tab">

                                                <div class="sherah-table p-0">
                                                    <table class="product-overview-table mg-top-30">
                                                        <thead>
                                                            <tr>
                                                                <th>SKU</th>
                                                                <th>Giá</th>
                                                                <th>Số lượng</th>
                                                                <th>Trạng thái</th>
                                                                <th>Kích thước</th>
                                                                <th>Màu sắc</th>
                                                                <th>Chất liệu</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($product->variants as $variant)
                                                                <tr>
                                                                    <td>{{ $variant->sku }}</td>
                                                                    <td>{{ number_format($variant->price, 0, ',', '.') }}₫
                                                                    </td>
                                                                    <td>{{ $variant->quantity }}</td>
                                                                    <td>
                                                                        {{ $variant->status == 0 ? 'Hiển thị' : 'Ẩn' }}
                                                                    </td>
                                                                    <td>
                                                                        @foreach ($variant->attributes->where('type', 'size') as $attr)
                                                                            <span
                                                                                class="badge bg-secondary">{{ $attr->value }}</span>
                                                                        @endforeach
                                                                    </td>
                                                                    <td>
                                                                        @foreach ($variant->attributes->where('type', 'color') as $attr)
                                                                            <span
                                                                                class="badge bg-primary">{{ $attr->value }}</span>
                                                                        @endforeach
                                                                    </td>
                                                                    <td>
                                                                        @foreach ($variant->attributes->where('type', 'material') as $attr)
                                                                            <span
                                                                                class="badge bg-success">{{ $attr->value }}</span>
                                                                        @endforeach
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="p_tab_2" role="tabpanel"
                                                aria-labelledby="nav-home-tab">
                                                <ul class="sherah-features-list">
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Fiber or filament: type, size, length
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Yarn: diameter, twist, weight or size, count, fiber content for
                                                        mixed yarns, ply.
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Weight: ounces per squared or yards per pound.
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Thickness: vertical depth.
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Fabric structure
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Woven fabrics: weave type, warp and filling yarn count per linear
                                                        inch
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Knitted fabric: knit type, wale and course count per inch
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Finishes: chemicals such as resins, starches, waxes and mechanical
                                                        effects such as
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Calendaring and napping applied to the woven fabric to yield or
                                                        enhance style, durability, and utility values.
                                                    </li>
                                                    <li><svg class="sherah-offset__fill"
                                                            xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="11" viewBox="0 0 12 11">
                                                            <g id="Group_1022" data-name="Group 1022"
                                                                transform="translate(-165.75 -19.435)">
                                                                <path id="Path_550" data-name="Path 550"
                                                                    d="M165.75,24.587c.03-.212.052-.424.091-.634a5.39,5.39,0,0,1,7.9-3.832c.034.018.067.039.112.065l-.594,1.028a4.214,4.214,0,0,0-4.085-.04,4.027,4.027,0,0,0-2.048,2.56,4.254,4.254,0,0,0,3.005,5.353,4.023,4.023,0,0,0,3.607-.767,4.223,4.223,0,0,0,1.622-3.369h1.212c-.03.3-.042.6-.09.892a5.39,5.39,0,0,1-1.64,3.124,5.363,5.363,0,0,1-7.062.271,5.344,5.344,0,0,1-1.932-3.29c-.039-.214-.062-.43-.092-.646Z" />
                                                                <path id="Path_551" data-name="Path 551"
                                                                    d="M271.957,39.458a1.187,1.187,0,0,0-.106.085l-5.782,5.782a1.168,1.168,0,0,0-.08.1L263,42.428l.807-.8,2.126,2.127,5.18-5.18.848.857Z"
                                                                    transform="translate(-94.207 -18.545)" />
                                                            </g>
                                                        </svg>
                                                        Fabric width: The length of the filling or course
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane fade" id="p_tab_3" role="tabpanel"
                                                aria-labelledby="nav-home-tab">
                                                <!-- Sherah Review -->
                                                <div class="sherah-user-reviews">
                                                    <!-- Single Review -->
                                                    <div class="sherah-user-reviews__single">
                                                        <div class="shera-user-reviews_thumb">
                                                            <img src="img/review-1.png">
                                                        </div>
                                                        <div class="sherah-user-reviews__content">
                                                            <h4 class="sherah-user-reviews_title">Abubokkor Siddik</h4>
                                                            <div
                                                                class="sherah-product-card__rating sherah-dflex sherah-flex-gap-5">
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                            </div>
                                                            <p class="sherah-user-reviews__text">This is some unreal
                                                                beauty!I really liked it! What a beautiful light it comes
                                                                from! The radius of bright light is about meters</p>
                                                            <div class="sherah-user-reviews__buttons">
                                                                <a href="#"
                                                                    class="sherah-color3 sherah-color3__bg--opactity">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.136"
                                                                        height="15.5" viewBox="0 0 17.136 15.5">
                                                                        <path id="Icon"
                                                                            d="M106.729,13.669v.694a.779.779,0,0,0-.022.1,5.407,5.407,0,0,1-.909,2.507,10.756,10.756,0,0,1-1.877,2.153c-1.417,1.265-2.855,2.505-4.29,3.75a.9.9,0,0,1-1.28-.03q-1.646-1.415-3.287-2.836a17.082,17.082,0,0,1-2.561-2.63,5.638,5.638,0,0,1-1.136-2.513,4.777,4.777,0,0,1,1.049-4.005,4.03,4.03,0,0,1,3.775-1.423,3.938,3.938,0,0,1,2.419,1.328c.138.149.264.31.4.477.069-.089.128-.169.192-.246s.135-.162.208-.239A3.931,3.931,0,0,1,103.71,9.6a4.192,4.192,0,0,1,2.863,3.17C106.65,13.062,106.679,13.368,106.729,13.669Z"
                                                                            transform="translate(-90.443 -8.519)"
                                                                            fill="none" stroke="#09ad95"
                                                                            stroke-width="1.7" />
                                                                    </svg> 80
                                                                </a>
                                                                <a href="#"
                                                                    class="sherah-color2 sherah-color2__bg--opactity">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.684"
                                                                        height="15.304" viewBox="0 0 17.684 15.304">
                                                                        <path id="Icon"
                                                                            d="M122.755,24.156c-.059.315-.1.635-.18.945a7.044,7.044,0,0,1-1.362,2.647l-.383.482-1.064-.84.358-.454a5.942,5.942,0,0,0,1.108-2.061,4.449,4.449,0,0,0-.089-2.687,4.951,4.951,0,0,0-2.707-3.014,4.9,4.9,0,0,0-2.089-.447q-4.115-.007-8.231,0c-.032,0-.065,0-.094,0l3.064,3.06-.963.962-4.69-4.694,4.71-4.711.925.925-3.1,3.1h.24q4.005,0,8.01,0a6.442,6.442,0,0,1,3.671,1.067,6.311,6.311,0,0,1,2.422,3,5.989,5.989,0,0,1,.417,1.86.716.716,0,0,0,.025.114Z"
                                                                            transform="translate(-105.221 -13.137)"
                                                                            fill="#ff6767" stroke="#ff6767"
                                                                            stroke-width="0.3" />
                                                                    </svg> Reply
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Single Review -->
                                                    <!-- Single Review -->
                                                    <div
                                                        class="sherah-user-reviews__single sherah-user-reviews__single--reply">
                                                        <div class="shera-user-reviews_thumb">
                                                            <img src="img/review-2.png">
                                                        </div>
                                                        <div class="sherah-user-reviews__content">
                                                            <h4 class="sherah-user-reviews_title">Admin</h4>
                                                            <div
                                                                class="sherah-product-card__rating sherah-dflex sherah-flex-gap-5">
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                                <span class="sherah-color4"><i
                                                                        class="fa fa-star"></i></span>
                                                            </div>
                                                            <p class="sherah-user-reviews__text">Thank Your for opinion.
                                                            </p>
                                                            <div class="sherah-user-reviews__buttons">
                                                                <a href="#"
                                                                    class="sherah-color3 sherah-color3__bg--opactity">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.136"
                                                                        height="15.5" viewBox="0 0 17.136 15.5">
                                                                        <path id="Icon"
                                                                            d="M106.729,13.669v.694a.779.779,0,0,0-.022.1,5.407,5.407,0,0,1-.909,2.507,10.756,10.756,0,0,1-1.877,2.153c-1.417,1.265-2.855,2.505-4.29,3.75a.9.9,0,0,1-1.28-.03q-1.646-1.415-3.287-2.836a17.082,17.082,0,0,1-2.561-2.63,5.638,5.638,0,0,1-1.136-2.513,4.777,4.777,0,0,1,1.049-4.005,4.03,4.03,0,0,1,3.775-1.423,3.938,3.938,0,0,1,2.419,1.328c.138.149.264.31.4.477.069-.089.128-.169.192-.246s.135-.162.208-.239A3.931,3.931,0,0,1,103.71,9.6a4.192,4.192,0,0,1,2.863,3.17C106.65,13.062,106.679,13.368,106.729,13.669Z"
                                                                            transform="translate(-90.443 -8.519)"
                                                                            fill="none" stroke="#09ad95"
                                                                            stroke-width="1.7" />
                                                                    </svg> 80
                                                                </a>
                                                                <a href="#"
                                                                    class="sherah-color2 sherah-color2__bg--opactity">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.684"
                                                                        height="15.304" viewBox="0 0 17.684 15.304">
                                                                        <path id="Icon"
                                                                            d="M122.755,24.156c-.059.315-.1.635-.18.945a7.044,7.044,0,0,1-1.362,2.647l-.383.482-1.064-.84.358-.454a5.942,5.942,0,0,0,1.108-2.061,4.449,4.449,0,0,0-.089-2.687,4.951,4.951,0,0,0-2.707-3.014,4.9,4.9,0,0,0-2.089-.447q-4.115-.007-8.231,0c-.032,0-.065,0-.094,0l3.064,3.06-.963.962-4.69-4.694,4.71-4.711.925.925-3.1,3.1h.24q4.005,0,8.01,0a6.442,6.442,0,0,1,3.671,1.067,6.311,6.311,0,0,1,2.422,3,5.989,5.989,0,0,1,.417,1.86.716.716,0,0,0,.025.114Z"
                                                                            transform="translate(-105.221 -13.137)"
                                                                            fill="#ff6767" stroke="#ff6767"
                                                                            stroke-width="0.3" />
                                                                    </svg> Reply
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Single Review -->
                                                </div>
                                                <!-- End Sherah Review -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $('.button.plus').on('click', function() {
            // var $qty = $('.input-number');
            var $button = $(this);
            var $input = $button.closest('.quantity').find("input.input-number");
            var currentVal = parseInt($input.val(), 10);
            if (!isNaN(currentVal)) {
                $input.val(currentVal + 1);
            }
        });
        $('.button.minus').on('click', function() {
            var $qty = $('.input-number');
            var $button = $(this);
            var $input = $button.closest('.quantity').find("input.input-number");
            var currentVal = parseInt($input.val(), 10);
            if (!isNaN(currentVal) && currentVal > 1) {
                $input.val(currentVal - 1);
            }
        });
    </script>
@endsection

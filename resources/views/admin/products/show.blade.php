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
                                            <div class="sherah-product-tabs__list list-group" id="list-tab" role="tablist">
                                                <a class="list-group-item active" data-bs-toggle="list" href="#p_tab_1"
                                                    role="tab">Biến thể</a>

                                                <a class="list-group-item" data-bs-toggle="list" href="#p_tab_orders"
                                                    role="tab">Đơn hàng</a>

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
                                                                        {{ $variant->status == 0 ? 'Ẩn' : 'Hiển thị' }}
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
                                            {{-- Tab ĐƠN HÀNG --}}
                                            <div class="tab-pane fade" id="p_tab_orders" role="tabpanel">
                                                <div class="sherah-table p-0">
                                                    <table class="product-overview-table mg-top-30">
                                                        <thead>
                                                            <tr>
                                                                <th>Mã đơn</th>
                                                                <th>Ngày đặt</th>
                                                                <th>Khách hàng</th>
                                                                <th>Biến thể / SKU</th>
                                                                <th>Số lượng</th>
                                                                <th>Giá bán</th>
                                                                <th>Thành tiền</th>
                                                                <th>Trạng thái</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                // đề phòng controller chưa truyền biến
                                                                $orderItems = $orderItems ?? collect();
                                                            @endphp

                                                            @forelse($orderItems as $item)
                                                                <tr>
                                                                    <!-- Mã đơn -->
                                                                    <td>
                                                                        @if ($item->order ?? false)
                                                                            <a
                                                                                href="{{ route('admin.orders.show', $item->order->id) }}">
                                                                                {{ $item->order->code ?? '#' . $item->order->id }}
                                                                            </a>
                                                                        @else
                                                                            #{{ $item->order_id }}
                                                                        @endif
                                                                    </td>

                                                                    <!-- Ngày đặt -->
                                                                    <td>
                                                                        @if ($item->order ?? false)
                                                                            {{ $item->order->created_at->format('d/m/Y H:i') }}
                                                                        @endif
                                                                    </td>

                                                                    <!-- Khách hàng -->
                                                                    <td>
                                                                        @if (($item->order ?? false) && ($item->order->user ?? false))
                                                                            {{ $item->order->user->full_name ?? ($item->order->user->username ?? 'Khách') }}
                                                                        @else
                                                                            {{ $item->order->customer_name ?? 'Khách lẻ' }}
                                                                        @endif
                                                                    </td>

                                                                    <!-- Biến thể / SKU -->
                                                                    <td>
                                                                        {{ optional($item->variant)->sku ?? ($item->sku ?? 'N/A') }}
                                                                    </td>

                                                                    <!-- Số lượng -->
                                                                    <td>{{ $item->quantity }}</td>

                                                                    <!-- Giá bán -->
                                                                    <td>{{ number_format($item->price, 0, ',', '.') }}₫
                                                                    </td>

                                                                    <!-- Thành tiền -->
                                                                    <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                                                    </td>

                                                                    <!-- Trạng thái đơn -->
                                                                    <td>
                                                                        @if ($item->order ?? false)
                                                                            @switch($item->order->order_status)
                                                                                @case('pending')
                                                                                    Chờ xử lý
                                                                                @break
                                                                                @case('confirmed')
                                                                                    Chờ chuẩn bị
                                                                                @break

                                                                                @case('shipping')
                                                                                    Đang giao
                                                                                @break

                                                                                @case('completed')
                                                                                    Hoàn tất
                                                                                @break

                                                                                @case('cancelled')
                                                                                    Đã hủy
                                                                                @break
                                                                                @case('returned')
                                                                                    Đã hoàn hàng
                                                                                @break
                                                                                @case('shipped')
                                                                                    Đã giao
                                                                                @break

                                                                                @default
                                                                                    {{ $item->order->order_status }}
                                                                            @endswitch
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="8" class="text-center text-muted">
                                                                            Sản phẩm này chưa xuất hiện trong đơn hàng nào.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
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
                                                <div class="tab-pane fade" id="p_tab_3" role="tabpanel">
                                                    <div class="sherah-user-reviews">

                                                        @php
                                                            $reviews = $product->reviews ?? collect();
                                                        @endphp

                                                        @forelse($reviews as $review)
                                                            <div class="sherah-user-reviews__single">
                                                                <div class="shera-user-reviews_thumb">
                                                                    <img src="{{ optional($review->user)->avatar_url ?? asset('img/default-avatar.png') }}"
                                                                        alt="avatar"
                                                                        style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
                                                                </div>
                                                                <div class="sherah-user-reviews__content">
                                                                    <h4 class="sherah-user-reviews_title">
                                                                        {{ optional($review->user)->full_name ?? (optional($review->user)->username ?? 'Khách ẩn danh') }}
                                                                    </h4>

                                                                    <div
                                                                        class="sherah-product-card__rating sherah-dflex sherah-flex-gap-5">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <span
                                                                                class="{{ $i <= ($review->rating ?? 0) ? 'sherah-color4' : 'text-muted' }}">
                                                                                <i class="fa fa-star"></i>
                                                                            </span>
                                                                        @endfor
                                                                        <span class="small text-muted ms-2">
                                                                            {{ $review->created_at->format('d/m/Y H:i') }}
                                                                        </span>
                                                                    </div>

                                                                    <p class="sherah-user-reviews__text">
                                                                        {{ $review->comment ?? ($review->content ?? '') }}
                                                                    </p>

                                                                    <div>
                                                                        <img src="{{ asset('storage/' . $review->image) }}"
                                                                            alt="review image" style="max-width:100px" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                                                        @endforelse

                                                    </div>
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

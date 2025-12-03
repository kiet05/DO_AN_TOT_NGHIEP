@extends('frontend.layouts.app')

@section('title', 'Trang chủ - ' . config('app.name'))

@push('styles')
    <style>
        /* Banner hiển thị full ảnh theo đúng tỉ lệ gốc, không crop */
        .hero-banner .carousel-item img.hero-banner-img {
            width: 100%;
            height: auto;
            /* tự co theo tỉ lệ ảnh */
            display: block;
        }

        /* Nếu muốn giới hạn chiều cao tối đa (tuỳ bạn, có thể bỏ) */
        @media (min-width: 992px) {
            .hero-banner .carousel-item img.hero-banner-img {
                max-height: 520px;
            }
        }

        .category-card img {
            width: 100%;
            height: 160px;
            /* chỉnh theo ý bạn: 140, 180... */
            object-fit: cover;
            /* lấp đầy khung, chấp nhận crop một chút */
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
@endpush

@section('content')
    @if (!empty($searchTerm ?? ''))
        <section class="py-5 bg-light border-bottom">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Kết quả cho "{{ $searchTerm }}"</h2>
                        <p class="text-muted mb-0">{{ $searchResults->count() }} sản phẩm phù hợp</p>
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-outline-dark">Xóa tìm kiếm</a>
                </div>

                @if ($searchResults->count())
                    <div class="row g-4">
                        @foreach ($searchResults as $product)
                            <div class="col-6 col-md-3">
                                @include('frontend.partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        Không tìm thấy sản phẩm nào. Hãy thử từ khóa khác.
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Hero Banner -->
    @if (isset($banners) && $banners->count() > 0)
        <section class="hero-banner mb-5">
            <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <a href="{{ $banner->link ?? '#' }}">
                                {{-- CODE CŨ (để tham khảo)
                        <img src="{{ asset('storage/' . $banner->image) }}"
                             class="d-block w-100"
                             alt="{{ $banner->title }}"
                             style="height: 500px; object-fit: cover;">
                        --}}

                                {{-- CODE MỚI: bỏ height cố định + object-fit để không bị crop --}}
                                <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100 hero-banner-img"
                                    alt="{{ $banner->title }}">
                            </a>
                        </div>
                    @endforeach
                </div>
                @if ($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                @endif
            </div>
        </section>
    @endif

    <div class="container">
        <!-- Danh mục sản phẩm -->
        @if (isset($categories) && $categories->count() > 0)
            <section class="categories-section mb-5">
                <h2 class="section-title">DANH MỤC SẢN PHẨM</h2>
                <div class="row g-4">
                    @foreach ($categories->take(8) as $category)
                        <div class="col-6 col-md-3">
                            <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card">
                                @php
                                    $imgUrl = $category->image
                                        ? asset('storage/' . $category->image) // ví dụ: /storage/categories/abc.jpg
                                        : null;
                                @endphp

                                @if ($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $category->name }}">
                                @else
                                    {{-- fallback: icon áo như cũ, tránh bị icon ảnh hỏng --}}
                                    <div class="mb-3">
                                        <i class="fas fa-tshirt fa-3x text-secondary"></i>
                                    </div>
                                @endif

                                <h5 class="mt-2">{{ $category->name }}</h5>

                                {{-- debug tạm thời: xem trong DB đang lưu gì --}}
                                {{-- <small>{{ $category->image }}</small> --}}
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif


        <!-- Sản phẩm khuyến mãi -->
        @if (isset($saleProducts) && $saleProducts->count() > 0)
            <section class="sale-products mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">SẢN PHẨM KHUYẾN MÃI</h2>
                    <a href="{{ route('products.index', ['sale' => 1]) }}" class="btn btn-outline-dark">Xem tất cả</a>
                </div>
                <div class="row g-4">
                    @foreach ($saleProducts as $product)
                        <div class="col-6 col-md-3">
                            @include('frontend.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Sản phẩm nổi bật -->
        @if (isset($featuredProducts) && $featuredProducts->count() > 0)
            <section class="featured-products mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">SẢN PHẨM NỔI BẬT</h2>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Xem tất cả</a>
                </div>
                <div class="row g-4">
                    @foreach ($featuredProducts as $product)
                        <div class="col-6 col-md-3">
                            @include('frontend.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Sản phẩm mới -->
        @if (isset($newProducts) && $newProducts->count() > 0)
            <section class="new-products mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">SẢN PHẨM MỚI</h2>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Xem tất cả</a>
                </div>
                <div class="row g-4">
                    @foreach ($newProducts as $product)
                        <div class="col-6 col-md-3">
                            @include('frontend.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

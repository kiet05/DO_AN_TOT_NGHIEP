@extends('frontend.layouts.app')

@section('title', 'Trang chủ - ' . config('app.name'))

@section('content')
    <!-- Hero Banner -->
    @if(isset($banners) && $banners->count() > 0)
    <section class="hero-banner mb-5">
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($banners as $index => $banner)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <a href="{{ $banner->link ?? '#' }}">
                        <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100" alt="{{ $banner->title }}" style="height: 500px; object-fit: cover;">
                    </a>
                </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            @endif
        </div>
    </section>
    @endif

    <div class="container">
        <!-- Danh mục sản phẩm -->
        @if(isset($categories) && $categories->count() > 0)
        <section class="categories-section mb-5">
            <h2 class="section-title">DANH MỤC SẢN PHẨM</h2>
            <div class="row g-4">
                @foreach($categories->take(8) as $category)
                <div class="col-6 col-md-3">
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card">
                        <div class="mb-3">
                            <i class="fas fa-tshirt fa-3x text-secondary"></i>
                        </div>
                        <h5>{{ $category->name }}</h5>
                    </a>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Sản phẩm khuyến mãi -->
        @if(isset($saleProducts) && $saleProducts->count() > 0)
        <section class="sale-products mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">SẢN PHẨM KHUYẾN MÃI</h2>
                <a href="{{ route('products.index', ['sale' => 1]) }}" class="btn btn-outline-dark">Xem tất cả</a>
            </div>
            <div class="row g-4">
                @foreach($saleProducts as $product)
                <div class="col-6 col-md-3">
                    @include('frontend.partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Sản phẩm nổi bật -->
        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
        <section class="featured-products mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">SẢN PHẨM NỔI BẬT</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Xem tất cả</a>
            </div>
            <div class="row g-4">
                @foreach($featuredProducts as $product)
                <div class="col-6 col-md-3">
                    @include('frontend.partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Sản phẩm mới -->
        @if(isset($newProducts) && $newProducts->count() > 0)
        <section class="new-products mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">SẢN PHẨM MỚI</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Xem tất cả</a>
            </div>
            <div class="row g-4">
                @foreach($newProducts as $product)
                <div class="col-6 col-md-3">
                    @include('frontend.partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
@endsection


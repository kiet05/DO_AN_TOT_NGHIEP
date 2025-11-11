@extends('frontend.layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
<style>
    .product-detail-image {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .product-detail-image img {
        width: 100%;
        height: auto;
    }
    
    .product-thumbnails {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .product-thumbnail {
        width: 80px;
        height: 80px;
        border: 2px solid var(--border-color);
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.3s;
    }
    
    .product-thumbnail.active {
        border-color: var(--secondary-color);
    }
    
    .product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-detail-info h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .product-price-large {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-color);
    }
    
    .product-price-old {
        font-size: 20px;
        color: #999;
        text-decoration: line-through;
        margin-left: 15px;
    }
    
    .product-variants {
        margin: 20px 0;
    }
    
    .variant-option {
        display: inline-block;
        padding: 8px 15px;
        margin: 5px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .variant-option:hover {
        border-color: var(--secondary-color);
    }
    
    .variant-option.selected {
        background: var(--secondary-color);
        color: #fff;
        border-color: var(--secondary-color);
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 20px 0;
    }
    
    .quantity-input {
        display: flex;
        align-items: center;
        border: 1px solid var(--border-color);
        border-radius: 4px;
    }
    
    .quantity-input button {
        border: none;
        background: var(--bg-light);
        padding: 8px 15px;
        cursor: pointer;
    }
    
    .quantity-input input {
        border: none;
        width: 60px;
        text-align: center;
        padding: 8px;
    }
    
    .btn-buy-now {
        background: var(--secondary-color);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 4px;
        font-weight: 600;
        transition: background 0.3s;
    }
    
    .btn-buy-now:hover {
        background: #b8941f;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
            @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div class="product-detail-image">
                <img id="main-image" src="{{ asset('storage/' . $product->image_main) }}" alt="{{ $product->name }}">
            </div>
            @if($product->images->count() > 0)
            <div class="product-thumbnails">
                <div class="product-thumbnail active" onclick="changeImage('{{ asset('storage/' . $product->image_main) }}')">
                    <img src="{{ asset('storage/' . $product->image_main) }}" alt="Main">
                </div>
                @foreach($product->images as $image)
                <div class="product-thumbnail" onclick="changeImage('{{ asset('storage/' . $image->image_path) }}')">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Image">
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Product Info -->
        <div class="col-md-6">
            <div class="product-detail-info">
                <h1>{{ $product->name }}</h1>
                
                <div class="product-price-large mb-3">
                    <span id="product-price">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
                    @if($product->is_on_sale)
                    <span class="product-price-old" id="product-price-old"></span>
                    @endif
                </div>
                
                @if($product->description)
                <div class="product-description mb-4">
                    <h5>Mô tả sản phẩm</h5>
                    <p>{{ $product->description }}</p>
                </div>
                @endif
                
                @if($product->variants->count() > 0)
                <div class="product-variants">
                    <h5>Chọn biến thể</h5>
                    @foreach($product->variants as $variant)
                    <div class="variant-option" 
                         data-variant-id="{{ $variant->id }}"
                         data-price="{{ $variant->price }}"
                         data-original-price="{{ $variant->original_price ?? $variant->price }}"
                         onclick="selectVariant({{ $variant->id }}, {{ $variant->price }}, {{ $variant->original_price ?? $variant->price }})">
                        {{ $variant->name ?? 'Biến thể ' . $variant->id }}
                    </div>
                    @endforeach
                </div>
                @endif
                
                <div class="quantity-selector">
                    <label>Số lượng:</label>
                    <div class="quantity-input">
                        <button type="button" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" value="1" min="1" readonly>
                        <button type="button" onclick="increaseQuantity()">+</button>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <button class="btn btn-dark btn-lg flex-fill" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-bag me-2"></i>Thêm vào giỏ
                    </button>
                    <button class="btn-buy-now btn-lg" onclick="buyNow({{ $product->id }})">
                        Mua ngay
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mt-5">
        <h3 class="section-title">Sản phẩm liên quan</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-6 col-md-3">
                @include('frontend.partials.product-card', ['product' => $relatedProduct])
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    let selectedVariantId = null;
    let selectedPrice = {{ $product->base_price }};
    let selectedOriginalPrice = null;
    
    function changeImage(src) {
        document.getElementById('main-image').src = src;
        document.querySelectorAll('.product-thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        event.target.closest('.product-thumbnail').classList.add('active');
    }
    
    function selectVariant(variantId, price, originalPrice) {
        selectedVariantId = variantId;
        selectedPrice = price;
        selectedOriginalPrice = originalPrice;
        
        document.querySelectorAll('.variant-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        event.target.classList.add('selected');
        
        document.getElementById('product-price').textContent = formatPrice(price);
        if (originalPrice && originalPrice > price) {
            document.getElementById('product-price-old').textContent = formatPrice(originalPrice);
        } else {
            document.getElementById('product-price-old').textContent = '';
        }
    }
    
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + '₫';
    }
    
    function increaseQuantity() {
        const qty = document.getElementById('quantity');
        qty.value = parseInt(qty.value) + 1;
    }
    
    function decreaseQuantity() {
        const qty = document.getElementById('quantity');
        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
        }
    }
    
    function addToCart(productId) {
        const quantity = document.getElementById('quantity')?.value || 1;
        // TODO: Implement add to cart functionality
        alert('Đã thêm vào giỏ hàng!');
    }
    
    function buyNow(productId) {
        const quantity = document.getElementById('quantity')?.value || 1;
        // TODO: Implement buy now functionality
        alert('Chức năng đang phát triển!');
    }
    
    function quickView(productId) {
        // TODO: Implement quick view modal
        window.location.href = '/products/' + productId;
    }
</script>
@endpush


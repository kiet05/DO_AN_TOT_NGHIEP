@php
    $mainImage = $product->image_main 
        ? asset('storage/' . $product->image_main) 
        : ($product->images->first() 
            ? asset('storage/' . $product->images->first()->image_path) 
            : asset('img/no-image.png'));
    
    $salePrice = $product->base_price;
    $originalPrice = null;
    $discount = 0;
    
    if ($product->is_on_sale && $product->variants->count() > 0) {
        $variant = $product->variants->first();
        $salePrice = $variant->price ?? $product->base_price;
        $originalPrice = $variant->original_price ?? null;
        if ($originalPrice && $originalPrice > $salePrice) {
            $discount = round((($originalPrice - $salePrice) / $originalPrice) * 100);
        }
    }
@endphp

<div class="product-card">
    <div class="product-image">
        <a href="{{ route('products.show', $product->id) }}">
            <img src="{{ $mainImage }}" alt="{{ $product->name }}">
        </a>
        @if($product->is_new)
            <span class="product-badge">Mới</span>
        @elseif($discount > 0)
            <span class="product-badge">-{{ $discount }}%</span>
        @endif
    </div>
    <div class="product-info">
        <h6 class="product-name">
            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                {{ $product->name }}
            </a>
        </h6>
        <div class="product-price">
            <span class="price-current">{{ number_format($salePrice, 0, ',', '.') }}₫</span>
            @if($originalPrice && $originalPrice > $salePrice)
                <span class="price-old">{{ number_format($originalPrice, 0, ',', '.') }}₫</span>
                <span class="price-discount">-{{ $discount }}%</span>
            @endif
        </div>
        <div class="product-actions">
            <button class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                <i class="fas fa-shopping-bag me-1"></i> Thêm vào giỏ
            </button>
            <button class="btn-quick-view" onclick="quickView({{ $product->id }})">
                Xem nhanh
            </button>
        </div>
    </div>
</div>


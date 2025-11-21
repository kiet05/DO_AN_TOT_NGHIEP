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

                /* Wrapper cho nhóm thuộc tính */
        .attr-group {
            margin-bottom: 1rem;
        }

        /* Tên thuộc tính (Size, Color, Material) */
        .attr-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: .3rem;
            color: #333;
        }

        /* Danh sách nút */
        .attr-values {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* Nút thuộc tính */
        .attr-btn {
            padding: 6px 14px;
            border: 1.6px solid #dcdcdc;
            border-radius: 6px;
            background: #fafafa;
            cursor: pointer;
            font-size: 14px;
            color: #333;
            transition: all 0.2s ease-in-out;
        }

        /* Hover */
        .attr-btn:hover {
            border-color: #007bff;
            background: #f0f7ff;
            color: #007bff;
        }

        /* Khi được chọn */
        .attr-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.4);
            transform: translateY(-1px);
        }

        /* Nút disabled (hết hàng) */
        .attr-btn.disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        .buy-form {
    margin-top: 1rem;
    width: 100%;
}

.product-price, .product-stock, .quantity-wrapper {
    margin-bottom: 1rem;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.product-price .value {
    font-size: 20px;
    font-weight: 700;
    color: #ff2d2d;
}

.product-stock .value {
    font-weight: 600;
    color: #333;
}

/* Quantity Box */
.quantity-box {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    width: fit-content;
}

.qty-btn {
    background: #f5f5f5;
    border: none;
    width: 36px;
    height: 36px;
    font-size: 20px;
    cursor: pointer;
    transition: .2s;
}

.qty-btn:hover {
    background: #e2e2e2;
}

#qty {
    width: 60px;
    height: 36px;
    border: none;
    text-align: center;
    font-size: 16px;
}

#qty:focus {
    outline: none;
}

/* Add to Cart button */
.add-cart-btn {
    width: 100%;
    padding: 12px;
    background: #ff4d4f;
    border-radius: 6px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: .2s;
}

.add-cart-btn:hover {
    background: #e23d3f;
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

@php
    $defaultVariant = $product->variants->first(function ($variant) {
        return $variant->status == 1 && $variant->quantity > 0;
    }) ?? $product->variants->first();
@endphp

@section('content')
    <div class="container my-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                @if ($product->category)
                    <li class="breadcrumb-item"><a
                            href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
                    </li>
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
                @if ($product->images->count() > 0)
                    <div class="product-thumbnails">
                        <div class="product-thumbnail active"
                            onclick="changeImage('{{ asset('storage/' . $product->image_main) }}')">
                            <img src="{{ asset('storage/' . $product->image_main) }}" alt="Main">
                        </div>
                        @foreach ($product->images as $image)
                            <div class="product-thumbnail"
                                onclick="changeImage('{{ asset('storage/' . $image->image_url) }}')">
                                <img src="{{ asset('storage/' . $image->image_url) }}" alt="Image">
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
                        @if ($product->is_on_sale)
                            <span class="product-price-old" id="product-price-old"></span>
                        @endif
                    </div>

                    @if ($product->description)
                        <div class="product-description mb-4">
                            <h5>Mô tả sản phẩm</h5>
                            <p>{{ $product->description }}</p>
                        </div>
                    @endif

                    {{-- @if ($product->variants->count() > 0) --}}
                        <div class="product-variants">
                            {{-- <h5>Chọn biến thể</h5> --}}
                    {{-- @if ($product->variants->count() > 0)
                        <div class="product-variants">
                            <h5>Chọn biến thể</h5>

                            @foreach ($product->variants as $variant)
                                @php
                                    // Nhóm attribute_values theo tên attribute (Size, Color, ...)
                                    $groups = $variant->attributes->groupBy(function($av) {
                                        return optional($av->attribute)->name ?? 'Other';
                                    });
                                    // Tạo label ngắn gọn để hiển thị cho option
                                    $label = $groups->map(function($values, $attrName){
                                        return $attrName . ': ' . $values->pluck('value')->filter()->unique()->join(', ');
                                    })->values()->join(' | ');
                                @endphp

                                <div class="variant-option"
                                    data-variant-id="{{ $variant->id }}"
                                    data-price="{{ $variant->price }}"
                                    data-original-price="{{ $variant->original_price ?? $variant->price }}"
                                    data-stock="{{ $variant->quantity }}"
                                    onclick="selectVariant(this)"
                                    style="cursor:pointer; padding:.4rem; border:1px solid #eee; margin-bottom:.4rem;">
                                    {!! e($label) !!}
                                </div>
                            @endforeach
                        </div>
                    @endif --}}

                    {{-- QUANTITY + HIDDEN --}}
                    {{-- <form method="POST" action="{{ route('cart.add', $product->id) }}">
                        @csrf

                        <input type="hidden" id="selectedVariantId" name="variant_id" value="">

                        <div class="quantity-selector" style="margin-top:.8rem;">
                            <label>Số lượng:</label>
                            <div class="quantity-input" style="display:flex; align-items:center; gap:.5rem; margin-top:.4rem;">
                                <button type="button" id="btnDecrease" onclick="decreaseQuantity()">-</button>

                                <input type="number"
                                    id="quantity"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    max="{{ $totalStock }}"
                                    style="width:70px; text-align:center;">

                                <button type="button" id="btnIncrease" onclick="increaseQuantity()">+</button>
                            </div>

                            <p style="margin-top:.5rem;">
                                Còn lại: <strong id="stockDisplay">{{ $totalStock }}</strong> sản phẩm
                            </p>
                        </div>
                    </form> 
                                            </div>
                    @endif --}}
                    {{-- ATTR SELECTORS --}}
@php
  // build map attributeName => unique values
  $attrGroups = collect();
  foreach($product->variants as $v){
    foreach($v->attributes as $av){
      $attrGroups[$av->attribute->name ?? 'Other'] = ($attrGroups[$av->attribute->name ?? 'Other'] ?? collect())->push($av)->unique('id');
    }
  }
@endphp

<div class="attr-wrap">
  @foreach($attrGroups as $name => $vals)
    <div><strong>{{ $name }}</strong>
      <div>
        @foreach($vals as $val)
          <button type="button" class="attr-btn" data-id="{{ $val->id }}" data-name="{{ $name }}">{{ $val->value }}</button>
        @endforeach
      </div>
    </div>
  @endforeach
</div>

<form method="POST" action="{{ route('cart.add', $product->id) }}" class="buy-form">
    @csrf
    <input type="hidden" id="variant_id" name="variant_id" value="">

    {{-- PRICE --}}
    <div class="product-price">
        <span class="label">Giá:</span>
        <span class="value" id="price">{{ number_format($product->base_price) }}₫</span>
    </div>

    {{-- STOCK --}}
    <div class="product-stock">
        <span class="label">Còn lại:</span>
        <span class="value" id="stock">{{ $totalStock }}</span>
    </div>

    {{-- QUANTITY --}}
    <div class="quantity-wrapper">
        <span class="label">Số lượng:</span>
        <div class="quantity-box">
            <button type="button" class="qty-btn" id="dec">–</button>
            <input type="number" id="qty" name="quantity"
                   value="1" min="1" max="{{ $totalStock }}">
            <button type="button" class="qty-btn" id="inc">+</button>
        </div>
    </div>
</form>


                    <div class="d-flex gap-3">
                        <button id="addBtn" type="button" class="btn btn-dark btn-lg flex-fill"
                            onclick="addToCart({{ $product->id }}, event)">
                            <i class="fas fa-shopping-bag me-2"></i>Thêm vào giỏ
                        </button>
                        <button type="button" class="btn-buy-now btn-lg" onclick="buyNow({{ $product->id }}, event)">
                            Mua ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if (isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="mt-5">
                <h3 class="section-title">Sản phẩm liên quan</h3>
                <div class="row g-4">
                    @foreach ($relatedProducts as $relatedProduct)
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
        const DEFAULT_VARIANT_ID = {{ $defaultVariant?->id ?? 'null' }};
        const VARIANTS = [
        @foreach($product->variants as $v)
            {id:{{ $v->id }},price:{{ $v->price ?? 0 }},stock:{{ $v->quantity ?? 0 }},attrs:[{!! $v->attributes->pluck('id')->join(',') !!}] }@if(!$loop->last),@endif
        @endforeach
        ];
        const HAS_VARIANT_OPTIONS = document.querySelectorAll('.attr-btn').length > 0;

        function changeImage(src) {
            document.getElementById('main-image').src = src;
            document.querySelectorAll('.product-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            event.target.closest('.product-thumbnail').classList.add('active');
        }

        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN').format(Number(value) || 0) + '₫';
        }

        function resolveVariantId() {
            const variantInput = document.getElementById('variant_id');
            const selected = variantInput?.value;
            if (selected) {
                return selected;
            }
            if (!HAS_VARIANT_OPTIONS && DEFAULT_VARIANT_ID) {
                variantInput.value = DEFAULT_VARIANT_ID;
                return DEFAULT_VARIANT_ID;
            }
            return null;
        }

        function addToCart(productId, evt) {
            const variantId = resolveVariantId();
            if (!variantId) {
                alert('Vui lòng chọn phân loại sản phẩm trước khi thêm vào giỏ.');
                return;
            }

            const quantity = parseInt(document.getElementById('qty')?.value || 1, 10) || 1;

            const btn = evt?.currentTarget || evt?.target?.closest('button');
            const originalText = btn ? btn.innerHTML : '';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> Đang thêm...';
            }

            fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_variant_id: variantId,
                        quantity: quantity
                    })
                })
                .then(response => {
                    // Check if response is 401 (Unauthenticated)
                    if (response.status === 401) {
                        return response.json().then(data => {
                            if (confirm(data.message + '\n\nBạn có muốn đăng nhập ngay bây giờ?')) {
                                window.location.href = '{{ route('login') }}';
                            }
                            throw new Error('Unauthenticated');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update cart count
                        if (window.updateCartCount) {
                            window.updateCartCount(data.cart_count);
                        }

                        // Show success message
                        alert(data.message || 'Đã thêm vào giỏ hàng!');

                        // Optionally open cart sidebar
                        // document.getElementById('cart-sidebar').classList.add('open');
                        // document.getElementById('cart-overlay').classList.add('show');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    if (error.message !== 'Unauthenticated') {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
                    }
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
        }

        function buyNow(productId, evt) {
            const variantId = resolveVariantId();

            if (!variantId) {
                alert('Vui lòng chọn phân loại sản phẩm trước khi mua.');
                return;
            }

            const quantity = parseInt(document.getElementById('qty')?.value || 1, 10) || 1;

            const btn = evt?.currentTarget || evt?.target?.closest('button');
            const originalText = btn ? btn.innerHTML : '';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> Đang xử lý...';
            }

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(response => {
                if (response.status === 401) {
                    return response.json().then(data => {
                        if (confirm(data.message + '\n\nBạn có muốn đăng nhập ngay bây giờ?')) {
                            window.location.href = '{{ route('login') }}';
                        }
                        throw new Error('Unauthenticated');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (window.updateCartCount) {
                        window.updateCartCount(data.cart_count);
                    }
                    window.location.href = '{{ route('checkout.index') }}';
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi mua ngay');
                }
            })
            .catch(error => {
                if (error.message !== 'Unauthenticated') {
                    console.error(error);
                    alert('Không thể thực hiện mua ngay, vui lòng thử lại.');
                }
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        function quickView(productId) {
            // TODO: Implement quick view modal
            window.location.href = '/products/' + productId;
        }


// document.addEventListener('DOMContentLoaded', function () {
//     const qtyInput = document.getElementById('quantity');
//     const btnInc = document.getElementById('btnIncrease');
//     const btnDec = document.getElementById('btnDecrease');
//     const stockDisplay = document.getElementById('stockDisplay');
//     const selectedVariantIdInput = document.getElementById('selectedVariantId');
//     const addToCartBtn = document.getElementById('addToCartBtn');

//     // safety: nếu không có qtyInput -> exit
//     if (!qtyInput) return;

//     function updateButtons() {
//         const val = parseInt(qtyInput.value || 0, 10);
//         const min = parseInt(qtyInput.min || 1, 10);
//         const max = parseInt(qtyInput.max || 0, 10);
//         btnDec.disabled = val <= min;
//         btnInc.disabled = val >= max || max === 0;
//         addToCartBtn.disabled = (max === 0); // disable add to cart khi hết hàng
//     }

//     // selectVariant nhận DOM element .variant-option
//     window.selectVariant = function(element) {
//         const variantId = element.dataset.variantId;
//         const stock = parseInt(element.dataset.stock || 0, 10);

//         // set hidden input để gửi form
//         selectedVariantIdInput.value = variantId;

//         // update stock display and max
//         stockDisplay.textContent = stock;
//         qtyInput.max = stock;

//         // nếu value > stock thì set lại
//         let curVal = parseInt(qtyInput.value || 0, 10);
//         if (isNaN(curVal) || curVal < parseInt(qtyInput.min || 1, 10)) {
//             curVal = parseInt(qtyInput.min || 1, 10);
//         }
//         if (curVal > stock) {
//             qtyInput.value = stock > 0 ? stock : 0;
//         } else {
//             qtyInput.value = curVal;
//         }

//         // highlight active option
//         document.querySelectorAll('.variant-option').forEach(el => el.classList.remove('active'));
//         element.classList.add('active');

//         updateButtons();
//     }

//     window.increaseQuantity = function() {
//         const max = parseInt(qtyInput.max || 0, 10);
//         let val = parseInt(qtyInput.value || 0, 10);
//         if (isNaN(val)) val = parseInt(qtyInput.min || 1, 10);
//         if (val < max) {
//             qtyInput.value = val + 1;
//         }
//         updateButtons();
//     }

//     window.decreaseQuantity = function() {
//         const min = parseInt(qtyInput.min || 1, 10);
//         let val = parseInt(qtyInput.value || 0, 10);
//         if (isNaN(val)) val = min;
//         if (val > min) {
//             qtyInput.value = val - 1;
//         }
//         updateButtons();
//     }

//     // cho phép gõ thủ công nhưng clamp
//     qtyInput.addEventListener('input', function () {
//         const min = parseInt(qtyInput.min || 1, 10);
//         const max = parseInt(qtyInput.max || 0, 10);
//         let v = parseInt(qtyInput.value || 0, 10);
//         if (isNaN(v)) v = min;
//         if (v < min) v = min;
//         if (v > max) v = max;
//         qtyInput.value = v;
//         updateButtons();
//     });

//     // init: chọn variant đầu tiên nếu có
//     const firstOption = document.querySelector('.variant-option');
//     if (firstOption) {
//         selectVariant(firstOption);
//     } else {
//         // nếu ko có variant: dùng tổng totalStock
//         stockDisplay.textContent = qtyInput.max;
//         updateButtons();
//     }
// });

    document.addEventListener('click', e => {
    if (!e.target.classList.contains('attr-btn')) return;
    const btn = e.target;
    const name = btn.dataset.name;
    // toggle active
    document.querySelectorAll('.attr-btn[data-name="'+name+'"]').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    applySelection();
    });

    function getSelectedIds(){
    return Array.from(document.querySelectorAll('.attr-btn.active')).map(b=>Number(b.dataset.id));
    }

    function arraysInclude(all, part){
    return part.every(p => all.includes(p));
    }

    function applySelection(){
    const sel = getSelectedIds();
    if (sel.length === 0) { resetBase(); return; }
    // find variant that includes all selected attr ids (prefer smallest extra attrs)
    let match = null;
    for (const v of VARIANTS){
        if (arraysInclude(v.attrs, sel)){
        if (!match || v.attrs.length < match.attrs.length) match = v;
        }
    }
    if (match){ applyVariant(match); } else { noVariant(); }
    }

    function applyVariant(v){
    document.getElementById('variant_id').value = v.id;
    document.getElementById('price').textContent = formatCurrency(v.price);
    document.getElementById('stock').textContent = v.stock;
    const qty = document.getElementById('qty'); qty.max = v.stock; if(Number(qty.value)>v.stock) qty.value = v.stock||0;
    const addBtn = document.getElementById('addBtn');
    if (addBtn) {
        addBtn.disabled = v.stock === 0;
    }
    }
    function resetBase(){
    document.getElementById('variant_id').value = '';
    document.getElementById('price').textContent = formatCurrency({{ $product->base_price }});
    document.getElementById('stock').textContent = '{{ $totalStock }}';
    document.getElementById('qty').max = {{ $totalStock }};
    const addBtn = document.getElementById('addBtn');
    if (addBtn) {
        addBtn.disabled = false;
    }
    }
    function noVariant(){
    document.getElementById('variant_id').value = '';
    document.getElementById('price').textContent = '—';
    document.getElementById('stock').textContent = 0;
    document.getElementById('qty').value = 0; document.getElementById('qty').max = 0;
    const addBtn = document.getElementById('addBtn');
    if (addBtn) {
        addBtn.disabled = true;
    }
    }

    // qty buttons
    document.getElementById('inc').addEventListener('click', ()=>{ const q=document.getElementById('qty'); if(Number(q.value)<Number(q.max)) q.value=Number(q.value)+1;});
    document.getElementById('dec').addEventListener('click', ()=>{ const q=document.getElementById('qty'); if(Number(q.value)>Number(q.min)) q.value=Number(q.value)-1;});

    // init
    resetBase();
    if (!document.querySelector('.attr-btn') && DEFAULT_VARIANT_ID) {
        const defaultVariant = VARIANTS.find(v => v.id === DEFAULT_VARIANT_ID);
        if (defaultVariant) {
            applyVariant(defaultVariant);
        }
    }
    </script>
@endpush

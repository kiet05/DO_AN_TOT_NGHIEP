@extends('frontend.layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
    <style>
        /* MAIN IMAGE */
        .product-detail-image {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-detail-image img {
            width: 100%;
        }

        /* THUMBNAILS */
        .product-thumbnails {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .product-thumbnail {
            width: 80px;
            height: 80px;
            border: 1px solid #ccc;
            border-radius: 6px;
            cursor: pointer;
            overflow: hidden;
        }

        .product-thumbnail.active {
            border-color: #ffb300;
        }

        .product-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* PRICE – ĐỔI SANG ĐEN */
        .product-price-main {
            font-size: 32px;
            font-weight: 700;
            color: #000;
        }

        /* ATTRIBUTE GROUP */
        .attr-group {
            margin-bottom: 1rem;
        }

        .attr-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .attr-values {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* ATTRIBUTE BUTTON – BASE */
        .attr-btn {
            padding: 7px 15px;
            background: #f8f8f8;
            border: 1.5px solid #cccccc;
            border-radius: 6px;
            cursor: pointer;
            transition: .2s;
            font-size: 14px;
        }

        /* COLOR PREVIEW (TÊN + CHẤM MÀU) */
        .attr-btn.color-preview {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* CHẤM MÀU */
        .color-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1px solid #ddd;
        }

        /* ACTIVE */
        .attr-btn.active {
            background: #ffb300 !important;
            border-color: #ffb300 !important;
            color: #fff !important;
        }


        /* DISABLED */
        .attr-btn.disabled {
            opacity: .3;
            pointer-events: none;
        }

        /* QUANTITY – BỎ ĐƯỜNG KẺ */
        .quantity-box {
            display: flex;
            align-items: center;
            border: none !important;
            background: transparent !important;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: #eee;
            border-radius: 6px;
            cursor: pointer;
        }

        #qty {
            width: 60px;
            border: none !important;
            text-align: center;
        }

        /* ADD TO CART BUTTON */
        .btn-cart {
            width: 100%;
            padding: 12px;
            background: #1c1c1c;
            color: #fff;
            border-radius: 6px;
            border: none;
            font-size: 16px;
        }

        /* BUY BUTTON */
        .btn-buy {
            padding: 12px 24px;
            background: #ffb300;
            color: #fff;
            border-radius: 6px;
            border: none;
            font-size: 16px;
        }
    </style>
@endpush


@php
    $defaultVariant = $product->variants->first();
@endphp


@section('content')
    <div class="container my-5">

        <!-- Breadcrumb -->
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row">

            <!-- LEFT -->
            <div class="col-md-6">
                <div class="product-detail-image">
                    <img id="main-image" src="{{ asset('storage/' . $product->image_main) }}">
                </div>

                @if ($product->images->count())
                    <div class="product-thumbnails">
                        <div class="product-thumbnail active"
                            onclick="changeImage('{{ asset('storage/' . $product->image_main) }}', this)">
                            <img src="{{ asset('storage/' . $product->image_main) }}">
                        </div>

                        @foreach ($product->images as $img)
                            <div class="product-thumbnail"
                                onclick="changeImage('{{ asset('storage/' . $img->image_url) }}', this)">
                                <img src="{{ asset('storage/' . $img->image_url) }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIGHT -->
            <div class="col-md-6">
                <h1>{{ $product->name }}</h1>

                <div class="product-price-main mb-3">
                    <span id="price">{{ number_format($product->base_price) }}₫</span>
                </div>

                <div class="product-description mb-3">
                    {!! $product->description !!}
                </div>

                <!-- ATTRIBUTES -->
                @php
                    $grouped = [];
                    foreach ($product->variants as $v) {
                        foreach ($v->attributes as $a) {
                            $grouped[$a->attribute->name][$a->id] = $a;
                        }
                    }
                @endphp

                <div id="attr-area">
                    @foreach ($grouped as $name => $vals)
                        <div class="attr-group">
                            <div class="attr-title">{{ $name }}</div>
                            <div class="attr-values">

                                @foreach ($vals as $val)
                                    {{-- Color special render --}}
                                    @if (strtolower($name) == 'color')
                                        <button class="attr-btn color-preview" data-attr="{{ $val->id }}"
                                            data-group="{{ $name }}">

                                            {{-- Tên màu --}}
                                            <span>{{ $val->value }}</span>

                                            {{-- Chấm màu hiển thị màu thật --}}
                                            <span class="color-dot"
                                                style="
                  background: {{ strtolower($val->value) }};
                  display:inline-block;
                  width:18px;
                  height:18px;
                  border-radius:50%;
                  border:1px solid #ddd;
              ">
                                            </span>
                                        </button>
                                    @else
                                        <button class="attr-btn" data-attr="{{ $val->id }}"
                                            data-group="{{ $name }}">
                                            {{ $val->value }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" id="variant_id">

                <!-- STOCK -->
                <div class="my-2">
                    Còn lại: <strong id="stock">{{ $product->variants->sum('quantity') }}</strong>
                </div>

                <!-- QUANTITY -->
                <div class="quantity-box my-3">
                    <button class="qty-btn" id="dec">–</button>
                    <input id="qty" type="number" value="1" min="1" max="99">
                    <button class="qty-btn" id="inc">+</button>
                </div>

                <!-- BUTTONS -->
                <div class="d-flex gap-3">
                    <button id="addBtn" class="btn-cart" onclick="addToCart({{ $product->id }}, event)">
                        Thêm vào giỏ
                    </button>
                    <button class="btn-buy" onclick="buyNow({{ $product->id }}, event)">
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
        // ---------------- IMAGE CHANGE ----------------
        function changeImage(src, el) {
            document.getElementById('main-image').src = src;
            document.querySelectorAll('.product-thumbnail').forEach(x => x.classList.remove('active'));
            el.classList.add('active');
        }


        // ---------------- VARIANTS ----------------
        const VARIANTS = [
            @foreach ($product->variants as $v)
                {
                    id: {{ $v->id }},
                    price: {{ $v->price }},
                    stock: {{ $v->quantity }},
                    attrs: [{!! $v->attributes->pluck('id')->join(',') !!}]
                },
            @endforeach
        ];

        const attrButtons = document.querySelectorAll('.attr-btn');
        const variantIdInput = document.getElementById("variant_id");


        // ---------------- CLICK HANDLER (CHUẨN) ----------------
        attrButtons.forEach(btn => {
            btn.addEventListener('click', () => {

                const group = btn.dataset.group;

                // Nếu đang active → HUỶ chọn
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');

                    // Nếu không còn thuộc tính nào active → reset
                    const anyActive = document.querySelectorAll('.attr-btn.active').length > 0;
                    if (!anyActive) {
                        resetToBase();
                    } else {
                        applyVariantFilter();
                    }
                    return;
                }

                // Click vào nút khác trong group → bỏ active cũ
                document.querySelectorAll(`.attr-btn[data-group="${group}"]`)
                    .forEach(x => x.classList.remove('active'));

                btn.classList.add('active');
                applyVariantFilter();
            });
        });


        // ---------------- GET SELECTED ATTRS ----------------
        function getSelectedAttrs() {
            return Array.from(document.querySelectorAll('.attr-btn.active'))
                .map(b => Number(b.dataset.attr));
        }


        // ---------------- FILTER VARIANT ----------------
        function applyVariantFilter() {
            const selected = getSelectedAttrs();

            attrButtons.forEach(b => b.classList.remove("disabled"));

            let match = null;
            let smallest = 999;

            for (const v of VARIANTS) {
                const ok = selected.every(s => v.attrs.includes(s));
                if (ok && v.attrs.length < smallest) {
                    smallest = v.attrs.length;
                    match = v;
                }
            }

            if (match) {
                updateVariantInfo(match);
                disableInvalid(selected);
            } else {
                showNoVariant();
            }
        }


        // ---------------- DISABLE INVALID OPTIONS ----------------
        function disableInvalid(selected) {
            attrButtons.forEach(btn => {

                if (btn.classList.contains('active')) return;

                const test = [...selected, Number(btn.dataset.attr)];

                const ok = VARIANTS.some(v => test.every(x => v.attrs.includes(x)));

                if (!ok) btn.classList.add('disabled');
            });
        }


        // ---------------- UPDATE VARIANT ----------------
        function updateVariantInfo(v) {
            document.getElementById("price").textContent =
                new Intl.NumberFormat('en-US').format(v.price) + "₫";

            document.getElementById("stock").textContent = v.stock;

            variantIdInput.value = v.id;

            const qty = document.getElementById("qty");
            qty.max = v.stock;
            if (qty.value > v.stock) qty.value = v.stock;
            if (v.stock == 0) qty.value = 0;

            document.getElementById("addBtn").disabled = (v.stock === 0);
        }


        // ---------------- RESET TO BASE ----------------
        function resetToBase() {
            document.getElementById("price").textContent =
                "{{ number_format($product->base_price) }}₫";

            document.getElementById("stock").textContent =
                "{{ $product->variants->sum('quantity') }}";

            variantIdInput.value = "";

            attrButtons.forEach(b => b.classList.remove("disabled"));

            const qty = document.getElementById("qty");
            qty.value = 1;
            qty.max = 99;
        }


        // ---------------- NO VARIANT ----------------
        function showNoVariant() {
            variantIdInput.value = "";
            document.getElementById("price").textContent = "—";
            document.getElementById("stock").textContent = "0";

            const qty = document.getElementById("qty");
            qty.value = 0;
            qty.max = 0;

            document.getElementById("addBtn").disabled = true;

            attrButtons.forEach(b => {
                if (!b.classList.contains("active"))
                    b.classList.add("disabled");
            });
        }


        // ---------------- QTY BUTTONS ----------------
        document.getElementById("inc").addEventListener("click", () => {
            const q = document.getElementById("qty");
            if (Number(q.value) < Number(q.max)) q.value++;
        });

        document.getElementById("dec").addEventListener("click", () => {
            const q = document.getElementById("qty");
            if (Number(q.value) > Number(q.min)) q.value--;
        });


        // ---------------- ADD TO CART ----------------
        function getVariantId() {
            const id = document.getElementById("variant_id").value;
            return id && id !== "" ? id : null;
        }

        function addToCart(productId, evt) {
            const variantId = getVariantId();
            if (!variantId) {
                alert("Vui lòng chọn phân loại sản phẩm.");
                return;
            }

            const qty = Number(document.getElementById("qty").value || 1);
            const btn = evt.currentTarget;

            const old = btn.innerHTML;
            btn.innerHTML = "Đang thêm...";
            btn.disabled = true;

            fetch("{{ route('cart.add') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        product_variant_id: variantId,
                        quantity: qty
                    })
                })
                .then(r => r.json())
                .then(res => {
                    alert(res.message || "Đã thêm vào giỏ!");
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = old;
                });
        }


        // ---------------- BUY NOW ----------------
        function buyNow(productId, evt) {
            const variantId = getVariantId();
            if (!variantId) {
                alert("Vui lòng chọn phân loại sản phẩm.");
                return;
            }

            const qty = Number(document.getElementById("qty").value || 1);

            fetch("{{ route('cart.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    product_variant_id: variantId,
                    quantity: qty
                })
            }).then(() => window.location.href = "{{ route('checkout.index') }}");
        }
    </script>
@endpush

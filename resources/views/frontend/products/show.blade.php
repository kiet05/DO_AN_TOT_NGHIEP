@extends('frontend.layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
    <style>
        /* TABS MÔ TẢ / ĐÁNH GIÁ */
        .detail-tabs-wrapper {
            margin-top: 40px;
        }

        .detail-tabs {
            display: flex;
            gap: 32px;
            border-bottom: 1px solid #eee;
            margin-bottom: 0;
            padding-left: 0;
            list-style: none;
            justify-content: center;
            /* căn giữa các li */

        }

        .detail-tab-item {
            padding: 10px 0;
            font-size: 15px;
            font-weight: 600;
            color: #888;
            cursor: pointer;
            position: relative;
            white-space: nowrap;
        }

        .detail-tab-item.active {
            color: #111;
        }

        .detail-tab-item.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 100%;
            height: 2px;
            background: #111;
        }

        .detail-tab-pane {
            display: none;
            padding: 24px 0;
        }

        .detail-tab-pane.active {
            display: block;
        }

        /* MÔ TẢ – THU GỌN / XEM THÊM */
        .product-description-wrap {
            position: relative;
        }

        .product-description {
            position: relative;
            line-height: 1.6;
            font-size: 14px;
        }

        /* trạng thái thu gọn */
        .product-description.collapsed {
            max-height: 260px;
            /* muốn ít / nhiều hơn chỉnh số này */
            overflow: hidden;
        }

        /* hiệu ứng mờ phía dưới khi bị cắt bớt */
        .product-description.collapsed::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 60px;
            background: linear-gradient(to top, #ffffff, rgba(255, 255, 255, 0));
        }

        /* nút Xem thêm / Thu gọn */
        .description-toggle {
            margin-top: 8px;
            padding: 0;
            border: none;
            background: none;
            color: #111;
            font-weight: 600;
            cursor: pointer;
        }


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
            display: inline-block;
            background: #fff;
        }

        /* map màu theo tên */
        .color-dot.color-den {
            background: #000;
            /* Đen: chấm đen */
        }

        .color-dot.color-trang {
            background: #ffffff;
            /* Trắng */
        }

        .color-dot.color-navy {
            background: #001f3f;
            /* Navy xanh đậm */
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

        #qty {
            width: 60px;
            border: none !important;
            text-align: center;
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* NÚT + / - */
        .qty-btn {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid #ddd;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 500;
            line-height: 1;
            color: #222;
            cursor: pointer;
            transition: background 0.15s ease,
                border-color 0.15s ease,
                box-shadow 0.15s ease,
                transform 0.05s ease;
        }

        .qty-btn:hover {
            background: #ffb300;
            border-color: #ffb300;
            color: #fff;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .qty-btn:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* NÚT KHI BỊ DISABLED */
        .qty-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            background: #f1f1f1;
            border-color: #e5e5e5;
            color: #999;
            box-shadow: none;
        }

        /* Ẩn mũi tên tăng giảm mặc định của input number (Chrome) */
        #qty::-webkit-inner-spin-button,
        #qty::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        #qty[type=number] {
            -moz-appearance: textfield;
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

        /* Sao dánh giá */
        .rating-stars {
            font-size: 22px;
        }

        .rating-stars label {
            cursor: pointer;
            margin-right: 4px;
        }

        .rating-stars .star {
            cursor: pointer;
            color: #ddd;
            /* sao chưa chọn */
            transition: transform 0.1s ease-in-out, color 0.1s ease-in-out;
            margin-right: 4px;
        }

        .rating-stars .star.active {
            color: #ffc107;
            /* sao đã chọn */
        }

        .rating-stars .star:hover {
            transform: scale(1.1);
        }

        .review-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .review-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
@endpush

@php
    use Illuminate\Support\Str;

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
                                    @php
                                        // value kiểu màu?
                                        $isColor = Str::lower($val->type ?? '') === 'color';

                                        // nếu là màu thì tìm 1 variant có value này + có image_url
                                        $colorImage = asset('storage/' . $product->image_main);
                                        if ($isColor) {
                                            $variantForColor = $product->variants->first(function ($v) use ($val) {
                                                return $v->attributes->pluck('id')->contains($val->id) && $v->image_url;
                                            });

                                            if ($variantForColor && $variantForColor->image_url) {
                                                $colorImage = asset('storage/' . $variantForColor->image_url);
                                            }
                                        }
                                    @endphp

                                    @if ($isColor)
                                        @php
                                            // chuyển 'Đen', 'Trắng', 'Navy' → 'den', 'trang', 'navy'
                                            $colorSlug = Str::slug(Str::lower($val->value));
                                        @endphp

                                        <button class="attr-btn color-preview" data-attr="{{ $val->id }}"
                                            data-group="{{ $name }}" data-type="color"
                                            data-image="{{ $colorImage }}">
                                            <span>{{ $val->value }}</span>
                                            <span class="color-dot color-{{ $colorSlug }}"></span>
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



    {{-- TABS: MÔ TẢ & ĐÁNH GIÁ --}}
    <div class="container detail-tabs-wrapper">

        <ul class="detail-tabs">
            <li class="detail-tab-item active" data-tab="tab-description">
                Mô tả sản phẩm
            </li>
            <li class="detail-tab-item" data-tab="tab-reviews">
                Đánh Giá – Nhận Xét Từ Khách Hàng
            </li>
            <li class="detail-tab-item" data-tab="tab-return-policy">
                Chính sách đổi trả
            </li>
            <li class="detail-tab-item" data-tab="tab-privacy">
                Chính sách bảo mật
            </li>
            <li class="detail-tab-item" data-tab="tab-faq">
                Câu hỏi thường gặp
            </li>
        </ul>

        <div class="detail-tabs-content">

            {{-- PANE 1: MÔ TẢ --}}
            <div class="detail-tab-pane active" id="tab-description">
                <div class="product-description-wrap">
                    <div class="product-description" id="product-description">
                        {!! $product->description !!}
                    </div>

                    <button type="button" class="description-toggle" id="descriptionToggle">
                        Xem thêm
                    </button>
                </div>
            </div>


            {{-- PANE 2: ĐÁNH GIÁ --}}
            <div class="detail-tab-pane" id="tab-reviews">

                {{-- FLASH MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success mt-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="product-reviews" class="mt-4">
                    <h3 class="mb-3">Đánh giá sản phẩm</h3>

                    {{-- Tổng quan sao --}}
                    <div class="mb-4">
                        @php $rounded = round($avgRating, 1); @endphp
                        <strong>Điểm trung bình:</strong>
                        <span class="text-warning ms-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($avgRating))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </span>
                        <span class="ms-2">({{ $rounded }}/5 từ {{ $reviewsCount }} đánh giá)</span>
                    </div>

                    {{-- FORM ĐÁNH GIÁ --}}
                    @auth
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Viết đánh giá của bạn</h5>

                                <form action="{{ route('products.reviews.store', $product->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    {{-- Sao --}}
                                    <div class="mb-3">
                                        <label class="form-label d-block">Đánh giá sản phẩm này</label>
                                        <div class="rating-stars">
                                            <input type="hidden" name="rating" id="ratingInput"
                                                value="{{ old('rating', 0) }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="star {{ old('rating') >= $i ? 'active' : '' }}"
                                                    data-value="{{ $i }}">★</span>
                                            @endfor
                                        </div>
                                        @error('rating')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Nội dung --}}
                                    <div class="mb-3">
                                        <label class="form-label">Nội dung đánh giá</label>
                                        <textarea name="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" required>{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ảnh --}}
                                    <div class="mb-3">
                                        <label class="form-label">Ảnh sản phẩm (tùy chọn)</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <small class="text-muted">Tối đa 2MB, jpg/png/webp.</small>
                                        @error('image')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-dark">Gửi</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đánh giá sản phẩm.</p>
                    @endauth

                    {{-- DANH SÁCH ĐÁNH GIÁ --}}
                    <h5 class="mb-3">Đánh giá của khách hàng khác</h5>

                    @forelse ($reviews as $review)
                        <div class="review-card">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>{{ $review->user->name ?? 'Khách hàng' }}</strong>
                                <small class="text-muted">
                                    {{ optional($review->created_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>

                            <div class="text-warning mb-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>

                            <p class="mb-2">{{ $review->comment }}</p>

                            @if ($review->image)
                                <a href="{{ asset('storage/' . $review->image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $review->image) }}" alt="Ảnh đánh giá">
                                </a>
                            @endif
                        </div>
                    @empty
                        <p>Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên!</p>
                    @endforelse
                </div>
            </div>
            {{-- PANE 3: CHÍNH SÁCH ĐỔI TRẢ --}}
            <div class="detail-tab-pane" id="tab-return-policy">
                <h3 class="mb-3">Chính sách đổi trả</h3>
                <ul>
                    <li>Thời gian đổi trả: trong vòng <strong>7 ngày</strong> kể từ khi nhận hàng.</li>
                    <li>Sản phẩm còn nguyên tag, chưa giặt, chưa sử dụng và không bị hư hỏng do khách hàng.</li>
                    <li>Hóa đơn/phiếu mua hàng hoặc thông tin đặt hàng online đầy đủ.</li>
                    <li>Không áp dụng đổi trả với sản phẩm giảm giá sâu, thanh lý trừ khi lỗi do nhà sản xuất.</li>
                </ul>
                <p class="mt-3 mb-1"><strong>Quy trình đổi trả:</strong></p>
                <ol>
                    <li>Liên hệ CSKH qua hotline hoặc fanpage để đăng ký đổi/ trả.</li>
                    <li>Đóng gói sản phẩm và gửi về kho theo hướng dẫn.</li>
                    <li>Sau khi kiểm tra, shop sẽ tiến hành đổi sản phẩm hoặc hoàn tiền theo thỏa thuận.</li>
                </ol>
            </div>

            {{-- PANE 4: CHÍNH SÁCH BẢO MẬT (TÙY CHỌN) --}}
            <div class="detail-tab-pane" id="tab-privacy">
                <h3 class="mb-3">Chính sách bảo mật</h3>
                <p>Chúng tôi cam kết bảo mật tuyệt đối mọi thông tin cá nhân của khách hàng.</p>
                <ul>
                    <li>Thông tin thu thập chỉ dùng cho mục đích xử lý đơn hàng và chăm sóc khách hàng.</li>
                    <li>Không bán, cho thuê hoặc chia sẻ thông tin của bạn cho bên thứ ba khi chưa có sự đồng ý.</li>
                    <li>Dữ liệu được lưu trữ trên hệ thống có biện pháp bảo mật phù hợp.</li>
                </ul>
            </div>

            {{-- PANE 5: CÂU HỎI THƯỜNG GẶP --}}
            <div class="detail-tab-pane" id="tab-faq">
                <h3 class="mb-3">Câu hỏi thường gặp</h3>

                <p class="mb-1"><strong>1. Thời gian giao hàng là bao lâu?</strong></p>
                <p>Thông thường từ <strong>2–5 ngày làm việc</strong> tùy khu vực. Một số khu vực xa có thể lâu hơn.</p>

                <p class="mb-1 mt-3"><strong>2. Phí ship được tính như thế nào?</strong></p>
                <p>Phí vận chuyển được hiển thị ở bước thanh toán, tùy theo địa chỉ nhận hàng và đơn vị vận chuyển.</p>

                <p class="mb-1 mt-3"><strong>3. Tôi có thể kiểm tra hàng trước khi thanh toán không?</strong></p>
                <p>Khách hàng được <strong>kiểm tra ngoại quan sản phẩm</strong> trước khi thanh toán với đơn COD.</p>

                <p class="mb-1 mt-3"><strong>4. Tôi muốn đổi size, phải làm sao?</strong></p>
                <p>Bạn vui lòng giữ sản phẩm nguyên trạng và liên hệ CSKH để được hướng dẫn đổi size trong thời hạn cho
                    phép.</p>
            </div>
        </div>
    </div>
    {{-- END TABS --}}


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
            const main = document.getElementById('main-image');
            if (main) main.src = src;

            document.querySelectorAll('.product-thumbnail')
                .forEach(x => x.classList.remove('active'));
            if (el) el.classList.add('active');
        }

        // ---------------- VARIANTS DATA ----------------
        const VARIANTS = [
            @foreach ($product->variants as $v)
                {
                    id: {{ $v->id }},
                    price: {{ $v->price }},
                    stock: {{ $v->quantity }},
                    image: "{{ $v->image_url ? asset('storage/' . $v->image_url) : '' }}",
                    attrs: [{!! $v->attributes->pluck('id')->join(',') !!}]
                },
            @endforeach
        ];

        let attrButtons = [];
        let variantIdInput = null;

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
        // Cập nhật trạng thái nút +/-
        function refreshQtyButtons() {
            const incBtn = document.getElementById('inc');
            const decBtn = document.getElementById('dec');
            const qty = document.getElementById('qty');

            if (!incBtn || !decBtn || !qty) return;

            const min = parseInt(qty.min) || 1;
            const max = parseInt(qty.max) || 99;
            const val = parseInt(qty.value) || min;

            decBtn.disabled = (val <= min || max === 0);
            incBtn.disabled = (val >= max || max === 0);
        }


        // ---------------- UPDATE VARIANT ----------------
        function updateVariantInfo(v) {
            document.getElementById("price").textContent =
                new Intl.NumberFormat('en-US').format(v.price) + "₫";

            document.getElementById("stock").textContent = v.stock;

            if (variantIdInput) {
                variantIdInput.value = v.id;
            }

            // Đổi ảnh theo variant nếu có
            if (v.image) {
                const mainImg = document.getElementById('main-image');
                if (mainImg) {
                    mainImg.src = v.image;
                }
            }

            const qty = document.getElementById("qty");
            qty.max = v.stock;
            if (qty.value > v.stock) qty.value = v.stock;
            if (v.stock == 0) qty.value = 0;

            document.getElementById("addBtn").disabled = (v.stock === 0);
            refreshQtyButtons();

        }

        // ---------------- RESET TO BASE ----------------
        function resetToBase() {
            document.getElementById("price").textContent =
                "{{ number_format($product->base_price) }}₫";

            document.getElementById("stock").textContent =
                "{{ $product->variants->sum('quantity') }}";

            if (variantIdInput) variantIdInput.value = "";

            attrButtons.forEach(b => b.classList.remove("disabled"));

            const qty = document.getElementById("qty");
            qty.value = 1;
            qty.max = 99;
            refreshQtyButtons();

        }

        // ---------------- NO VARIANT ----------------
        function showNoVariant() {
            if (variantIdInput) variantIdInput.value = "";
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
            refreshQtyButtons();

        }

        // ---------------- ADD TO CART / BUY NOW ----------------
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

        // ================== INIT SAU KHI DOM SẴN SÀNG ==================
        document.addEventListener('DOMContentLoaded', function() {
            // ===== ATTR BUTTONS =====
            attrButtons = document.querySelectorAll('.attr-btn');
            variantIdInput = document.getElementById("variant_id");

            attrButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.dataset.group;
                    const type = this.dataset.type; // "color" nếu là nút màu

                    // Nếu là COLOR thì đổi ảnh ngay theo data-image
                    if (type === 'color') {
                        const imgUrl = this.dataset.image;
                        const mainImg = document.getElementById('main-image');
                        if (imgUrl && mainImg) {
                            mainImg.src = imgUrl;
                        }
                    }

                    // Nếu đang active → huỷ chọn
                    if (this.classList.contains('active')) {
                        this.classList.remove('active');

                        const anyActive = document.querySelectorAll('.attr-btn.active').length > 0;
                        if (!anyActive) {
                            resetToBase();
                        } else {
                            applyVariantFilter();
                        }
                        return;
                    }

                    // Bỏ active cũ trong cùng group
                    document.querySelectorAll(`.attr-btn[data-group="${group}"]`)
                        .forEach(x => x.classList.remove('active'));

                    this.classList.add('active');
                    applyVariantFilter();
                });
            });

            const incBtn = document.getElementById('inc');
            const decBtn = document.getElementById('dec');
            const qtyInput = document.getElementById('qty');

            if (incBtn && decBtn && qtyInput) {
                incBtn.addEventListener('click', function() {
                    const max = parseInt(qtyInput.max) || 99;
                    let val = parseInt(qtyInput.value) || 1;

                    if (val < max) {
                        qtyInput.value = val + 1;
                    }
                    // sau khi đổi số lượng thì cập nhật trạng thái nút
                    refreshQtyButtons();
                });

                decBtn.addEventListener('click', function() {
                    const min = parseInt(qtyInput.min) || 1;
                    let val = parseInt(qtyInput.value) || min;

                    if (val > min) {
                        qtyInput.value = val - 1;
                    }
                    refreshQtyButtons();
                });

                // gọi 1 lần ban đầu
                refreshQtyButtons();
            }


            // ===== TABS =====
            const tabBtns = document.querySelectorAll('.detail-tab-item');
            const tabPanes = document.querySelectorAll('.detail-tab-pane');

            if (tabBtns.length) {
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const targetId = this.dataset.tab;

                        tabBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        tabPanes.forEach(pane => {
                            pane.classList.toggle('active', pane.id === targetId);
                        });
                    });
                });
            }

            // ===== SAO ĐÁNH GIÁ =====
            const stars = document.querySelectorAll('.rating-stars .star');
            const ratingInput = document.getElementById('ratingInput');

            if (stars.length && ratingInput) {
                function setRating(value) {
                    ratingInput.value = value;
                    stars.forEach(star => {
                        const v = Number(star.dataset.value);
                        star.classList.toggle('active', v <= value);
                    });
                }

                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = Number(this.dataset.value);
                        setRating(value);
                    });
                });

                if (ratingInput.value) {
                    setRating(Number(ratingInput.value));
                }
            }

            // ===== MÔ TẢ – XEM THÊM / THU GỌN =====
            const descEl = document.getElementById('product-description');
            const descToggle = document.getElementById('descriptionToggle');

            if (descEl && descToggle) {
                const COLLAPSE_HEIGHT = 260;

                if (descEl.scrollHeight <= COLLAPSE_HEIGHT + 20) {
                    descToggle.style.display = 'none';
                } else {
                    descEl.classList.add('collapsed');

                    descToggle.addEventListener('click', function() {
                        const isCollapsed = descEl.classList.contains('collapsed');
                        descEl.classList.toggle('collapsed');
                        this.textContent = isCollapsed ? 'Thu gọn' : 'Xem thêm';
                    });
                }
            }
        });
    </script>
@endpush

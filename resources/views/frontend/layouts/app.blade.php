<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'EGA')) - Thời trang nam cao cấp</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Pagination CSS - Load sau Bootstrap để override -->
    <style>
        /* CSS pagination với specificity cao để override Bootstrap */
        body .pagination,
        body ul.pagination {
            justify-content: center;
            margin-top: 2rem;
            margin-bottom: 2rem;
            font-size: 0.875rem;
            display: flex;
            flex-wrap: wrap;
        }

        body .pagination .page-link,
        body .pagination li a {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25;
            min-width: 32px;
            max-width: 40px;
            width: auto;
            height: 32px;
            text-align: center;
            border: 1px solid #dee2e6;
            color: #495057;
            background-color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        body .pagination .page-item:first-child .page-link,
        body .pagination .page-item:last-child .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            min-width: 32px;
            max-width: 40px;
            height: 32px;
        }

        body .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #e9ecef;
        }

        body .pagination .page-item.active .page-link {
            background-color: var(--secondary-color, #d4af37);
            border-color: var(--secondary-color, #d4af37);
            color: #fff;
            z-index: 1;
        }

        body .pagination .page-link:hover:not(.disabled) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #495057;
        }

        body .pagination .page-item.active .page-link:hover {
            background-color: var(--secondary-color, #d4af37);
            border-color: var(--secondary-color, #d4af37);
            color: #fff;
        }

        /* Giảm kích thước icon trong pagination */
        body .pagination .page-link svg,
        body .pagination .page-link i {
            width: 0.875rem;
            height: 0.875rem;
            font-size: 0.875rem;
            max-width: 0.875rem;
            max-height: 0.875rem;
        }

        /* Đảm bảo các nút Previous/Next không quá to */
        body .pagination .page-item:first-child,
        body .pagination .page-item:last-child {
            font-size: 0.875rem;
        }
    </style>

    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #d4af37;
            --text-color: #1b1b18;
            --bg-light: #f8f8f8;
            --border-color: #e5e5e5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            background-color: #fff;
        }

        /* Header */
        .header-top {
            background-color: var(--primary-color);
            color: #fff;
            padding: 8px 0;
            font-size: 13px;
        }


        .header-main {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo img {
            max-height: 50px;
            width: auto;
        }


        .search-box {
            position: relative;
        }

        .search-box input {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 10px 40px 10px 15px;
        }

        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #666;
        }

        .header-icons a {
            color: var(--text-color);
            font-size: 20px;
            margin-left: 20px;
            text-decoration: none;
            position: relative;
        }

        .header-icons .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--secondary-color);
            color: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Navigation */
        .main-nav {
            background-color: #fff;
            border-top: 1px solid var(--border-color);
        }

        .main-nav .nav-link {
            color: var(--text-color);
            font-weight: 500;
            padding: 15px 20px;
            text-decoration: none;
            transition: color 0.3s;
        }


        .main-nav .nav-link:hover {
            color: var(--secondary-color);
        }

        /* Product Card */
        .product-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
        }


        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }


        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--secondary-color);
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .product-badge--soldout {
            background: #c53030;
        }


        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-color);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price-current {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-color);
        }

        .price-old {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
        }

        .price-discount {
            font-size: 12px;
            color: #e74c3c;
            font-weight: 600;
        }

        .product-actions {
            margin-top: 10px;
            display: flex;
            gap: 8px;
        }

        .btn-add-cart {
            flex: 1;
            background: var(--primary-color);
            color: #fff;
            border: none;
            padding: 8px;
            border-radius: 4px;
            font-size: 13px;
            transition: background 0.3s;
        }


        .btn-add-cart:hover {
            background: #333;
        }

        .btn-quick-view {
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 13px;
            color: var(--text-color);
            transition: all 0.3s;
        }


        .btn-quick-view:hover {
            background: var(--bg-light);
        }

        /* Footer */
        .footer {
            background-color: var(--primary-color);
            color: #fff;
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        .footer h5 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }


        .footer a:hover {
            color: var(--secondary-color);
        }

        /* Cart Sidebar */
        .cart-sidebar-item {
            position: relative;
            transition: opacity 0.2s ease;
        }

        .remove-cart-item {
            cursor: pointer;
            transition: all 0.2s ease;
            opacity: 0.6;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-cart-item:hover {
            opacity: 1;
            transform: scale(1.1);
            color: #dc3545 !important;
        }

        .cart-sidebar {
            position: fixed;
            right: -400px;
            top: 0;
            width: 400px;
            height: 100vh;
            background: #fff;

            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            transition: right 0.3s;
            z-index: 2000;
            overflow-y: auto;
        }


        .cart-sidebar.open {
            right: 0;
        }

        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.5);
            z-index: 1999;
            display: none;
        }

        .cart-overlay.show {
            display: block;
        }

        /* Section Title */
        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Category Grid */
        .category-card {
            text-align: center;
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--text-color);
            display: block;
        }

        .category-card:hover {
            border-color: var(--secondary-color);
            transform: translateY(-5px);
        }

        .category-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .user-menu-dropdown {
            min-width: 220px;
            padding: 0.4rem 0;
        }

        /* bỏ margin mặc định của form để không bị thụt */
        .user-menu-dropdown li form {
            margin: 0;
        }

        /* TẤT CẢ item (a + button) đều chung style này */
        .user-menu-dropdown .dropdown-item {
            display: flex !important;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 1rem;

            font-size: 0.9rem;
            /* chữ to vừa phải, đều với Logout */
        }

        /* text bên trong */
        .user-menu-dropdown .dropdown-item span {
            font-size: 0.9rem;
        }

        /* icon: cùng width để thẳng hàng */
        .user-menu-dropdown .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
            /* icon to hơn chút */
        }
    </style>


    @stack('styles')
</head>

<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span>Hotline: 0964942121 (8:30-21:30, Tất cả các ngày trong tuần)</span>
                </div>
                <div class="col-md-6 text-end">
                    {{-- link Liên hệ dẫn tới trang Liên hệ & Hỗ trợ --}}
                    <a href="{{ route('contact.index') }}" class="text-white me-3">Liên hệ</a>

                    <a href="#" class="text-white">Thông báo của tôi</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Main -->
    <header class="header-main">
        <div class="container">
            <div class="row align-items-center py-3">
                <div class="col-md-3">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('logo-ega-horizontal.svg') }}" alt="EGA">
                    </a>
                </div>
                <div class="col-md-6">
                    <div class="search-box">
                        <form method="GET" class="search-box">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                                value="{{ request('search') }}">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="col-md-3 d-flex justify-content-end align-items-center header-icons">
                    @auth
                        <div class="dropdown me-3">
                            <a href="#" class="dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-user fa-lg"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end user-menu-dropdown" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-id-card fa-sm"></i>
                                        <span>Trang cá nhân</span>
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('order.index') }}">
                                        <i class="fas fa-receipt fa-sm"></i>
                                        <span>Đơn hàng của tôi</span>
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item d-flex align-items-center" type="submit">
                                            <i class="fas fa-sign-out-alt fa-sm"></i>
                                            <span>Đăng xuất</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>

                        </div>
                    @else
                        <a href="{{ route('login') }}" class="me-3"><i class="fas fa-user fa-lg"></i></a>
                    @endauth

                    <a href="#" id="cart-toggle" class="position-relative">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                        <span
                            class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                </div>



            </div>
        </div>

        <!-- Navigation -->
        <nav class="main-nav">
            <div class="container">
                <div class="d-flex">
                    <a href="{{ route('home') }}" class="nav-link">Trang chủ</a>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                Sản phẩm
                            </a>
                            <ul class="dropdown-menu">
                                @php
                                    $navCategories = \App\Models\Category::whereNull('parent_id')
                                        ->where('status', 1)
                                        ->limit(6)
                                        ->get();
                                @endphp
                                @foreach ($navCategories as $category)
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}"
                                        class="nav-link">{{ $category->name }}</a>
                                @endforeach
                            </ul>
                        </li>
                    </ul>


                    {{-- Link Liên hệ & Hỗ trợ trên menu chính --}}
                    <a href="{{ route('contact.index') }}" class="nav-link">Liên hệ &amp; Hỗ trợ</a>
                    {{-- Thêm dòng này --}}
                    <a href="{{ route('blog.index') }}" class="nav-link">Tin tức / Blog</a>

                    {{-- @php
                        $navCategories = \App\Models\Category::whereNull('parent_id')
                            ->where('status', 1)
                            ->limit(6)
                            ->get();
                    @endphp
                    @foreach ($navCategories as $category)
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="nav-link">{{ $category->name }}</a>
                    @endforeach --}}
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Thông tin liên hệ</h5>
                    <p>Địa chỉ: Tầng 8, tòa nhà Ford, số 313 Trường Chinh, quận Thanh Xuân, Hà Nội</p>
                    <p>Điện thoại: 0964942121</p>
                    <p>Email: cskh@ega.vn</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Nhóm liên kết</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Tìm kiếm</a></li>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Đăng ký nhận tin</h5>
                    <p>Để cập nhật những sản phẩm mới, nhận thông tin ưu đãi đặc biệt</p>
                    <div class="input-group mt-3">
                        <input type="email" class="form-control" placeholder="Nhập email của bạn">
                        <button class="btn btn-outline-light" type="button">Đăng ký</button>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: #444;">
            <div class="text-center">
                <p class="mb-0">Copyright © 2025 EGA. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-overlay" id="cart-overlay"></div>
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Giỏ hàng</h4>
                <button class="btn btn-sm" id="cart-close"><i class="fas fa-times"></i></button>
            </div>
            <div id="cart-content">
                <p class="text-center text-muted">Chưa có sản phẩm trong giỏ hàng</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load cart count on page load
        function loadCartCount() {
            @auth

            fetch('{{ route('cart.count') }}')
                .then(response => response.json())
                .then(data => {
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    cartCountElements.forEach(el => {
                        el.textContent = data.count || 0;
                        el.style.display = data.count > 0 ? 'flex' : 'none';
                    });
                })
                .catch(error => console.error('Error loading cart count:', error));
        @else
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = 0;
                el.style.display = 'none';
            });
        @endauth
        }

        // Load cart sidebar content
        function loadCartSidebar() {
            fetch('{{ route('cart.sidebar') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-content').innerHTML = data.html;
                    // Attach remove event listeners after loading
                    attachRemoveItemListeners();
                })
                .catch(error => {
                    console.error('Error loading cart sidebar:', error);

                    document.getElementById('cart-content').innerHTML =
                        '<p class="text-center text-muted">Có lỗi xảy ra khi tải giỏ hàng</p>';
                });
        }

        // Attach remove item event listeners
        function attachRemoveItemListeners() {
            document.querySelectorAll('.remove-cart-item').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const itemId = this.getAttribute('data-item-id');
                    removeItemFromSidebar(itemId);
                });
            });
        }

        // Remove item from sidebar
        function removeItemFromSidebar(itemId) {
            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                return;
            }

            const itemElement = document.querySelector(`.cart-sidebar-item[data-item-id="${itemId}"]`);
            const removeButton = document.querySelector(`.remove-cart-item[data-item-id="${itemId}"]`);

            if (itemElement) {
                itemElement.style.opacity = '0.5';
                itemElement.style.pointerEvents = 'none';
            }

            if (removeButton) {
                removeButton.disabled = true;
                removeButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`{{ url('cart') }}/${itemId}/remove`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload sidebar to update content (will show empty message if cart is empty)
                        loadCartSidebar();
                        // Update cart count
                        loadCartCount();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                        if (itemElement) {
                            itemElement.style.opacity = '1';
                            itemElement.style.pointerEvents = 'auto';
                        }
                        if (removeButton) {
                            removeButton.disabled = false;
                            removeButton.innerHTML = '<i class="fas fa-times"></i>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm');
                    if (itemElement) {
                        itemElement.style.opacity = '1';
                        itemElement.style.pointerEvents = 'auto';
                    }
                    if (removeButton) {
                        removeButton.disabled = false;
                        removeButton.innerHTML = '<i class="fas fa-times"></i>';
                    }
                });
        }

        // Cart Toggle
        document.getElementById('cart-toggle')?.addEventListener('click', function(e) {
            e.preventDefault();
            @auth
            loadCartSidebar();
        @endauth
        document.getElementById('cart-sidebar').classList.add('open'); document.getElementById('cart-overlay')
        .classList.add('show');
        });

        document.getElementById('cart-close')?.addEventListener('click', function() {
            document.getElementById('cart-sidebar').classList.remove('open');
            document.getElementById('cart-overlay').classList.remove('show');
        });

        document.getElementById('cart-overlay')?.addEventListener('click', function() {
            document.getElementById('cart-sidebar').classList.remove('open');
            this.classList.remove('show');
        });

        // Load cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCartCount();
        });

        // Global function to update cart count (used by other pages)
        window.updateCartCount = function(count) {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = count || 0;
                el.style.display = (count > 0) ? 'flex' : 'none';
            });
        };

        // Global function to add to cart from product card
        window.addToCartFromCard = function(productId, variantId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }


            const btn = event ? event.target.closest('button') : null;
            const originalText = btn ? btn.innerHTML : '';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang thêm...';
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
                        quantity: 1
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

                        // Show success message (you can replace with toast notification)
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Đã thêm vào giỏ hàng!');
                        } else {
                            alert(data.message || 'Đã thêm vào giỏ hàng!');
                        }
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
        };
    </script>

    @stack('scripts')
    <!-- Nút chat tròn -->
    <div id="chat-toggle"
        style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 55px;
        height: 55px;
        background: #1877f2;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 26px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 999999;
     ">
        💬
    </div>

    <!-- CHATBOX MESSENGER -->
    <div id="chat-box"
        style="
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 330px;
        max-height: 420px;
        background: #fff;
        border-radius: 12px;
        display: none;
        flex-direction: column;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        z-index: 999998;
     ">

        <!-- Header -->
        <div
            style="
        background: #1877f2;
        padding: 12px;
        color: white;
        font-weight: bold;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        gap: 10px;
    ">
            <img src="https://cdn-icons-png.flaticon.com/512/1946/1946429.png"
                style="width: 32px; height: 32px; border-radius: 50%;">
            Trợ lý AI – Hỗ trợ khách hàng
        </div>

        <!-- Nội dung chat -->
        <div id="chat-messages"
            style="
            padding: 10px;
            overflow-y: auto;
            height: 300px;
            background: #f0f2f5;
         ">
        </div>

        <!-- Thanh nhập tin -->
        <div style="padding: 10px; display: flex; gap: 5px; background: #fff;">
            <input id="chat-input" type="text" placeholder="Nhập tin nhắn..."
                style="
                    flex: 1;
                    padding: 8px;
                    border-radius: 20px;
                    border: 1px solid #ccc;
               ">
            <button onclick="sendChat()"
                style="
                    padding: 8px 14px;
                    border-radius: 20px;
                    border: none;
                    background: #1877f2;
                    color: white;
               ">
                Gửi
            </button>
        </div>
    </div>

    <script>
        // Lấy CSRF token
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ⭐ Hover vào nút → mở chat
        document.getElementById("chat-toggle").addEventListener("click", function() {
            const box = document.getElementById("chat-box");
            box.style.display = box.style.display === "flex" ? "none" : "flex";
        });


        // ⭐ Rời khỏi khung chat → đóng
        document.addEventListener("click", function(e) {
            if (
                isChatOpen &&
                !chatBox.contains(e.target) &&
                !chatToggle.contains(e.target)
            ) {
                isChatOpen = false;
                chatBox.style.display = "none";
            }
        });

        // ⭐ Enter để gửi
        document.getElementById("chat-input").addEventListener("keypress", function(e) {
            if (e.key === "Enter") sendChat();
        });

        // ⭐ Hàm gửi tin
        async function sendChat() {
            const input = document.getElementById('chat-input');
            const text = input.value.trim();
            if (!text) return;

            const box = document.getElementById("chat-messages");

            // Tin người dùng (bên phải)
            box.innerHTML += `
            <div style="display: flex; justify-content: flex-end; margin-bottom: 8px;">
                <div style="
                    background: #1877f2;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 16px;
                    max-width: 70%;
                    font-size: 14px;
                ">
                    ${text}
                </div>
                <img src="https://cdn-icons-png.flaticon.com/512/1946/1946429.png"
                     style="width: 28px; height: 28px; border-radius: 50%; margin-left: 6px;">
            </div>
        `;
            input.value = "";

            // Loading
            box.innerHTML += `
            <div id="typing" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png"
                     style="width: 28px; height: 28px; border-radius: 50%;">
                <div style="
                    background: #e4e6eb;
                    padding: 6px 12px;
                    border-radius: 18px;
                    font-size: 13px;
                    color: #555;
                ">
                    AI đang nhập...
                </div>
            </div>
        `;

            box.scrollTop = box.scrollHeight;

            try {
                // ⭐⭐ GỌI API ĐÚNG ROUTE + CÓ CSRF
                const res = await fetch('{{ route('ai.chat') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        message: text
                    })
                });

                const data = await res.json();
                document.getElementById("typing")?.remove();

                const answer = data.answer ||
                    'Xin lỗi, mình chưa nhận được câu trả lời phù hợp. Bạn thử hỏi lại giúp mình nhé.';

                // Tin AI (bên trái)
                box.innerHTML += `
                <div style="display: flex; justify-content: flex-start; margin-bottom: 8px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png"
                         style="width: 28px; height: 28px; border-radius: 50%; margin-right: 6px;">
                    <div style="
                        background: #e4e6eb;
                        padding: 8px 12px;
                        border-radius: 16px;
                        max-width: 70%;
                        font-size: 14px;
                    ">
                        ${answer}
                    </div>
                </div>
            `;
            } catch (e) {
                console.error(e);
                document.getElementById("typing")?.remove();

                box.innerHTML += `
                <div style="display: flex; justify-content: flex-start; margin-bottom: 8px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png"
                         style="width: 28px; height: 28px; border-radius: 50%; margin-right: 6px;">
                    <div style="
                        background: #e4e6eb;
                        padding: 8px 12px;
                        border-radius: 16px;
                        max-width: 70%;
                        font-size: 14px;
                        color: #c53030;
                    ">
                        Xin lỗi, hệ thống đang gặp lỗi. Bạn thử lại sau ít phút nhé.
                    </div>
                </div>
            `;
            }

            box.scrollTop = box.scrollHeight;
        }
    </script>



</body>

</html>

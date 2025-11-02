<div class="sidebar" data-color="purple" data-image="{{ asset('assets/img/sidebar-5.jpg') }}">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text">Fashion Admin</a>
        </div>
        <ul class="nav">
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="pe-7s-graph"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                <a href="">
                    <i class="pe-7s-box2"></i>
                    <p>Products</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="pe-7s-cart"></i>
                    <p>Orders</p>
                </a>
            </li>
            <li class="{{ request()->is('admin/accounts*') ? 'active' : '' }}">
                <a href="{{ route('admin.accounts.index') }}">
                    <i class="pe-7s-users"></i> {{-- icon pe-7s đồng bộ với menu --}}
                    <p>Quản lý tài khoản Admin</p>
                </a>
            </li>

            </li>
            {{-- Quản lý nội dung (Menu đa cấp) --}}
            @php
                // Mở sẵn nếu đang ở trang con (giữ UX tốt khi reload hoặc đi thẳng link sâu)
                $contentOpen =
                    request()->is('admin/banners*') || request()->is('admin/posts*') || request()->is('admin/pages*');
            @endphp

            <li id="contentMenu" class="nav-item {{ $contentOpen ? 'menu-open' : '' }}"
                aria-expanded="{{ $contentOpen ? 'true' : 'false' }}">
                <a href="#" class="nav-link toggle-submenu {{ $contentOpen ? 'active' : '' }}"
                    data-toggle="treeview">
                    <i class="bi bi-folder2-open nav-icon"></i>
                    <p>Quản lý nội dung</p>
                </a>

                <ul class="nav nav-treeview" style="display: {{ $contentOpen ? 'block' : 'none' }};">
                    {{-- Banner --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.banners.index') }}"
                            class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                            <i class="bi bi-image nav-icon"></i>
                            <p>Banner</p>
                        </a>
                    </li>

                    {{-- Post --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.posts.index') }}"
                            class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-text nav-icon"></i>
                            <p>Post</p>
                        </a>
                    </li>

                    
                </ul>
            </li>


        </ul>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cho mọi menu đa cấp dùng data-toggle="treeview"
        document.querySelectorAll('[data-toggle="treeview"]').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault(); // không điều hướng khi bấm tiêu đề nhóm
                const li = this.closest('li.nav-item');
                const sub = li.querySelector('.nav-treeview');
                const isOpen = li.classList.toggle('menu-open');

                // set trạng thái hiển thị
                if (sub) sub.style.display = isOpen ? 'block' : 'none';
                // đánh dấu active cho header nhóm
                this.classList.toggle('active', isOpen);
                // ARIA for accessibility
                li.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    });
</script>

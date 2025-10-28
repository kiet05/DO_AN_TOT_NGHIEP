<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống</title>

    {{-- AdminLTE & Bootstrap qua CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- Navbar --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="bi bi-list"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                </li>
            </ul>
        </nav>

        {{-- Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">Quản Trị Admin</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">

                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.index') }}"
                                class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        {{-- Quản lý nội dung (parent) --}}
                        @php
                            $contentOpen = request()->routeIs('admin.banners.*') || request()->routeIs('admin.posts.*');
                        @endphp
                        <li class="nav-item {{ $contentOpen ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $contentOpen ? 'active' : '' }}">
                                <i class="bi bi-folder2-open nav-icon"></i>
                                <p>
                                    Quản lý nội dung
                                    <i class="right bi bi-chevron-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                {{-- Banner --}}
                                <li class="nav-item">
                                    <a href="{{ route('admin.banners.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                                        <i class="bi bi-image nav-icon"></i>
                                        <p>Banner</p>
                                    </a>
                                </li>

                                {{-- Bài viết --}}
                                <li class="nav-item">
                                    <a href="{{ route('admin.posts.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                                        <i class="bi bi-journal-text nav-icon"></i>
                                        <p>Post</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.pages.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                                        <i class="bi bi-file-earmark-richtext nav-icon"></i>
                                        <p>Page</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>


        {{-- Nội dung chính --}}
        <div class="content-wrapper p-4">
            @yield('content')
        </div>

    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>

</html>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Dashboard')</title>

  {{-- Bootstrap 5.3.3 (CHỈ 1 BẢN) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Custom admin CSS --}}
  <link rel="stylesheet" href="{{ asset('admin/css/admin-style.css') }}">

  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

  {{-- Header --}}
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('admin.orders.index') }}">📦 Admin Dashboard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link">Xin chào, {{ auth()->user()->name ?? 'Admin' }}</span>
          </li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-light">Đăng xuất</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid flex-grow-1">
    <div class="row">
      {{-- Sidebar --}}
      <nav id="sidebar" class="col-md-2 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                📦 Quản lý đơn hàng
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                👤 Quản lý khách hàng
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->is('admin/vouchers*') ? 'active' : '' }}" href="#">
                🎟 Quản lý voucher
              </a>
            </li>
            {{-- Thêm menu khác nếu cần --}}
          </ul>
        </div>
      </nav>

      {{-- Main content --}}
      <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
        @yield('content')
      </main>
    </div>
  </div>

  {{-- Footer --}}
  <footer class="bg-dark text-white text-center py-2 mt-auto">
    &copy; {{ date('Y') }} Admin Dashboard
  </footer>

  {{-- Bootstrap JS bundle (đã gồm Popper) — CHỈ 1 BẢN --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  @stack('scripts')

  {{-- (Tuỳ chọn) tăng z-index để dropdown không bị che/cắt --}}
  <style>
    .dropdown-menu { z-index: 1060; }
  </style>
</body>
</html>

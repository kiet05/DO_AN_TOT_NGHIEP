<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Fashion Admin')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- CSS gốc LBD --}}
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/light-bootstrap-dashboard.css?v=1.4.0') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/demo.css') }}" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet">
  <link href="{{ asset('assets/css/pe-icon-7-stroke.css') }}" rel="stylesheet">

  {{-- Vá dropdown bị ẩn/cắt --}}
  <style>
    .dropdown-menu {
        z-index: 2060 !important;
        display: none;
        position: absolute !important;
    }
    .dropdown.open > .dropdown-menu {
        display: block !important;
    }
    .table-wrap { overflow: visible !important; }
    .card-body, .content, .main-panel, .container-fluid { position: relative; }
  </style>

  @stack('styles')
</head>

<body>
  <div class="wrapper">
    @include('layouts.admin.sidebar')

    <div class="main-panel">
      @include('layouts.admin.navbar')

      <div class="content">
        <div class="container-fluid">
          @yield('content')
        </div>
      </div>

      <footer class="footer">
        <div class="container-fluid text-center">
          <p class="copyright pull-right mb-0">
            &copy; {{ date('Y') }} Fashion Admin
          </p>
        </div>
      </footer>
    </div>
  </div>

  {{-- JS gốc LBD --}}
  <script src="{{ asset('assets/js/jquery.3.2.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap-notify.js') }}"></script>
  <script src="{{ asset('assets/js/light-bootstrap-dashboard.js?v=1.4.0') }}"></script>
  <script src="{{ asset('assets/js/demo.js') }}"></script>

  {{-- ✅ Vá dropdown bằng JS thuần, bỏ phụ thuộc Bootstrap --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Bắt tất cả các nút dropdown
      document.querySelectorAll('[data-bs-toggle="dropdown"], [data-toggle="dropdown"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          const parent = btn.closest('.dropdown, .btn-group');
          const menu = parent ? parent.querySelector('.dropdown-menu') : null;

          // Ẩn các dropdown khác
          document.querySelectorAll('.dropdown.open').forEach(d => {
            if (d !== parent) d.classList.remove('open');
          });

          // Toggle mở/đóng
          if (parent && menu) {
            parent.classList.toggle('open');
          }
        });
      });

      // Click ngoài sẽ đóng lại dropdown
      document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown, .btn-group')) {
          document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
        }
      });

      // Chặn click trong menu lan ra
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
      });
    });
  </script>

  @stack('scripts')
</body>
</html>

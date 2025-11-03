<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sherah - HTML eCommerce Dashboard Template')</title>

    <!-- Moved CSS links from original -->
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <link rel="icon" href="img/favicon.png">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/charts.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jvector-map.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slickslider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('/style.css') }}">
    @stack('styles')
</head>

<body id="sherah-dark-light">
    <div class="sherah-body-area">
        @include('partials.navbar')
        @include('partials.header')

        <main class="sherah-main-content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts from original -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-migrate.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/charts.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-jvectormap.js') }}"></script>
    <script src="{{ asset('assets/js/jvector-map.js') }}"></script>
    <script src="{{ asset('assets/js/slickslider.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>

</html>

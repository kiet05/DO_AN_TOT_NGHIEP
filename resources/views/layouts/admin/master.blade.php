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

    {{-- Shop Information Modal --}}
    @php
        $shopSetting = \App\Models\ShopSetting::first();
    @endphp
    <div class="modal fade" id="shopInfoModal" tabindex="-1" aria-labelledby="shopInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shopInfoModalLabel">Thông tin shop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($shopSetting)
                        <div class="row">
                            @if($shopSetting->logo)
                                <div class="col-12 text-center mb-4">
                                    <img src="{{ asset('storage/' . $shopSetting->logo) }}" alt="Logo" style="max-width: 200px; max-height: 200px;">
                                </div>
                            @endif
                            
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-phone"></i> Hotline:</strong>
                                <p>{{ $shopSetting->hotline ?? 'Chưa cập nhật' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-envelope"></i> Email:</strong>
                                <p>{{ $shopSetting->email ?? 'Chưa cập nhật' }}</p>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <strong><i class="fas fa-map-marker-alt"></i> Địa chỉ:</strong>
                                <p>{{ $shopSetting->address ?? 'Chưa cập nhật' }}</p>
                            </div>
                            
                            @if($shopSetting->facebook || $shopSetting->instagram || $shopSetting->zalo || $shopSetting->tiktok || $shopSetting->youtube || $shopSetting->twitter)
                                <div class="col-12 mt-4">
                                    <h6>Mạng xã hội:</h6>
                                    <div class="row">
                                        @if($shopSetting->facebook)
                                            <div class="col-md-6 mb-2">
                                                <strong><i class="fab fa-facebook"></i> Facebook:</strong>
                                                <p><a href="{{ $shopSetting->facebook }}" target="_blank">{{ $shopSetting->facebook }}</a></p>
                                            </div>
                                        @endif
                                        
                                        @if($shopSetting->instagram)
                                            <div class="col-md-6 mb-2">
                                                <strong><i class="fab fa-instagram"></i> Instagram:</strong>
                                                <p><a href="{{ $shopSetting->instagram }}" target="_blank">{{ $shopSetting->instagram }}</a></p>
                                            </div>
                                        @endif
                                        
                                        @if($shopSetting->zalo)
                                            <div class="col-md-6 mb-2">
                                                <strong>Zalo:</strong>
                                                <p>{{ $shopSetting->zalo }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($shopSetting->tiktok)
                                            <div class="col-md-6 mb-2">
                                                <strong><i class="fab fa-tiktok"></i> TikTok:</strong>
                                                <p><a href="{{ $shopSetting->tiktok }}" target="_blank">{{ $shopSetting->tiktok }}</a></p>
                                            </div>
                                        @endif
                                        
                                        @if($shopSetting->youtube)
                                            <div class="col-md-6 mb-2">
                                                <strong><i class="fab fa-youtube"></i> YouTube:</strong>
                                                <p><a href="{{ $shopSetting->youtube }}" target="_blank">{{ $shopSetting->youtube }}</a></p>
                                            </div>
                                        @endif
                                        
                                        @if($shopSetting->twitter)
                                            <div class="col-md-6 mb-2">
                                                <strong><i class="fab fa-twitter"></i> Twitter/X:</strong>
                                                <p><a href="{{ $shopSetting->twitter }}" target="_blank">{{ $shopSetting->twitter }}</a></p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-center">Chưa có thông tin shop. Vui lòng cập nhật tại <a href="{{ route('admin.shop-settings.edit') }}">Cài đặt thông tin shop</a></p>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.shop-settings.edit') }}" class="btn btn-primary">Chỉnh sửa</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

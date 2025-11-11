<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head-auth')
    </head>
    <body>
        <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 p-4">
            <div class="auth-container w-100" style="max-width: 420px; padding: 40px;">
                <div class="text-center mb-4">
                    <div class="auth-logo">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h1 class="auth-title">{{ config('app.name', 'EGA') }}</h1>
                </div>
                
                {{ $slot }}
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>

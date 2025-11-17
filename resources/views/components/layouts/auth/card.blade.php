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
                        <img src="{{ asset('logo-ega-icon.svg') }}" alt="EGA" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <p class="auth-subtitle" style="color: #718096; font-size: 12px; margin-top: 10px;">GENTLEMEN'S FASHION</p>
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

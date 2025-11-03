<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            body {
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }
        </style>
    </head>
    <body class="min-h-screen antialiased">
        {{ $slot }}
        @fluxScripts
    </body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    {{-- Vite + Livewire --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 dark:bg-zinc-900 text-gray-900 dark:text-white min-h-screen flex items-center justify-center">

    {{-- Container chính --}}
    <div class="w-full max-w-md bg-white dark:bg-zinc-800 shadow-xl rounded-2xl p-8 space-y-6">
        {{-- Logo hoặc tiêu đề --}}
        <div class="text-center mb-6">
            <a href="/" class="text-2xl font-semibold tracking-tight text-primary-600 dark:text-primary-400">
                {{ config('app.name', 'MyApp') }}
            </a>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $subtitle ?? 'Xác thực tài khoản của bạn' }}
            </p>
        </div>

        {{-- Nội dung Livewire --}}
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>

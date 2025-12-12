<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/png" href="{{ asset('images/pizza-icon.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gradient-to-br from-orange-50 via-white to-amber-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 overflow-hidden">
            <div class="absolute -top-24 -left-24 size-72 rounded-full bg-orange-200/40 blur-3xl dark:bg-orange-500/10"></div>
            <div class="absolute -bottom-16 -right-10 size-80 rounded-full bg-amber-200/40 blur-3xl dark:bg-amber-500/10"></div>
            <div class="relative z-10 font-sans text-gray-900 dark:text-gray-100 px-4 sm:px-6 lg:px-10 py-10 space-y-8">
                <header class="flex items-center gap-4">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('images/pizza-icon.png') }}" alt="Pizza Planet logo" class="size-12 drop-shadow-md">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-orange-600 dark:text-orange-300">Welcome to</p>
                            <p class="text-2xl font-bold">Pizza Planet</p>
                        </div>
                    </a>
                </header>

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

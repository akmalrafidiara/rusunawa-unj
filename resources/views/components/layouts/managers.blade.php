<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <x-layouts.managers.sidebar />

        <!-- Mobile User Menu -->
        <x-layouts.managers.mobile-menu />

        <flux:main>
            {{ $slot }}
        </flux:main>

        @fluxScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
    </body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <x-layouts.frontend.navbar />

        {{ $slot }}

        <livewire:frontend.footer />

        @auth('web')
            <a href="{{ route('dashboard') }}" {{-- Ganti dengan nama rute dashboard Anda --}}
                class="fixed bottom-5 right-5 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-lg transition-transform duration-300 ease-in-out hover:scale-110 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-300"
                aria-label="Kembali ke Dashboard Manager" title="Kembali ke Dashboard Manager">

                <flux:icon name="pencil" class="h-7 w-7" />
            </a>
        @endauth
        @fluxScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    </body>

</html>

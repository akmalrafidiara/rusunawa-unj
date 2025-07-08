<?php

use App\Models\Content;

$LogoTitle = optional(Content::where('content_key', 'logo_title')->first())->content_value ?? '';

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div
        class="relative flex flex-col min-h-screen lg:grid lg:grid-cols-2 lg:h-dvh lg:max-w-none lg:px-0 lg:items-center lg:justify-center">
        {{-- Ini adalah div untuk SLOT (Kiri di Desktop, Bawah di Mobile) --}}
        <div class="w-full lg:p-8 relative flex-grow bg-white dark:bg-zinc-800 -mt-8 rounded-t-3xl shadow-lg lg:mt-0 lg:rounded-none lg:shadow-none lg:h-full">
            {{-- Logo BPU untuk Desktop Saja --}}
            <div class="absolute top-8 left-12 z-20 hidden lg:block">
                <a href="{{ route('home') }}" class="flex items-center font-medium" wire:navigate>
                    <span class="flex h-25 w-25 items-center justify-center rounded-md">
                        <x-default.app-logo-icon/>
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    <span class="ml-2 text-xl font-semibold text-black dark:text-white">{{$LogoTitle}}</span>
                </a>
            </div>

            {{-- DIV SLOT UTAMA - Ubah ini untuk menengahkan konten secara vertikal dan memberi jarak --}}
            {{-- Tambahkan 'h-full' agar flexbox mengambil tinggi penuh, dan 'items-center' untuk menengahkan --}}
            {{-- Tambahkan 'pt-20' untuk jarak dari logo di atas, hanya di desktop (lg:pt-20) --}}
            <div class="flex w-full flex-col justify-center space-y-6 p-8 lg:p-0c lg:h-full lg:items-center lg:pt-20 lg:pl-4">
                {{ $slot }}
            </div>
        </div>

        {{-- Ini adalah div untuk BANNER/GAMBAR (Kanan di Desktop, Atas di Mobile) --}}
        <div
            class="relative h-64 lg:h-full bg-muted overflow-hidden order-first lg:order-last flex-none lg:flex-col lg:p-0 lg:flex dark:border-e dark:border-neutral-800">
            <div class="absolute inset-0"
                style="background-image: url('{{ asset('images/banner-image-login.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
                aria-hidden="true">
            </div>
            {{-- Logo dan Tulisan "Rusunawa UNJ" untuk Mobile Saja --}}
            <div class="absolute top-4 left-4 z-20 flex flex-col items-start lg:hidden">
                <a href="{{ route('home') }}" class="flex items-center font-medium" wire:navigate>
                    <span class="flex h-25 w-25 items-center justify-center rounded-md">
                        <x-default.app-logo-icon class="size-20 fill-current text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <span class="-mt-6 px-2 text-black text-xl font-semibold">{{$LogoTitle}}</span>
            </div>
        </div>

    </div>
    @fluxScripts
</body>

</html>
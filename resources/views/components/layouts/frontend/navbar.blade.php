<?php

use App\Models\Content;

$LogoTitle = optional(Content::where('content_key', 'logo_title')->first())->content_value ?? '';

$navMenu = [
    'home' => [
        'label' => 'Beranda',
        'route' => route('home'),
        'active' => request()->routeIs('home'),
    ],
    'tenancy' => [
        'label' => 'Sewa Kamar',
        'route' => route('tenancy.index'),
        'active' => request()->routeIs('tenancy.index'),
    ],
    'complaint' => [
        'label' => 'Pengaduan',
        'route' => route('complaint.track-complaint'),
        'active' => request()->routeIs('complaint.track-complaint'),
    ],
    'announcement' => [
        'label' => 'Pengumuman',
        'route' => route('announcement.index'),
        'active' => request()->routeIs('announcement.index'),
    ],
    'rules' => [
        'label' => 'Tata Tertib',
        'route' => route('rules.index'),
        'active' => request()->routeIs('rules.index'),
    ],
];
?>

<div x-data="{ open: false }" class="bg-white dark:bg-zinc-800 shadow-md">
    <div class="container mx-auto flex items-center justify-between py-4 px-6">
        {{-- Logo --}}
        <a href="/" class="flex items-center space-x-2">
            <span class="flex h-8">
                <x-default.app-logo-icon />
            </span>
            <span class="font-bold text-lg text-gray-800 dark:text-white hidden md:inline">{{ $LogoTitle }}</span>
        </a>

        {{-- Desktop Navigation and User Menu --}}
        <div class="hidden md:flex items-center space-x-8">
            {{-- Navigasi --}}
            <nav class="flex items-center space-x-8">
                @foreach ($navMenu as $key => $menu)
                    <a href="{{ $menu['route'] }}" wire:navigate
                        class="relative {{ $menu['active']
                            ? 'text-green-600 font-semibold before:absolute before:-bottom-0.5 before:left-0 before:w-full before:h-0.5 before:bg-green-600 before:content-[\'\']'
                            : 'text-gray-900 dark:text-gray-300 hover:text-green-600 transition' }}">
                        {{ $menu['label'] }}
                    </a>
                @endforeach

                {{-- Desktop Theme Selector (Dropdown List) --}}
                <div x-data="{ showThemeSelector: false }" class="relative">
                    <button @click="showThemeSelector = !showThemeSelector"
                        class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">
                        <flux:icon name="sun" class="w-5 h-5" />
                    </button>

                    <div x-show="showThemeSelector" x-transition @click.outside="showThemeSelector = false"
                        class="absolute right-0 top-full mt-2 z-50 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-1.5 min-w-[120px]">
                        {{-- Theme options as list items --}}
                        <button @click="$flux.appearance = 'light'; showThemeSelector = false"
                            class="flex items-center space-x-2 w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-green-600">
                            <flux:icon name="sun" class="w-4 h-4" />
                            <span>{{ __('Light') }}</span>
                        </button>
                        <button @click="$flux.appearance = 'dark'; showThemeSelector = false"
                            class="flex items-center space-x-2 w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-green-600">
                            <flux:icon name="moon" class="w-4 h-4" />
                            <span>{{ __('Dark') }}</span>
                        </button>
                        <button @click="$flux.appearance = 'system'; showThemeSelector = false"
                            class="flex items-center space-x-2 w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-green-600">
                            <flux:icon name="computer-desktop" class="w-4 h-4" />
                            <span>{{ __('System') }}</span>
                        </button>
                    </div>
                </div>
            </nav>

            @if (Route::has('occupant.auth'))
                <div class="hidden md:block">
                    @auth('occupant')
                        {{-- Authenticated Occupant Button --}}
                        <a href="{{ route('occupant.dashboard') }}" wire:navigate
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('occupant.auth') }}" wire:navigate
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                            Masuk
                        </a>
                    @endauth
                </div>
            @endif
        </div>

        {{-- Mobile Menu Button and Theme Selector --}}
        <div class="md:hidden flex items-center space-x-4">
            {{-- Mobile Theme Selector (Dropdown List) --}}
            <div x-data="{ showThemeSelectorMobile: false }" class="relative">
                <button @click="showThemeSelectorMobile = !showThemeSelectorMobile"
                    class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">
                    <flux:icon name="sun" class="w-5 h-5" />
                </button>

                <div x-show="showThemeSelectorMobile" x-transition @click.outside="showThemeSelectorMobile = false"
                    class="absolute right-0 top-full mt-2 z-50 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-1.5 min-w-[120px]">
                    {{-- Theme options as list items --}}
                    <button @click="$flux.appearance = 'light'; showThemeSelectorMobile = false"
                        class="flex items-center space-x-2 w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-green-600">
                        <flux:icon name="sun" class="w-4 h-4" />
                        <span>{{ __('Light') }}</span>
                    </button>
                    <button @click="$flux.appearance = 'dark'; showThemeSelectorMobile = false"
                        class="flex items-center space-x-2 w-full text-left px-3 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-green-600">
                        <flux:icon name="moon" class="w-4 h-4" />
                        <span>{{ __('Dark') }}</span>
                    </button>
                    <button @click="$flux.appearance = 'system'; showThemeSelectorMobile = false"
                        class="flex items-center space-x-2 w-full text-left px-3 py-2 rounded-md text-sm text-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:text-green-600">
                        <flux:icon name="computer-desktop" class="w-4 h-4" />
                        <span>{{ __('System') }}</span>
                    </button>
                </div>
            </div>

            {{-- Mobile menu open button --}}
            <button @click="open = !open" class="text-gray-800 dark:text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>


    {{-- Full-screen Mobile Navigation (Overlay) --}}

    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 bg-white dark:bg-zinc-800 md:hidden flex flex-col p-6">

        {{-- Header of the mobile menu (Logo and Close button) --}}
        <div class="flex items-center justify-between mb-8"> {{-- Added items-center and justify-between --}}
            {{-- Logo in mobile menu --}}
            <a href="/" class="flex items-center space-x-2" @click="open = false"> {{-- Added @click="open = false" to close menu on logo click --}}
                <img src="{{ asset('images/bpu-unj-logo.png') }}" alt="Rusunawa UNJ" class="h-8">
                <span class="font-bold text-lg text-gray-800 dark:text-white">Rusunawa UNJ</span>
            </a>

            {{-- Close button --}}
            <button @click="open = false"
                class="text-gray-800 dark:text-white focus:outline-none p-2 rounded-md hover:bg-gray-100 dark:hover:bg-zinc-700">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        {{-- Mobile navigation links --}}
        <nav class="flex flex-col flex-grow space-y-2">
            @foreach ($navMenu as $key => $menu)
                <a href="{{ $menu['route'] }}" wire:navigate @click="open = false"
                    class="block px-3 py-2 rounded-md text-base font-medium
                        {{ $menu['active']
                            ? 'text-green-600 bg-gray-100 dark:bg-zinc-700'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-green-600' }}">
                    {{ $menu['label'] }}
                </a>
            @endforeach

            {{-- Mobile Auth Buttons --}}
            @if (Route::has('login'))
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate @click="open = false"
                            class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition text-base">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('occupant.auth') }}"  wire:navigate @click="open = false"
                            class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition text-base">
                            Masuk
                        </a>
                    @endauth
                </div>
            @endif
        </nav>
    </div>
</div>

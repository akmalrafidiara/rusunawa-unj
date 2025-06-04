<?php

$adminSidebarMenu = [
    [
        'group' => 'Dashboard',
        'items' => [
            [
                'icon' => 'home',
                'label' => __('Overview'),
                'route' => route('dashboard'),
                'current' => request()->routeIs('dashboard'),
                'badge' => null,
            ],
        ],
    ],
    [
        'group' => 'Respon Cepat',
        'items' => [
            [
                'icon' => 'identification',
                'label' => __('Verifikasi Penghuni'),
                'route' => route('occupant.verification'),
                'current' => request()->routeIs('occupant.verification'),
                'badge' => 3,
            ],
            [
                'icon' => 'credit-card',
                'label' => __('Konfirmasi Pembayaran'),
                'route' => route('payment.confirmation'),
                'current' => request()->routeIs('payment.confirmation'),
                'badge' => 5,
            ],
        ],
    ],
    [
        'group' => 'Penyewaan',
        'items' => [
            [
                'icon' => 'rectangle-stack',
                'label' => __('Kontrak'),
                'route' => route('contracts'),
                'current' => request()->routeIs('contracts'),
                'badge' => null,
            ],
            [
                'icon' => 'banknotes',
                'label' => __('Tagihan'),
                'route' => route('invoices'),
                'current' => request()->routeIs('invoices'),
                'badge' => null,
            ],
            [
                'icon' => 'user-circle',
                'label' => __('Data Penghuni'),
                'route' => route('occupants'),
                'current' => request()->routeIs('occupants'),
                'badge' => null,
            ],
        ],
    ],
    [
        'group' => 'Oprasional',
        'items' => [
            [
                'icon' => 'document-chart-bar',
                'label' => __('Laporan Keuangan'),
                'route' => route('income.reports'),
                'current' => request()->routeIs('income.reports'),
                'badge' => null,
            ],
            [
                'icon' => 'users',
                'label' => __('Manajemen User'),
                'route' => route('users'),
                'current' => request()->routeIs('users'),
                'badge' => null,
            ],
            [
                'expandable' => true,
                'label' => __('Manajemen Unit'),
                'items' => [
                    [
                        'label' => __('Unit'),
                        'route' => route('units'),
                        'current' => request()->routeIs('units'),
                    ],
                    [
                        'label' => __('Tipe Unit'),
                        'route' => route('unit.types'),
                        'current' => request()->routeIs('unit.types'),
                    ],
                    [
                        'label' => __('Rate Unit'),
                        'route' => route('unit.rates'),
                        'current' => request()->routeIs('unit.rates'),
                    ],
                    [
                        'label' => __('Cluster Unit'),
                        'route' => route('unit.clusters'),
                        'current' => request()->routeIs('unit.clusters'),
                    ],
                ],
            ],
            [
                'icon' => 'flag',
                'label' => __('Laporan & Keluhan'),
                'route' => route('reports.and.complaints'),
                'current' => request()->routeIs('reports.and.complaints'),
                'badge' => 1,
            ],
            [
                'icon' => 'wrench-screwdriver',
                'label' => __('Maintenance'),
                'route' => route('maintenance'),
                'current' => request()->routeIs('maintenance'),
                'badge' => null,
            ],
        ],
    ],
    [
        'group' => 'Manajemen Konten',
        'items' => [
            [
                'icon' => 'megaphone',
                'label' => __('Pengumuman'),
                'route' => route('dashboard'),
                'current' => request()->routeIs('not'),
                'badge' => null,
            ],
            [
                'icon' => 'phone',
                'label' => __('Kontak'),
                'route' => route('dashboard'),
                'current' => request()->routeIs('not'),
                'badge' => null,
            ],
            [
                'icon' => 'photo',
                'label' => __('Galeri'),
                'route' => route('dashboard'),
                'current' => request()->routeIs('not'),
                'badge' => null,
            ],
            [
                'icon' => 'document-text',
                'label' => __('Peraturan'),
                'route' => route('dashboard'),
                'current' => request()->routeIs('not'),
                'badge' => null,
            ],
            [
                'expandable' => true,
                'label' => __('Konten Halaman'),
                'items' => [
                    [
                        'label' => __('Banner & Footer'),
                        'route' => route('dashboard'),
                        'current' => request()->routeIs('not'),
                    ],
                    [
                        'label' => __('Tentang Rusunawa'),
                        'route' => route('dashboard'),
                        'current' => request()->routeIs('not'),
                    ],
                    [
                        'label' => __('Lokasi Rusunawa'),
                        'route' => route('dashboard'),
                        'current' => request()->routeIs('not'),
                    ],
                    [
                        'label' => __('FAQ'),
                        'route' => route('dashboard'),
                        'current' => request()->routeIs('not'),
                    ],
                ],
            ],
        ],
    ],
];

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 w-full">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-default.app-logo />
            </a>

            {{-- Dynamic Sidebar Menu --}}
            <flux:navlist variant="outline" class="space-y-4">
                @foreach ($adminSidebarMenu as $menu)
                    <flux:navlist.group :heading="__($menu['group'])" class="grid">
                        @foreach ($menu['items'] as $item)
                            @if (isset($item['expandable']) && $item['expandable'])
                                <flux:navlist.group :heading="$item['label']" expandable :expanded="false">
                                    @foreach ($item['items'] as $subItem)
                                        <flux:navlist.item :href="$subItem['route']" :current="$subItem['current']"
                                            wire:navigate>
                                            {{ $subItem['label'] }}
                                        </flux:navlist.item>
                                    @endforeach
                                </flux:navlist.group>
                            @else
                                <flux:navlist.item :icon="$item['icon']" :href="$item['route']"
                                    :current="$item['current']" wire:navigate :badge="$item['badge']" badge-color="red">
                                    {{ $item['label'] }}
                                </flux:navlist.item>
                            @endif
                        @endforeach
                    </flux:navlist.group>
                @endforeach
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                {{-- blade-formatter-disable --}}
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down" />
                {{-- blade-formatter-enable --}}
                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                            {{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                {{-- blade-formatter-disable --}}
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                {{-- blade-formatter-enable --}}
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                            {{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
    </body>

</html>

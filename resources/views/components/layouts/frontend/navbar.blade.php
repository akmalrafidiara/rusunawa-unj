@php
    $navMenu = [
        'home' => [
            'label' => 'Beranda',
            'route' => route('home'),
            'active' => request()->routeIs('home'),
        ],
        'tenancy' => [
            'label' => 'Sewa Kamar',
            'route' => route('tenancy'),
            'active' => request()->routeIs('tenancy'),
        ],
        // 'complaint' => [
        //     'label' => 'Pengaduan',
        //     'route' => route('complaint'),
        //     'active' => request()->routeIs('complaint'),
        // ],
        // 'announcement' => [
        //     'label' => 'Pengumuman',
        //     'route' => route('announcement'),
        //     'active' => request()->routeIs('announcement'),
        // ],
        // 'rules' => [
        //     'label' => 'Tata Tertib',
        //     'route' => route('rules'),
        //     'active' => request()->routeIs('rules'),
        // ],
    ];
@endphp

<div class="bg-white dark:bg-zinc-800 shadow-md">
    <div class="container mx-auto flex items-center justify-between py-4 px-6">
        {{-- Logo --}}
        <a href="/" class="flex items-center space-x-2">
            <img src="{{ asset('images/bpu-unj-logo.png') }}" alt="Rusunawa UNJ" class="h-8">
            <span class="font-bold text-lg text-gray-800 dark:text-white">Rusunawa UNJ</span>
        </a>

        <div class="flex items-center space-x-8">
            {{-- Navigasi --}}
            <nav class="hidden md:flex items-center space-x-8">
                @foreach ($navMenu as $key => $menu)
                    <a href="{{ $menu['route'] }}" wire:navigate
                        class="relative {{ $menu['active']
                            ? 'text-green-600 font-semibold before:absolute before:-bottom-0.5 before:left-0 before:w-full before:h-0.5 before:bg-green-600 before:content-[\'\']'
                            : 'text-gray-900 dark:text-gray-300 hover:text-green-600 transition' }}">
                        {{ $menu['label'] }}
                    </a>
                @endforeach
            </nav>

            @if (Route::has('login'))
                <div class="hidden md:block">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                            Masuk
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>

<x-layouts.frontend title="Dashboard Penghuni">

    @php
        // Mengambil data penghuni yang sedang login
        $pic = Auth::guard('contract')->user()->pic()->first();
        // Nama yang ditampilkan adalah nama penghuni yang login
        $picName = $pic->full_name ?? 'Penghuni';
    @endphp

    {{-- HEADER DENGAN BACKGROUND --}}
    <div class="w-full bg-cover bg-center h-64" {{-- style="background-image: url('{{ asset('images/banner-image-complaint.jpg') }}');"> --}}
        style="background-image: url('https://w.wallhaven.cc/full/ly/wallhaven-lyq5or.jpg');">
        <div class="w-full h-full bg-gray-900/50 flex items-center">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Judul Halaman --}}
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.6);">
                    Kamar Saya
                </h1>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 -mt-20">
        {{-- Grid utama untuk 2/3 kolom --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Mobile: Top Cards (Hai, (nama) & Menu Cepat) - Ini akan diatur agar muncul pertama di mobile --}}
            {{-- Menggunakan order-first untuk memastikan ini selalu di atas di mobile --}}
            <div class="lg:hidden col-span-1 order-first grid grid-cols-3 gap-4 mb-4">
                {{-- Card: Hai, (nama) (hanya untuk mobile di sini) --}}
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 border dark:border-zinc-700 col-span-2 flex flex-col justify-center h-full">
                    <p class="font-semibold text-lg text-gray-900 dark:text-white">PIC, {{ $picName }}</p>
                </div>

                {{-- Mobile : Icon-only dropdown for Notif & Logout --}}
                <div x-data="{ open: false }" @click.away="open = false" class="relative col-span-1">
                    <button @click="open = !open"
                        class="w-full h-full bg-white dark:bg-zinc-800 rounded-lg shadow-md flex items-center justify-center text-gray-900 dark:text-white p-2">
                        <span class="sr-only">Menu Cepat</span>
                        <flux:icon name="chevron-double-down" class="w-7 h-7" />
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-md shadow-lg py-1 z-20 border dark:border-zinc-700">
                        <a href="#"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                            <flux:icon name="bell" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" />
                            Notifikasi
                            <span class="ml-auto flex-shrink-0 relative">
                                <span class="absolute top-0 right-0 h-2 w-2 flex items-center justify-center">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                            </span>
                        </a>
                        <form method="POST" action="{{ route('contract.auth.logout') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                <flux:icon name="arrow-right-start-on-rectangle"
                                    class="w-5 h-5 mr-3 text-red-500 dark:text-red-400" />
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Kolom Kiri: Detail Pembayaran & Data Penghuni --}}
            <livewire:contracts.dashboard.contract />

            {{-- Kolom Kanan: Desktop Top Cards & Livewire Components --}}
            <div class="lg:col-span-2 flex flex-col gap-6">
                {{-- Desktop Only: Separate small cards for Notif & Logout & Hai (nama) --}}
                {{-- Terlihat di desktop (lg:flex), disembunyikan di mobile (hidden) --}}
                <div class="hidden lg:grid lg:grid-cols-10 lg:items-stretch gap-4">
                    {{-- Card: Hai, (nama) --}}
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 border dark:border-zinc-700 lg:col-span-8 flex flex-col justify-center h-full">
                        <p class="font-semibold text-lg text-gray-900 dark:text-white">PIC, {{ $picName }}
                        </p>
                    </div>

                    {{-- Notif & Logout di desktop --}}
                    <div class="lg:col-span-2 flex gap-4 items-stretch">
                        <a href="#"
                            class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 border dark:border-zinc-700 flex flex-col items-center justify-center flex-1 h-full w-full text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <span class="sr-only">Lihat Notifikasi</span>
                            <flux:icon name="bell" class="w-6 h-6" />
                            <span class="absolute top-1 right-1 h-2 w-2 flex items-center justify-center">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                        </a>
                        <form method="POST" action="{{ route('contract.auth.logout') }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-3 border dark:border-zinc-700 flex flex-col items-center justify-center w-full h-full text-red-500 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                <span class="sr-only">Logout</span>
                                <flux:icon name="arrow-right-start-on-rectangle" class="w-6 h-6" />
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Livewire components --}}
                <livewire:contracts.dashboard.announcements />
                <livewire:contracts.dashboard.complaints />
                <livewire:contracts.dashboard.emergency-contacts />
            </div>
        </div>
    </div>
</x-layouts.frontend>

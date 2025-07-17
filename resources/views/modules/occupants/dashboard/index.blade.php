<x-layouts.frontend title="Dashboard Penghuni">

    @php
        // Mengambil data kontrak dan PIC dari penghuni yang sedang login
        $occupantUser = Auth::guard('occupant')->user();
        $contract = $occupantUser?->contracts()->with('pic')->first();

        // Jika PIC ditemukan, gunakan namanya. Jika tidak, gunakan nama penghuni sebagai fallback.
        $picName = $contract && $contract->pic->isNotEmpty() ? $contract->pic->first()->full_name : $occupantUser->full_name;
    @endphp

    {{-- HEADER DENGAN BACKGROUND --}}
    {{-- UKURAN DIPERBESAR: Ditambahkan class 'h-64' untuk membuat banner lebih tinggi --}}
    <div class="w-full bg-cover bg-center h-64"
        style="background-image: url('{{ asset('images/banner-image-complaint.jpg') }}');">
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
    {{-- Margin atas disesuaikan agar tumpukan terlihat bagus dengan header yang lebih tinggi --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 -mt-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri --}}
            <div class="lg:col-span-1 flex flex-col gap-6">
                <livewire:occupants.dashboard.payment-details />
                <livewire:occupants.dashboard.occupant-data />
            </div>

            {{-- Kolom Kanan --}}
            <div class="lg:col-span-2 flex flex-col gap-6">
                
                {{-- CARD BARU: Informasi PIC dan Notifikasi dipindahkan ke sini --}}
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 border dark:border-zinc-700">
                    <div class="flex justify-between items-center gap-4">
                        {{-- Info PIC --}}
                        <div>
                             <p class="text-sm text-gray-500 dark:text-gray-400">Selamat Datang,</p>
                             <p class="font-semibold text-lg text-gray-900 dark:text-white">{{ $picName }}</p>
                        </div>
                        
                        {{-- Tombol Notifikasi --}}
                        <a href="#"
                            class="relative p-2 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white focus:outline-none bg-gray-100 hover:bg-gray-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 rounded-full transition-colors">
                            <span class="sr-only">Lihat Notifikasi</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span class="absolute top-1 right-1 h-3 w-3 flex items-center justify-center">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        </a>
                    </div>
                </div>

                <livewire:occupants.dashboard.announcements />
                <livewire:occupants.dashboard.complaints />
                <livewire:occupants.dashboard.emergency-contacts />
            </div>
        </div>
    </div>
</x-layouts.frontend>
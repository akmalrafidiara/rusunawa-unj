<x-layouts.frontend>

    {{-- Hero Section --}}
    <div class="relative w-full h-[711px] bg-cover bg-center mb-20"
        style="background-image: url('{{ asset('images/banner-image-main.jpg') }}');">
        {{-- Gradient Overlay --}}
        {{-- <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-transparent"></div> --}}

        <div class="relative container mx-auto px-6 h-full flex flex-col justify-between">

            {{-- Text --}}
            <div class="absolute top-1/2 transform -translate-y-1/2">
                <h1 class="text-5xl font-bold text-9ray-600 leading-tight">
                    Rusunawa <br>Universitas Negeri Jakarta
                </h1>
                <p class="mt-4 text-lg text-gray-600 max-w-lg">
                    Sebuah solusi tempat tinggal praktis di lingkungan kampus, ideal untuk mendukung aktivitas harian
                    Anda.
                </p>

                {{-- Statistik --}}
                <div class="mt-8 flex space-x-12">
                    <div>
                        <p class="text-4xl font-bold text-emerald-800">50+</p>
                        <p class="text-sm text-gray-900">Kamar Siap Huni</p>
                    </div>
                    <div class="w-0.5 bg-gray-600"></div>
                    <div>
                        <p class="text-4xl font-bold text-emerald-800">20+</p>
                        <p class="text-sm text-gray-900">Fasilitas Pendukung</p>
                    </div>
                    <div class="w-0.5 bg-gray-600"></div>
                    <div>
                        <p class="text-4xl font-bold text-emerald-800">1000+</p>
                        <p class="text-sm text-gray-900">Penghuni dalam 3 Tahun Terakhir</p>
                    </div>
                </div>
            </div>

            {{-- Form Kamar --}}
            <div
                class="absolute -bottom-20 left-1/2 transform -translate-x-1/2 max-w-10/12 bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-lg w-full">
                <livewire:frontend.unit-avaibility-check-form mode="redirect" />
            </div>
        </div>
    </div>


    <div class="container mx-auto px-4 py-8">

        <!-- Hero Section -->
        <section class="text-center py-16">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 dark:text-white">
                Selamat Datang di Rusunawa Universitas Negeri Jakarta
            </h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                Sistem pengelolaan kamar asrama yang terintegrasi dan mudah digunakan oleh penghuni maupun pengelola.
            </p>
            <div class="mt-6 flex justify-center gap-4">
                <flux:button variant="primary">Mulai Kelola
                    Kamar</flux:button>
                <flux:button variant="primary" wire:navigate>Informasi Lebih
                    Lanjut</flux:button>
            </div>
        </section>

        <!-- Features Section -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="text-center p-6 bg-white dark:bg-zinc-800 rounded shadow-sm">
                <div
                    class="w-12 h-12 mx-auto mb-4 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <flux:icon name="home" class="h-6 w-6" />
                </div>
                <h3 class="text-xl font-semibold mb-2">Manajemen Kamar Terpusat</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Lacak status kamar secara real-time, kelola data penghuni, dan atur alokasi kamar dengan mudah.
                </p>
            </div>

            <div class="text-center p-6 bg-white dark:bg-zinc-800 rounded shadow-sm">
                <div
                    class="w-12 h-12 mx-auto mb-4 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                    <flux:icon name="calendar" class="h-6 w-6" />
                </div>
                <h3 class="text-xl font-semibold mb-2">Booking Kamar Online</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Penghuni dapat memesan kamar secara online dengan sistem konfirmasi instan.
                </p>
            </div>

            <div class="text-center p-6 bg-white dark:bg-zinc-800 rounded shadow-sm">
                <div
                    class="w-12 h-12 mx-auto mb-4 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                    <flux:icon name="bell" class="h-6 w-6" />
                </div>
                <h3 class="text-xl font-semibold mb-2">Notifikasi & Pengaduan</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Sistem notifikasi real-time dan fitur pengaduan yang memudahkan komunikasi antara penghuni dan
                    pengelola.
                </p>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="text-center py-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Siap mulai menggunakan layanan kami?</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Daftar sekarang atau masuk ke akun Anda untuk mengelola kamar dan booking.
            </p>
            <div class="mt-6 flex justify-center gap-4">
                @guest
                    <flux:button variant="primary" wire:navigate :href="route('login')">Masuk</flux:button>
                @endguest
            </div>
        </section>
    </div>
</x-layouts.frontend>

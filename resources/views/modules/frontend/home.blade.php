<x-layouts.frontend>
    <div class="container mx-auto px-4 py-8">
        <!-- Header Otentikasi -->
        <header class="w-full max-w-full text-sm mb-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <flux:button variant="primary" :href="route('dashboard')" wire:navigate>
                            Dashboard
                        </flux:button>
                    @else
                        <flux:button variant="primary" :href="route('login')" wire:navigate>
                            Masuk
                        </flux:button>
                    @endauth
                </nav>
            @endif
        </header>

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

<?php

namespace App\Livewire\Frontend\Complaint;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Report; // Pastikan model Report diimpor

new #[Layout('components.layouts.frontend'), Title('Rusunawa UNJ | Pengaduan Berhasil')] class extends Component {
    public ?Report $report = null;

    // Metode mount akan menerima unique_id dari URL
    public function mount(string $unique_id): void
    {
        // Mencari laporan berdasarkan unique_id, atau menampilkan 404 jika tidak ditemukan
        $this->report = Report::where('unique_id', $unique_id)->firstOrFail();
    }
};

?>

<section class="w-full">
    {{-- Memasukkan header umum untuk halaman pengaduan --}}
    @include('modules.frontend.complaint.complaint-heading')
    <div class="container mx-auto relative overflow-hidden -mt-32 md:-mt-25 lg:-mt-25">
        {{-- Menggunakan layout khusus untuk halaman pengaduan --}}
        <x-frontend.complaint.layout>
            <div class="p-8 text-center">
                <div class="flex flex-col items-center justify-center mb-6">
                    {{-- Ikon centang untuk indikasi sukses --}}
                    <flux:icon.check-circle class="w-24 h-24 text-green-500 mb-4" />
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Pengaduan Berhasil Diajukan!</h2>
                    <p class="text-gray-700 dark:text-gray-300 text-lg">Laporan Anda telah berhasil kami terima.</p>
                </div>

                <div class="mb-8">
                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">ID Laporan Anda:</p>
                    <div
                        class="relative inline-flex items-center bg-gray-100 dark:bg-zinc-700 rounded-lg px-4 py-2 font-mono text-xl text-gray-800 dark:text-gray-200">
                        <span>{{ $report->unique_id }}</span>
                        {{-- Tombol untuk menyalin ID --}}
                        <button x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ $report->unique_id }}'); copied = true; setTimeout(() => copied = false, 2000);"
                            class="ml-3 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <flux:icon.clipboard class="w-5 h-5 text-gray-500 dark:text-gray-300" x-show="!copied" />
                            <flux:icon.check class="w-5 h-5 text-green-500" x-show="copied" />
                            <span class="sr-only" x-text="copied ? 'Disalin!' : 'Salin ID'"></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Gunakan ID ini untuk melacak status
                        pengaduan Anda.</p>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    {{-- Tombol Lacak Pengaduan --}}
                    <a href="{{ route('complaint.track-complaint', ['unique_id' => $report->unique_id]) }}"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200"
                        wire:navigate>
                        Lacak Pengaduan
                    </a>
                    {{-- Tombol Kembali ke Dashboard (asumsi ada rute dashboard) --}}
                    <a href="{{ route('contract.dashboard') }}"
                        class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-200 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600"
                        wire:navigate>
                        Kembali ke Dashboard
                    </a>
                    {{-- Tombol Buat Laporan Baru --}}
                    <a href="{{ route('complaint.create-complaint') }}"
                        class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200"
                        wire:navigate>
                        Buat Laporan Baru
                    </a>
                </div>
            </div>
        </x-frontend.complaint.layout>
    </div>
</section>
<?php

use function Livewire\Volt\{state, on, computed};
use App\Models\UnitType;
use Illuminate\Support\Facades\Storage;

// Mendefinisikan state untuk komponen Volt ini
state([
    'showDetailModal' => false,
    'selectedUnitTypeId' => null, // Hanya menyimpan ID untuk mengambil model secara dinamis
    'currentImageIndex' => 0,
]);

// Mendengarkan event 'open-unit-detail-modal' yang dikirim dari komponen lain
on(['open-unit-detail-modal' => function ($unitTypeId) {
    $this->selectedUnitTypeId = $unitTypeId; // Mengatur ID untuk memicu properti computed
    $this->currentImageIndex = 0;           // Mengatur ulang indeks gambar untuk modal baru
    $this->showDetailModal = true;
    $this->dispatch('lock-body-scroll');    // Mengirim event untuk mengunci scroll halaman utama
}]);

// Properti computed: Memuat model UnitType lengkap dengan relasi saat dibutuhkan
// Ini efisien karena model hanya diambil saat modal diminta
$selectedUnitType = computed(function () {
    if (!$this->selectedUnitTypeId) {
        return null; // Mengembalikan null jika tidak ada ID yang diatur (modal tertutup)
    }
    // Mengambil UnitType dengan semua relasi yang diperlukan
    $unitType = UnitType::with(['attachments', 'unitPrices.occupantType', 'units'])->find($this->selectedUnitTypeId);

    // Mendekode fasilitas jika disimpan sebagai string JSON di database
    if ($unitType && is_string($unitType->facilities)) {
        $unitType->facilities = json_decode($unitType->facilities, true);
    }
    return $unitType;
});

// Metode untuk menutup modal detail
$closeDetailModal = function () {
    $this->showDetailModal = false;
    $this->selectedUnitTypeId = null; // Menghapus ID yang dipilih untuk mengatur ulang status modal
    $this->currentImageIndex = 0;     // Mengatur ulang indeks gambar
    $this->dispatch('unlock-body-scroll'); // Mengirim event untuk membuka scroll halaman utama
};

// Metode untuk menavigasi ke gambar berikutnya di carousel
$nextImage = function () {
    if ($this->selectedUnitType && $this->selectedUnitType->attachments->count() > 0) {
        $this->currentImageIndex = ($this->currentImageIndex + 1) % $this->selectedUnitType->attachments->count();
    }
};

// Metode untuk menavigasi ke gambar sebelumnya di carousel
$prevImage = function () {
    if ($this->selectedUnitType && $this->selectedUnitType->attachments->count() > 0) {
        $this->currentImageIndex = ($this->currentImageIndex - 1 + $this->selectedUnitType->attachments->count()) % $this->selectedUnitType->attachments->count();
    }
};

?>

<div>
    {{-- Overlay dan Konten Modal --}}
    @if ($showDetailModal && $this->selectedUnitType) {{-- Pastikan modal hanya ditampilkan jika tipe unit dipilih --}}
    <div class="fixed inset-0 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
        {{-- Kontainer Modal --}}
        <div class="rounded-xl shadow-2xl p-6 w-full max-w-4xl max-h-[90vh] md:max-h-[90vh] overflow-y-auto relative animate-fade-in-scale my-4 md:my-0
                     bg-white dark:bg-zinc-800
        ">
            {{-- Tombol Tutup --}}
            <button wire:click="closeDetailModal" class="absolute top-4 right-4 p-2 rounded-full transition-colors duration-200
                                 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200
                                 dark:text-zinc-400 dark:hover:text-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600
            ">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            {{-- Judul Modal (Responsif untuk Ponsel) --}}
            <h3 class="text-xl md:text-2xl font-bold mb-6 flex items-center flex-wrap
                        text-gray-900 dark:text-white
            ">
                <flux:icon name="question-mark-circle" class="w-6 h-6 mr-2 text-green-600 dark:text-green-400 flex-shrink-0" />
                <span class="mr-1">Detail Tipe Unit:</span>
                <span class="text-green-600 dark:text-green-400">{{ $this->selectedUnitType->name }}</span>
            </h3>

            {{-- Bagian Carousel Gambar --}}
            @if ($this->selectedUnitType->attachments->isNotEmpty())
            <div class="relative mb-6">
                <img src="{{ Storage::url($this->selectedUnitType->attachments[$currentImageIndex]->path) }}"
                    alt="{{ $this->selectedUnitType->name }} - Gambar {{ $currentImageIndex + 1 }}"
                    class="w-full h-48 sm:h-64 md:h-72 lg:h-80 xl:h-96 object-cover rounded-lg
                           shadow-md border border-gray-200 dark:border-zinc-700
                ">

                {{-- Tombol Navigasi Gambar Sebelumnya --}}
                @if ($this->selectedUnitType->attachments->count() > 1 && $currentImageIndex > 0)
                <button wire:click="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full p-2 focus:outline-none transition-colors duration-200
                                 bg-white shadow-md hover:bg-gray-100
                                 dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-700
                ">
                    <flux:icon name="chevron-left" class="w-6 h-6 text-gray-700 dark:text-zinc-200" />
                </button>
                @endif

                {{-- Tombol Navigasi Gambar Berikutnya --}}
                @if ($this->selectedUnitType->attachments->count() > 1 && $currentImageIndex < $this->selectedUnitType->attachments->count() - 1)
                    <button wire:click="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full p-2 focus:outline-none transition-colors duration-200
                                 bg-white shadow-md hover:bg-gray-100
                                 dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-700
                    ">
                        <flux:icon name="chevron-right" class="w-6 h-6 text-gray-700 dark:text-zinc-200" />
                    </button>
                @endif

                {{-- Indikator Gambar (Opsional) --}}
                @if ($this->selectedUnitType->attachments->count() > 1)
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                    @foreach ($this->selectedUnitType->attachments as $index => $attachment)
                    <span class="w-2 h-2 rounded-full transition-colors duration-200
                                 {{ $index === $currentImageIndex ? 'bg-green-600 dark:bg-green-500' : 'bg-gray-300 dark:bg-zinc-600' }}
                    "></span>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            {{-- Gambar placeholder jika tidak ada lampiran --}}
            <img src="{{ asset('images/placeholder.png') }}"
                alt="Tipe Unit Placeholder" class="w-full h-64 object-cover rounded-lg mb-6
                                         border border-gray-200 dark:border-zinc-700
            ">
            @endif

            {{-- Bagian Deskripsi --}}
            <div class="mb-6 pb-4 border-b
                         border-gray-100 dark:border-zinc-700
            ">
                <h4 class="text-xl font-extrabold mb-2
                            text-gray-900 dark:text-white
                ">Deskripsi:</h4>
                <p class="leading-relaxed
                           text-gray-700 dark:text-zinc-300
                ">{{ $this->selectedUnitType->description }}</p>
            </div>

            {{-- Bagian Harga Tersedia --}}
            <div class="mb-6 pb-4 border-b
                         border-gray-100 dark:border-zinc-700
            ">
                <h4 class="text-xl font-extrabold mb-4
                            text-gray-900 dark:text-white
                ">Pilihan Harga Tersedia:</h4>
                @if ($this->selectedUnitType->unitPrices->isNotEmpty())
                @php
                // Mengelompokkan harga berdasarkan tipe penghuni untuk organisasi yang lebih baik
                $groupedPrices = $this->selectedUnitType->unitPrices->groupBy(function ($price) {
                return $price->occupantType->name ?? 'Lain-lain';
                });
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($groupedPrices as $occupantTypeName => $prices)
                    <div class="rounded-lg p-4 border
                                 bg-gray-50 shadow-sm border-gray-200
                                 dark:bg-zinc-900 dark:shadow-none dark:border-zinc-700
                    ">
                        <h5 class="text-lg font-bold mb-3 flex items-center
                                    text-gray-800 dark:text-zinc-100
                        ">
                            {{-- Ikon dinamis berdasarkan Tipe Penghuni --}}
                            @if ($occupantTypeName === 'Internal UNJ')
                            <flux:icon name="academic-cap" class="w-6 h-6 mr-2 text-green-600 dark:text-green-400" />
                            @elseif ($occupantTypeName === 'Eksternal')
                            <flux:icon name="user-group" class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" />
                            @else
                            <flux:icon name="tag" class="w-6 h-6 mr-2 text-gray-600 dark:text-zinc-400" />
                            @endif
                            Harga untuk {{ $occupantTypeName }}
                        </h5>
                        <ul class="space-y-2">
                            {{-- Menampilkan setiap opsi harga, diurutkan --}}
                            @foreach ($prices->sortBy('price') as $price)
                            <li class="flex justify-between items-center p-3 rounded-md border shadow-xs
                                        bg-white border-gray-100
                                        dark:bg-zinc-800 dark:border-zinc-700 dark:shadow-none
                            ">
                                <span class="font-medium flex items-center
                                              text-gray-700 dark:text-zinc-200
                                ">
                                    <flux:icon name="currency-dollar" class="w-4 h-4 mr-1 text-gray-400 dark:text-zinc-500" />
                                    {{ $price->pricing_basis->label() ?? 'N/A' }}
                                    @if ($price->duration)
                                    <span class="text-sm ml-1
                                                  text-gray-500 dark:text-zinc-400
                                    ">({{ $price->duration }})</span>
                                    @endif
                                </span>
                                <span class="font-bold text-lg
                                              text-green-600 dark:text-green-400
                                ">
                                    Rp{{ number_format($price->price, 0, ',', '.') }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center py-4 px-4
                           text-gray-700 dark:text-zinc-300
                ">Harga tidak tersedia untuk tipe unit ini.</p>
                @endif
            </div>

            {{-- Bagian Fasilitas Termasuk --}}
            @if (!empty($this->selectedUnitType->facilities))
            <h4 class="text-xl font-extrabold mb-3 border-t pt-4
                        text-gray-900 dark:text-white
                        border-gray-100 dark:border-zinc-700
            ">Fasilitas Termasuk:</h4>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4
                        text-gray-700 dark:text-zinc-200
            ">
                @foreach ($this->selectedUnitType->facilities as $facility)
                <li class="flex items-center text-base p-2 rounded-md shadow-sm
                            bg-gray-100 dark:bg-zinc-900
                ">
                    <flux:icon name="check-circle" class="w-5 h-5 mr-2 text-green-500 dark:text-green-400 flex-shrink-0" />
                    {{ $facility }}
                </li>
                @endforeach
            </ul>
            @endif

            {{-- Tombol Tutup Modal --}}
            <div class="text-right border-t pt-4 mt-4
                         border-gray-100 dark:border-zinc-700
            ">
                <button wire:click="closeDetailModal" class="font-semibold py-2 px-6 rounded-lg transition-colors duration-300 transform hover:scale-105
                                 bg-green-600 hover:bg-green-700 text-white shadow-md
                                 dark:bg-green-500 dark:hover:bg-green-600 dark:text-white dark:shadow-none
                ">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
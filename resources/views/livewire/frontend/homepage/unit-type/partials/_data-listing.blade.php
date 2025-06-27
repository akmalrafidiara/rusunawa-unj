<?php

use function Livewire\Volt\{state, mount};
use App\Models\UnitType;
use App\Enums\UnitStatus;
use Illuminate\Support\Facades\Storage;

// Mendefinisikan state untuk komponen Volt ini
state([
    'processedUnitTypes' => [],
]);

// Hook siklus hidup: berjalan sekali saat komponen pertama kali di-mount
mount(function () {
    // Memuat relasi yang diperlukan secara eager untuk pengambilan data yang efisien
    $unitTypesCollection = UnitType::with(['attachments', 'unitPrices.occupantType', 'units'])->get();

    // Memproses koleksi untuk menyiapkan data yang akan ditampilkan di kartu utama
    $this->processedUnitTypes = $unitTypesCollection->map(function ($unitType) {
        $prices = $unitType->unitPrices->pluck('price');

        // Menghitung kamar yang tersedia dan total kamar untuk setiap tipe unit
        $availableRoomsCount = $unitType->units->where('status', UnitStatus::AVAILABLE->value)->count();
        $totalRoomsCount = $unitType->units->count();

        return [
            'id' => $unitType->id,
            'name' => $unitType->name,
            'description' => $unitType->description,
            'room_count' => $unitType->room_count,
            'size_m2' => $unitType->size_m2,
            'minPrice' => $prices->min(),
            'maxPrice' => $prices->max(),
            'attachments' => $unitType->attachments, // Tetap sertakan lampiran untuk gambar kartu
            'available_rooms_count' => $availableRoomsCount,
            'total_rooms_count' => $totalRoomsCount,
        ];
    })->toArray(); // Mengkonversi ke array biasa untuk persistensi Livewire yang lebih baik
});

?>

<div>
    {{-- Grid untuk menampilkan tipe unit --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        {{-- Melakukan loop melalui processedUnitTypes untuk menampilkan setiap kartu tipe unit --}}
        @forelse ($processedUnitTypes as $unitType)
        <div class="rounded-lg overflow-hidden transform transition-transform duration-300 hover:scale-105
                     bg-white shadow-xl
                     dark:bg-zinc-900 dark:shadow-none
        ">
            {{-- Menampilkan gambar unit, atau placeholder jika tidak ada gambar --}}
            <img src="{{ $unitType['attachments']->isNotEmpty() ? Storage::url($unitType['attachments']->first()->path) : asset('images/placeholder.png') }}"
                alt="{{ $unitType['name'] }}" class="w-full h-48 object-cover">
            <div class="p-4">
                {{-- Tampilan Rentang Harga --}}
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">
                    @if ($unitType['minPrice'] !== null && $unitType['maxPrice'] !== null)
                    @if ($unitType['minPrice'] === $unitType['maxPrice'])
                    Rp{{ number_format($unitType['minPrice'], 0, ',', '.') }}
                    @else
                    Rp{{ number_format($unitType['minPrice'], 0, ',', '.') }} - Rp{{ number_format($unitType['maxPrice'], 0, ',', '.') }}
                    @endif
                    @else
                    Harga Tidak Tersedia
                    @endif
                </div>

                {{-- Nama/Tipe Kamar --}}
                <h3 class="text-xl font-semibold text-gray-800 dark:text-zinc-100 mb-2">{{ $unitType['name'] }}</h3>

                {{-- Detail Kamar (Tersedia / Total) --}}
                <div class="flex items-center text-sm mb-4
                             text-gray-600 dark:text-zinc-300
                ">
                    <svg class="w-4 h-4 mr-1
                                 text-gray-500 dark:text-zinc-400
                    " fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $unitType['available_rooms_count'] }}</span> kamar tersedia dari total
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $unitType['total_rooms_count'] }}</span> kamar
                    </span>
                </div>

                {{-- Tombol "Lihat Detail Kamar" --}}
                <button
                    wire:click="$dispatch('open-unit-detail-modal', { unitTypeId: {{ $unitType['id'] }} })"
                    class="w-full font-semibold py-2 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center
                           bg-green-600 hover:bg-green-700 text-white shadow-md
                           dark:bg-green-500 dark:hover:bg-green-600 dark:text-white dark:shadow-none
                ">
                    Lihat Detail Kamar
                    <svg class="w-4 h-4 ml-2
                                 text-white dark:text-white {{-- Ikon dalam tombol selalu putih --}}
                    " fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-gray-600 dark:text-zinc-400 py-10">Belum ada tipe unit yang tersedia saat ini.</p>
        @endforelse
    </div>
</div>
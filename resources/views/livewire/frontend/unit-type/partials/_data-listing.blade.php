<?php

use function Livewire\Volt\{state, mount};
use App\Models\UnitType;
use App\Enums\UnitStatus;
use Illuminate\Support\Facades\Storage;

// Define the state for this Volt component
state([
    'processedUnitTypes' => [],
]);

// Lifecycle hook: runs once when the component is first mounted
mount(function () {
    // Eager load necessary relationships for efficient data retrieval
    $unitTypesCollection = UnitType::with(['attachments', 'unitPrices.occupantType', 'units'])->get();

    // Process the collection to prepare data for display on the main cards
    $this->processedUnitTypes = $unitTypesCollection->map(function ($unitType) {
        $prices = $unitType->unitPrices->pluck('price');

        // Calculate available and total rooms for each unit type
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
            'attachments' => $unitType->attachments, // Keep attachments for card images
            'available_rooms_count' => $availableRoomsCount,
            'total_rooms_count' => $totalRoomsCount,
        ];
    })->toArray(); // Convert to a plain array for better Livewire persistence
});

?>

<div>
    {{-- Grid to display unit types --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        {{-- Loop through processedUnitTypes to display each unit type card --}}
        @forelse ($processedUnitTypes as $unitType)
        <div class="bg-white rounded-lg shadow-xl overflow-hidden transform transition-transform duration-300 hover:scale-105">
            {{-- Display unit image, or a placeholder if no image exists --}}
            <img src="{{ $unitType['attachments']->isNotEmpty() ? Storage::url($unitType['attachments']->first()->path) : asset('images/placeholder.png') }}"
                alt="{{ $unitType['name'] }}" class="w-full h-48 object-cover">
            <div class="p-4">
                {{-- Price Range Display --}}
                <div class="text-2xl font-bold text-green-600 mb-2">
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

                {{-- Room Name/Type --}}
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $unitType['name'] }}</h3>

                {{-- Room Details (Available / Total Count) --}}
                <div class="flex items-center text-gray-600 text-sm mb-4">
                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><span class="font-bold text-green-600">{{ $unitType['available_rooms_count'] }}</span> kamar tersedia dari total <span class="font-bold text-green-600">{{ $unitType['total_rooms_count'] }}</span> kamar</span>
                </div>

                {{-- "View Room Details" Button --}}
                {{-- Dispatches an event to open the detail modal, passing the unit type ID --}}
                <button
                    wire:click="$dispatch('open-unit-detail-modal', { unitTypeId: {{ $unitType['id'] }} })"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-300 shadow-md flex items-center justify-center">
                    Lihat Detail Kamar
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-gray-600 py-10">Belum ada tipe unit yang tersedia saat ini.</p>
        @endforelse
    </div>
</div>
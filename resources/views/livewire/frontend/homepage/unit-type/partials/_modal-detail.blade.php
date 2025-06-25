<?php

use function Livewire\Volt\{state, on, computed};
use App\Models\UnitType;
use Illuminate\Support\Facades\Storage;

// Define the state for this Volt component
state([
    'showDetailModal' => false,
    'selectedUnitTypeId' => null, // Stores only the ID to fetch the model dynamically
    'currentImageIndex' => 0,
]);

// Listen for the 'open-unit-detail-modal' event dispatched from other components
on(['open-unit-detail-modal' => function ($unitTypeId) {
    $this->selectedUnitTypeId = $unitTypeId; // Set the ID to trigger computed property
    $this->currentImageIndex = 0;           // Reset image index for new modal
    $this->showDetailModal = true;
    $this->dispatch('lock-body-scroll');    // Dispatch event to lock main page scroll
}]);

// Computed property: Loads the full UnitType model with relationships when needed
// This is efficient as the model is only fetched when the modal is requested
$selectedUnitType = computed(function () {
    if (!$this->selectedUnitTypeId) {
        return null; // Return null if no ID is set (modal is closed)
    }
    // Fetch UnitType with all necessary relationships
    $unitType = UnitType::with(['attachments', 'unitPrices.occupantType', 'units'])->find($this->selectedUnitTypeId);

    // Decode facilities if stored as a JSON string in the database
    if ($unitType && is_string($unitType->facilities)) {
        $unitType->facilities = json_decode($unitType->facilities, true);
    }
    return $unitType;
});

// Method to close the detail modal
$closeDetailModal = function () {
    $this->showDetailModal = false;
    $this->selectedUnitTypeId = null; // Clear selected ID to reset modal state
    $this->currentImageIndex = 0;       // Reset image index
    $this->dispatch('unlock-body-scroll'); // Dispatch event to unlock main page scroll
};

// Method to navigate to the next image in the carousel
$nextImage = function () {
    if ($this->selectedUnitType && $this->selectedUnitType->attachments->count() > 0) {
        $this->currentImageIndex = ($this->currentImageIndex + 1) % $this->selectedUnitType->attachments->count();
    }
};

// Method to navigate to the previous image in the carousel
$prevImage = function () {
    if ($this->selectedUnitType && $this->selectedUnitType->attachments->count() > 0) {
        $this->currentImageIndex = ($this->currentImageIndex - 1 + $this->selectedUnitType->attachments->count()) % $this->selectedUnitType->attachments->count();
    }
};

?>

<div>
    {{-- Modal Overlay and Content --}}
    @if ($showDetailModal && $this->selectedUnitType) {{-- Ensure modal is shown only if a unit type is selected --}}
    <div class="fixed inset-0 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
        {{-- Modal Container --}}
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-4xl max-h-[90vh] md:max-h-[90vh] overflow-y-auto relative animate-fade-in-scale my-4 md:my-0">
            {{-- Close Button --}}
            <button wire:click="closeDetailModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            {{-- Modal Title (Responsive for Mobile) --}}
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 flex items-center flex-wrap">
                <flux:icon name="question-mark-circle" class="w-6 h-6 mr-2 text-green-600 flex-shrink-0" />
                <span class="mr-1">Detail Tipe Unit:</span>
                <span class="text-green-600">{{ $this->selectedUnitType->name }}</span>
            </h3>

            {{-- Image Carousel Section --}}
            @if ($this->selectedUnitType->attachments->isNotEmpty())
            <div class="relative mb-6">
                <img src="{{ Storage::url($this->selectedUnitType->attachments[$currentImageIndex]->path) }}"
                    alt="{{ $this->selectedUnitType->name }} - Gambar {{ $currentImageIndex + 1 }}"
                    class="w-full h-48 sm:h-64 md:h-72 lg:h-80 xl:h-96 object-cover rounded-lg shadow-md max-h-[40vh] md:max-h-[50vh]">

                {{-- Previous Image Navigation Button --}}
                @if ($this->selectedUnitType->attachments->count() > 1 && $currentImageIndex > 0)
                <button wire:click="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none transition-colors duration-200">
                    <flux:icon name="chevron-left" class="w-6 h-6 text-gray-700" />
                </button>
                @endif

                {{-- Next Image Navigation Button --}}
                @if ($this->selectedUnitType->attachments->count() > 1 && $currentImageIndex < $this->selectedUnitType->attachments->count() - 1)
                    <button wire:click="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none transition-colors duration-200">
                        <flux:icon name="chevron-right" class="w-6 h-6 text-gray-700" />
                    </button>
                @endif

                {{-- Image Indicators (Optional) --}}
                @if ($this->selectedUnitType->attachments->count() > 1)
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                    @foreach ($this->selectedUnitType->attachments as $index => $attachment)
                    <span class="w-2 h-2 rounded-full {{ $index === $currentImageIndex ? 'bg-green-600' : 'bg-gray-300' }}"></span>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            {{-- Placeholder image if no attachments exist --}}
            <img src="{{ asset('images/placeholder.png') }}"
                alt="Tipe Unit Placeholder" class="w-full h-64 object-cover rounded-lg mb-6 border border-gray-200">
            @endif

            {{-- Description Section --}}
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h4 class="text-xl font-extrabold text-gray-900 mb-2">Deskripsi:</h4>
                <p class="text-gray-700 leading-relaxed">{{ $this->selectedUnitType->description }}</p>
            </div>

            {{-- Available Prices Section --}}
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h4 class="text-xl font-extrabold text-gray-900 mb-4">Pilihan Harga Tersedia:</h4>
                @if ($this->selectedUnitType->unitPrices->isNotEmpty())
                @php
                // Group prices by occupant type for better organization
                $groupedPrices = $this->selectedUnitType->unitPrices->groupBy(function ($price) {
                return $price->occupantType->name ?? 'Lain-lain';
                });
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($groupedPrices as $occupantTypeName => $prices)
                    <div class="bg-gray-50 rounded-lg p-4 shadow-sm border border-gray-200">
                        <h5 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            {{-- Dynamic icons based on Occupant Type --}}
                            @if ($occupantTypeName === 'Internal UNJ')
                            <flux:icon name="academic-cap" class="w-6 h-6 mr-2 text-green-600" />
                            @elseif ($occupantTypeName === 'Eksternal')
                            <flux:icon name="user-group" class="w-6 h-6 mr-2 text-blue-600" />
                            @else
                            <flux:icon name="tag" class="w-6 h-6 mr-2 text-gray-600" />
                            @endif
                            Harga untuk {{ $occupantTypeName }}
                        </h5>
                        <ul class="space-y-2">
                            {{-- Display each price option, sorted --}}
                            @foreach ($prices->sortBy('price') as $price)
                            <li class="flex justify-between items-center bg-white p-3 rounded-md border border-gray-100 shadow-xs">
                                <span class="text-gray-700 font-medium flex items-center">
                                    <flux:icon name="currency-dollar" class="w-4 h-4 mr-1 text-gray-400" />
                                    {{ $price->pricing_basis->label() ?? 'N/A' }}
                                    @if ($price->duration)
                                    <span class="text-sm text-gray-500 ml-1">({{ $price->duration }})</span>
                                    @endif
                                </span>
                                <span class="font-bold text-lg text-green-600">
                                    Rp{{ number_format($price->price, 0, ',', '.') }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-700 text-center py-4 px-4">Harga tidak tersedia untuk tipe unit ini.</p>
                @endif
            </div>

            {{-- Included Facilities Section --}}
            @if (!empty($this->selectedUnitType->facilities))
            <h4 class="text-xl font-extrabold text-gray-900 mb-3 border-t pt-4 border-gray-100">Fasilitas Termasuk:</h4>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700 mb-4">
                @foreach ($this->selectedUnitType->facilities as $facility)
                <li class="flex items-center text-base bg-gray-100 p-2 rounded-md shadow-sm">
                    <flux:icon name="check-circle" class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" />
                    {{ $facility }}
                </li>
                @endforeach
            </ul>
            @endif

            {{-- Close Modal Button --}}
            <div class="text-right border-t border-gray-100 pt-4 mt-4">
                <button wire:click="closeDetailModal" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors duration-300 shadow-md transform hover:scale-105">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content; // Pastikan model Content ada di App\Models

state([
    'mainLocationTitle' => '',
    'subLocationTitle' => '',
    'locationAddress' => '',
    'locationEmbedLink' => '',
    'nearbyLocations' => [],
]);

mount(function () {
    // Mengambil setiap bagian 'Lokasi Kami' secara terpisah berdasarkan content_key
    $this->mainLocationTitle = optional(Content::where('content_key', 'location_main_title')->first())->content_value ?? '';
    $this->subLocationTitle = optional(Content::where('content_key', 'location_sub_title')->first())->content_value ?? '';
    $this->locationAddress = optional(Content::where('content_key', 'location_address')->first())->content_value ?? '';
    $this->locationEmbedLink = optional(Content::where('content_key', 'location_embed_link')->first())->content_value ?? '';

    // Mengambil lokasi terdekat, pastikan itu array
    $nearbyLocationsContent = optional(Content::where('content_key', 'location_nearby_locations')->first())->content_value;
    $this->nearbyLocations = is_array($nearbyLocationsContent) ? $nearbyLocationsContent : [];
});

?>

{{-- Konten HTML untuk menampilkan bagian Lokasi Kami --}}
{{-- Mengganti background-image dengan warna solid Tailwind CSS --}}
<div class="relative w-full py-16 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="container mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        {{-- Bagian Kiri: Detail Alamat dan Lokasi Terdekat --}}
        <div class="text-center lg:text-left">
            <span class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2 block">Lokasi Kami</span>
            @if ($mainLocationTitle)
                <h3 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                    {{ $mainLocationTitle }}
                </h3>
            @endif

            @if ($subLocationTitle)
                <p class="text-gray-700 text-lg md:text-xl leading-relaxed mb-6">
                    {{ $subLocationTitle }}
                </p>
            @endif

            @if ($locationAddress)
                <div class="mb-8">
                    <p class="text-gray-800 text-xl font-semibold">{{ $locationAddress }}</p>
                </div>
            @endif

            @if (!empty($nearbyLocations))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($nearbyLocations as $location)
                        <div class="flex items-center bg-white p-4 rounded-lg shadow-md border border-gray-100 transform transition-transform duration-200 hover:scale-105">
                            {{-- Contoh ikon, sesuaikan dengan ikon Flux yang ada --}}
                            <flux:icon name="map-pin" class="w-6 h-6 mr-3 flex-shrink-0 text-blue-600" />
                            <p class="text-gray-800 text-base">{{ $location }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Bagian Kanan: Peta Lokasi --}}
        <div>
            @if ($locationEmbedLink)
                <div class="w-full h-80 sm:h-96 md:h-[500px] rounded-lg overflow-hidden shadow-2xl border-4 border-white">
                    {{-- HATI-HATI: Pastikan $locationEmbedLink berisi HTML iframe yang aman jika berasal dari CMS. --}}
                    {!! $locationEmbedLink !!}
                </div>
            @else
                <div class="w-full h-80 sm:h-96 md:h-[500px] bg-gray-200 flex items-center justify-center rounded-lg shadow-lg text-gray-500">
                    Peta Lokasi Tidak Tersedia
                </div>
            @endif
        </div>
    </div>
</div>
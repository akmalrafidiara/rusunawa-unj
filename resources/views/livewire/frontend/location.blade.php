<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content; // Pastikan model Content ada di AppModels

state([
    'mainLocationTitle' => '',
    'subLocationTitle' => '',
    'locationAddress' => '',
    'locationEmbedLink' => '',
    'nearbyLocations' => [],
    'mapEmbedCode' => '',
]);

mount(function () {
    // Mengambil setiap bagian 'Lokasi Kami' secara terpisah berdasarkan content_key
    $this->mainLocationTitle = optional(Content::where('content_key', 'location_main_title')->first())->content_value ?? '';
    $this->subLocationTitle = optional(Content::where('content_key', 'location_sub_title')->first())->content_value ?? '';
    $this->locationAddress = optional(Content::where('content_key', 'location_address')->first())->content_value ?? '';

    // Mengambil link embed peta asli dari database
    $rawEmbedLink = optional(Content::where('content_key', 'location_embed_link')->first())->content_value ?? '';
    $this->locationEmbedLink = $rawEmbedLink;

    $modifiedEmbedLink = $rawEmbedLink;

    // Ganti atribut width dan height menjadi 100% menggunakan regex
    $modifiedEmbedLink = preg_replace('/width="[^"]*"/i', 'width="100%"', $modifiedEmbedLink);
    $modifiedEmbedLink = preg_replace('/height="[^"]*"/i', 'height="100%"', $modifiedEmbedLink);

    // Pastikan ada style="width:100%; height:100%;" untuk override jika ada inline style lain
    if (strpos($modifiedEmbedLink, 'style=') !== false) {
        $modifiedEmbedLink = preg_replace('/style="([^"]*)"/i', 'style="$1; width:100%; height:100%;"', $modifiedEmbedLink);
    } else {
        $modifiedEmbedLink = str_replace('<iframe', '<iframe style="width:100%; height:100%;"', $modifiedEmbedLink);
    }

    // Simpan link embed yang sudah diproses ke properti state baru
    $this->mapEmbedCode = $modifiedEmbedLink;


    // Mengambil lokasi terdekat
    $nearbyLocationsContent = optional(Content::where('content_key', 'location_nearby_locations')->first())->content_value;

    if (is_array($nearbyLocationsContent)) {
        $loadedNearbyLocations = $nearbyLocationsContent;
    } elseif (is_string($nearbyLocationsContent)) {
        $decoded = json_decode($nearbyLocationsContent, true);
        $loadedNearbyLocations = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    } else {
        $loadedNearbyLocations = [];
    }

    // Definisikan array warna untuk latar belakang ikon
    $iconColors = ['bg-blue-500', 'bg-red-500', 'bg-purple-500', 'bg-yellow-500', 'bg-pink-500', 'bg-indigo-500', 'bg-green-500', 'bg-cyan-500'];

    // Tambahkan warna ke setiap item lokasi terdekat
    $this->nearbyLocations = array_map(function($item, $index) use ($iconColors) {
        return [
            'text' => $item,
            'color' => $iconColors[$index % count($iconColors)],
        ];
    }, $loadedNearbyLocations, array_keys($loadedNearbyLocations));
});

?>

{{-- Konten HTML untuk menampilkan bagian Lokasi Kami --}}
<div class="relative w-full py-6 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="container mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        {{-- Bagian Kiri: Detail Alamat dan Lokasi Terdekat (di desktop) --}}
        <div class="text-center lg:text-left">
            <span class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2 block text-left">Lokasi Kami</span>
            @if ($mainLocationTitle)
                <h3 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight text-left">
                    {{ $mainLocationTitle }}
                </h3>
            @endif

            {{-- Peta Lokasi di sini untuk mobile --}}
            @if ($mapEmbedCode)
                <div class="lg:hidden w-full h-80 sm:h-96 rounded-lg overflow-hidden shadow-2xl border-4 border-white mb-8">
                    {!! $mapEmbedCode !!}
                </div>
            @else
                <div class="lg:hidden w-full h-80 sm:h-96 bg-gray-200 flex items-center justify-center rounded-lg shadow-lg text-gray-500 mb-8">
                    Peta Lokasi Tidak Tersedia
                </div>
            @endif

            {{-- Lanjutkan dengan detail lokasi lainnya --}}
            @if ($subLocationTitle)
                <p class="text-gray-800 text-2xl md:text-3xl font-semibold text-left mb-4">
                    {{ $subLocationTitle }}
                </p>
            @endif

            @if ($locationAddress)
                <div class="mb-8">
                    <p class="text-gray-700 text-base leading-relaxed text-left">{{ $locationAddress }}</p>
                </div>
            @endif

            @if (!empty($nearbyLocations))
                <div class="flex flex-wrap justify-start gap-x-3 gap-y-4 mb-6">
                    @foreach ($nearbyLocations as $location)
                        <div class="flex items-center transform transition-transform duration-200 hover:scale-105 w-full sm:w-auto">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-2 {{ $location['color'] }} sm:w-10 sm:h-10">
                                <flux:icon name="map-pin" class="w-5 h-5 text-white sm:w-6 sm:h-6" />
                            </div>
                            <div class="bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100 sm:px-5 sm:py-3 min-w-0 max-w-[calc(100%-40px)]">
                                <p class="text-gray-800 text-sm font-bold break-words text-left">{{ $location['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Bagian Kanan: Peta Lokasi (khusus desktop) --}}
        <div class="hidden lg:block">
            @if ($mapEmbedCode)
                <div class="w-full h-80 sm:h-96 md:h-[450px] rounded-lg overflow-hidden shadow-2xl border-4 border-white">
                    {!! $mapEmbedCode !!}
                </div>
            @else
                <div class="w-full h-80 sm:h-96 md:h-[450px] bg-gray-200 flex items-center justify-center rounded-lg shadow-lg text-gray-500">
                    Peta Lokasi Tidak Tersedia
                </div>
            @endif
        </div>
    </div>
</div>

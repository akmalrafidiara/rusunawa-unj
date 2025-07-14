<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content;

state([
    'bannerTitle' => '',
    'bannerText' => '',
    'bannerImageUrl' => '',
    'dayaTariks' => [],
]);

mount(function () {
    $this->bannerTitle = optional(Content::where('content_key', 'banner_title')->first())->content_value ?? 'Data belum tersedia';
    $this->bannerText = optional(Content::where('content_key', 'banner_text')->first())->content_value ?? 'Data belum tersedia';
    $this->bannerImageUrl = optional(Content::where('content_key', 'banner_image_url')->first())->content_value ?? asset('images/placeholder.png');

    $loadedDayaTariks = optional(Content::where('content_key', 'banner_daya_tariks')->first())->content_value ?? [];
    $this->dayaTariks = is_array($loadedDayaTariks) ? $loadedDayaTariks : [];
});

?>

<div class="relative w-full min-h-[650px] sm:min-h-[600px] md:min-h-[600px] lg:min-h-[700px] overflow-hidden shadow-lg flex md:items-center md:bg-cover md:bg-center"
    style="background-image: url('{{ $bannerImageUrl ?: asset('images/placeholder.png') }}');">

    {{-- Gambar latar belakang untuk layar kecil (mobile) --}}
    <div class="absolute inset-0 bg-cover bg-center md:hidden"
        style="background-image: url('{{ $bannerImageUrl ?: asset('images/placeholder.png') }}');">
        {{-- Overlay untuk mode gelap di mobile --}}
        <div
            class="absolute inset-0 bg-white opacity-0 dark:bg-zinc-800 dark:opacity-70 transition-opacity duration-300">
        </div>
    </div>

    {{-- Overlay untuk mode gelap di layar besar (desktop) --}}
    <div
        class="absolute inset-0 hidden md:block bg-white opacity-0 dark:bg-zinc-800 dark:opacity-70 transition-opacity duration-300">
    </div>


    {{-- Konten teks --}}
    <div class="container mx-auto relative z-10 w-full px-4 sm:px-6 py-10 sm:py-16 md:py-20 lg:py-24">
        <div class="text-left max-w-xl md:max-w-2xl w-full">
            {{-- Judul Banner --}}
            <h1
                class="text-gray-800 dark:text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold mb-2 sm:mb-4">
                {{ $bannerTitle }}
            </h1>
            {{-- Teks Banner --}}
            <p
                class="text-gray-600 dark:text-zinc-300 text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-8 leading-relaxed">
                {{ $bannerText }}
            </p>

            {{-- Menampilkan Daya Tarik --}}
            @if (!empty($dayaTariks))
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-4 sm:gap-y-0 sm:gap-x-6 md:gap-y-8 mt-4">
                    @foreach ($dayaTariks as $dt)
                        <div class="flex flex-col items-start">
                            <span
                                class="text-green-600 dark:text-green-400 text-2xl sm:text-4xl font-bold mb-0.5">{{ $dt['value'] ?? '' }}</span>
                            <span
                                class="text-gray-600 dark:text-zinc-300 text-sm sm:text-base whitespace-nowrap overflow-hidden text-ellipsis">{{ $dt['label'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

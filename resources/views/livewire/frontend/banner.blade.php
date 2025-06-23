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
    $this->bannerTitle = optional(Content::where('content_key', 'banner_title')->first())->content_value ?? '';
    $this->bannerText = optional(Content::where('content_key', 'banner_text')->first())->content_value ?? '';
    $this->bannerImageUrl = optional(Content::where('content_key', 'banner_image_url')->first())->content_value ?? '';

    $loadedDayaTariks = optional(Content::where('content_key', 'banner_daya_tariks')->first())->content_value;
    $this->dayaTariks = is_array($loadedDayaTariks) ? $loadedDayaTariks : [];
});

?>

{{-- Konten HTML untuk menampilkan banner sesuai gambar --}}
<div class="relative w-full min-h-[400px] sm:min-h-[450px] md:min-h-[600px] lg:min-h-[700px] bg-cover bg-center overflow-hidden shadow-lg flex items-center"
    style="background-image: url('{{ $bannerImageUrl ?: asset('images/placeholder.png') }}');">
    <div class="relative z-10 w-full px-4 sm:px-6 md:pl-20 py-10 sm:py-16 md:py-20 lg:py-24">
        <div class="text-left max-w-xl md:max-w-2xl w-full">
            {{-- Judul Banner dengan warna abu-abu --}}
            <h1 class="text-gray-800 dark:text-gray-100 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold mb-2 sm:mb-4 animate-fade-in-down">
                {{ $bannerTitle }}
            </h1>
            {{-- Teks Banner diubah menjadi abu-abu --}}
            <p class="text-gray-600 dark:text-gray-300 text-base sm:text-lg md:text-xl lg:text-2xl mb-4 sm:mb-8 leading-relaxed animate-fade-in-up">
                {{ $bannerText }}
            </p>

            {{-- Menampilkan Daya Tarik dalam layout 3 kolom seperti di gambar --}}
            @if (!empty($dayaTariks))
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-4 sm:gap-y-0 sm:gap-x-6 md:gap-y-8 mt-4 animate-fade-in-up-delay">
                @foreach ($dayaTariks as $dt)
                <div class="flex flex-col items-start">
                    <span class="text-green-600 dark:text-green-400 text-2xl sm:text-4xl font-bold mb-0.5">{{ $dt['value'] ?? '' }}</span>
                    <span class="text-gray-600 dark:text-gray-300 text-sm sm:text-base whitespace-nowrap overflow-hidden text-ellipsis">{{ $dt['label'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
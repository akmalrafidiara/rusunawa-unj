<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content;
use Illuminate\Support\Facades\Storage;

state([
    'complaintTitle' => '',
    'complaintDescription' => '',
    'complaintImageUrl' => '',
    'advantages' => [],
]);

mount(function () {
    $contentItems = Content::whereIn('content_key', [
        'complaint_service_title',
        'complaint_service_description',
        'complaint_service_image_url',
        'complaint_service_advantages',
    ])->get()->keyBy('content_key');

    $this->complaintTitle = $contentItems->get('complaint_service_title')->content_value ?? 'Layanan Pengaduan';
    $this->complaintDescription = $contentItems->get('complaint_service_description')->content_value ?? 'Kami menyediakan layanan pengaduan yang mudah, cepat, dan transparan untuk memastikan setiap masukan Anda didengar dan ditindaklanjuti dengan serius.';

    $this->complaintImageUrl = $contentItems->get('complaint_service_image_url')->content_value ?? asset('images/default-complaint-banner.jpg');

    $loadedAdvantages = $contentItems->get('complaint_service_advantages')->content_value;
    $this->advantages = is_array($loadedAdvantages)
        ? $loadedAdvantages
        : (json_decode($loadedAdvantages, true) ?? []);

    // Definisikan array warna untuk latar belakang ikon (opsional, bisa disesuaikan)
    $iconColors = ['bg-red-600', 'bg-blue-600', 'bg-yellow-600', 'bg-green-600', 'bg-purple-600', 'bg-pink-600'];

    $this->advantages = array_map(function ($item, $index) use ($iconColors) {
        return [
            'text' => $item,
            'color' => $iconColors[$index % count($iconColors)]
        ];
    }, $this->advantages, array_keys($this->advantages));
});

?>

<div class="relative w-full py-6 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="container mx-auto relative z-10 flex flex-col lg:flex-row items-center gap-12">
        {{-- Bagian Kiri: Teks Konten & Gambar Mobile --}}
        <div class="lg:w-1/2 text-center lg:text-left">
            <span class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2 block text-left sm:pl-4">Layanan Pengaduan Terintegrasi</span>
            <h3 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight text-left sm:pl-4">
                {{ $complaintTitle }}
            </h3>

            {{-- Bagian Gambar Mobile (Hanya Tampil di Mobile, di bawah judul, di atas deskripsi) --}}
            <div class="lg:hidden flex justify-center items-center p-4 transform transition-transform duration-200 hover:scale-105">
                @if ($complaintImageUrl)
                    <img src="{{ $complaintImageUrl }}" alt="{{ $complaintTitle }}"
                         class="w-full h-auto max-w-lg object-cover mb-4">
                @else
                    {{-- Placeholder jika tidak ada gambar --}}
                    <div class="bg-gray-200 dark:bg-gray-700 w-full max-w-lg h-64 flex items-center justify-center text-gray-500 mb-4">
                        Tidak ada gambar pengaduan.
                    </div>
                @endif
            </div>

            <p class="text-gray-700 dark:text-gray-300 text-base md:text-xl leading-relaxed mb-8 text-center lg:text-left sm:pl-4">
                {{ $complaintDescription }}
            </p>

            {{-- Menampilkan Keunggulan Layanan --}}
            @if (!empty($advantages))
                <div class="space-y-4 mb-8 sm:pl-4">
                    @foreach ($advantages as $advantage)
                        <div class="flex items-center text-sm transform transition-transform duration-200 hover:scale-105">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $advantage['color'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <div class="flex-grow bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700">
                                <p class="text-gray-800 dark:text-gray-200 font-semibold text-m leading-snug text-justify"> {{-- TAMBAH text-left DI SINI --}}
                                    {{ $advantage['text'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tombol Kirim Pengaduan --}}
            <div class="sm:pl-4">
                <a href="/kirim-pengaduan"
                   class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Kirim Pengaduan Anda
                </a>
            </div>
        </div>

        {{-- Bagian Kanan: Gambar Murni (Hanya Tampil di Desktop) --}}
        <div class="hidden lg:w-1/2 lg:flex justify-center items-center p-4 transform transition-transform duration-200 hover:scale-105">
            @if ($complaintImageUrl)
                <img src="{{ $complaintImageUrl }}" alt="{{ $complaintTitle }}"
                     class="w-full h-auto max-w-lg object-cover">
            @else
                {{-- Placeholder jika tidak ada gambar --}}
                <div class="bg-gray-200 dark:bg-gray-700 w-full max-w-lg h-64 flex items-center justify-center text-gray-500">
                    Tidak ada gambar pengaduan.
                </div>
            @endif
        </div>
    </div>
</div>
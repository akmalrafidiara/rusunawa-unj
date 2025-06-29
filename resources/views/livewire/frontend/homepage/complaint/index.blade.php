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
    // Mengambil konten dari database
    $this->complaintTitle = optional(Content::where('content_key', 'complaint_service_title')->first())->content_value ?? 'Data belum tersedia';
    $this->complaintDescription = optional(Content::where('content_key', 'complaint_service_description')->first())->content_value ?? 'Data belum tersedia';
    
    // Atur URL gambar. Jika kosong, gunakan placeholder langsung di sini.
    $this->complaintImageUrl = optional(Content::where('content_key', 'complaint_service_image_url')->first())->content_value ?? asset('images/placeholder.png');

    $advantagesContent = optional(Content::where('content_key', 'complaint_service_advantages')->first())->content_value;

    if (is_array($advantagesContent)) {
        $loadedAdvantages = $advantagesContent;
    } elseif (is_string($advantagesContent)) {
        $decoded = json_decode($advantagesContent, true);
        $loadedAdvantages = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    } else {
        $loadedAdvantages = [];
    }

    // Definisikan array warna untuk latar belakang ikon
    $iconColors = ['bg-red-600', 'bg-blue-600', 'bg-yellow-600', 'bg-green-600', 'bg-purple-600', 'bg-pink-600'];

    // Tambahkan warna ke setiap item daya tarik
    $this->advantages = array_map(function($item, $index) use ($iconColors) {
        return [
            'text' => $item,
            'color' => $iconColors[$index % count($iconColors)] // Ambil warna secara berurutan, ulang jika item lebih banyak
        ];
    }, $loadedAdvantages, array_keys($loadedAdvantages));
});

?>

<div class="relative w-full py-6 px-4 sm:px-6 lg:px-5 overflow-hidden">
    <div class="container mx-auto relative z-10 flex flex-col lg:flex-row items-center gap-12">
        {{-- Bagian Kiri: Teks Konten & Gambar Mobile --}}
        <div class="lg:w-1/2 text-center lg:text-left">
            <span class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2 block text-left sm:pl-4">Layanan Pengaduan Terintegrasi</span>
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
                    <img src="{{ asset('images/placeholder.png') }}" alt="Placeholder Gambar"
                         class="w-full h-auto max-w-lg object-cover mb-4">
                @endif
            </div>

            <p class="text-gray-700 dark:text-zinc-300 text-base md:text-xl leading-relaxed mb-8 text-center lg:text-left sm:pl-4 text-left">
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
                            <div class="flex-grow bg-white dark:bg-zinc-900 p-3 rounded-2xl shadow-md border border-gray-100 dark:border-zinc-900">
                                <p class="text-gray-800 dark:text-zinc-100 font-semibold text-m leading-snug text-justify">
                                    {{ $advantage['text'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tombol Kirim Pengaduan --}}
            <div class="sm:pl-4">
                <a href="/complaint/create-complaint"
                   class="inline-flex items-center justify-center
                          bg-green-600 hover:bg-green-700
                          dark:bg-green-500 dark:hover:bg-green-600
                          text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
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
                <img src="{{ asset('images/placeholder.png') }}" alt="Placeholder Gambar"
                     class="w-full h-auto max-w-lg object-cover mb-4">
            @endif
        </div>
    </div>
</div>
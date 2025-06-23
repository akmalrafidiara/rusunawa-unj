<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content;
use Illuminate\Support\Facades\Storage;

state([
    'aboutTitle' => '',
    'aboutDescription' => '',
    'aboutImageUrl' => '',
    'aboutLink' => '',
    'dayaTariks' => [],
]);

mount(function () {
    $this->aboutTitle = optional(Content::where('content_key', 'about_us_title')->first())->content_value ?? '';
    $this->aboutDescription = optional(Content::where('content_key', 'about_us_description')->first())->content_value ?? '';
    $this->aboutImageUrl = optional(Content::where('content_key', 'about_us_image_url')->first())->content_value ?? '';
    $this->aboutLink = optional(Content::where('content_key', 'about_us_link')->first())->content_value ?? '';

    $loadedDayaTariks = optional(Content::where('content_key', 'about_us_daya_tariks')->first())->content_value;
    // Ensure $loadedDayaTariks is an array, even if it's null or not an array from DB
    $this->dayaTariks = is_array($loadedDayaTariks) ? $loadedDayaTariks : [];

    // Definisikan array warna untuk latar belakang ikon
    $iconColors = ['bg-blue-500', 'bg-red-500', 'bg-purple-500', 'bg-yellow-500', 'bg-pink-500', 'bg-indigo-500'];

    // Tambahkan warna ke setiap item daya tarik
    $this->dayaTariks = array_map(function($item, $index) use ($iconColors) {
        return [
            'text' => $item,
            'color' => $iconColors[$index % count($iconColors)] // Ambil warna secara berurutan, ulang jika item lebih banyak
        ];
    }, $this->dayaTariks, array_keys($this->dayaTariks));
});

?>

<div class="relative w-full py-0 px-4 sm:px-6 lg:px-8 overflow-hidden">
    {{-- Background Overlay (Jika diperlukan, disesuaikan dengan gambar latar) --}}
    {{-- <div class="absolute inset-0 bg-pink-100 bg-opacity-70"></div> --}}

    <div class="container mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        {{-- Bagian Kiri: Gambar-gambar (Hanya untuk Desktop/Large Screens) --}}
        <div class="hidden lg:block relative -left-8">
            <div class="relative w-[600px] h-[500px] mx-auto"> {{-- Ukuran ditingkatkan untuk desktop --}}
                {{-- Top-Left Kotak --}}
                <div class="absolute top-0 left-0 w-[280px] h-[280px] rounded-2xl overflow-hidden shadow-xl transform rotate-3 z-10 border-2 border-teal-500"
                    style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 200% auto; background-position: top 10% left 15%;">
                </div>
                {{-- Top-Right Kotak --}}
                <div class="absolute top-10 right-0 w-[350px] h-[180px] rounded-2xl overflow-hidden shadow-lg transform -rotate-2 z-20 border-2 border-teal-500"
                    style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 200% auto; background-position: top 0% right 0%;">
                </div>
                {{-- Bottom-Left Kotak --}}
                <div class="absolute bottom-0 left-10 w-[300px] h-[220px] rounded-2xl overflow-hidden shadow-lg transform rotate-1 z-0 border-2 border-teal-500"
                    style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 250% auto; background-position: bottom 30% left 20%;">
                </div>
                {{-- Bottom-Right Kotak --}}
                <div class="absolute bottom-5 right-10 w-[320px] h-[320px] rounded-2xl overflow-hidden shadow-lg transform -rotate-5 z-10 border-2 border-teal-500"
                    style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 180% auto; background-position: bottom 20% right 30%;">
                </div>
            </div>
        </div>

        {{-- Bagian Kanan: Teks Konten --}}
        <div class="text-center lg:text-left">
            {{-- Tambahkan padding kiri dan ubah warna teks menjadi hijau --}}
            <span class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2 block text-left sm:pl-4">Tentang Kami</span>
            {{-- Tambahkan padding kiri untuk mobile dan pastikan rata kiri --}}
            <h3 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight text-left sm:pl-4">
                {{ $aboutTitle }}
            </h3>

            {{-- START: Bagian Gambar Mobile Baru --}}
            @if ($aboutImageUrl)
                {{-- Mengurangi max-width dan height di mobile --}}
                <div class="lg:hidden relative mt-8 mb-8 mx-auto" style="width: 100%; max-width: 380px;">
                    <div class="relative w-full h-[320px] mx-auto">
                        {{-- Ukuran kotak-kotak gambar disesuaikan --}}
                        <div class="absolute top-0 left-0 w-[160px] h-[160px] rounded-2xl overflow-hidden shadow-xl transform rotate-3 z-10 border-2 border-teal-500"
                            style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 200% auto; background-position: top 10% left 15%;">
                        </div>
                        <div class="absolute top-8 right-0 w-[200px] h-[100px] rounded-2xl overflow-hidden shadow-lg transform -rotate-2 z-20 border-2 border-teal-500"
                            style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 200% auto; background-position: top 0% right 0%;">
                        </div>
                        <div class="absolute bottom-0 left-8 w-[180px] h-[120px] rounded-2xl overflow-hidden shadow-lg transform rotate-1 z-0 border-2 border-teal-500"
                            style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 250% auto; background-position: bottom 30% left 20%;">
                        </div>
                        <div class="absolute bottom-4 right-8 w-[190px] h-[190px] rounded-2xl overflow-hidden shadow-lg transform -rotate-5 z-10 border-2 border-teal-500"
                            style="background-image: url('{{ $aboutImageUrl ?: asset('images/placeholder.png') }}'); background-size: 180% auto; background-position: bottom 20% right 30%;">
                        </div>
                    </div>
                </div>
            @endif
            {{-- END: Bagian Gambar Mobile Baru --}}

            {{-- Deskripsi rata tengah di mobile, rata kiri di desktop --}}
            <p class="text-gray-700 text-m md:text-xl leading-relaxed mb-8 text-center lg:text-left sm:pl-4">
                {{ $aboutDescription }}
            </p>

            {{-- Menampilkan Keunggulan Kami (Daya Tarik) --}}
            @if (!empty($dayaTariks))
                {{-- Tambahkan padding kiri untuk mobile --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-2 gap-y-3.5 mb-8 sm:pl-4 lg:gap-4"> {{-- MENGUBAH: Menggunakan gap-x-2 dan gap-y-1 untuk mobile --}}
                    @foreach ($dayaTariks as $dayaTarik)
                        <div class="flex items-center text-sm bg-white p-3 rounded-2xl shadow-md border border-gray-100 transform transition-transform duration-200 hover:scale-105 lg:text-base lg:p-4 lg:rounded-4xl">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center mr-2 {{ $dayaTarik['color'] }} lg:w-8 lg:h-8 lg:mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-white lg:w-5 lg:h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="text-gray-800 font-bold text-m lg:text-m">{{ $dayaTarik['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tombol Hubungi Kami --}}
            {{-- Tambahkan padding kiri untuk mobile agar sejajar dengan teks di atasnya --}}
            <div class="sm:pl-4">
                <a href="{{ $aboutLink ?: '#' }}"
                   class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>
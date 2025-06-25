<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content;
use Illuminate\Support\Facades\Storage; // Tetap sertakan jika mungkin digunakan di bagian lain komponen Anda

state([
    'aboutTitle' => '',
    'aboutDescription' => '',
    'aboutImageUrl' => '',
    'dayaTariks' => [],
]);

mount(function () {

    $this->aboutTitle = optional(Content::where('content_key', 'about_us_title')->first())->content_value ?? 'Data belum tersedia';
    $this->aboutDescription = optional(Content::where('content_key', 'about_us_description')->first())->content_value ?? 'Data belum tersedia';
    
    // Atur URL gambar. Jika kosong, gunakan placeholder langsung di sini.
    $this->aboutImageUrl = optional(Content::where('content_key', 'about_us_image_url')->first())->content_value ?? asset('images/placeholder.png');

    // Penanganan daya tariks: Pastikan di-decode sebagai array, sama seperti nearbyLocations
    $dayaTariksContent = optional(Content::where('content_key', 'about_us_daya_tariks')->first())->content_value;

    if (is_array($dayaTariksContent)) {
        $loadedDayaTariks = $dayaTariksContent;
    } elseif (is_string($dayaTariksContent)) {
        $decoded = json_decode($dayaTariksContent, true);
        $loadedDayaTariks = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    } else {
        $loadedDayaTariks = [];
    }

    // Definisikan array warna untuk latar belakang ikon
    $iconColors = ['bg-blue-500', 'bg-red-500', 'bg-purple-500', 'bg-yellow-500', 'bg-pink-500', 'bg-indigo-500'];

    // Tambahkan warna ke setiap item daya tarik
    $this->dayaTariks = array_map(function($item, $index) use ($iconColors) {
        return [
            'text' => $item,
            'color' => $iconColors[$index % count($iconColors)] // Ambil warna secara berurutan, ulang jika item lebih banyak
        ];
    }, $loadedDayaTariks, array_keys($loadedDayaTariks));
});

?>

<div class="relative w-full py-0 px-4 sm:px-6 lg:px-8 overflow-hidden">
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
            <p class="text-gray-700 text-base md:text-xl leading-relaxed mb-8 text-center lg:text-left sm:pl-4">
                {{ $aboutDescription }}
            </p>

            {{-- Menampilkan Keunggulan Kami (Daya Tarik) --}}
            @if (!empty($dayaTariks))
                {{-- Tambahkan padding kiri untuk mobile --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 mb-8 sm:pl-4 lg:gap-x-8 lg:gap-y-5">
                    @foreach ($dayaTariks as $dayaTarik)
                        <div class="flex items-center text-sm transform transition-transform duration-200 hover:scale-105">
                            {{-- Ikon tetap di luar bungkus --}}
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $dayaTarik['color'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            {{-- Teks dan bungkusnya--}}
                            <div class="flex-grow bg-white p-3 rounded-2xl shadow-md border border-gray-100">
                                <p class="text-gray-800 font-semibold text-m leading-snug">
                                    {{ $dayaTarik['text'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tombol Hubungi Kami --}}
            <div class="sm:pl-4">
                <a href="#contact"
                   class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>
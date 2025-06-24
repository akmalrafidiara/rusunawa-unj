<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content; // Pastikan model Content ada di App\Models

state([
    // 'contactTitle' => '', // Dihapus, sekarang hardcode
    // 'contactDescription' => '', // Dihapus, sekarang hardcode
    'phoneNumber' => '',
    'email' => '',
    'address' => '',
    // 'operationalHours' => '', // Dihapus
]);

mount(function () {
    // Mengambil data kontak utama dari CMS
    $this->phoneNumber = optional(Content::where('content_key', 'contact_phone_number')->first())->content_value ?? '';
    $this->email = optional(Content::where('content_key', 'contact_email')->first())->content_value ?? '';
    $this->address = optional(Content::where('content_key', 'contact_address')->first())->content_value ?? 'Jl. Pemuda No.10, Rawamangun, Jakarta Timur, DKI Jakarta 13220';
    // Data operationalHours tidak lagi diambil atau ditampilkan
});

?>

{{-- Konten HTML untuk menampilkan bagian Kontak Kami --}}
<div class="p-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Kolom Kiri: Informasi Kontak --}}
        <div class="text-center lg:text-left py-4 relative">
            <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Kontak Kami</span>
            <h3 class="text-4xl font-extrabold text-gray-900 mb-6 leading-tight">
                Ingin Tahu Lebih Banyak? {{-- Hardcode Contact Title --}}
            </h3>
            <p class="text-gray-700 text-lg leading-relaxed mb-8">
                Punya pertanyaan lebih lanjut tentang kami? Kontak kami atau kirimkan pesan melalui form berikut. Kami akan menghubungi Anda kembali secepatnya. {{-- Hardcode Contact Description --}}
            </p>

            {{-- Detail Kontak dengan Ikon --}}
            <ul class="space-y-4 text-gray-700 text-lg">
                @if ($phoneNumber)
                    <li class="flex items-center justify-center lg:justify-start">
                        <flux:icon name="phone" class="w-6 h-6 mr-3 text-blue-600 flex-shrink-0" />
                        <span>{{ $phoneNumber }}</span>
                    </li>
                @endif
                @if ($email)
                    <li class="flex items-center justify-center lg:justify-start">
                        {{-- Mengganti flux:icon "mail" dengan SVG inline Heroicon "Mail" --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-1 12H4a2 2 0 01-2-2V6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ $email }}</span>
                    </li>
                @endif
                @if ($address)
                    <li class="flex items-center justify-center lg:justify-start">
                        <flux:icon name="map-pin" class="w-6 h-6 mr-3 text-blue-600 flex-shrink-0" />
                        <span>{{ $address }}</span>
                    </li>
                @endif
                {{-- Operational Hours dihapus --}}
            </ul>
        </div>

        {{-- Kolom Kanan: Panggil Komponen Livewire Form --}}
        <div class="py-4"> {{-- Tambahkan padding vertikal agar sejajar dengan sisi kiri --}}
            @livewire('frontend.guest-question-form') {{-- Panggil komponen Livewire form yang baru dibuat --}}
        </div>
    </div>
</div>

<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content;

state([
    'phoneNumber' => '',
    'email' => '',
    'address' => '',
]);

mount(function () {
    // Mengambil data kontak utama dari CMS
    $this->phoneNumber = optional(Content::where('content_key', 'contact_phone_number')->first())->content_value ?? 'Data belum tersedia';
    $this->email = optional(Content::where('content_key', 'contact_email')->first())->content_value ?? 'Data belum tersedia';
    $this->address = optional(Content::where('content_key', 'contact_address')->first())->content_value ?? 'Data belum tersedia';
});

?>

{{-- Konten HTML untuk menampilkan bagian Kontak Kami --}}
<div class="relative w-full py-2 px-0 lg:px-4 overflow-hidden text-left relative"> {{-- Removed py-4 as it's now in parent index.blade.php --}}
    <span class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2 block">Kontak Kami</span>
    <h3 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
        Ingin Tahu Lebih Banyak?
    </h3>
    <p class="text-gray-700 text-lg leading-relaxed mb-8">
        Punya pertanyaan lebih lanjut tentang kami? Kontak kami atau kirimkan pesan melalui form berikut. Kami akan menghubungi Anda kembali secepatnya.
    </p>

    {{-- Detail Kontak dengan Ikon --}}
    <ul class="space-y-4 text-gray-700">
        @if ($phoneNumber)
            <li class="flex items-center">
                {{-- Icon telepon --}}
                <div class="p-2 bg-white rounded-full shadow-md mr-3 flex-shrink-0">
                    <flux:icon name="phone" class="w-6 h-6 text-green-600" />
                </div>
                <span class="text-base md:text-lg">{{ $phoneNumber }}</span>
            </li>
        @endif
        @if ($email)
            <li class="flex items-center">
                {{-- Icon email --}}
                <div class="p-2 bg-white rounded-full shadow-md mr-3 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-1 12H4a2 2 0 01-2-2V6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-base md:text-lg">{{ $email }}</span>
            </li>
        @endif
        @if ($address)
            <li class="flex items-center">
                {{-- Icon alamat --}}
                <div class="p-2 bg-white rounded-full shadow-md mr-3 flex-shrink-0">
                    <flux:icon name="map-pin" class="w-6 h-6 text-green-600" />
                </div>
                <span class="text-base md:text-lg">{{ $address }}</span>
            </li>
        @endif
    </ul>
</div>
<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content;

state([
    'phoneNumber' => '',
    'email' => '',
    'address' => '',
    'map_address' => '',
]);

mount(function () {
    // Mengambil data kontak utama dari CMS
    $this->phoneNumber = optional(Content::where('content_key', 'contact_phone_number')->first())->content_value ?? 'Data belum tersedia';
    $this->email = optional(Content::where('content_key', 'contact_email')->first())->content_value ?? 'Data belum tersedia';
    $this->address = optional(Content::where('content_key', 'contact_address')->first())->content_value ?? 'Data belum tersedia';
    $this->map_address = optional(Content::where('content_key', 'contact_map_address')->first())->content_value ?? 'Data belum tersedia';
});

?>

{{-- Konten HTML untuk menampilkan bagian Kontak Kami --}}
<div class="relative w-full py-2 px-0 lg:px-4 overflow-hidden text-left relative">
    <span class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2 block">Kontak Kami</span>
    <h3 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight">
        Ingin Tahu Lebih Banyak?
    </h3>
    <p class="text-gray-700 dark:text-zinc-300 text-sm lg:text-lg leading-relaxed mb-8">
        Punya pertanyaan lebih lanjut tentang kami? Kontak kami atau kirimkan pesan melalui form berikut. Kami akan menghubungi Anda kembali secepatnya.
    </p>

    {{-- Detail Kontak dengan Ikon --}}
    <ul class="space-y-4 text-gray-700 dark:text-zinc-100">
        @if ($phoneNumber)
            <li class="flex items-center">
                {{-- Icon telepon --}}
                <div class="p-2 rounded-full mr-3 flex-shrink-0 bg-white shadow-md dark:bg-zinc-900 dark:shadow-none">
                    <flux:icon name="phone" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $phoneNumber)) }}" onclick="window.open(this.href); return false;" class="text-base md:text-lg hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">{{ $phoneNumber }}</a>
            </li>
        @endif
        @if ($email)
            <li class="flex items-center">
                {{-- Icon email --}}
                <div class="p-2 rounded-full mr-3 flex-shrink-0 bg-white shadow-md dark:bg-zinc-900 dark:shadow-none">
                    <flux:icon name="envelope" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <a href="mailto:{{ $email }}" onclick="window.open(this.href); return false;" class="text-base md:text-lg hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">{{ $email }}</a>
            </li>
        @endif
        @if ($address)
            <li class="flex items-center">
                {{-- Icon alamat --}}
                <div class="p-2 rounded-full mr-3 flex-shrink-0 bg-white shadow-md dark:bg-zinc-900 dark:shadow-none">
                    <flux:icon name="map-pin" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <a href="{{ $map_address }}" onclick="window.open(this.href); return false;" class="text-base md:text-lg hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">{{ $address }}</a>
            </li>
        @endif
    </ul>
</div>
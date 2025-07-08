<?php

use function Livewire\Volt\{state, mount};
use App\Models\Content; // Pastikan model Content ada di App\Models
use Illuminate\Support\Facades\Storage; // Untuk mengakses gambar dari storage

state([
    'footerLogoUrl' => '',
    'footerTitle' => '',
    'footerText' => '',
    'phoneNumber' => '',
    'email' => '',
    'operationalHours' => '',
]);

mount(function () {
    $this->footerLogoUrl = optional(Content::where('content_key', 'footer_logo_url')->first())->content_value ?? '';
    $this->footerTitle = optional(Content::where('content_key', 'footer_title')->first())->content_value ?? 'Data belum tersedia';
    $this->footerText = optional(Content::where('content_key', 'footer_text')->first())->content_value ?? 'Data belum tersedia';
    $this->phoneNumber = optional(Content::where('content_key', 'contact_phone_number')->first())->content_value ?? 'Data belum tersedia';
    $this->email = optional(Content::where('content_key', 'contact_email')->first())->content_value ?? 'Data belum tersedia';
    $this->operationalHours = optional(Content::where('content_key', 'contact_operational_hours')->first())->content_value ?? 'Data belum tersedia';
});

?>

{{-- Konten HTML untuk menampilkan bagian Footer --}}
<footer class="text-gray-800 dark:text-gray-300 mt-auto">
    {{-- Bagian atas footer --}}
    <div class="bg-gray-50 dark:bg-zinc-900 py-10 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-12 gap-x-16 gap-y-8">
            {{-- Kolom Kiri: Logo dan Alamat  --}}
            <div class="px-4 md:col-span-4 text-left">
                @if ($footerLogoUrl)
                    {{-- Menampilkan logo jika ada URL yang valid --}}
                    <img src="{{ url($footerLogoUrl) }}" alt="{{ $footerTitle }}" class="h-16 md:mx-0 mb-4 rounded-lg">
                @else
                    {{-- Placeholder jika tidak ada logo --}}
                    <div
                        class="h-16 w-16 md:mx-0 mb-4 bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-gray-400 flex items-center justify-center rounded-lg text-lg font-bold">
                        {{-- Logo --}}
                    </div>
                @endif
                <h3 class="text-gray-900 dark:text-gray-100 text-xl font-bold mb-2">{{ $footerTitle }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $footerText }}</p>
            </div>

            {{-- Kolom Kontak Kami --}}
            <div class="px-4 lg:px-25 md:col-span-4 text-left">
                <h4 class="text-green-700 dark:text-green-400 text-lg font-semibold mb-4">Kontak Kami</h4>
                <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                    <li class="flex items-center justify-start">
                        <flux:icon name="phone" class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" />
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $phoneNumber)) }}" onclick="window.open(this.href); return false;" class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">{{ $phoneNumber }}</a>
                    </li>
                    <li class="flex items-center justify-start">
                        <flux:icon name="envelope" class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" />
                        <a href="mailto:{{ $email }}" onclick="window.open(this.href); return false;" class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">{{ $email }}</a>
                    </li>
                    <li class="flex items-center justify-start">
                        <flux:icon name="clock" class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" />
                        <span class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">{{ $operationalHours }}</span>
                    </li>
                </ul>
            </div>

            {{-- Kolom Tautan Terkait --}}
            <div class="px-4 lg:px-0 md:col-span-2 text-left">
                <h4 class="text-green-700 dark:text-green-400 text-lg font-semibold mb-4">Tautan Terkait</h4>
                <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                    <li>
                        <a href="https://bpu.unj.ac.id" onclick="window.open(this.href); return false;"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Website
                            BPU UNJ</a>
                    </li>
                    <li>
                        <a href="https://unj.ac.id" onclick="window.open(this.href); return false;"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Website
                            UNJ</a>
                    </li>
                    <li>
                        <a href="https://sso.unj.ac.id" onclick="window.open(this.href); return false;"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">SSO
                            UNJ</a>
                    </li>
                    <li>
                        <a href="https://siakad.unj.ac.id" onclick="window.open(this.href); return false;"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Siakad
                            UNJ</a>
                    </li>
                </ul>
            </div>

            {{-- Kolom Tautan Cepat --}}
            <div class="px-4 lg:px-0 md:col-span-2 text-left">
                <h4 class="text-green-700 dark:text-green-400 text-lg font-semibold mb-4">Tautan Cepat</h4>
                <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                    <li>
                        <a href="#"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Login
                            Penghuni</a>
                    </li>
                    <li>
                        <a href="/complaint/track-complaint"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Cek
                            Pengaduan</a>
                    </li>
                    <li>
                        <a href="/tenancy"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Sewa
                            Kamar</a>
                    </li>
                    <li>
                        <a href="/#unit-types"
                            class="hover:text-green-600 dark:hover:text-green-500 transition-colors duration-200">Tipe
                            Kamar</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Hak Cipta --}}
    <div class="bg-green-600 dark:bg-green-700 py-4 text-center">
        <p class="text-white text-sm">© {{ date('Y') }} - Badan Pengelola Usaha Universitas Negeri Jakarta</p>
    </div>
</footer>

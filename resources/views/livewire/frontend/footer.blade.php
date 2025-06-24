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
<footer class="text-gray-800">
    {{-- Bagian atas footer dengan latar belakang abu-abu --}}
    <div class="bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
        {{-- Mengatur ulang grid untuk kontrol yang lebih baik dengan 12 kolom --}}
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-12 gap-x-16 gap-y-8">
            {{-- Kolom Kiri: Logo dan Alamat (md:col-span-4 untuk mendorong konten ke kanan dan memperlebar jarak) --}}
            {{-- Mengubah text-center menjadi text-left untuk tampilan mobile --}}
            <div class="px-4 md:col-span-4 text-left">
                @if ($footerLogoUrl)
                    {{-- Perubahan: Hapus 'mx-auto' agar rata kiri di mobile, pertahankan 'md:mx-0' untuk desktop --}}
                    <img src="{{ $footerLogoUrl }}" alt="{{ $footerTitle }}" class="h-16 md:mx-0 mb-4 rounded-lg">
                @else
                    {{-- Placeholder jika tidak ada logo --}}
                    <div class="h-16 w-16 md:mx-0 mb-4 bg-gray-200 text-gray-500 flex items-center justify-center rounded-lg text-lg font-bold">
                        {{-- Logo --}}
                    </div>
                @endif
                <h3 class="text-gray-900 text-xl font-bold mb-2">{{ $footerTitle }}</h3>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $footerText }}</p>
            </div>

            {{-- Kolom Kontak Kami (md:col-span-3, bergeser ke kanan dan sedikit lebih besar) --}}
            {{-- Mengubah text-center menjadi text-left untuk tampilan mobile --}}
            <div class="px-4 lg:px-25 md:col-span-4 text-left"> 
                <h4 class="text-green-700 text-lg font-semibold mb-4">Kontak Kami</h4>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-center justify-start">
                        {{-- Ganti flux:icon dengan SVG inline Heroicon "Phone" --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>{{ $phoneNumber }}</span>
                    </li>
                    <li class="flex items-center justify-start">
                        {{-- Mengganti flux:icon "mail" dengan SVG inline Heroicon "Mail" --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-1 12H4a2 2 0 01-2-2V6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ $email }}</span>
                    </li>
                    <li class="flex items-center justify-start">
                        {{-- Ganti flux:icon dengan SVG inline Heroicon "Clock" --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $operationalHours }}</span>
                    </li>
                </ul>
            </div>

            {{-- Kolom Tautan Terkait (md:col-span-2) --}}
            {{-- Mengubah text-center menjadi text-left untuk tampilan mobile --}}
            <div class="px-4 lg:px-0 md:col-span-2 text-left">
                <h4 class="text-green-700 text-lg font-semibold mb-4">Tautan Terkait</h4>
                <ul class="space-y-3 text-gray-700">
                    <li><a href="https://bpu.unj.ac.id" class="hover:text-green-600 transition-colors duration-200">Website BPU UNJ</a></li>
                    <li><a href="https://unj.ac.id" class="hover:text-green-600 transition-colors duration-200">Website UNJ</a></li>
                    <li><a href="https://sso.unj.ac.id" class="hover:text-green-600 transition-colors duration-200">SSO UNJ</a></li>
                    <li><a href="https://siakad.unj.ac.id" class="hover:text-green-600 transition-colors duration-200">Siakad UNJ</a></li>
                </ul>
            </div>

            {{-- Kolom Tautan Cepat (md:col-span-2) --}}
            {{-- Mengubah text-center menjadi text-left untuk tampilan mobile --}}
            <div class="px-4 lg:px-0 md:col-span-2 text-left">
                <h4 class="text-green-700 text-lg font-semibold mb-4">Tautan Cepat</h4>
                <ul class="space-y-3 text-gray-700">
                    <li><a href="#" class="hover:text-green-600 transition-colors duration-200">Login Penghuni</a></li>
                    <li><a href="#" class="hover:text-green-600 transition-colors duration-200">Cek Pengaduan</a></li>
                    <li><a href="/tenancy" class="hover:text-green-600 transition-colors duration-200">Sewa Kamar</a></li>
                    <li><a href="#unit-types" class="hover:text-green-600 transition-colors duration-200">Tipe Kamar</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Hak Cipta --}}
    {{-- Ubah background menjadi hijau dan teks menjadi putih, serta buat full-width --}}
    <div class="bg-green-600 py-4 text-center">
        <p class="text-white text-sm">© {{ date('Y') }} - Badan Pengelola Usaha Universitas Negeri Jakarta</p>
    </div>
</footer>

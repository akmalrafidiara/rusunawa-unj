<?php

use function Livewire\Volt\{state, mount};
use App\Models\Faq; // Pastikan model Faq ada di App\Models

state([
    'faqs' => [],
    'openFaqId' => null, // Untuk melacak ID FAQ yang sedang terbuka
]);

mount(function () {
    // Mengambil semua FAQ dari database, diurutkan berdasarkan priority
    $this->faqs = Faq::orderBy('priority', 'asc')->get();
});

// Metode untuk membuka atau menutup item FAQ
$toggleFaq = function ($faqId) {
    $this->openFaqId = ($this->openFaqId === $faqId) ? null : $faqId;
};

?>

{{-- Konten HTML untuk menampilkan bagian FAQ --}}
<div class="relative w-full py-2 px-4 sm:px-6 lg:px-8 overflow-hidden">
    {{-- Grid utama dengan perataan item secara vertikal di tengah --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="text-left lg:sticky lg:top-0 lg:self-start">
            <span class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-2 block">Frequently Asked Question</span>
            <h3 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
                Pertanyaan yang Sering Ditanyakan
            </h3>
        </div>

        {{-- Bagian Kanan: Daftar FAQ Akordeon --}}
        @if ($faqs->isNotEmpty())
        <div class="max-w-3xl mx-auto lg:mx-0 w-full">
            @foreach ($faqs as $faq)
            <div class="bg-white overflow-hidden border-b-2 {{ $openFaqId === $faq->id ? 'border-green-600 border-b-4' : 'border-green-300' }}">
                <button type="button" wire:click="toggleFaq({{ $faq->id }})"
                    class="w-full px-6 py-4 text-left font-semibold text-base text-gray-800 transition-colors duration-200 focus:outline-none
                    {{ $openFaqId === $faq->id ? 'bg-green-100 text-green-800' : 'hover:bg-gray-100' }}">
                    {{-- Menggunakan grid untuk memisahkan pertanyaan dan ikon --}}
                    <div class="grid grid-cols-[1fr_auto] items-center gap-4"> {{-- 1fr untuk pertanyaan, auto untuk ikon --}}
                        <span class="pr-4">{{ $faq->question }}</span>
                        <svg class="w-6 h-6 transform transition-transform duration-300 {{ $openFaqId === $faq->id ? 'rotate-180 text-green-600' : 'text-gray-500' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                @if ($openFaqId === $faq->id)
                <div class="px-6 pb-4 pt-2 text-gray-700 leading-relaxed border-t border-gray-100 bg-white rounded-b-md">
                    <div class="trix-content">
                        <span class="text-sm block">
                            {!! $faq->answer !!}
                        </span>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p class="col-span-full text-center text-gray-600 py-10">Belum ada pertanyaan umum yang tersedia saat ini.</p>
        @endif
    </div>
</div>
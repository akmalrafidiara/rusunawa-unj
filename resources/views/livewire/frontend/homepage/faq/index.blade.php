<?php

use function Livewire\Volt\{state, mount};
use App\Models\Faq;

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
            <span class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2 block">Frequently Asked Question</span>
            <h3 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
                Pertanyaan yang Sering Ditanyakan
            </h3>
        </div>

        {{-- Bagian Kanan: Daftar FAQ Akordeon --}}
        @if ($faqs->isNotEmpty())
        <div class="max-w-3xl mx-auto lg:mx-0 w-full">
            @foreach ($faqs as $faq)
            <div class="overflow-hidden border-b-2
                        {{ $openFaqId === $faq->id ? 'border-green-600 dark:border-green-500 border-b-4' : 'border-green-300 dark:border-zinc-700' }}">
                <button type="button" wire:click="toggleFaq({{ $faq->id }})"
                    class="w-full px-6 py-4 text-left font-semibold text-base transition-colors duration-200 focus:outline-none
                    {{ $openFaqId === $faq->id ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-800 dark:text-zinc-100' }}">
                    {{-- Menggunakan grid untuk memisahkan pertanyaan dan ikon --}}
                    <div class="grid grid-cols-[1fr_auto] items-center gap-4">
                        <span class="pr-4">{{ $faq->question }}</span>
                        <flux:icon name="chevron-down" class="w-6 h-6 transform transition-transform duration-300
                            {{ $openFaqId === $faq->id ? 'rotate-180 text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-zinc-400' }}" />
                    </div>
                </button>
                @if ($openFaqId === $faq->id)
                <div class="px-6 pb-4 pt-2 leading-relaxed border-t
                            text-gray-700 dark:text-zinc-300
                            border-gray-100 dark:border-zinc-700
                            rounded-b-md
                ">
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
        <p class="col-span-full text-center text-gray-600 dark:text-zinc-400 py-10">Belum ada pertanyaan umum yang tersedia saat ini.</p>
        @endif
    </div>
</div>
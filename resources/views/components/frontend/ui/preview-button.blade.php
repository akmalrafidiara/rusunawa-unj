@props(['file'])

{{--
    Komponen ini sekarang mengelola state-nya sendiri dengan Alpine.js
    x-data: Menginisialisasi state untuk modal lightbox
--}}
<div x-data="{ showPreview: false, previewUrl: '' }">
    {{-- TOMBOL PREVIEW --}}
    <button type="button"
        @if ($file && str_starts_with($file->getMimeType(), 'image/')) {{-- Jika file adalah gambar, siapkan data untuk lightbox --}}
                x-on:click="previewUrl = '{{ $file->temporaryUrl() }}'; showPreview = true"
            @elseif ($file)
                {{-- Jika bukan gambar (misal: PDF), buka di tab baru --}}
                onclick="window.open('{{ $file->temporaryUrl() }}', '_blank')" @endif
        class="flex items-center gap-2 mt-1 text-emerald-600 dark:text-emerald-500 hover:text-emerald-700 transition-colors cursor-pointer">

        @if ($file && str_starts_with($file->getMimeType(), 'image/'))
            {{-- Ikon Mata untuk Gambar --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        @elseif ($file)
            {{-- Ikon Dokumen untuk file lain --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @endif

        <span class="font-semibold underline">{{ $file ? $file->getClientOriginalName() : 'Tidak ada file' }}</span>
    </button>

    {{-- ====================================================== --}}
    {{--             MODAL LIGHTBOX UNTUK GAMBAR              --}}
    {{-- ====================================================== --}}
    <div x-show="showPreview" x-on:keydown.escape.window="showPreview = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

        <div x-show="showPreview" x-transition.opacity class="fixed inset-0 bg-black/75"></div>

        <div x-show="showPreview" x-transition class="relative w-full max-w-3xl rounded-lg">
            <img :src="previewUrl" alt="Image Preview" class="w-full h-auto rounded-lg shadow-xl">
            <button x-on:click="showPreview = false"
                class="absolute -top-2 -right-2 h-8 w-8 rounded-full bg-white text-gray-600 hover:bg-gray-200 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>

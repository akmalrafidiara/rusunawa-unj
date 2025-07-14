@props(['name', 'label', 'helpText' => null, 'required' => true])

@php
    // Dapatkan file yang diupload dari properti Livewire induk
    $uploadedFile = $this->getPropertyValue($name);
@endphp

<div x-data="{
    showPreview: false,
    previewUrl: '',
    isUploading: false,
    progress: 0,
    isDragging: false
}" x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false; progress = 0" x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress">

    {{-- Label untuk keseluruhan komponen --}}
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="mt-1">
        {{-- =============================================== --}}
        {{--      JIKA FILE SUDAH DI-UPLOAD / TERPILIH       --}}
        {{-- =============================================== --}}
        @if ($uploadedFile)
            <div
                class="flex items-center justify-between rounded-md border border-gray-300 bg-white dark:border-gray-600 dark:bg-zinc-800 p-2.5 shadow-sm">
                {{-- Tombol untuk Melihat File (Membuka Lightbox/Tab Baru) --}}
                <button type="button" {{-- Logika ini sekarang aman karena berada di dalam @if ($uploadedFile) --}}
                    @if (str_starts_with($uploadedFile->getMimeType(), 'image/')) x-on:click="previewUrl = '{{ $uploadedFile->temporaryUrl() }}'; showPreview = true"
                        @else
                            onclick="window.open('{{ $uploadedFile->temporaryUrl() }}', '_blank')" @endif
                    class="flex flex-grow items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">

                    @if (str_starts_with($uploadedFile->getMimeType(), 'image/'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    @endif
                    <span class="truncate max-w-xs">{{ $uploadedFile->getClientOriginalName() }}</span>
                </button>

                {{-- Tombol untuk Menghapus File --}}
                <button type="button" wire:click="$set('{{ $name }}', null)" wire:loading.attr="disabled"
                    class="ml-2 flex-shrink-0 text-gray-400 hover:text-red-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            {{-- =================================== --}}
            {{--      JIKA BELUM ADA FILE          --}}
            {{-- =================================== --}}
        @else
            <label for="{{ $name }}" x-on:dragover.prevent="isDragging = true"
                x-on:dragleave.prevent="isDragging = false"
                x-on:drop.prevent="isDragging = false; if ($event.dataTransfer.files.length > 0) { @this.upload('{{ $name }}', $event.dataTransfer.files[0]) }"
                class="relative flex w-full cursor-pointer justify-center rounded-md border-2 border-dashed px-6 py-10 transition-colors duration-200
                {{ $errors->has($name) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}
                :class="{ 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20'
                : isDragging }">

                <input id="{{ $name }}" name="{{ $name }}" type="file" class="sr-only"
                    wire:model="{{ $name }}">

                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                        viewBox="0 0 48 48" aria-hidden="true">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="mt-2 flex text-sm text-gray-600 dark:text-gray-400"><span
                            class="font-medium text-emerald-600">Upload file</span>
                        <p class="pl-1">atau tarik dan lepas</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, PDF hingga 2MB</p>
                </div>
            </label>
        @endif
    </div>

    {{-- Progress bar akan muncul di bawah kedua state di atas --}}
    <div x-show="isUploading" class="mt-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
        <div class="bg-emerald-600 h-2.5 rounded-full transition-all" :style="`width: ${progress}%`"></div>
    </div>

    @if ($helpText)
        <p class="text-xs text-gray-400 mt-2">{{ $helpText }}</p>
    @endif
    @error($name)
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror

    {{-- ====================================================== --}}
    {{--             MODAL LIGHTBOX UNTUK GAMBAR              --}}
    {{-- ====================================================== --}}
    <div x-show="showPreview" x-on:keydown.escape.window="showPreview = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

        <div x-show="showPreview" x-transition.opacity class="fixed inset-0 bg-black/75"></div>

        <div x-show="showPreview" x-transition class="relative w-full max-w-3xl rounded-lg">
            <img :src="previewUrl" alt="Image Preview" class="w-full max-h-96 object-contain rounded-lg">
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

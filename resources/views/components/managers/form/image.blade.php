@props([
    'model' => null,
    'id' => uniqid('image_upload_'),
    'label' => 'Pilih File',
    'accept' => 'image/jpeg,image/png',
    'maxlength' => 2048, // in KB
    'existingImageUrl' => null,
    'helperText' => 'Format file .jpg, .jpeg, .png (Maksimal 2MB)',
])

<div class="mb-4">
    <div class="flex items-center space-x-2 border rounded-md p-1 @error($model) border-red-500 @else border-gray-500 @enderror">
        <input
            type="file"
            wire:model.live="{{ $model }}" {{-- Menggunakan .live untuk pembaruan real-time --}}
            id="{{ $id }}"
            accept="{{ $accept }}"
            maxlength="{{ $maxlength }}"
            class="hidden"
        >
        <button type="button"
            onclick="document.getElementById('{{ $id }}').click()"
            class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            wire:loading.attr="disabled" {{-- Menonaktifkan tombol saat loading --}}
            wire:target="{{ $model }}" {{-- Menargetkan properti model untuk efek loading --}}
        >
            {{ $label }}
        </button>

        {{-- Efek Loading --}}
        <div wire:loading wire:target="{{ $model }}" class="text-sm text-gray-500 flex items-center space-x-2">
            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mengunggah...</span>
        </div>

        {{-- Nama File yang Dipilih/Diunggah --}}
        <span class="text-gray-700 dark:text-gray-300 text-sm truncate" wire:loading.remove wire:target="{{ $model }}">
            @if ($model && $this->$model)
                {{ $this->$model->getClientOriginalName() }}
            @else
                Tidak ada file yang dipilih
            @endif
        </span>
    </div>

    @error($model)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @else
        <p class="text-gray-500 text-sm mt-1">{{ $helperText }}</p>
    @enderror

    @if ($model && $this->$model)
        <div class="mt-2">
            <img src="{{ $this->$model->temporaryUrl() }}" style="max-width: 150px; height: auto;" class="rounded-md border border-gray-200">
            <p class="text-gray-500 text-sm mt-1">Preview gambar baru</p>
        </div>
    @elseif ($existingImageUrl)
        <div class="mt-2">
            <img src="{{ $existingImageUrl }}" style="max-width: 150px; height: auto;" class="rounded-md border border-gray-200">
            <p class="text-gray-500 text-sm mt-1">Gambar saat ini</p>
        </div>
    @endif
</div>
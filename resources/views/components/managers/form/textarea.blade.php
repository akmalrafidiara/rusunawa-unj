@props([
    'rows' => 3,
    'placeholder' => '',
])

@php
    // Ambil nama field dari wire:model
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    // Cek apakah ada error validasi
    $error = $wireModel && $errors->has($wireModel);
@endphp

<div class="w-full">
    <textarea {{-- Gabungkan semua atribut yang dikirim, termasuk wire:model --}}
        {{ $attributes->merge(['class' => 'block w-full rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white dark:placeholder-zinc-500 py-2 px-4 ' . ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')]) }}
        rows="{{ $rows }}" placeholder="{{ $placeholder }}"></textarea> {{-- Nilai sudah di-handle oleh wire:model, jadi tidak perlu diisi di sini --}}

    {{-- Tampilkan pesan error jika ada --}}
    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

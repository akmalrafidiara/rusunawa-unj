@props(['rows' => 3])

@php
    // Ambil nama field dari wire:model
    $wireModel = $attributes->whereStartsWith('wire:model')->first();

    // Cek apakah ada error validasi
    $error = $wireModel && $errors->has($wireModel);

    // Ambil nilai dari Livewire component
    $value = $wireModel ? $wireModel : '';
@endphp

<div class="w-full">
    <textarea wire:model="{{ $wireModel }}" rows="{{ $rows }}"
        class="w-full min-h-30 border-gray-300 dark:border-gray-600 bg-transparent dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $value }}</textarea>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

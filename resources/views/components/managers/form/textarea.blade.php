@props(['rows' => 3, 'placeholder' => ''])

@php
    // Ambil nama field dari wire:model
    $wireModel = $attributes->whereStartsWith('wire:model')->first();

    // Cek apakah ada error validasi
    $error = $wireModel && $errors->has($wireModel);

    // Ambil nilai dari Livewire component
    $value = $wireModel ? $wireModel : '';
@endphp

<div class="w-full">
    <textarea wire:model="{{ $wireModel }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
        class="w-full min-h-30 {{ $error ? 'border-red-500' : 'border-gray-500' }} bg-transparent dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ $value }}</textarea>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

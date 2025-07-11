@props(['name', 'label', 'type' => 'text', 'whatsapp' => false, 'placeholder' => '', 'required' => true])

@php
    $inputBaseClass =
        'w-full mt-1 block py-2 px-3 border dark:bg-zinc-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition';
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium mb-1">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="relative">
        @if ($whatsapp)
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">+62</span>
        @endif
        <input id="{{ $name }}" type="{{ $type }}" wire:model.live="{{ $name }}"
            placeholder="{{ $placeholder }}"
            class="{{ $inputBaseClass }} {{ $whatsapp ? 'pl-12' : '' }} @error($name) border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
    </div>
    @error($name)
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>

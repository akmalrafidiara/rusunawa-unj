@props(['name', 'label', 'options', 'required' => true, 'disabled' => false])

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
    <select id="{{ $name }}" wire:model.live="{{ $name }}"
        class="{{ $inputBaseClass }} {{ $errors->has($name) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} @if ($disabled) opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-600 @endif"
        @if ($disabled) disabled @endif>
        <option value="">Pilih {{ $label }}</option>
        @foreach ($options as $option)
            <option value="{{ $option->id ?? $option }}">{{ $option->name ?? $option }}</option>
        @endforeach
    </select>
    @error($name)
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>

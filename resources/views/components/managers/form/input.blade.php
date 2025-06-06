@props([
    'name' => null,
    'type' => 'text',
    'placeholder' => 'Masukkan nilai...',
    'icon' => null,
    'class' => 'w-full',
    'required' => false,
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $error = $wireModel && $errors->has($wireModel);
@endphp

<div class="w-full">
    <div class="relative">
        @if ($icon)
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <flux:icon :name="$icon" class="h-5 w-5 text-gray-400 dark:text-gray-200" />
            </div>
        @endif

        <input type="{{ $type }}" name="{{ $name }}"
            {{ $attributes->merge(['wire:model' => $wireModel]) }} placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="block w-full border rounded-md {{ $error ? 'border-red-500' : 'border-gray-500' }} dark:placeholder-gray-200 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 py-2 {{ $icon ? 'pl-10' : 'pl-4' }} pr-4 {{ $class }}">
    </div>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

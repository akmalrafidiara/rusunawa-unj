@props([
    'type' => 'text',
    'placeholder' => 'Masukkan nilai...',
    'icon' => null,
    'class' => 'w-full',
    'required' => false,
    'clearable' => false,
])

{{-- !IMPORTANT NOTE --}}
{{-- THIS COMPONENT UNDER DEBUGING, THIS COMPONENT CANT USING CLEARABLE FOR SHOWING CLEAR BUTTON --}}

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

        <input type="{{ $type }}" {{ $attributes->merge(['wire:model' => $wireModel]) }}
            placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
            class="block w-full border rounded-md {{ $error ? 'border-red-500' : 'border-gray-500' }} dark:placeholder-zinc-500 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 py-2 {{ $icon ? 'pl-10' : 'pl-4' }} pr-{{ $clearable ? '10' : '4' }} {{ $class }}">

        {{-- Tombol Clear --}}
        @if ($clearable && $wireModel)
            @if ($this->{$wireModel})
                <button type="button" wire:click="$set('{{ $wireModel }}', null)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none z-10">
                    &times;
                </button>
            @endif
        @endif
    </div>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

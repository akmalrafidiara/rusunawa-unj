@props([
    'placeholder' => 'Cari...', // Changed default placeholder to 'Cari...'
    'icon' => 'magnifying-glass', // Default icon for search
    'class' => 'w-full',
    'clearable' => true, // Search inputs are often clearable
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $error = $wireModel && $errors->has($wireModel);

    // THIS IS NOT A BUG, this is to get the actual wire:model value
    $value = $wireModel ? data_get($__livewire ?? null, $wireModel) : null;
@endphp

<div class="w-full">
    <div class="relative">
        {{-- Always show a search icon for a search component --}}
        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
            <flux:icon :name="$icon" class="h-5 w-5 text-gray-400 dark:text-gray-200" />
        </div>

        {{-- The core input for searching --}}
        <input type="search" {{-- Use type="search" for semantic correctness and browser features --}}
            {{ $attributes->merge(['wire:model.live.debounce.300ms' => $wireModel]) }} {{-- Add .live and .debounce for better search experience --}}
            placeholder="{{ $placeholder }}"
            class="block w-full border rounded-md {{ $error ? 'border-red-500' : 'border-gray-300' }} dark:placeholder-zinc-500 bg-transparent focus:outline-none focus:ring-2 focus:ring-green-500 py-2 pl-10 pr-{{ $clearable ? '10' : '4' }} {{ $class }}" />

        {{-- Clearable button for search input --}}
        @if ($clearable && $wireModel && $value)
            <button type="button" wire:click="$set('{{ $wireModel }}', null)"
                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none z-10">
                <flux:icon name="x-mark" />
            </button>
        @endif
    </div>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>
@props([
    'options' => [],
    'label' => 'Pilih Opsi',
])

@php
    $wireModelName = $attributes->whereStartsWith('wire:model')->first();
    $error = $wireModelName && $errors->has($wireModelName);
@endphp

<div class="w-full">
    <div x-data="{
        open: false,
        selectedValue: @entangle($attributes->wire('model')),
        options: @js($options),
        isArrayOfValues: @js(is_array($options) && !empty($options) && !isset(current($options)['value'])),
        activeIndex: -1,
    }" x-init="..." @click.away="open = false" class="relative">

        <button type="button" @click="open = !open" {{-- PERBAIKAN UTAMA ADA DI SINI --}}
            class="relative w-full cursor-pointer rounded-md border bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:outline-none focus:ring-1 sm:text-sm 
                   {{ $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }} 
                   dark:border-gray-600 dark:bg-zinc-800 dark:text-white">

            <span
                x-text="selectedValue ? (isArrayOfValues ? selectedValue : (options.find(o => o.value === selectedValue)?.label ?? selectedValue)) : '{{ $label ?? 'Pilih salah satu...' }}'"
                class="block truncate"></span>

            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="h-5 w-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </span>
        </button>

        <div x-show="open" x-cloak x-transition
            class="absolute z-10 mt-1 w-full rounded-md bg-white text-base shadow-lg ring-1 ring-gray-300 ring-opacity-5 focus:outline-none dark:bg-zinc-800 dark:ring-gray-600 sm:text-sm max-h-60 overflow-y-auto">

            @if (!$attributes->has('disabled'))
                <div @click="selectedValue = null; open = false"
                    class="cursor-pointer px-4 py-2 hover:bg-emerald-50 dark:hover:bg-zinc-700 rounded"
                    :class="{ 'bg-emerald-100 dark:bg-emerald-900/30': selectedValue === null }">
                    {{ $label ?? 'Pilih salah satu...' }}
                </div>
            @endif

            <template x-for="(option, index) in options" :key="index">
                <div @click="selectedValue = isArrayOfValues ? option : option.value; open = false"
                    class="cursor-pointer px-4 py-2 hover:bg-emerald-50 dark:hover:bg-zinc-700 rounded"
                    :class="{
                        'bg-emerald-100 dark:bg-emerald-900/30': isArrayOfValues ? selectedValue == option :
                            selectedValue === option.value
                    }">
                    <span x-text="isArrayOfValues ? option : option.label"></span>
                </div>
            </template>
        </div>

        {{-- Input Hidden tidak perlu diubah --}}
        <input type="hidden" {{ $attributes->whereStartsWith('wire:model') }}>
    </div>

    {{-- Pesan error validasi --}}
    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModelName) }}</span>
    @endif
</div>

@props([
    'options' => [],
    'label' => '',
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $error = $wireModel && $errors->has($wireModel);
@endphp

<div class="w-full">

    <select
        {{ $attributes->merge(['class' => 'block w-full rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white dark:placeholder-zinc-500 py-2 px-4 ' . ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')]) }}>
        <option value="" disabled>{{ $label }}</option>

        @foreach ($options as $option)
            {{-- The options will now have a consistent dark mode background --}}
            <option value="{{ $option['value'] }}" class="dark:bg-zinc-800">{{ $option['label'] }}</option>
        @endforeach
    </select>

    {{-- Error message display remains the same --}}
    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

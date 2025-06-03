@props(['type' => null])

@php
    $baseClasses = 'inline-flex items-center px-2 py-1 text-xs font-medium rounded-md border';

    $colorClasses = match ($type) {
        'Bulanan' => 'border-blue-500 text-blue-500',
        'Harian' => 'border-orange-500 text-orange-500',
        default => 'border-gray-300 text-gray-700',
    };
@endphp

<span class="{{ $baseClasses }} {{ $colorClasses }}">
    {{ $slot }}
</span>

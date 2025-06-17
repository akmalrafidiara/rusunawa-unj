@props(['type' => '', 'color' => [], 'class' => ''])

@php
    if ($type && $color === []) {
        $color = match ($type) {
            'success' => ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'],
            'danger' => ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400'],
            'warning' => ['bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'],
            'info' => ['bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400'],
            default => ['bg-gray-100', 'text-gray-800', 'dark:bg-gray-900/30', 'dark:text-gray-400'],
        };
    }
@endphp

<span
    class="
    inline-flex items-center px-2 py-1 text-xs font-medium rounded-md 
    {{ is_array($color) ? implode(' ', $color) : '' }} 
    {{ $class }}">
    {{ $slot }}
</span>

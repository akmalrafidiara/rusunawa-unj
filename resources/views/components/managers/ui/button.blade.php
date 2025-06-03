@props(['variant' => 'primary'])

@php
    $classes = match ($variant) {
        'primary'
            => 'bg-[var(--color-accent)] hover:bg-[var(--color-accent-content)] text-[var(--color-accent-foreground)]',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        default => '',
    };
@endphp

<button
    {{ $attributes->merge([
        'class' => "px-4 py-2 rounded-md cursor-pointer {$classes} whitespace-nowrap",
    ]) }}>
    {{ $slot }}
</button>

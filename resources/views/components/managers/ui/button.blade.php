@props(['variant' => 'primary', 'type' => 'button'])

{{-- Variant can be 'primary', 'secondary', or 'danger' --}}
{{-- Type can be 'button', 'submit', etc. --}}

@php
    $classes = match ($variant) {
        'primary'
            => 'bg-[var(--color-accent)] hover:bg-[var(--color-accent-content)] text-[var(--color-accent-foreground)]',
        'secondary'
            => 'bg-gray-200 hover:bg-gray-500 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white dark:bg-red-500 dark:hover:bg-red-600',
        default => '',
    };
@endphp

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "px-4 py-2 rounded-md cursor-pointer {$classes} whitespace-nowrap flex items-center justify-center gap-2",
    ]) }}>

    @if ($attributes->has('icon'))
        <span class="inline-flex items-center">
            {{-- blade-formatter-disable  --}}
            <flux:icon :name="$attributes->get('icon')" variant="outline" class="w-4 h-4" />
            {{-- blade-formatter-enable --}}
        </span>
        @if (trim($slot))
            <span class="ml-1">
                {{ $slot }}
            </span>
        @endif
    @else
        {{ $slot }}
    @endif
</button>

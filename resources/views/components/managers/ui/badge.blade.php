@props(['type', 'color' => [], 'class' => ''])

<span
    class="
    inline-flex items-center px-2 py-1 text-xs font-medium rounded-md 
    {{ is_array($color) ? implode(' ', $color) : '' }} 
    {{ $class }}">
    {{ $slot }}
</span>

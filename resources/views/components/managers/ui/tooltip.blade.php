<!-- resources/views/components/ui/tooltip.blade.php -->
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="group">
        {{ $slot }}
    </button>
    <div x-show="open" x-cloak x-transition.opacity.duration.200ms
        class="absolute z-10 mt-2 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-600 rounded shadow-lg p-2 text-sm">
        {{ $tooltip }}
    </div>
</div>

@props([
    'trigger' => null,
])

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
        class="px-4 py-2 border rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white shadow-sm flex justify-between items-center w-full bg-transparent text-gray-700"
        :class="{ 'ring-2 ring-blue-500': open }" type="button">
        @if (isset($trigger) && $trigger)
            {{ $trigger }}
        @else
            <span>Open</span>
        @endif
        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div x-show="open" @click.away="open = false" x-transition
        class="absolute right-0 mt-2 min-w-full p-5 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg z-50 ">
        <div class="py-1 {{ $attributes->get('class') }}">
            {{ $slot }}
        </div>
    </div>
</div>

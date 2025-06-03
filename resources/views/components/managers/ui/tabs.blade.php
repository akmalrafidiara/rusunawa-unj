<div class="flex flex-col sm:flex-row items-start justify-between gap-4 mb-4">
    <div class="flex gap-2">
        @foreach (['semua', 'bulanan', 'harian'] as $tabName)
            <button wire:click="$set('tab', '{{ $tabName }}')"
                class="px-4 py-2 rounded-md {{ $tab === $tabName ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700' }}">
                {{ ucfirst($tabName) }}
            </button>
        @endforeach
    </div>
    {{ $actions ?? '' }}
</div>

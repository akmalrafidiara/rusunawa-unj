@props(['wireModel', 'rows' => 3])

<div class="space-y-1">
    <textarea {{ $attributes->whereStartsWith('wire:model') }} rows="{{ $rows }}"
        class="w-full border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
</div>

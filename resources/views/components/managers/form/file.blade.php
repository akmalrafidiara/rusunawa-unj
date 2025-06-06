@props(['wireModel', 'label', 'help' => 'Unggah file dalam format JPG/PNG (max 2MB)'])

<div class="space-y-1">
    <input type="file" {{ $attributes->whereStartsWith('wire:model') }} accept="image/*"
        class="block w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-gray-400"
        aria-describedby="file_input_help">
    <p id="file_input_help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $help }}</p>
</div>

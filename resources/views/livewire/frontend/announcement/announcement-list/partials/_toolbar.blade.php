{{-- Filter and Search Section --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 md:space-x-4">
    <x-frontend.search wire:model.live="search" clearable placeholder="Cari Pengumuman..."
        icon="magnifying-glass" class="w-full shadow" />

    <div class="w-full md:w-1/3">
        <select wire:model.live="categoryFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-zinc-800 dark:text-gray-100 dark:border-white-500">
            <option value="">Semua Kategori</option>
            @foreach ($categoryOptions as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
            @endforeach
        </select>
    </div>
</div>
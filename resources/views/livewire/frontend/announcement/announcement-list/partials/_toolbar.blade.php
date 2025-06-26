{{-- Filter and Search Section --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 md:space-x-4">
    <div class="w-full md:w-1/2">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search announcements by title or description..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div class="w-full md:w-1/3">
        <select wire:model.live="categoryFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">All Categories</option>
            @foreach ($categoryOptions as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
            @endforeach
        </select>
    </div>
</div>
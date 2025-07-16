<x-managers.ui.card-side class="p-4 h-full flex flex-col">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Daftar Keluhan</h3>
    
    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 dark:border-zinc-700 mb-4">
        <button wire:click="$set('tab', 'aktif')"
                class="flex-1 px-4 py-2 text-sm font-medium transition-colors duration-200 ease-in-out text-center relative
                       {{ $tab === 'aktif' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white' }}">
            Aktif
            @if($tab === 'aktif')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-green-500"></div>
            @endif
        </button>
        <button wire:click="$set('tab', 'selesai')"
                class="flex-1 px-4 py-2 text-sm font-medium transition-colors duration-200 ease-in-out text-center relative
                       {{ $tab === 'selesai' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white' }}">
            Selesai
            @if($tab === 'selesai')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-green-500"></div>
            @endif
        </button>
    </div>

    @include('livewire.managers.oprations.reports-and-complaints.report-list.partials._toolbar')
    
    @include('livewire.managers.oprations.reports-and-complaints.report-list.partials._data-list')
</x-managers.ui.card-side>
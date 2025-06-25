<div class="flex flex-col sm:flex-row gap-4">
    {{-- Search Form --}}
    <x-managers.form.input wire:model.live="search" clearable placeholder="Cari Pertanyaan..."
        icon="magnifying-glass" class="w-full" />

    <div class="flex gap-4">
        {{-- Dropdown for Filters and Sorting --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal class="w-5 h-5 text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out" />
            </x-slot>
            @php
            // Options for sorting
            $orderByOptions = [
                // 'fullName' can be added if you want to sort by full name as well
                ['value' => 'created_at', 'label' => 'Tanggal Dikirim'],
            ];

            $sortOptions = [
                ['value' => 'desc', 'label' => 'Terbaru'],
                ['value' => 'asc', 'label' => 'Terlama'],
            ];
            @endphp

            {{-- Sort Filter --}}
            <x-managers.form.small>Urutkan</x-managers.form.small>
            <div class="flex gap-2 p-2 rounded-md bg-white">
                <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                    label="Urutkan Berdasarkan" wire:key="dropdown-order-by" /> {{-- 'disabled' attribute removed --}}

                <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Arah Urutan"
                    wire:key="dropdown-sort" /> {{-- 'disabled' attribute removed --}}
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>

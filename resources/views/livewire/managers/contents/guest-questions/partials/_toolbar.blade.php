<div class="flex flex-col sm:flex-row gap-4">
    {{-- Search Form --}}
    <x-managers.form.input wire:model.live="search" clearable placeholder="Cari Nama, Email, atau Pesan..."
        icon="magnifying-glass" class="w-full" />

    <div class="flex gap-4">
        {{-- Dropdown for Filters and Sorting --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal
                    class="w-5 h-5 text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out" />
            </x-slot>
            @php
                // Options for filtering by read status
                $readFilterOptions = [
                    ['value' => '', 'label' => 'Semua Status'],
                    ['value' => 'read', 'label' => 'Sudah Dibaca'],
                    ['value' => 'unread', 'label' => 'Belum Dibaca'],
                ];

                // Options for sorting (hanya Tanggal Dikirim)
                $orderByOptions = [
                    ['value' => 'created_at', 'label' => 'Tanggal Dikirim'],
                ];

                $sortOptions = [
                    ['value' => 'desc', 'label' => 'Terbaru'],
                    ['value' => 'asc', 'label' => 'Terlama'],
                ];
            @endphp

            {{-- Read Status Filter --}}
            <x-managers.form.small>Status Baca</x-managers.form.small>
            <div class="flex gap-2 p-2 rounded-md bg-white">
                <x-managers.ui.dropdown-picker wire:model.live="readFilter" :options="$readFilterOptions"
                    label="Semua Status" wire:key="dropdown-read-filter" disabled/>
            </div>

            {{-- Sort Filter --}}
            <x-managers.form.small>Urutkan</x-managers.form.small>
            <div class="flex gap-2 p-2 rounded-md bg-white">
                <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                    label="Urutkan Berdasarkan" wire:key="dropdown-order-by" disabled />

                <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Arah Urutan"
                    wire:key="dropdown-sort" disabled />
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>
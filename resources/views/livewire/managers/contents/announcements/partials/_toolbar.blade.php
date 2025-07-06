<div class="flex flex-col sm:flex-row gap-4">

    {{-- Search Form --}}
    <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pengumuman..."
        icon="magnifying-glass" class="w-full" />

    <div class="flex gap-4">
        {{-- Add Announcement Button --}}
        <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
            Tambah Pengumuman
        </x-managers.ui.button>

        {{-- Dropdown for Filters and Sorting --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal />
            </x-slot>
            @php
            $orderByOptions = [
            ['value' => 'title', 'label' => 'Judul'],
            ['value' => 'created_at', 'label' => 'Tanggal Dibuat'],
            ];

            $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
            @endphp

            <x-managers.form.small>Filter</x-managers.form.small>
            <div class="flex gap-2">
                {{-- Filter Status --}}
                <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions" label="Semua Status"
                    wire:key="dropdown-status" />

                {{-- Filter Kategori --}}
                <x-managers.ui.dropdown-picker wire:model.live="categoryFilter" :options="$categoryOptions" label="Semua Kategori"
                    wire:key="dropdown-category" />
            </div>

            {{-- Sort Filter --}}
            <x-managers.form.small>Urutkan</x-managers.form.small>
            <div class="flex gap-2">
                <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                    label="Urutkan Berdasarkan" wire:key="dropdown-order-by" disabled />

                <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                    wire:key="dropdown-sort" disabled />
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>
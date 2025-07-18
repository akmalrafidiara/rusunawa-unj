<div class="flex flex-col sm:flex-row gap-4">

    {{-- Search Form --}}
    <x-managers.form.input wire:model.live="search" clearable placeholder="Cari Nomor Invoice, Penyewa, atau Unit..."
        icon="magnifying-glass" class="w-full" />

    <div class="flex gap-4">
        {{-- Add Invoice Button --}}
        <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
            Tambah Tagihan
        </x-managers.ui.button>

        {{-- Dropdown for Filters --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal />
            </x-slot>
            @php
                $orderByOptions = [
                    ['value' => 'created_at', 'label' => 'Tanggal Dibuat'],
                    ['value' => 'due_at', 'label' => 'Tanggal Jatuh Tempo'],
                    ['value' => 'amount', 'label' => 'Jumlah'],
                ];

                $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
            @endphp

            <x-managers.form.small>Filter</x-managers.form.small>
            <div class="flex gap-2">
                <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions" label="Semua Status"
                    wire:key="dropdown-status" />
            </div>

            {{-- Sort Filter --}}
            <x-managers.form.small>Urutkan</x-managers.form.small>
            <div class="flex gap-2">
                <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions" label="Urutkan Berdasarkan"
                    wire:key="dropdown-order-by" />

                <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                    wire:key="dropdown-sort" />
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>

{{-- Panel --}}
<div class="flex justify-end items-center gap-2 w-full sm:w-auto">
    {{-- Per Page Input --}}
    <span>Baris</span>
    <div class="w-22">
        <x-managers.form.input wire:model.live="perPage" type="number" placeholder="10" />
    </div>

    {{-- Export Button --}}
    <span>Unduh</span>
    <x-managers.ui.button-export />
</div>

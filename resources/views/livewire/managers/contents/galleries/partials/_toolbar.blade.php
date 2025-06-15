<div class="flex flex-col sm:flex-row gap-4">
    {{-- Search Form --}}
    <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pertanyaan..." icon="magnifying-glass"
        class="w-full" />

    <div class="flex gap-4">
        {{-- Add Galleries Button --}}
        <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
            Tambah Foto Galeri
        </x-managers.ui.button>

        {{-- Dropdown for Filters and Sorting --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal />
            </x-slot>

            {{-- Sort Options --}}
            @php
                $sortOptions = [
                    ['value' => 'asc', 'label' => 'Menaik'],
                    ['value' => 'desc', 'label' => 'Menurun'],
                ];
                $orderByOptions = [
                    ['value' => 'created_at', 'label' => 'Tanggal Dibuat'],
                ];
            @endphp

            <x-managers.form.small>Urutkan</x-managers.form.small>
            <div class="flex gap-2">
                <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                    label="Urutkan Berdasarkan" wire:key="dropdown-orderBy" disabled />

                <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions"
                    label="Arah Urutan" wire:key="dropdown-sort" disabled />
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>
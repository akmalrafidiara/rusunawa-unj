    {{-- Toolbar --}}
    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4">
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pengguna..." icon="magnifying-glass"
            class="w-full" />

        <div class="flex gap-4">
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah Pengguna
            </x-managers.ui.button>

            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'name', 'label' => 'Nama'],
                        ['value' => 'email', 'label' => 'Email'],
                        ['value' => 'created_at', 'label' => 'Tanggal'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp
                <x-managers.form.small>Filter</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="roleFilter" :options="$roleOptions" label="Semua Role"
                        wire:key="dropdown-role" />

                    <x-managers.ui.dropdown-picker wire:model.live="perPage" :options="[10, 25, 50, 100]"
                        label="Jumlah per halaman" wire:key="dropdown-per-page" disabled />
                </div>

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

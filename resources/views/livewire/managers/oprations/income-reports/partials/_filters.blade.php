<!-- Toolbar -->
<div class="flex flex-col lg:flex-row gap-4">

    {{-- Time Period Filter --}}
    @php
        $filterTypeOptions = [
            ['value' => 'all_time', 'label' => 'Semua Waktu'],
            ['value' => 'daily', 'label' => 'Hari Ini'],
            ['value' => 'monthly', 'label' => 'Bulan Ini'],
            ['value' => 'yearly', 'label' => 'Tahun Ini'],
            ['value' => 'custom', 'label' => 'Kustom'],
        ];
    @endphp

    <x-managers.ui.dropdown-picker wire:model.live="filterType" :options="$filterTypeOptions" label="Periode Waktu"
        class="w-full lg:w-auto lg:min-w-48" />

    @if ($filterType === 'custom')
        <div class="flex flex-col sm:flex-row gap-4 lg:gap-2">
            <x-managers.form.input type="date" wire:model.live="startDate" placeholder="Dari Tanggal"
                class="w-full sm:flex-1" />
            <x-managers.form.input type="date" wire:model.live="endDate" placeholder="Sampai Tanggal"
                class="w-full sm:flex-1" />
        </div>
    @endif

    <div class="flex justify-end lg:ml-auto">
        {{-- Dropdown for Advanced Filters --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal />
            </x-slot>

            <x-managers.form.small>Filter Berdasarkan</x-managers.form.small>
            <div class="flex flex-col sm:flex-row gap-2">
                <x-managers.ui.dropdown-picker wire:model.live="occupantFilter" :options="$occupantOptions" label="Semua Penghuni"
                    wire:key="dropdown-occupant" class="w-full sm:w-auto" />

                <x-managers.ui.dropdown-picker wire:model.live="contractFilter" :options="$contractOptions" label="Semua Kontrak"
                    wire:key="dropdown-contract" class="w-full sm:w-auto" />
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                <x-managers.ui.button wire:click="resetFilters" variant="secondary" class="w-full">
                    Reset Filter
                </x-managers.ui.button>
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>

{{-- Toolbar for searching and filtering --}}
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <x-managers.form.input wire:model.live.debounce.300ms="search" clearable placeholder="Cari unit atau gedung..." icon="magnifying-glass" class="w-full" />

    <div class="flex gap-4">
        {{-- Dropdown for Filters and Sorting --}}
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal />
            </x-slot>

            {{-- Filter Jadwal --}}
            <x-managers.form.small>Filter Jadwal</x-managers.form.small>
            <x-managers.ui.dropdown-picker wire:model.live="filterScheduleStatus" :options="$scheduleStatusOptions" label="Semua Status Jadwal" />
        </x-managers.ui.dropdown>
    </div>
</div>
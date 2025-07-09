<div class="lg:col-span-1">
    {{-- Toolbar for searching and filtering (diluar card box) --}}
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

    {{-- Kartu untuk daftar jadwal --}}
    <x-managers.ui.card class="p-4 h-full flex flex-col">
        @include('livewire.managers.oprations.maintenance.maintenance-schedules.partials._sidebar-schedule-list')
    </x-managers.ui.card>

    @include('livewire.managers.oprations.maintenance.maintenance-schedules.partials._modal-form-schedule')
</div>
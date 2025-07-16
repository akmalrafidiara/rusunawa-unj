{{-- Toolbar untuk pencarian dan filter --}}
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <x-managers.form.input wire:model.live.debounce.300ms="search" clearable placeholder="Cari ID, subjek, atau kamar..." icon="magnifying-glass" class="w-full" />
    <div class="flex gap-4">
        <x-managers.ui.dropdown class="flex flex-col gap-2">
            <x-slot name="trigger">
                <flux:icon.adjustments-horizontal />
            </x-slot>
            <x-managers.form.small>Filter Status</x-managers.form.small>
            <div class="flex gap-2">
                <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions" label="Semua Status" />
            </div>
        </x-managers.ui.dropdown>
    </div>
</div>
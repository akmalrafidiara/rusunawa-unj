<x-managers.ui.card-side class="p-4 h-full flex flex-col">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Kamar</h4>
        @if (!$is_admin_user)
        <x-managers.ui.button wire:click="createSchedule" variant="primary" size="sm">
            <flux:icon.plus class="w-4 h-4 mr-1" />
            Tambah Jadwal
        </x-managers.ui.button>
        @endif
    </div>

    @include('livewire.managers.oprations.maintenance.maintenance-schedules.partials._toolbar')

    @include('livewire.managers.oprations.maintenance.maintenance-schedules.partials._sidebar-schedule-list')

    @include('livewire.managers.oprations.maintenance.maintenance-schedules.partials._modal-form-schedule')
</x-managers.ui.card-side>
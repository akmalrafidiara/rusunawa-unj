<div class="lg:col-span-2 flex flex-col gap-6">
    @if ($selectedSchedule)
        @include('livewire.managers.oprations.maintenance.maintenance-records.partials._schedule-details-content')
        @include('livewire.managers.oprations.maintenance.maintenance-records.partials._record-history-content')
    @else
        <x-managers.ui.card class="p-4 lg:col-span-2 text-center text-gray-500 dark:text-gray-400">
            <p>Pilih kamar dari daftar di samping untuk melihat detail jadwal dan riwayat pemeliharaan.</p>
        </x-managers.ui.card>
    @endif

    @include('livewire.managers.oprations.maintenance.maintenance-records.partials._modal-form-record')
    @include('livewire.managers.oprations.maintenance.maintenance-records.partials._modal-detail')
</div>
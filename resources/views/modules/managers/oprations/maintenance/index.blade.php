<x-layouts.managers :title="__('Maintenance')">
    <div class="flex flex-col gap-6">
        <x-managers.ui.page-title title="Maintenance / Pemeliharaan"
            subtitle="Kelola Maintenance atau Pemeliharaan yang dilakukan pada unit hunian" />

        {{-- Main Layout: Daftar Kamar (Left - MaintenanceSchedules), Detail Schedule & History (Right - MaintenanceRecords) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT COLUMN: Daftar Kamar --}}
            <livewire:managers.maintenance.maintenance-schedules />

            {{-- RIGHT COLUMN: Schedule Details & History --}}
            <livewire:managers.maintenance.maintenance-records />
        </div>
    </div>
</x-layouts.managers>
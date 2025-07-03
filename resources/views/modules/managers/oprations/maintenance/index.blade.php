<x-layouts.managers :title="__('Maintenance')">
    <div class="flex flex-col gap-6">
        <x-managers.ui.page-title title="Maintenance / Pemeliharaan"
            subtitle="Kelola Maintenance atau Pemeliharaan yang dilakukan pada unit hunian" />

        {{-- Livewire Maintenance Component --}}
        <livewire:managers.maintenance />
    </div>
</x-layouts.managers>
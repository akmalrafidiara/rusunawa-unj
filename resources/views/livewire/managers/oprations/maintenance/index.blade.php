<div>
    {{-- Toolbar for searching and filtering --}}
    @include('livewire.managers.oprations.maintenance.partials._toolbar')

    {{-- Main Layout: Daftar Kamar (Left), Detail Schedule & History (Right) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: Daftar Kamar (Navigation for Schedules) --}}
        @include('livewire.managers.oprations.maintenance.partials._sidebar-schedule-list')

        {{-- RIGHT COLUMN: Schedule Details & History --}}
        @include('livewire.managers.oprations.maintenance.partials._main-content')
    </div>

    {{-- Modals --}}
    @include('livewire.managers.oprations.maintenance.partials._modal-form-schedule')
    @include('livewire.managers.oprations.maintenance.partials._modal-form-record')
    @include('livewire.managers.oprations.maintenance.partials._modal-detail')
</div>
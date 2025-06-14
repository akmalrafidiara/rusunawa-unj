<div class="flex flex-col gap-6">
    {{-- Toolbar --}}
    @include('livewire.managers.contents.announcements.partials._toolbar')

    {{-- Data Table --}}
    @include('livewire.managers.contents.announcements.partials._data-table')

    {{-- Modal Form --}}
    @include('livewire.managers.contents.announcements.partials._modal-form')

    {{-- Modal Detail --}}
    @include('livewire.managers.contents.announcements.partials._modal-detail')
</div>
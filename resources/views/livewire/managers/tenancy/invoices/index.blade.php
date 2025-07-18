<div class="flex flex-col gap-6">
    {{-- Toolbar --}}
    @include('livewire.managers.tenancy.invoices.partials._toolbar')

    {{-- Data Table --}}
    @include('livewire.managers.tenancy.invoices.partials._data-table')

    {{-- Modal Form --}}
    @include('livewire.managers.tenancy.invoices.partials._modal-form')

    {{-- Modal Detail --}}
    @include('livewire.managers.tenancy.invoices.partials._modal-detail')
</div>

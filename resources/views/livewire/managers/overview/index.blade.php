<div class="space-y-6">
    @include('livewire.managers.overview.partials.header')

    @include('livewire.managers.overview.partials.key-stats')

    @include('livewire.managers.overview.partials.secondary-stats')

    @include('livewire.managers.overview.partials.charts')

    @include('livewire.managers.overview.partials.announcements')

    @include('livewire.managers.overview.partials.quick-actions')
</div>

@push('scripts')
    @include('livewire.managers.overview.partials.scripts')
@endpush

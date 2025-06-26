<div class="container mx-auto px-6 lg:px-12 mb-8 relative overflow-hidden">
    {{-- Filter and Search Section --}}
    @include('livewire.frontend.announcement.announcement-list.partials._toolbar')

    {{-- Announcements Grid --}}
    @include('livewire.frontend.announcement.announcement-list.partials._data-list')
</div>
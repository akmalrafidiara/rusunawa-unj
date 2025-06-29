<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-12">
        {{-- Main Announcement Content --}}
        @include('livewire.frontend.announcement.announcement-detail.partials._main-content', ['announcement' => $announcement])

        {{-- Related Announcements Sidebar --}}
        @include('livewire.frontend.announcement.announcement-detail.partials._sidebar-announcement', ['relatedAnnouncements' => $relatedAnnouncements])
    </div>
</div>
<x-layouts.managers :title="__('Pengumuman')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Pengumuman"
            subtitle="Kelola data referensi pengumuman untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Announcements --}}
        <livewire:managers.announcement />
    </div>
</x-layouts.managers>
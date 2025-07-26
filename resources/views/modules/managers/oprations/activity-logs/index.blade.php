<x-layouts.managers :title="__('Activity Logs')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Aktifitas Pengguna" subtitle="Catatan aktivitas pengguna sistem Rusunawa UNJ" />

        {{-- Dynamic Content - User Management --}}
        <livewire:managers.activity-log />
    </div>
</x-layouts.managers>

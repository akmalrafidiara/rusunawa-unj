<x-layouts.managers :title="__('User Management')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Manajemen Pengguna" subtitle="Kelola data pengguna sistem Rusunawa UNJ" />

        {{-- Dynamic Content - User Management --}}
        <livewire:managers.users />
    </div>
</x-layouts.managers>

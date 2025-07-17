<x-layouts.managers :title="__('Occupants')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Penghuni" subtitle="Kelola data Penghuni" />

        {{-- Dynamic Content - Penghuni --}}
        <livewire:managers.occupant />
    </div>
</x-layouts.managers>

<x-layouts.managers :title="__('Contracts')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Kontrak" subtitle="Kelola data Kontrak" />

        {{-- Dynamic Content - Kontrak --}}
        <livewire:managers.contract />
    </div>
</x-layouts.managers>

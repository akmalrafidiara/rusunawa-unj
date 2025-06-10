<x-layouts.managers :title="__('Unit')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Unit" subtitle="Kelola data Unit" />

        {{-- Dynamic Content - Unit --}}
        <livewire:managers.unit />
    </div>
</x-layouts.managers>

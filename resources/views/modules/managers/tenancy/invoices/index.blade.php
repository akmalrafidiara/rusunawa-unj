<x-layouts.managers :title="__('Invoices')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Tagihan" subtitle="Kelola data Tagihan" />

        {{-- Dynamic Content - Tagihan --}}
        <livewire:managers.invoice />
    </div>
</x-layouts.managers>

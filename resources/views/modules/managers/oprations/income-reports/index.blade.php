<x-layouts.managers :title="__('Income Reports')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Laporan Pemasukan"
            subtitle="Kelola data laporan pemasukan sistem Rusunawa UNJ" />

        {{-- Dynamic Content - Income Reports --}}
        <livewire:managers.IncomeReport />
    </div>
</x-layouts.managers>

<x-layouts.managers :title="__('Laporan & Keluhan')">
    <div class="flex flex-col gap-6">
        <x-managers.ui.page-title title="Laporan & Keluhan"
            subtitle="Kelola Laporan dan Keluhan yang dibuat oleh penghuni" />

        {{-- Main Layout: Report List (Left) and Report Details (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-start">
            {{-- KOLOM KIRI: Daftar Laporan --}}
            <livewire:managers.reports-and-complaints.report-list />

            {{-- KOLOM KANAN: Detail Laporan --}}
            <livewire:managers.reports-and-complaints.report-details />
        </div>
    </div>
</x-layouts.managers>
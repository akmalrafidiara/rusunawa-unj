<!-- Filters Section -->
<div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Time Period Filter -->
        @php
            $filterTypeOptions = [
                ['value' => 'all_time', 'label' => 'Semua Waktu'],
                ['value' => 'daily', 'label' => 'Hari Ini'],
                ['value' => 'monthly', 'label' => 'Bulan Ini'],
                ['value' => 'yearly', 'label' => 'Tahun Ini'],
                ['value' => 'custom', 'label' => 'Kustom'],
            ];
        @endphp

        <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Periode Waktu</label>
                <x-managers.ui.dropdown-picker wire:model.live="filterType" :options="$filterTypeOptions" label="Pilih Periode"
                    class="w-full sm:min-w-48" />
            </div>

            @if ($filterType === 'custom')
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-auto">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Dari
                            Tanggal</label>
                        <x-managers.form.input type="date" wire:model.live="startDate" class="w-full" />
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Sampai
                            Tanggal</label>
                        <x-managers.form.input type="date" wire:model.live="endDate" class="w-full" />
                    </div>
                </div>
            @endif
        </div>

        <!-- Advanced Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Penghuni</label>
                <x-managers.ui.dropdown-picker wire:model.live="occupantFilter" :options="$occupantOptions" label="Semua Penghuni"
                    class="w-full sm:min-w-48" />
            </div>

            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Kontrak</label>
                <x-managers.ui.dropdown-picker wire:model.live="contractFilter" :options="$contractOptions" label="Semua Kontrak"
                    class="w-full sm:min-w-48" />
            </div>

            <div class="flex items-end">
                <x-managers.ui.button wire:click="resetFilters" variant="secondary" size="sm">
                    <flux:icon.arrow-path class="w-4 h-4 mr-1" />
                    Reset Filter
                </x-managers.ui.button>
            </div>
        </div>
    </div>
</div>

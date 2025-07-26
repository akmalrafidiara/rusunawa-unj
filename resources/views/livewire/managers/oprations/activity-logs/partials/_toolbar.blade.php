    {{-- Toolbar --}}
    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4 justify-end">
        {{-- No "Tambah Pengguna" button for activity logs --}}
        @php
            $orderByOptions = [
                ['value' => 'created_at', 'label' => 'Tanggal'],
                ['value' => 'event', 'label' => 'Event'],
                ['value' => 'causer', 'label' => 'Pengguna'],
            ];

            $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];

            $loggableTypeOptions = [
                ['value' => 'App\\Models\\User', 'label' => 'User'],
                ['value' => 'App\\Models\\Contract', 'label' => 'Contract'],
            ];
        @endphp
        <x-managers.form.small>Filter</x-managers.form.small>
        <div class="flex gap-2">
            <x-managers.ui.dropdown-picker wire:model.live="perPage" :options="[10, 25, 50, 100]" label="Jumlah per halaman"
                wire:key="dropdown-per-page" />
        </div>

        <div class="flex gap-2">
            <x-managers.ui.dropdown-picker wire:model.live="loggable_type" :options="$loggableTypeOptions" label="Semua Tipe"
                wire:key="dropdown-loggable-type" />
            <x-managers.form.input wire:model.live="loggable_id" placeholder="Loggable ID" class="w-32" />
        </div>

        <x-managers.form.small>Urutkan</x-managers.form.small>
        <div class="flex gap-2">
            <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions" label="Urutkan Berdasarkan"
                wire:key="dropdown-order-by" />

            <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                wire:key="dropdown-sort" />
        </div>
    </div>

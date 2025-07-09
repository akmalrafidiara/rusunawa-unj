{{-- Isi dari _schedule-details-content.blade.php --}}
<x-managers.ui.card class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Jadwal Maintenance</h4>
        <div class="flex gap-2">
            @if (!$is_admin_user)
            {{-- Memanggil metode di komponen MaintenanceRecords ini --}}
            <x-managers.ui.button wire:click="editSelectedSchedule({{ $selectedSchedule->id }})" variant="secondary" size="sm">
                Edit Jadwal
            </x-managers.ui.button>
            @endif
            @if (!$is_admin_user)
            {{-- Memanggil metode di komponen MaintenanceRecords ini --}}
            <x-managers.ui.button wire:click="confirmDeleteScheduleOnParent({{ $selectedSchedule->id }})" variant="danger" size="sm">
                Hapus Jadwal
            </x-managers.ui.button>
            @endif
        </div>
    </div>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Informasi jadwal pemeliharaan rutin</p>

    <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4 bg-gray-50 dark:bg-zinc-700">
        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kamar</p>
                <p class="font-medium text-gray-900 dark:text-white">Kamar {{ $selectedSchedule->unit->room_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Gedung</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ optional($selectedSchedule->unit->unitCluster)->name ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Status Ketersediaan Unit</p>
                @if ($selectedSchedule->unit->status)
                <x-managers.ui.badge :color="$selectedSchedule->unit->status->color()">
                    {{ $selectedSchedule->unit->status->label() }}
                </x-managers.ui.badge>
                @else
                -
                @endif
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">Frekuensi Maintenance</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $selectedSchedule->frequency_months }} Bulan Sekali</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">Pemeliharaan Selanjutnya</p>
                <div class="flex items-center gap-2">
                    <p class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selectedSchedule?->next_due_date)?->format('d F Y') }}</p>
                    <x-managers.ui.badge :color="$selectedSchedule->status->color()">
                        {{ $selectedSchedule->status->label() }}
                    </x-managers.ui.badge>
                </div>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir Selesai</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    @if($selectedSchedule?->last_completed_at)
                    {{ \Carbon\Carbon::parse($selectedSchedule->last_completed_at)->format('d F Y') }}
                    @else
                    -
                    @endif
                </p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">Catatan Jadwal</p>
                <p class="text-sm text-gray-800 dark:text-gray-200">{{ $selectedSchedule->notes ?: '-' }}</p>
            </div>
        </div>
    </div>
</x-managers.ui.card>

@if (!$is_admin_user)
<x-managers.ui.card class="p-4">
    <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Update Maintenance</h4>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Catat pemeliharaan secara berkala atau tambahkan riwayat darurat.</p>
    <div class="flex gap-2">
        <x-managers.ui.button wire:click="createRecord({{ $selectedSchedule->id }})" variant="primary">
            Tambah Riwayat Rutin
        </x-managers.ui.button>
        <x-managers.ui.button wire:click="createRecord(null, {{ optional($selectedSchedule)->unit_id }})" variant="secondary">
            Tambah Riwayat Darurat
        </x-managers.ui.button>
    </div>
</x-managers.ui.card>
@endif
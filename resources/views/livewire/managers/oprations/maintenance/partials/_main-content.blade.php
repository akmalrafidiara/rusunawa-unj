{{-- RIGHT COLUMN: Schedule Details & History --}}
<div class="lg:col-span-2 flex flex-col gap-6">
    @if ($currentScheduleId)
    @php
    //
    @endphp

    @if ($selectedSchedule)
    {{-- Section: Jadwal Maintenance (Detail of Selected Schedule) --}}
    <x-managers.ui.card class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white">Jadwal Maintenance</h4>
            <div class="flex gap-2">
                <x-managers.ui.button wire:click="editSchedule({{ $selectedSchedule->id }})" variant="secondary" size="sm">
                    Edit Jadwal
                </x-managers.ui.button>
                <x-managers.ui.button wire:click="confirmDeleteSchedule({{ $selectedSchedule->id }})" variant="danger" size="sm">
                    Hapus Jadwal
                </x-managers.ui.button>
            </div>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Informasi jadwal pemeliharaan rutin</p>

        <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4 bg-gray-50 dark:bg-zinc-700">
            <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kamar</p>
                    <p class="font-medium text-gray-900 dark:text-white text-lg">Kamar {{ $selectedSchedule->unit->room_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gedung</p>
                    <p class="font-medium text-gray-900 dark:text-white text-lg">{{ optional($selectedSchedule->unit->unitCluster)->name ?: '-' }}</p>
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
                    <p class="font-medium text-gray-900 dark:text-white text-lg">{{ $selectedSchedule->frequency_months }} Bulan Sekali</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pemeliharaan Selanjutnya</p>
                    <div class="flex items-center gap-2">
                        <p class="font-medium text-gray-900 dark:text-white text-lg">{{ \Carbon\Carbon::parse($selectedSchedule?->next_due_date)?->format('d F Y') }}</p>
                        <x-managers.ui.badge :color="$selectedSchedule->status->color()">
                            {{ $selectedSchedule->status->label() }}
                        </x-managers.ui.badge>
                    </div>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir Selesai</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        @if($relatedRecords->where('type', \App\Enums\MaintenanceRecordType::ROUTINE->value)->where('status', \App\Enums\MaintenanceRecordStatus::COMPLETED_LATE OR COMPLETED_ON_TIME OR COMPLETED_EARLY ->value)->isNotEmpty())
                        {{ \Carbon\Carbon::parse($selectedSchedule?->last_completed_at)?->format('d F Y') }}
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

    {{-- Section: Update Maintenance (Add New Record for current schedule) --}}
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

    {{-- Section: Riwayat Maintenance (History of Records for selected Unit/Schedule) --}}
    <x-managers.ui.card class="p-4">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Riwayat Maintenance</h4>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Riwayat pemeliharaan rutin dan darurat untuk kamar ini.</p>

        <div class="space-y-4">
            @forelse ($relatedRecords as $record)
            <div wire:key="record-history-{{ $record->id }}" class="p-4 border rounded-lg
                            {{ $record->type->value === \App\Enums\MaintenanceRecordType::URGENT->value ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-700' : 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' }}
                        ">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-900 dark:text-white">
                        Perawatan {{ $record->type->label() }}
                    </span>
                    <x-managers.ui.badge :color="$record->status->color()">
                        {{ $record->status->label() }}
                    </x-managers.ui.badge>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $record->completion_date ? \Carbon\Carbon::parse($record->completion_date)->format('d F Y') : \Carbon\Carbon::parse($record->scheduled_date)->format('d F Y') }}
                </p>
                <p class="text-sm text-gray-800 dark:text-gray-200 mt-2">Deskripsi Perawatan: {{ $record->notes ?: '-' }}</p>

                @if($record->attachments->isNotEmpty())
                <div class="mt-3">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bukti:</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ($record->attachments as $attachment)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($attachment->path) }}" target="_blank" class="block rounded-md overflow-hidden">
                            @if (\Illuminate\Support\Str::startsWith($attachment->mime_type, 'image/'))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($attachment->path) }}" alt="{{ $attachment->name }}" class="w-full h-16 object-cover rounded-md">
                            @else
                            <div class="w-full h-16 flex items-center justify-center bg-gray-100 rounded-md text-gray-500 text-xs p-1">
                                <flux:icon.document class="w-4 h-4 mr-1" /> {{ \Illuminate\Support\Str::limit($attachment->name, 10) }}
                            </div>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="flex justify-end gap-2 mt-4">
                    {{-- HANYA BISA LIHAT DETAIL --}}
                    <x-managers.ui.button wire:click="detailRecord({{ $record->id }})" variant="info" size="sm" title="Lihat Detail">
                        <flux:icon.eye class="w-4" />
                    </x-managers.ui.button>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">Tidak ada riwayat pemeliharaan untuk kamar ini.</p>
            @endforelse
        </div>
    </x-managers.ui.card>
    @else
    <x-managers.ui.card class="p-4 lg:col-span-2 text-center text-gray-500 dark:text-gray-400">
        <p>Tidak ada jadwal yang dipilih atau ditemukan. Silakan pilih dari daftar kamar di samping.</p>
    </x-managers.ui.card>
    @endif
    @else
    <x-managers.ui.card class="p-4 lg:col-span-2 text-center text-gray-500 dark:text-gray-400">
        <p>Pilih kamar dari daftar di samping untuk melihat detail jadwal dan riwayat pemeliharaan.</p>
    </x-managers.ui.card>
    @endif
</div>
{{-- Record Detail Modal --}}
@if ($currentRecordIdForDetail)
    <x-managers.ui.modal title="Detail Rekaman Pemeliharaan" :show="$showRecordDetailModal" class="max-w-lg">
        <div class="space-y-4">
            <p><strong>Unit:</strong> Kamar {{ \App\Models\Unit::find($recordUnitId)->room_number ?? 'N/A' }}</p>
            <p><strong>Tipe:</strong>
                <x-managers.ui.badge type="{{ $recordType === 'routine' ? 'info' : 'danger' }}">
                    {{ \App\Enums\MaintenanceRecordType::from($recordType)->label() }}
                </x-managers.ui.badge>
            </p>
            @if ($recordMaintenanceScheduleId)
                <p><strong>Terhubung ke Jadwal:</strong> Jadwal Rutin #{{ $recordMaintenanceScheduleId }}</p>
            @endif
            <p><strong>Tanggal Terjadwal:</strong> {{ \Carbon\Carbon::parse($recordScheduledDate)?->format('d F Y') }}</p>
            <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($recordCompletionDate)?->format('d F Y') ?: '-' }}</p>
            <p><strong>Status:</strong>
                <x-managers.ui.badge :color="\App\Enums\MaintenanceRecordStatus::from($recordStatus)->color()">
                    {{ \App\Enums\MaintenanceRecordStatus::from($recordStatus)->label() }}
                </x-managers.ui.badge>
            </p>
            @if ($recordType === \App\Enums\MaintenanceRecordType::ROUTINE->value)
                <p><strong>Terlambat:</strong>
                    @if($isLate)
                        <flux:icon.x-circle class="w-5 h-5 text-red-500 inline-block" /> Ya
                    @else
                        <flux:icon.check-circle class="w-5 h-5 text-green-500 inline-block" /> Tidak
                    @endif
                </p>
            @endif
            <p><strong>Catatan:</strong> {{ $recordNotes ?: '-' }}</p>
            @if (!empty($existingRecordAttachments))
                <div>
                    <strong>Lampiran:</strong>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-2">
                        @foreach ($existingRecordAttachments as $attachment)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($attachment['path']) }}" target="_blank" class="block">
                                @if (\Illuminate\Support\Str::startsWith($attachment['mime_type'], 'image'))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($attachment['path']) }}" class="w-full h-24 object-cover rounded-lg shadow-sm" alt="{{ $attachment['name'] }}">
                                @else
                                    <div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded-lg shadow-sm text-gray-500 dark:bg-zinc-700 dark:text-gray-400">
                                        <flux:icon.document class="w-8 h-8" />
                                    </div>
                                @endif
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ $attachment['name'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showRecordDetailModal', false)">Tutup</x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
@endif
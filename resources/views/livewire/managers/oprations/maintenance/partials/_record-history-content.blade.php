{{-- Section: Riwayat Maintenance (History of Records for selected Unit/Schedule) --}}
@if ($currentScheduleId && $selectedSchedule)
<x-managers.ui.card class="p-4">
    <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Riwayat Maintenance</h4>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Riwayat pemeliharaan rutin dan darurat untuk kamar ini.</p>

    <div class="space-y-4">
        @forelse ($relatedRecordsPaginator as $record)
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
                @if (!$is_admin_user) {{-- Hide Edit Record button for admin --}}
                <x-managers.ui.button wire:click="editRecord({{ $record->id }})" variant="secondary" size="sm" title="Edit">
                    <flux:icon.pencil class="w-4" />
                </x-managers.ui.button>
                @endif
                @if (!$is_admin_user) {{-- Hide Delete Record button for admin --}}
                <x-managers.ui.button wire:click="confirmDeleteRecord({{ $record->id }})" variant="danger" size="sm" title="Hapus">
                    <flux:icon.trash class="w-4" />
                </x-managers.ui.button>
                @endif
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 dark:text-gray-400 py-4">Tidak ada riwayat pemeliharaan untuk kamar ini.</p>
        @endforelse
    </div>

    {{-- Add pagination here for relatedRecords --}}
    @if ($relatedRecordsPaginator->hasPages())
        <x-managers.ui.pagination :paginator="$relatedRecordsPaginator" class="mt-4" />
    @endif
</x-managers.ui.card>
@endif
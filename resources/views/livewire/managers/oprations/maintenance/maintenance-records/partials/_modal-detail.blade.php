{{-- Isi dari _modal-detail.blade.php --}}
<x-managers.ui.modal title="Detail Kegiatan Pemeliharaan" :show="$showRecordDetailModal" class="max-w-lg">
    <div class="space-y-6">
        <div
            class="flex items-center gap-4 p-4 bg-gradient-to-r {{ $recordType === \App\Enums\MaintenanceRecordType::URGENT->value ? 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-100 dark:border-red-800' : 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-100 dark:border-green-800' }} rounded-lg border">
            <div
                class="p-3 rounded-full {{ $recordType === \App\Enums\MaintenanceRecordType::URGENT->value ? 'bg-red-500 dark:bg-red-600' : 'bg-green-500 dark:bg-green-600' }}">
                @if ($recordType === \App\Enums\MaintenanceRecordType::URGENT->value)
                    <flux:icon.exclamation-triangle class="w-6 h-6 text-white" />
                @else
                    <flux:icon.wrench-screwdriver class="w-6 h-6 text-white" />
                @endif
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                    Kegiatan Pemeliharaan {{ \App\Enums\MaintenanceRecordType::from($recordType)->label() }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">Detail informasi kegiatan pemeliharaan</p>
            </div>
        </div>

        <x-managers.ui.card class="p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon.information-circle class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                Informasi Utama
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-700">
                    <span class="text-gray-600 dark:text-gray-300">Unit Kamar</span>
                    <span class="font-semibold text-gray-900 dark:text-white text-lg">
                        Kamar {{ \App\Models\Unit::find($recordUnitId)->room_number ?? 'N/A' }} -
                        {{ \App\Models\Unit::find($recordUnitId)->unitCluster?->name ?? 'Tidak Diketahui' }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-700">
                    <span class="text-gray-600 dark:text-gray-300">Tipe Pemeliharaan</span>
                    <x-managers.ui.badge :color="\App\Enums\MaintenanceRecordType::from($recordType)->value === 'routine' ? ['bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400'] : ['bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400']">
                        {{ \App\Enums\MaintenanceRecordType::from($recordType)->label() }}
                    </x-managers.ui.badge>
                </div>
            </div>
        </x-managers.ui.card>

        <x-managers.ui.card class="p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon.calendar class="w-5 h-5 text-purple-500 dark:text-purple-400" />
                Informasi Tanggal & Status
            </h4>
            <div class="space-y-3">
                @if ($recordType === \App\Enums\MaintenanceRecordType::ROUTINE->value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-700">
                        <span class="text-gray-600 dark:text-gray-300">Tanggal Terjadwal</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($recordScheduledDate)?->format('d F Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-700">
                        <span class="text-gray-600 dark:text-gray-300">Tanggal Selesai</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($recordCompletionDate)?->format('d F Y') ?: '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-300">Status</span>
                        {{-- Memperbaiki error enum: menggunakan tryFrom untuk fallback --}}
                        @php
                            $recordStatusEnum = \App\Enums\MaintenanceRecordStatus::tryFrom($recordStatus);
                            $statusColor = $recordStatusEnum
                                ? $recordStatusEnum->color()
                                : ['bg-gray-100', 'text-gray-800', 'dark:bg-zinc-700/30', 'dark:text-gray-400'];
                            $statusLabel = $recordStatusEnum ? $recordStatusEnum->label() : 'Tidak Diketahui';
                        @endphp
                        <x-managers.ui.badge :color="$statusColor">
                            {{ $statusLabel }}
                        </x-managers.ui.badge>
                    </div>
                @else
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-700">
                        <span class="text-gray-600 dark:text-gray-300">Tanggal Dilakukan Pemeliharaan</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($recordCompletionDate ?: $recordScheduledDate)?->format('d F Y') }}
                        </span>
                    </div>
                @endif
            </div>
        </x-managers.ui.card>

        <x-managers.ui.card class="p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon.document-text class="w-5 h-5 text-orange-500 dark:text-orange-400" />
                Catatan
            </h4>
            <p class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ $recordNotes ?: '-' }}</p>
        </x-managers.ui.card>

        @if (!empty($existingRecordAttachments))
            <x-managers.ui.card class="p-4">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon.paper-clip class="w-5 h-5 text-teal-500 dark:text-teal-400" />
                    Bukti Pemeliharaan (Lampiran)
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($existingRecordAttachments as $attachment)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($attachment['path']) }}" target="_blank"
                            class="block">
                            @if (\Illuminate\Support\Str::startsWith($attachment['mime_type'], 'image'))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($attachment['path']) }}"
                                    class="w-full h-24 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700"
                                    alt="{{ $attachment['name'] }}">
                            @else
                                <div
                                    class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded-lg shadow-sm text-gray-500 dark:bg-zinc-700 dark:text-gray-400 border border-gray-200 dark:border-zinc-700">
                                    <flux:icon.document class="w-8 h-8 mb-1" />
                                    <p class="text-xs text-center px-1 truncate w-full">{{ $attachment['name'] }}</p>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </x-managers.ui.card>
        @endif
    </div>
    <div class="flex justify-end gap-2 mt-10">
        <x-managers.ui.button type="button" variant="secondary"
            wire:click="$set('showRecordDetailModal', false)">Tutup</x-managers.ui.button>
    </div>
</x-managers.ui.modal>

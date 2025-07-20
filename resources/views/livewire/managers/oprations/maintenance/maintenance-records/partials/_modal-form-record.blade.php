{{-- Isi dari _modal-form-record.blade.php --}}
<x-managers.ui.modal title="{{ $modalType === 'create_record' ? 'Catat Pemeliharaan' : 'Edit Rekaman Pemeliharaan' }}" :show="$showRecordModal" class="max-w-md">
    <form wire:submit.prevent="saveRecord" class="space-y-4">
        <x-managers.form.label for="recordUnitId_display">Unit (Kamar)</x-managers.form.label>
        @php
            $selectedUnitLabel = collect($allAcUnitOptions)->firstWhere('value', $recordUnitId)['label'] ?? 'N/A';
        @endphp
        <x-managers.form.input-disable-data type="text" value="{{ $selectedUnitLabel }}" id="recordUnitId_display" disabled="true" />
        <input type="hidden" wire:model="recordUnitId">

        <x-managers.form.label for="recordType_display">Tipe Pemeliharaan</x-managers.form.label>
        <x-managers.form.input-disable-data value="{{ \App\Enums\MaintenanceRecordType::from($recordType)->label() }}" type="text" id="recordType_display" disabled="true" />
        <input type="hidden" wire:model="recordType">

        @if ($recordType === \App\Enums\MaintenanceRecordType::ROUTINE->value)
            <x-managers.form.label for="recordMaintenanceScheduleId_display">Jadwal Rutin Terkait</x-managers.form.label>
            @php
                $relatedScheduleLabel = \App\Models\MaintenanceSchedule::find($recordMaintenanceScheduleId)?->next_due_date?->format('d M Y') ?? 'N/A';
            @endphp
            <x-managers.form.input-disable-data type="text" value="Jadwal: {{ $relatedScheduleLabel }}" id="recordMaintenanceScheduleId_display" disabled="true" />
            <input type="hidden" wire:model="recordMaintenanceScheduleId">
        @endif

        <input type="hidden" wire:model="recordScheduledDate">

        <x-managers.form.label for="recordCompletionDate">Tanggal Penyelesaian Dilakukan <span class="text-red-500">*</span></x-managers.form.label>
        <x-managers.form.input-disable-data wire:model.live="recordCompletionDate" type="date" id="recordCompletionDate" />

        <x-managers.form.label for="recordNotes">Catatan Pemeliharaan</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="recordNotes" placeholder="Detail pekerjaan pemeliharaan" rows="3" id="recordNotes" />
        
        <x-managers.form.label>Lampiran (Foto/Dokumen)</x-managers.form.label>

        <div wire:key="filepond-record-attachments-wrapper">
            <x-filepond::upload wire:model.live="recordAttachments" multiple max-file-size="2MB" />
        </div>
        <div class="mt-2">
            @error('recordAttachments.*')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @else
            <x-managers.form.small>Max 2MB per file. Bisa upload banyak gambar atau file (PDF, dll).</x-managers.form.small>
            @enderror
        </div>

        @if ($existingRecordAttachments && count($existingRecordAttachments) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-300 rounded p-2 mt-4">
            <x-managers.form.small class="col-span-full">Lampiran Saat Ini</x-managers.form.small>
            @foreach ($existingRecordAttachments as $attachment)
            <div class="relative" wire:key="existing-attachment-{{ $attachment['id'] }}">
                @if (Str::startsWith($attachment['mime_type'], 'image/'))
                <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}"
                    class="w-full h-full max-w-full max-h-full object-cover rounded border" />
                @else
                <div class="w-full h-full max-w-full max-h-full flex items-center justify-center bg-gray-100 rounded border text-gray-500 text-xs p-1 overflow-hidden">
                    <flux:icon.document class="w-4 h-4 mr-1" /> {{ Str::limit($attachment['name'], 10) }}
                </div>
                @endif
                <button type="button" wire:click="queueAttachmentForDeletion({{ $attachment['id'] }})"
                    wire:loading.attr="disabled" wire:target="queueAttachmentForDeletion({{ $attachment['id'] }})"
                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                    <flux:icon name="x-mark" class="w-3 h-3" />
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showRecordModal', false)">Batal</x-managers.ui.button>
            @if (!$is_admin_user)
                <x-managers.ui.button type="submit" variant="primary">Simpan</x-managers.ui.button>
            @endif
        </div>
    </form>
</x-managers.ui.modal>
{{-- Modal untuk Jadwal Pemeliharaan (Create/Edit) --}}
<x-managers.ui.modal title="{{ $modalType === 'create_schedule' ? 'Buat Jadwal Pemeliharaan Rutin' : 'Edit Jadwal Pemeliharaan Rutin' }}" :show="$showScheduleModal" class="max-w-md">
    <form wire:submit.prevent="saveSchedule" class="space-y-4">
        {{-- Unit (Kamar): Untuk CREATE: dropdown aktif untuk memilih unit yang belum ada jadwal. Untuk EDIT: hanya teks read-only --}}
        <x-managers.form.label for="scheduleUnitId">Unit (Kamar)</x-managers.form.label>
        @if ($modalType === 'create_schedule')
            <x-managers.form.select wire:model.live="scheduleUnitId" :options="$unitOptions" label="Pilih Unit" id="scheduleUnitId" />
        @else
            {{-- Saat EDIT: tampilkan sebagai input teks yang disabled --}}
            @php
                $currentUnitLabel = collect($allAcUnitOptions)->firstWhere('value', $scheduleUnitId)['label'] ?? 'N/A';
            @endphp
            <x-managers.form.input type="text" value="{{ $currentUnitLabel }}" id="scheduleUnitId_display" disabled="true" />
            {{-- Input tersembunyi untuk memastikan ID unit tetap terkirim --}}
            <input type="hidden" wire:model="scheduleUnitId">
        @endif
        @error('scheduleUnitId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Frekuensi (Bulan Sekali): Bisa diganti di EDIT --}}
        <x-managers.form.label for="scheduleFrequencyMonths">Frekuensi (Bulan Sekali)</x-managers.form.label>
        <x-managers.form.select wire:model.live="scheduleFrequencyMonths" :options="$frequencyOptions" label="Pilih Frekuensi" id="scheduleFrequencyMonths" />
        @error('scheduleFrequencyMonths') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Tanggal Jatuh Tempo Berikutnya: Hanya saat CREATE, di EDIT read-only --}}
        @if ($modalType === 'create_schedule')
            <x-managers.form.label for="scheduleNextDueDate">Tanggal Jatuh Tempo Berikutnya</x-managers.form.label>
            <x-managers.form.input wire:model.live="scheduleNextDueDate" type="date" id="scheduleNextDueDate" />
            @error('scheduleNextDueDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        @else
            <x-managers.form.label for="scheduleNextDueDate_display">Tanggal Jatuh Tempo Berikutnya</x-managers.form.label>
            <x-managers.form.input value="{{ \Carbon\Carbon::parse($scheduleNextDueDate)?->format('d F Y') }}" type="text" id="scheduleNextDueDate_display" disabled="true" />
        @endif

        {{-- Status Jadwal: Hanya display di EDIT, otomatis untuk CREATE --}}
        @if ($modalType === 'edit_schedule')
            <x-managers.form.label for="scheduleStatus_display">Status Jadwal</x-managers.form.label>
            <x-managers.form.input value="{{ \App\Enums\MaintenanceScheduleStatus::from($scheduleStatus)->label() }}" type="text" id="scheduleStatus_display" disabled="true" />
        @endif

        {{-- Catatan Jadwal: Ditampilkan di kedua mode, bisa diedit --}}
        <x-managers.form.label for="scheduleNotes">Catatan Jadwal (opsional)</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="scheduleNotes" placeholder="Tambahkan catatan untuk jadwal ini" rows="3" id="scheduleNotes" />
        @error('scheduleNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showScheduleModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button type="submit" variant="primary">Simpan</x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

{{-- Modal untuk Rekaman Pemeliharaan (Create/Edit) --}}
<x-managers.ui.modal title="{{ $modalType === 'create_record' ? 'Catat Pemeliharaan' : 'Edit Rekaman Pemeliharaan' }}" :show="$showRecordModal" class="max-w-md">
    <form wire:submit.prevent="saveRecord" class="space-y-4">
        {{-- Unit (Kamar): Read-only --}}
        <x-managers.form.label for="recordUnitId_display">Unit (Kamar)</x-managers.form.label>
        @php
            $selectedUnitLabel = collect($allAcUnitOptions)->firstWhere('value', $recordUnitId)['label'] ?? 'N/A';
        @endphp
        <x-managers.form.input value="{{ $selectedUnitLabel }}" type="text" id="recordUnitId_display" disabled="true" />
        <input type="hidden" wire:model="recordUnitId">
        @error('recordUnitId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Tipe Pemeliharaan: Read-only --}}
        <x-managers.form.label for="recordType_display">Tipe Pemeliharaan</x-managers.form.label>
        <x-managers.form.input value="{{ \App\Enums\MaintenanceRecordType::from($recordType)->label() }}" type="text" id="recordType_display" disabled="true" />
        <input type="hidden" wire:model="recordType">
        @error('recordType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Jadwal Rutin Terkait: Read-only --}}
        @if ($recordType === \App\Enums\MaintenanceRecordType::ROUTINE->value)
            <x-managers.form.label for="recordMaintenanceScheduleId_display">Jadwal Rutin Terkait</x-managers.form.label>
            @php
                $relatedScheduleLabel = \App\Models\MaintenanceSchedule::find($recordMaintenanceScheduleId)?->next_due_date?->format('d M Y') ?? 'N/A';
            @endphp
            <x-managers.form.input value="Jadwal: {{ $relatedScheduleLabel }}" type="text" id="recordMaintenanceScheduleId_display" disabled="true" />
            <input type="hidden" wire:model="recordMaintenanceScheduleId">
            @error('recordMaintenanceScheduleId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        @endif

        {{-- Tanggal Terjadwal: DIHILANGKAN dari form pengaduan darurat dan rutin --}}
        {{-- Input tersembunyi tetap ada untuk nilai yang diatur oleh Livewire --}}
        <input type="hidden" wire:model="recordScheduledDate">
        {{-- @error('recordScheduledDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror --}}

        <x-managers.form.label for="recordCompletionDate">Tanggal Penyelesaian Dilakukan</x-managers.form.label>
        <x-managers.form.input wire:model.live="recordCompletionDate" type="date" id="recordCompletionDate" />
        @error('recordCompletionDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <x-managers.form.label for="recordNotes">Catatan Pemeliharaan</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="recordNotes" placeholder="Detail pekerjaan pemeliharaan" rows="3" id="recordNotes" />
        @error('recordNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        {{-- Attachments for 'recordAttachments' (new uploads) --}}
        <x-managers.form.label>Lampiran (Foto/Dokumen)</x-managers.form.label>

        {{-- New FilePond Uploader --}}
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

        {{-- Existing Attachments while Editing (adapted from Announcement) --}}
        @if ($existingRecordAttachments && count($existingRecordAttachments) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-300 rounded p-2 mt-4">
            <x-managers.form.small class="col-span-full">Lampiran Saat Ini</x-managers.form.small>
            @foreach ($existingRecordAttachments as $attachment)
            <div class="relative" wire:key="existing-attachment-{{ $attachment['id'] }}">
                @if (str_starts_with($attachment['mime_type'], 'image/'))
                <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}"
                    class="w-full h-full max-w-full max-h-full object-cover rounded border" />
                @else
                <div class="w-full h-full max-w-full max-h-full flex items-center justify-center bg-gray-100 rounded border text-gray-500 text-xs text-center p-1 overflow-hidden">
                    <flux:icon.document class="w-5 h-5 mr-1" /> {{ Str::limit($attachment['name'], 10) }}
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
            <x-managers.ui.button type="submit" variant="primary">Simpan</x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
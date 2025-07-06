{{-- Modal untuk Jadwal Pemeliharaan (Create/Edit) --}}
<x-managers.ui.modal title="{{ $modalType === 'create_schedule' ? 'Buat Jadwal Pemeliharaan Rutin' : 'Edit Jadwal Pemeliharaan Rutin' }}" :show="$showScheduleModal" class="max-w-md">
    <form wire:submit.prevent="saveSchedule" class="space-y-4">
        {{-- Unit (Kamar): Untuk CREATE: dropdown aktif untuk memilih unit yang belum ada jadwal. Untuk EDIT: hanya teks read-only --}}
        <x-managers.form.label for="scheduleUnitId">Unit (Kamar) @if($modalType === 'create_schedule')<span class="text-red-500">*</span>@endif</x-managers.form.label>
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

        {{-- Frekuensi (Bulan Sekali): Bisa diganti di EDIT --}}
        <x-managers.form.label for="scheduleFrequencyMonths">Frekuensi (Bulan Sekali) <span class="text-red-500">*</span></x-managers.form.label>
        <x-managers.form.select wire:model.live="scheduleFrequencyMonths" :options="$frequencyOptions" label="Pilih Frekuensi" id="scheduleFrequencyMonths" />

        {{-- Status Jadwal: Hanya muncul di EDIT mode dengan pembatasan opsi --}}
        @if ($modalType === 'edit_schedule')
            <x-managers.form.label for="scheduleStatus">Status Jadwal</x-managers.form.label>
            @php
                $filteredStatusOptions = [];
                // Add the original/current status with a fallback to prevent enum error
                $filteredStatusOptions[] = [
                    'value' => $originalScheduleStatus,
                    'label' => \App\Enums\MaintenanceScheduleStatus::from($originalScheduleStatus ?: \App\Enums\MaintenanceScheduleStatus::SCHEDULED->value)->label()
                ];
                // Add POSTPONED status, if it's not already the original status
                if ($originalScheduleStatus !== \App\Enums\MaintenanceScheduleStatus::POSTPONED->value) {
                    $filteredStatusOptions[] = ['value' => \App\Enums\MaintenanceScheduleStatus::POSTPONED->value, 'label' => \App\Enums\MaintenanceScheduleStatus::POSTPONED->label()];
                }
                // Ensure unique values and proper indexing
                $filteredStatusOptions = collect($filteredStatusOptions)->unique('value')->values()->toArray();
            @endphp
            <x-managers.form.select wire:model.live="scheduleStatus" :options="$filteredStatusOptions" label="Pilih Status" id="scheduleStatus" />
        @endif

        {{-- Tanggal Jatuh Tempo Asli (read-only) saat edit, hanya tampil jika status yang dipilih adalah Ditunda --}}
        @if ($modalType === 'edit_schedule' && $scheduleStatus === \App\Enums\MaintenanceScheduleStatus::POSTPONED->value)
            <div>
                <x-managers.form.label for="originalScheduleNextDueDate_display">Tanggal Jatuh Tempo Asli</x-managers.form.label>
                <input
                    type="date"
                    id="originalScheduleNextDueDate_display"
                    class="block w-full border rounded-md border-gray-500 dark:placeholder-zinc-500 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 py-2 pl-4 pr-4 dark:text-white"
                    value="{{ $originalScheduleNextDueDate }}"
                    disabled="true"
                />
            </div>
        @endif

        {{-- Tanggal Jatuh Tempo Berikutnya / Tanggal Penundaan Hingga (editable, visibility depends on status) --}}
        <div>
            <x-managers.form.label for="scheduleNextDueDate">
                @if ($modalType === 'edit_schedule' && $scheduleStatus === \App\Enums\MaintenanceScheduleStatus::POSTPONED->value)
                    Tanggal Penundaan Hingga <span class="text-red-500">*</span>
                @else
                    Tanggal Jatuh Tempo Berikutnya <span class="text-red-500">*</span>
                @endif
            </x-managers.form.label>
            <input
                wire:model.live="scheduleNextDueDate"
                type="date"
                id="scheduleNextDueDate"
                class="block w-full border rounded-md {{ $errors->has('scheduleNextDueDate') ? 'border-red-500' : 'border-gray-500' }} dark:placeholder-zinc-500 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 py-2 pl-4 pr-4 dark:text-white"
                {{-- Only enable if in create mode OR if in edit mode and status is postponed --}}
                @if ($modalType === 'edit_schedule' && $scheduleStatus !== \App\Enums\MaintenanceScheduleStatus::POSTPONED->value)
                    disabled="true"
                @endif
                @if ($modalType === 'edit_schedule' && $scheduleStatus === \App\Enums\MaintenanceScheduleStatus::POSTPONED->value)
                    min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                @endif
                {{-- For create mode, always min today --}}
                @if ($modalType === 'create_schedule')
                    min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                @endif
            />
            @error('scheduleNextDueDate') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
        </div>


        {{-- Catatan Jadwal: Ditampilkan di kedua mode, bisa diedit --}}
        <x-managers.form.label for="scheduleNotes">Catatan Jadwal (opsional)</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="scheduleNotes" placeholder="Tambahkan catatan untuk jadwal ini (termasuk alasan penundaan jika status Ditunda)" rows="3" id="scheduleNotes" />

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showScheduleModal', false)">Batal</x-managers.ui.button>
            @if (!$is_admin_user)
                <x-managers.ui.button type="submit" variant="primary">Simpan</x-managers.ui.button>
            @endif
        </div>
    </form>
</x-managers.ui.modal>
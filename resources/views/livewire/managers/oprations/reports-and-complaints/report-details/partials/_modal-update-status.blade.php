{{-- Modal untuk Update Status --}}
<x-managers.ui.modal id="update-status-modal" title="Perbarui Status Laporan" :show="$showUpdateStatusModal" class="max-w-md">
    <form wire:submit.prevent="saveStatusUpdate" class="space-y-4">
        <div>
            <x-managers.form.label>Status Saat Ini:</x-managers.form.label>
            <span class="px-2 py-1 rounded-full text-sm {{ implode(' ', $reportCurrentStatus->color()) }}">{{ $reportCurrentStatus->label() }}</span>
        </div>

        <div>
            <x-managers.form.label for="newStatus">Ubah Status Menjadi <span class="text-red-500">*</span></x-managers.form.label>
            <x-managers.form.select wire:model.live="newStatus" :options="$availableStatusOptions" label="Pilih Status Baru" />
            @error('newStatus')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div>
            <x-managers.form.label for="notes">Catatan/Laporan Pengerjaan <span class="text-red-500">*</span></x-managers.form.label>
            <x-managers.form.textarea wire:model.live="notes" rows="4" placeholder="Contoh: Lampu sudah diganti, perlu konfirmasi penghuni." />
            @error('notes')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div>
            <x-managers.form.label>Lampiran (Foto/Dokumen Pengerjaan)</x-managers.form.label>
            <div wire:key="filepond-new-attachments-wrapper-{{$reportIdBeingViewed}}">
                <x-filepond::upload wire:model.live="newAttachments" multiple max-file-size="2MB" accepted-file-types="image/*,application/pdf" />
            </div>
            <div class="mt-2">
                @error('newAttachments.*')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @else
                <x-managers.form.small>Ukuran Maksimal: 2MB per file. Format: Gambar (JPG, PNG, dll.) atau PDF.</x-managers.form.small>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary" wire:click="closeModal">Batal</x-managers.ui.button>
            @if ($this->canEditReport())
            <x-managers.ui.button type="submit" variant="primary">Simpan Perubahan</x-managers.ui.button>
            @endif
        </div>
    </form>
</x-managers.ui.modal>
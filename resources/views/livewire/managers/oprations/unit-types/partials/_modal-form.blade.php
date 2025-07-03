{{-- Modal Form --}}
<x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal && $modalType === 'form'" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <x-managers.form.label>Nama Tipe</x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Contoh: Studio, 1 Kamar, Loft" />

        <x-managers.form.label>Deskripsi Tipe</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="description" rows="3" />

        <x-managers.form.label>Memerlukan Pemeliharaan Rutin</x-managers.form.label>
        <x-managers.form.checkbox wire:model.live="requiresMaintenance" label="Ya, memerlukan Pemeliharaan Rutin"
                description="Jika diaktifkan, tipe kamar ini dapat dijadwalkan untuk pemeliharaan rutin." />

        <div>
            <x-managers.form.label>Fasilitas</x-managers.form.label>
            @if (!empty($facilities))
                @foreach ($facilities as $index => $facility)
                    <div class="flex items-center gap-2 mb-2" wire:key="facility-{{ $index }}">
                        <x-managers.form.input wire:model.live="facilities.{{ $index }}"
                            placeholder="Contoh: AC, Dapur" />
                        <x-managers.ui.button wire:click="removeFacility({{ $index }})" variant="danger"
                            size="sm" icon="trash" />
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada fasilitas.</p>
            @endif

            <div class="mt-3 flex gap-2">
                <x-managers.form.input wire:model.live="newFacility" placeholder="Masukkan fasilitas baru..." />
                <x-managers.ui.button wire:click="addFacility()" variant="secondary" size="sm" icon="plus">
                    Tambah
                </x-managers.ui.button>
            </div>
        </div>

        {{-- Attachments for 'attachments' morphMany --}}
        <x-managers.form.label>Gambar atau File Pendukung</x-managers.form.label>
        @if ($existingAttachments && count($existingAttachments) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-300 rounded p-2 mb-2">
                <x-managers.form.small class="col-span-full">File Terlampir Saat Ini</x-managers.form.small>
                @foreach ($existingAttachments as $attachment)
                    <div class="relative" wire:key="existing-attachment-{{ $attachment['id'] }}">
                        @if (str_starts_with($attachment['mime_type'], 'image/'))
                            <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}"
                                class="w-full h-full max-w-full max-h-full object-cover rounded border" />
                        @else
                            <div
                                class="w-full h-full max-w-full max-h-full flex items-center justify-center bg-gray-100 rounded border text-gray-500 text-xs text-center p-1 overflow-hidden">
                                <flux:icon.document class="w-5 h-5 mr-1" /> {{ $attachment['name'] }}
                            </div>
                        @endif
                        <button type="button" wire:click="queueAttachmentForDeletion({{ $attachment['id'] }})"
                            wire:loading.attr="disabled"
                            wire:target="queueAttachmentForDeletion({{ $attachment['id'] }})"
                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                            <flux:icon name="x-mark" class="w-3 h-3" />
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <div wire:key="filepond-attachments-wrapper">
            <x-filepond::upload wire:model.live="attachments" multiple max-file-size="5MB" />
        </div>

        <div class="mt-2">
            @error('attachments.*')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @else
                <x-managers.form.small>Max 5MB per file. Bisa upload banyak gambar atau file (PDF, DOC,
                    dll).</x-managers.form.small>
            @enderror
        </div>

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button wire:click="save()" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

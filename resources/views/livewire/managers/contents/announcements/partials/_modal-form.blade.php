{{-- Modal Create/Edit --}}
<x-managers.ui.modal title="Form Pengumuman" :show="$showModal && $modalType === 'form'" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <x-managers.form.label for="title">Judul Pengumuman</x-managers.form.label>
        <x-managers.form.input wire:model.live="title" placeholder="Masukkan judul pengumuman" id="title" />
        @error('title')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <x-managers.form.label for="description">Isi Pengumuman</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="description" placeholder="Masukkan deskripsi pengumuman" id="description" />
        @error('description')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <x-managers.form.label for="status">Status Pengumuman</x-managers.form.label>
        <x-managers.form.select wire:model.live="status" :options="$statusOptions" label="Pilih Status" id="status" />
        @error('status')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        {{-- Single Image for 'image' column --}}
        <x-managers.form.label>Gambar Banner</x-managers.form.label>
        @if ($existingImage && !$image)
        <div class="relative w-full h-32 mb-2">
            <img src="{{ asset('storage/' . $existingImage) }}" alt="Gambar Utama"
                class="w-full h-full object-contain rounded border" />
            <button type="button" wire:click="$set('existingImage', null); $set('image', null);"
                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                <flux:icon name="x-mark" class="w-3 h-3" />
            </button>
        </div>
        @elseif ($image)
        <div class="relative w-full h-32 mb-2">
            <img src="{{ $image->temporaryUrl() }}" alt="Gambar Preview"
                class="w-full h-full object-contain rounded border" />
            <button type="button" wire:click="$set('image', null)"
                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                <flux:icon name="x-mark" class="w-3 h-3" />
            </button>
        </div>
        @endif
        <input type="file" wire:model="image" class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100" />
        @error('image')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @else
        <x-managers.form.small>Max 2MB. Tipe: JPG, PNG, GIF, WEBP. Hanya satu gambar.</x-managers.form.small>
        @enderror

        {{-- Attachments for 'attachments' morphMany --}}
        <x-managers.form.label>Lampiran (Gambar atau File Pendukung)</x-managers.form.label>

        {{-- Existing Attachments while Editing --}}
        @if ($existingAttachments && count($existingAttachments) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-300 rounded p-2 mb-2">
            <x-managers.form.small class="col-span-full">Lampiran Saat Ini</x-managers.form.small>
            @foreach ($existingAttachments as $attachment)
            <div class="relative" wire:key="existing-attachment-{{ $attachment['id'] }}">
                @if (str_starts_with($attachment['mime_type'], 'image/'))
                <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}"
                    class="w-full h-16 object-cover rounded border" />
                @else
                <div class="w-full h-16 flex items-center justify-center bg-gray-100 rounded border text-gray-500 text-xs text-center p-1 overflow-hidden">
                    <flux:icon.document class="w-5 h-5 mr-1" /> {{ $attachment['name'] }}
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

        <div wire:key="filepond-attachments-wrapper">
            <x-filepond::upload wire:model.live="attachments" multiple max-file-size="5MB" />
        </div>

        <div class="mt-2">
            @error('attachments.*')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @else
            <x-managers.form.small>Max 5MB per file. Bisa upload banyak gambar atau file (PDF, DOC, dll).</x-managers.form.small>
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
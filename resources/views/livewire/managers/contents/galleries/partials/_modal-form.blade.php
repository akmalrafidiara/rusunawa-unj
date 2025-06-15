{{-- Modal Form Gallery --}}
<x-managers.ui.modal title="Form Galeri" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Caption -->
        <x-managers.form.label>Caption</x-managers.form.label>
        <x-managers.form.input wire:model.live="caption" placeholder="Masukkan caption gambar..." />

        <!-- Upload Gambar -->
        <x-managers.form.label>Gambar</x-managers.form.label>
        @if ($image)
            <div class="inline-flex gap-2 border border-gray-300 rounded p-2 mb-2">
                <x-managers.form.small>Preview</x-managers.form.small>
                <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}"
                    alt="Preview Gambar" class="w-16 h-16 object-cover rounded border" />
            </div>
        @endif

        <div class="mb-2">
            @if ($errors->has('image'))
                <span class="text-red-500 text-sm">{{ $errors->first('image') }}</span>
            @else
                <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
            @endif
        </div>

        <x-filepond::upload wire:model.live="image" />

        <!-- Tombol Aksi -->
        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button wire:click="save()" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
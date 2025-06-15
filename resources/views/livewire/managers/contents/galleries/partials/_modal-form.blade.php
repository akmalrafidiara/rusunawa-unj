{{-- Modal Form Gallery --}}
<x-managers.ui.modal title="Form Galeri" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Caption -->
        <x-managers.form.label>Caption</x-managers.form.label>
        <x-managers.form.input wire:model.live="caption" placeholder="Masukkan caption gambar..." />

        <!-- Upload Gambar -->
        <x-managers.form.label>Gambar</x-managers.form.label>
        @if ($image)
            <div class="relative w-full h-56 mb-2">
                <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}"
                    alt="Preview Gambar"
                    class="w-full h-full max-w-full max-h-full object-contain rounded border" />
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
            <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
        @enderror

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
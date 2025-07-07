<x-managers.ui.modal title="Form Regulasi" :show="$showModal" class="max-w-3xl">
    <form wire:submit.prevent="save" class="space-y-4">
        <x-managers.form.label>Judul <span class="text-red-500">*</span></x-managers.form.label>
        <x-managers.form.input wire:model.live="title" 
            placeholder="Masukkan judul regulasi..."
            :error="$errors->first('title')" 
        />

        <x-managers.form.label class="mt-4">Isi Pasal <span class="text-red-500">*</span></x-managers.form.label>
        <div wire:ignore>
            <input id="content-trix-editor" type="hidden" name="content" value="{{ $content }}">
            <trix-editor input="content-trix-editor" class="trix-content"></trix-editor>
        </div>
        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button type="submit" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
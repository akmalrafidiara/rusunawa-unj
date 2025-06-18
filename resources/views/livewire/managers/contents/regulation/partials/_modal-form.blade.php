<x-managers.ui.modal title="Form Regulasi" :show="$showModal" class="max-w-3xl"> {{-- Ganti Judul Modal --}}
    <form wire:submit.prevent="save" class="space-y-4">
        <x-managers.form.label>Judul Regulasi</x-managers.form.label> {{-- Ganti label --}}
        <x-managers.form.input wire:model.live="title" placeholder="Masukkan judul regulasi..." /> {{-- Ganti wire:model dari question menjadi title --}}
        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror {{-- Ganti error untuk question menjadi title --}}

        <x-managers.form.label class="mt-4">Isi Regulasi</x-managers.form.label> {{-- Ganti label --}}
        {{-- Ganti textarea dengan input Trix --}}
        <div wire:ignore>
            <input id="content-trix-editor" type="hidden" name="content" value="{{ $content }}"> {{-- Ganti id, name, dan value dari answer menjadi content --}}
            <trix-editor input="content-trix-editor" class="trix-content"></trix-editor> {{-- Ganti input id --}}
        </div>
        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror {{-- Ganti error untuk answer menjadi content --}}

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button type="submit" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
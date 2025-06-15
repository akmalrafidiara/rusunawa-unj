<x-managers.ui.modal title="Form FAQ" :show="$showModal" class="max-w-3xl">
    <form wire:submit.prevent="save" class="space-y-4">
        <x-managers.form.label>Pertanyaan</x-managers.form.label>
        <x-managers.form.input wire:model.live="question" placeholder="Masukkan pertanyaan..." />
        @error('question') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <x-managers.form.label class="mt-4">Jawaban</x-managers.form.label>
        {{-- Ganti textarea dengan input Trix --}}
        <div wire:ignore>
            <input id="answer-trix-editor" type="hidden" name="content" value="{{ $answer }}">
            <trix-editor input="answer-trix-editor" class="trix-content"></trix-editor>
        </div>
        @error('answer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button type="submit" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
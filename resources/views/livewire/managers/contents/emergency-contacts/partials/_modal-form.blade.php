{{-- Modal Create/Edit --}}
<x-managers.ui.modal title="Form Kontak Darurat" :show="$showModal && $modalType === 'form'" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        {{-- Nama Kontak --}}
        <x-managers.form.label for="name">Nama Kontak <span class="text-red-500">*</span></x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Masukkan nama kontak" id="name" />

        {{-- Peran Kontak --}}
        <x-managers.form.label for="role">Peran Kontak <span class="text-red-500">*</span></x-managers.form.label>
        <x-managers.form.select wire:model.live="role" :options="$roleOptions" label="Pilih Peran" id="role" />

        {{-- Telepon Kontak --}}
        <x-managers.form.label for="phone">Nomor Telepon <span class="text-red-500">*</span></x-managers.form.label>
        <x-managers.form.input wire:model.live="phone" placeholder="Masukkan nomor telepon" id="phone" />


        {{-- Alamat Kontak --}}
        <x-managers.form.label for="address">Alamat</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="address" placeholder="Masukkan alamat lengkap (opsional)" id="address" rows="3" />

        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button wire:click="save()" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
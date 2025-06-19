{{-- Modal Form --}}
<x-managers.ui.modal title="Form Tipe Penghuni" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- name -->
        <x-managers.form.label>Nama</x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Tipe Penghuni" />

        {{-- Description --}}
        <x-managers.form.label>Deskripsi</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="description" placeholder="Deskripsikan tipe penghuni ini"
            rows="3" />

        {{-- Requires Verification --}}
        <x-managers.form.label>Memerlukan Verifikasi</x-managers.form.label>
        <x-managers.form.checkbox wire:model.live="requiresVerification" label="Ya, memerlukan verifikasi"
            description="Jika diaktifkan, harga ini memerlukan verifikasi sebelum diterapkan." />

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

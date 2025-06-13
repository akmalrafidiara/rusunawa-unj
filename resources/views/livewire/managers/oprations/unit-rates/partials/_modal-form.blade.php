{{-- Modal Form --}}
<x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Price -->
        <x-managers.form.label>Price</x-managers.form.label>
        <x-managers.form.input wire:model.live="price" placeholder="Contoh: Rp175.000" rupiah />

        <!-- Occupant Type Tipe -->
        <x-managers.form.label>Tipe Penghuni</x-managers.form.label>
        {{-- <x-managers.form.input wire:model.live="occupantType" placeholder="Contoh: Internal UNJ" icon="user" /> --}}
        <x-managers.form.select-or-create wire:model="occupantType" :options="$occupantTypeOptions"
            placeholder="Pilih atau ketik untuk membuat baru..." />

        <!-- Pricing Base -->
        <x-managers.form.label>Dasar Penetapan Harga</x-managers.form.label>
        <x-managers.form.select wire:model.live="pricingBasis" :options="$pricingBasisOptions" label="Basis Harga" />

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

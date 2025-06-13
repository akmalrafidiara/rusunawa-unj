<x-managers.ui.modal title="Form User" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">

        {{-- Name --}}
        <x-managers.form.label>Nama Lengkap</x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Nama Lengkap" type="text" />

        {{-- Email --}}
        <x-managers.form.label>Email</x-managers.form.label>
        <x-managers.form.input wire:model.live="email" placeholder="Email" type="email" />

        {{-- Password --}}
        <x-managers.form.label>Password</x-managers.form.label>
        <x-managers.form.input wire:model.live="password" placeholder="Password" type="password" />

        {{-- Phone --}}
        <x-managers.form.label>No. Telepon</x-managers.form.label>
        <x-managers.form.input wire:model.live="phone" placeholder="Phone Number" type="text" />

        {{-- Role --}}
        <x-managers.form.label>Role</x-managers.form.label>
        <x-managers.form.select wire:model.live="role" :options="$roleOptions" label="Role" />

        <div class="flex justify-end gap-2">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button wire:click="save()" type="submit" variant="primary">Simpan</x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

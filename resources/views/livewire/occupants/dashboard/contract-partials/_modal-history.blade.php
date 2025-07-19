<x-managers.ui.modal title="Riwayat Transaksi" :show="$showModal && $modalType === 'history'" class="max-w-xl">

    <div class="flex justify-end mt-4">
        <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)" class="max-w-xl">
            Tutup
        </x-managers.ui.button>
    </div>
</x-managers.ui.modal>

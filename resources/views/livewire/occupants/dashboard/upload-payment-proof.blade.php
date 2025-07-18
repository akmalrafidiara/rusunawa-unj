<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-4">Unggah Bukti Pembayaran Invoice #{{ $invoice->id }}</h3>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="savePayment">
        <div class="mb-4">
            <x-frontend.form.file wire:model="proofOfPayment" name="proofOfPayment"
                label="Pilih Bukti Pembayaran (Gambar)" />
        </div>

        <div class="mb-6">
            <x-managers.form.label>
                Catatan Tambahan (Opsional)
            </x-managers.form.label>
            <x-managers.form.input type="text" wire:model="notes" name="notes"
                placeholder="Masukkan catatan tambahan jika diperlukan" />
        </div>

        <div class="flex items-center justify-end">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Unggah Bukti Pembayaran
            </button>
        </div>
    </form>
</div>

<x-managers.ui.card-side class="p-4 h-full flex flex-col">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Daftar Verifikasi Pembayaran</h3>

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <x-managers.form.input wire:model.live.debounce.300ms="search" clearable
            placeholder="Cari Nama Penghuni atau Nomor Invoice..." icon="magnifying-glass" class="w-full" />
    </div>

    {{-- Daftar Pembayaran --}}
    <div wire:poll.10s class="flex flex-col gap-4 overflow-y-auto pr-2" style="max-height: 70vh;">
        @forelse ($payments as $payment)
            <div wire:click="selectPayment({{ $payment->id }})"
                class="p-6 rounded-lg border cursor-pointer transition-colors duration-200
                {{ $paymentIdBeingSelected === $payment->id ? 'bg-green-100 border-green-500 dark:bg-green-900/30 dark:border-green-700' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600' }}">

                {{-- Occupant Name (from invoice -> contract -> occupant) --}}
                <div class="font-medium text-gray-800 dark:text-gray-200">
                    {{ $payment->invoice->contract->occupants->first()->full_name ?? 'N/A' }}
                </div>

                {{-- Invoice Number --}}
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Invoice: {{ $payment->invoice->invoice_number ?? 'N/A' }}
                </div>

                {{-- Uploaded At --}}
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Diunggah pada: {{ $payment->uploaded_at->translatedFormat('d F Y, H:i') }} WIB
                    <span class="text-xs text-gray-500 dark:text-gray-500 ml-2">
                        ({{ $payment->uploaded_at->diffForHumans() }})
                    </span>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-6">Tidak ada verifikasi pembayaran yang perlu
                ditinjau.</p>
        @endforelse
    </div>
    <x-managers.ui.pagination :paginator="$payments" class="mt-4" />
</x-managers.ui.card-side>

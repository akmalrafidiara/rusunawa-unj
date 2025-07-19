<div class="space-y-4 text-gray-700 dark:text-gray-300">

    <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
        <div class="flex items-center gap-2">
            <flux:icon name="credit-card" class="w-5 h-5 text-indigo-500" />
            <span class="font-semibold">Virtual Account | Mandiri:</span>
        </div>
        <span class="font-mono bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 p-1 rounded-md text-sm">
            {{ $contract->unit->virtual_account_number }}
        </span>
    </div>

    <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
        <div class="flex items-center gap-2">
            <flux:icon name="calendar" class="w-5 h-5 text-indigo-500" />
            <span class="font-semibold">Durasi Sewa:</span>
        </div>
        <span>
            {{ $contract->start_date->format('d M Y') }} -
            {{ $contract->end_date->format('d M Y') }}
        </span>
    </div>

    <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
        <div class="flex items-center gap-2">
            <flux:icon name="calendar-days" class="w-5 h-5 text-red-500" />
            <span class="font-semibold">Tanggal Jatuh Tempo:</span>
        </div>
        <span class="text-red-500 font-medium">
            {{ $latestInvoice->due_at->translatedFormat('d F Y H:i') }} WIB
        </span>
    </div>

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:icon.tag class="w-5 h-5 text-blue-500" />
            <span class="font-semibold">Status Tagihan:</span>
        </div>
        <x-managers.ui.badge class="{{ implode(' ', (array) $latestInvoice->status->color()) }}">
            {{ $latestInvoice->status->label() }}
        </x-managers.ui.badge>
    </div>

    <div class="flex justify-between border-t border-gray-200 dark:border-zinc-600 pt-4 mt-4">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                Rp{{ number_format($latestInvoice->amount, 0, ',', '.') }}
            </p>
        </div>

        @if ($latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID)
            <x-managers.ui.button
                class="bg-emerald-600 hover:bg-emerald-700 text-sm text-white font-bold rounded-lg transition-colors shadow-md"
                wire:click="showPaymentForm">
                Konfirmasi Pembayaran
            </x-managers.ui.button>
        @endif
    </div>
</div>

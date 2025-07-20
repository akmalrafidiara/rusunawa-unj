<x-managers.ui.modal title="Riwayat Transaksi" :show="$showModal && $modalType === 'history'" class="max-w-4xl">
    <div x-data="{ activeTab: 'invoices' }" class="space-y-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a @click="activeTab = 'invoices'"
                    :class="{ 'border-green-500 text-green-600 dark:text-green-400': activeTab === 'invoices', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'invoices' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer">
                    Riwayat Invoice
                </a>
                <a @click="activeTab = 'payments'"
                    :class="{ 'border-green-500 text-green-600 dark:text-green-400': activeTab === 'payments', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'payments' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer">
                    Riwayat Pembayaran
                </a>
            </nav>
        </div>

        {{-- Invoice History Tab --}}
        <div x-show="activeTab === 'invoices'" class="space-y-4">
            @if ($invoices->isNotEmpty())
                @foreach ($invoices as $invoice)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4 bg-gray-50 dark:bg-gray-800">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Invoice
                                #{{ $invoice->invoice_number }}</h4>
                            <x-managers.ui.badge :variant="$invoice->status->color()">{{ $invoice->status->label() }}</x-managers.ui.badge>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">{{ $invoice->description }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Jumlah: <span class="font-medium">Rp
                                {{ number_format($invoice->amount, 0, ',', '.') }}</span></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jatuh Tempo:
                            {{ $invoice->due_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat:
                            {{ $invoice->created_at->format('d M Y H:i') }}</p>

                        @if ($invoice->status === \App\Enums\InvoiceStatus::PAID)
                            <p class="text-xs text-gray-500 dark:text-gray-400">Terbayar Pada:
                                {{ $invoice->updated_at->format('d M Y H:i') }}</p>
                        @endif

                        @if ($invoice->payments->isNotEmpty())
                            <div class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-3">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Pembayaran
                                    Terkait:</h5>
                                <ul class="space-y-2">
                                    @foreach ($invoice->payments as $payment)
                                        <li
                                            class="flex justify-between items-center bg-white dark:bg-gray-700 p-2 rounded-md shadow-sm">
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-gray-200">Rp
                                                    {{ number_format($payment->amount_paid, 0, ',', '.') }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">Pada:
                                                    {{ $payment->uploaded_at->format('d M Y H:i') }}</p>
                                            </div>
                                            <x-managers.ui.badge
                                                :variant="$payment->status->color()">{{ $payment->status->label() }}</x-managers.ui.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <p class="text-center text-gray-500 dark:text-gray-400">Belum ada riwayat invoice.</p>
            @endif
        </div>

        {{-- Payment History Tab --}}
        <div x-show="activeTab === 'payments'" class="space-y-4" style="display: none;">
            @if ($payments->isNotEmpty())
                @foreach ($payments as $payment)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4 bg-gray-50 dark:bg-gray-800">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Pembayaran untuk Invoice
                                #{{ $payment->invoice->invoice_number ?? 'N/A' }}</h4>
                            <x-managers.ui.badge
                                :variant="$payment->status->color()">{{ $payment->status->label() }}</x-managers.ui.badge>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Jumlah Dibayar: <span class="font-medium">Rp
                                {{ number_format($payment->amount_paid, 0, ',', '.') }}</span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Tanggal Pembayaran:
                            {{ $payment->uploaded_at->format('d M Y H:i') }}</p>
                        @if ($payment->notes)
                            <p class="text-sm text-gray-700 dark:text-gray-300">Catatan: {{ $payment->notes }}</p>
                        @endif
                        @if ($payment->proof_of_payment_path)
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bukti
                                    Pembayaran:</label>
                                @php
                                    $extension = pathinfo($payment->proof_of_payment_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp

                                @if ($isImage)
                                    <img src="{{ Storage::url($payment->proof_of_payment_path) }}"
                                        alt="Bukti Pembayaran"
                                        class="max-w-full h-auto rounded-lg border border-gray-300 dark:border-gray-600">
                                @elseif(strtolower($extension) === 'pdf')
                                    <div
                                        class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Dokumen PDF</span>
                                            <a href="{{ Storage::url($payment->proof_of_payment_path) }}"
                                                target="_blank"
                                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors">
                                                Buka di Tab Baru
                                            </a>
                                        </div>
                                        <embed
                                            src="{{ Storage::url($payment->proof_of_payment_path) }}#toolbar=1&navpanes=1&scrollbar=1"
                                            type="application/pdf"
                                            class="w-full h-96 rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-700 dark:text-gray-300">Tidak ada bukti pembayaran yang
                                diunggah.</p>
                        @endif
                    </div>
                @endforeach
            @else
                <p class="text-center text-gray-500 dark:text-gray-400">Belum ada riwayat pembayaran.</p>
            @endif
        </div>

        <div class="flex justify-end mt-4">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </div>
</x-managers.ui.modal>

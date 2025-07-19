<div class="lg:col-span-2 flex flex-col gap-6">
    <x-managers.ui.card class="p-4 lg:col-span-2 text-center text-gray-500 dark:text-gray-400">
        @if ($paymentIdBeingSelected)
            {{-- Payment Details --}}
            <div class="text-left">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Verifikasi Pembayaran</h3>
                <div class="flex gap-3 mb-6">
                    <button wire:click="showResponseModal('accept')"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200 cursor-pointer">
                        Terima
                    </button>
                    <button wire:click="showResponseModal('reject')"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors duration-200 cursor-pointer">
                        Tolak
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Occupant and Contract Details --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Penghuni
                            (PIC)</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $payment->invoice->contract->pic->first()->full_name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp
                            Penghuni</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $payment->invoice->contract->pic->first()->whatsapp_number ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Kontrak</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $payment->invoice->contract->contract_code ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Unit</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $payment->invoice->contract->unit->room_number ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Detail Invoice</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                Invoice</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $payment->invoice->invoice_number ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                Invoice</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $payment->invoice->description ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Tagihan
                                Invoice</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ 'Rp ' . number_format($payment->invoice->amount ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Jatuh
                                Tempo Invoice</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $payment->invoice->due_at ? $payment->invoice->due_at->translatedFormat('d F Y H:i') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status
                                Invoice</label>
                            <x-managers.ui.badge :variant="$payment->invoice->status->color()" class="mt-1">
                                {{ $payment->invoice->status->label() }}
                            </x-managers.ui.badge>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Detail Pembayaran</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Pembayaran
                                yang Dilakukan</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ 'Rp ' . number_format($payment->amount_paid ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                Pembayaran</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $payment->payment_date ? $payment->payment_date->translatedFormat('d F Y H:i') : '-' }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                                Pembayaran</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->notes ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Proof of Payment File --}}
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Bukti Pembayaran</h4>
                    @if ($payment->proof_of_payment_path)
                        @php
                            $extension = pathinfo($payment->proof_of_payment_path, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        @if ($isImage)
                            <img src="{{ Storage::url($payment->proof_of_payment_path) }}" alt="Bukti Pembayaran"
                                class="max-w-full h-auto rounded-lg border border-gray-300 dark:border-gray-600">
                        @elseif(strtolower($extension) === 'pdf')
                            <div
                                class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Dokumen PDF</span>
                                    <a href="{{ Storage::url($payment->proof_of_payment_path) }}" target="_blank"
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors">
                                        Buka di Tab Baru
                                    </a>
                                </div>
                                <embed
                                    src="{{ Storage::url($payment->proof_of_payment_path) }}#toolbar=1&navpanes=1&scrollbar=1"
                                    type="application/pdf"
                                    class="w-full h-96 rounded border border-gray-200 dark:border-gray-700">
                            </div>
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tipe file tidak didukung untuk
                                pratinjau.</p>
                            <a href="{{ Storage::url($payment->proof_of_payment_path) }}" target="_blank"
                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors mt-2 inline-block">
                                Unduh File
                            </a>
                        @endif
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada bukti pembayaran yang diunggah.
                        </p>
                    @endif
                </div>
            </div>
        @else
            <p>Pilih verifikasi pembayaran dari daftar di samping untuk melihat detail.</p>
        @endif
    </x-managers.ui.card>
</div>

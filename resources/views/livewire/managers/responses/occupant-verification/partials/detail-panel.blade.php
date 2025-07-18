<div class="lg:col-span-2 flex flex-col gap-6">
    <x-managers.ui.card class="p-4 lg:col-span-2 text-center text-gray-500 dark:text-gray-400">
        @if ($occupantIdBeingSelected)
            {{-- Detail Occupant --}}
            <div class="text-left">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Penghuni</h3>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->full_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->whatsapp_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->status ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Files Section --}}
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Dokumen</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Identity Card File --}}
                        @if ($occupant->identity_card_file)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Identity
                                    Card</label>
                                @php
                                    $extension = pathinfo($occupant->identity_card_file, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp

                                @if ($isImage)
                                    <img src="{{ Storage::url($occupant->identity_card_file) }}" alt="Identitas"
                                        class="max-w-full h-auto rounded-lg border border-gray-300 dark:border-gray-600">
                                @elseif(strtolower($extension) === 'pdf')
                                    <div
                                        class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">PDF Document</span>
                                            <a href="{{ Storage::url($occupant->identity_card_file) }}" target="_blank"
                                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors">
                                                Open in New Tab
                                            </a>
                                        </div>
                                        <embed
                                            src="{{ Storage::url($occupant->identity_card_file) }}#toolbar=1&navpanes=1&scrollbar=1"
                                            type="application/pdf"
                                            class="w-full h-96 rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Community Card File --}}
                        @if ($occupant->community_card_file)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dokumen
                                    Kerjasama</label>
                                @php
                                    $extension = pathinfo($occupant->community_card_file, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp

                                @if ($isImage)
                                    <img src="{{ Storage::url($occupant->community_card_file) }}" alt="Kartu Keluarga"
                                        class="max-w-full h-auto rounded-lg border border-gray-300 dark:border-gray-600">
                                @elseif(strtolower($extension) === 'pdf')
                                    <div
                                        class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">PDF Document</span>
                                            <a href="{{ Storage::url($occupant->community_card_file) }}" target="_blank"
                                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors">
                                                Open in New Tab
                                            </a>
                                        </div>
                                        <embed
                                            src="{{ Storage::url($occupant->community_card_file) }}#toolbar=1&navpanes=1&scrollbar=1"
                                            type="application/pdf"
                                            class="w-full h-96 rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <p>Pilih laporan dari daftar di samping untuk melihat detail dan riwayat penanganan.</p>
        @endif
    </x-managers.ui.card>
</div>

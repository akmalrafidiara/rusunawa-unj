<x-managers.ui.modal title="Verifikasi Penghuni" :show="$showModal" class="max-w-4xl">
    <div class="space-y-6">
        @if ($occupant)
            {{-- Display the Occupant Verification Type --}}
            <div class="mb-4 p-3 bg-blue-100 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-md">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    Jenis Verifikasi: <span class="font-bold">{{ $occupantVerificationType }}</span>
                </p>
            </div>

            @if ($modalType === 'accept')
                <h2 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Data Penghuni Diterima
                </h2>
                <x-managers.form.label>Pesan</x-managers.form.label>
                <x-managers.form.input wire:model="responseMessage" />

                {{-- Only show price update for PIC for new contract initiation --}}
                @if ($occupantVerificationType === 'Pengajuan Kontrak Baru (PIC)')
                    <x-managers.form.label class="mt-4">Perbarui Harga Kontrak (opsional)</x-managers.form.label>
                    <x-managers.form.input wire:model="contractPrice" rupiah />
                @endif
            @elseif ($modalType === 'reject')
                <h2 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Data Penghuni Ditolak
                </h2>
                <x-managers.form.label>Alasan Penolakan</x-managers.form.label>
                <x-managers.form.textarea wire:model="responseMessage" placeholder="Masukkan alasan penolakan" />
                @error('responseMessage')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            @endif

            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Penghuni</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->full_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->whatsapp_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->gender->label() ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status
                            Penghuni</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->status->label() ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pelajar</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $occupant->is_student ? 'Ya' : 'Tidak' }}
                        </p>
                    </div>
                </div>

                @if ($occupant->is_student)
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Data Akademik</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIM/ID
                                    Mahasiswa</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $occupant->student_id ?? '-' }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fakultas</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->faculty ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Program
                                    Studi</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $occupant->study_program ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun
                                    Angkatan</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $occupant->class_year ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Files Section --}}
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Dokumen</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Identity Card File --}}
                        @if ($occupant->identity_card_file)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kartu
                                    Identitas (KTP)</label>
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
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Dokumen PDF</span>
                                            <a href="{{ Storage::url($occupant->identity_card_file) }}" target="_blank"
                                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors">
                                                Buka di Tab Baru
                                            </a>
                                        </div>
                                        <embed
                                            src="{{ Storage::url($occupant->identity_card_file) }}#toolbar=1&navpanes=1&scrollbar=1"
                                            type="application/pdf"
                                            class="w-full h-96 rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kartu
                                    Identitas (KTP)</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">Tidak ada file yang diunggah.
                                </p>
                            </div>
                        @endif

                        {{-- Community Card File --}}
                        @if ($occupant->community_card_file)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dokumen
                                    Keluarga/Komunitas</label>
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
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Dokumen PDF</span>
                                            <a href="{{ Storage::url($occupant->community_card_file) }}"
                                                target="_blank"
                                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition-colors">
                                                Buka di Tab Baru
                                            </a>
                                        </div>
                                        <embed
                                            src="{{ Storage::url($occupant->community_card_file) }}#toolbar=1&navpanes=1&scrollbar=1"
                                            type="application/pdf"
                                            class="w-full h-96 rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dokumen
                                    Keluarga/Komunitas</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">Tidak ada file yang diunggah.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif {{-- End of @if ($occupant) --}}

        <div class="flex gap-2 justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
            <x-managers.ui.button type="button" variant="{{ $modalType === 'accept' ? 'primary' : 'danger' }}"
                wire:click="{{ $modalType === 'accept' ? 'acceptOccupant' : 'rejectOccupant' }}">
                {{ $modalType === 'accept' ? 'Setujui' : 'Tolak' }}
            </x-managers.ui.button>
        </div>
    </div>
</x-managers.ui.modal>

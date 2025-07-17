@if ($occupantIdBeingSelected)
    <x-managers.ui.modal title="Detail Data Penghuni" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Header Penghuni --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800">
                <div class="p-3 bg-emerald-600 dark:bg-emerald-500 rounded-full">
                    {{-- Ganti dengan ikon user --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    {{-- Menampilkan nama lengkap dari properti --}}
                    <h3 class="text-xl font-bold text-zinc-800 dark:text-zinc-100">{{ $fullName }}</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">Detail informasi pribadi dan akademik</p>
                </div>
            </div>

            {{-- Informasi Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kartu Informasi Pribadi --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Informasi Pribadi
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Email</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $email }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">No. WhatsApp</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $whatsappNumber }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Jenis Kelamin</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $gender->label() }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Catatan</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">{{ $notes ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Kartu Status & Dokumen --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Status & Dokumen
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Status Verifikasi</span>
                            @php
                                $statusEnum = \App\Enums\OccupantStatus::tryFrom($status);
                            @endphp
                            <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        {{-- Menampilkan link ke file jika ada --}}
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">File KTP</span>

                            <div>
                                @if ($identityCardFile)
                                    @php
                                        // Tentukan apakah file ini adalah gambar
                                        $isImage =
                                            (is_string($identityCardFile) &&
                                                in_array(strtolower(pathinfo($identityCardFile, PATHINFO_EXTENSION)), [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                    'webp',
                                                ])) ||
                                            (!is_string($identityCardFile) &&
                                                str_starts_with($identityCardFile->getMimeType(), 'image/'));

                                        // Tentukan URL file berdasarkan apakah sudah disimpan atau belum
                                        $fileUrl = is_string($identityCardFile)
                                            ? asset('storage/' . $identityCardFile)
                                            : $identityCardFile->temporaryUrl();
                                    @endphp

                                    <a href="{{ $fileUrl }}" target="_blank" title="Lihat file penuh"
                                        class="group">
                                        @if ($isImage)
                                            {{-- Tampilkan gambar mini jika file adalah gambar --}}
                                            <img src="{{ $fileUrl }}" alt="Pratinjau KTP"
                                                class="w-24 h-16 object-cover rounded-md border border-zinc-300 group-hover:shadow-lg transition-shadow">
                                        @else
                                            {{-- Tampilkan ikon dan nama file jika bukan gambar --}}
                                            <div
                                                class="flex items-center gap-2 text-sm font-medium text-emerald-600 group-hover:text-emerald-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="underline">Lihat Dokumen</span>
                                            </div>
                                        @endif
                                    </a>
                                @else
                                    {{-- Tampilan jika tidak ada file --}}
                                    <span class="text-sm text-gray-400 italic">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">File Komunitas/KK</span>

                            <div>
                                @if ($communityCardFile)
                                    @php
                                        $isImage =
                                            (is_string($communityCardFile) &&
                                                in_array(strtolower(pathinfo($communityCardFile, PATHINFO_EXTENSION)), [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                    'webp',
                                                ])) ||
                                            (!is_string($communityCardFile) &&
                                                str_starts_with($communityCardFile->getMimeType(), 'image/'));
                                        $fileUrl = is_string($communityCardFile)
                                            ? asset('storage/' . $communityCardFile)
                                            : $communityCardFile->temporaryUrl();
                                    @endphp

                                    <a href="{{ $fileUrl }}" target="_blank" title="Lihat file penuh"
                                        class="group">
                                        @if ($isImage)
                                            <img src="{{ $fileUrl }}" alt="Pratinjau Komunitas/KK"
                                                class="w-24 h-16 object-cover rounded-md border border-zinc-300 group-hover:shadow-lg transition-shadow">
                                        @else
                                            <div
                                                class="flex items-center gap-2 text-sm font-medium text-emerald-600 group-hover:text-emerald-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="underline">Lihat Dokumen</span>
                                            </div>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400 italic">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Informasi Akademik (Hanya tampil jika isStudent true) --}}
            @if ($isStudent)
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 011.085.12l3 3a1 1 0 001.414 0l3-3a.999.999 0 011.085-.12l2.86-1.121a1 1 0 000-1.84l-7-3zM10 15a1 1 0 100-2 1 1 0 000 2z" />
                            <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Informasi Akademik
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">NIM</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">{{ $studentId }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Tahun Angkatan</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">{{ $classYear }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Fakultas</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">{{ $faculty }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Program Studi</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">{{ $studyProgram }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Daftar Kontrak --}}
            @if ($contracts && count($contracts) > 0)
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                clip-rule="evenodd" />
                        </svg>
                        Daftar Kontrak
                    </h4>
                    <div class="space-y-3">
                        @foreach ($contracts as $contract)
                            <div
                                class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h5 class="font-semibold text-zinc-800 dark:text-zinc-100">
                                            {{ $contract->unit->room_number }}</h5>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                            {{ $contract->unit->unitCluster->name }}</p>
                                    </div>
                                    <x-managers.ui.badge :type="$contract->status->value" :color="$contract->status->color()">
                                        {{ $contract->status->label() }}
                                    </x-managers.ui.badge>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-300">Periode</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                            {{ $contract->start_date->format('d/m/Y') }} -
                                            {{ $contract->end_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-300">Biaya Bulanan</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                            Rp {{ number_format($contract->total_price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-300">Tanggal Dibuat</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                            {{ $contract->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @if ($contract->notes)
                                        <div class="flex justify-between">
                                            <span class="text-zinc-600 dark:text-zinc-300">Catatan</span>
                                            <span class="font-medium text-zinc-800 dark:text-zinc-100 text-right">
                                                {{ $contract->notes }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                clip-rule="evenodd" />
                        </svg>
                        Daftar Kontrak
                    </h4>
                    <div class="text-center py-8">
                        <div class="text-zinc-400 dark:text-zinc-500 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-zinc-500 dark:text-zinc-400">Belum ada kontrak untuk penghuni ini</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Tombol Tutup --}}
        <div class="flex justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
@endif

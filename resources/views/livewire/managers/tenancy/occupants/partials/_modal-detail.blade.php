@if ($occupantIdBeingSelected)
    <x-managers.ui.modal title="Detail Data Penghuni" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Header Penghuni --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg border border-emerald-100 dark:border-emerald-800">
                <img class="h-12 w-12 rounded-full"
                    src="https://ui-avatars.com/api/?name={{ urlencode($fullName) }}&background=random&color=fff"
                    alt="Avatar">
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
                        <flux:icon name="identification" class="h-5 w-5 text-blue-500" />
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
                        <flux:icon name="information-circle" class="h-5 w-5 text-green-500" />
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
                                                <flux:icon name="document-text" class="h-6 w-6" />
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
                                                <flux:icon name="document-text" class="h-6 w-6" />
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
                        <flux:icon name="academic-cap" class="h-5 w-5 text-indigo-500" />
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
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <flux:icon name="shield-check" class="h-5 w-5 text-orange-500" />
                    Riwayat & Daftar Kontrak
                </h4>

                @if ($contracts && count($contracts) > 0)
                    <div class="space-y-4">
                        @foreach ($contracts as $contract)
                            <div
                                class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 transition-all hover:shadow-md hover:border-emerald-500">
                                {{-- Header Kartu Kontrak --}}
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Unit
                                            {{ $contract->unit->room_number }} •
                                            {{ $contract->unit->unitCluster->name }}</p>
                                        <h5 class="font-bold text-lg text-emerald-600 dark:text-emerald-500">
                                            {{ $contract->contract_code }}</h5>
                                    </div>
                                    <x-managers.ui.badge :color="$contract->status->color()">
                                        {{ $contract->status->label() }}
                                    </x-managers.ui.badge>
                                </div>

                                {{-- Detail Kontrak --}}
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm mt-4 pt-4 border-t border-dashed dark:border-zinc-700">
                                    {{-- Kolom Kiri --}}
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                                            <flux:icon name="calendar" class="h-4 w-4 text-zinc-400" />
                                            <span>{{ $contract->start_date->translatedFormat('d M Y') }} -
                                                {{ $contract->end_date->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                                            <flux:icon name="banknotes" class="h-4 w-4 text-zinc-400" />
                                            <span>Rp {{ number_format($contract->total_price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                                            <flux:icon name="moon" class="h-4 w-4 text-zinc-400" />
                                            <span>{{ $contract->pricing_basis->label() }}</span>
                                        </div>
                                    </div>
                                    {{-- Kolom Kanan --}}
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                                            <flux:icon name="tag" class="h-4 w-4 text-zinc-400" />
                                            <span>{{ $contract->occupantType->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                                            <flux:icon name="clock" class="h-4 w-4 text-zinc-400" />
                                            <span>Dibuat: {{ $contract->created_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Tampilan jika tidak ada kontrak --}}
                    <div class="text-center py-12">
                        <div class="text-zinc-400 dark:text-zinc-500 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-zinc-500 dark:text-zinc-400 font-medium">Penghuni ini belum memiliki kontrak.
                        </p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Daftar kontrak akan muncul di sini
                            setelah dibuat.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tombol Tutup --}}
        <div class="flex justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
@endif

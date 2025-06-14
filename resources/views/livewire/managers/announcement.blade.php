<div class="flex flex-col gap-6">

    <div class="flex flex-col sm:flex-row gap-4">

        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pengumuman..."
            icon="magnifying-glass" class="w-full" />

        <div class="flex gap-4">
            {{-- Add Announcement Button --}}
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah Pengumuman
            </x-managers.ui.button>

            {{-- Dropdown for Filters --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'title', 'label' => 'Judul'],
                        ['value' => 'created_at', 'label' => 'Tanggal Dibuat'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp

                <x-managers.form.small>Filter</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions" label="Semua Status"
                        wire:key="dropdown-status" />
                </div>

                {{-- Sort Filter --}}
                <x-managers.form.small>Urutkan</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                        label="Urutkan Berdasarkan" wire:key="dropdown-order-by" disabled />

                    <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                        wire:key="dropdown-sort" disabled />
                </div>
            </x-managers.ui.dropdown>
        </div>
    </div>

    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="[
            'Judul',
            'Status',
            'Tanggal Dibuat',
            'Deskripsi',
            'Aksi',
        ]">
            <x-managers.table.body>
                @forelse ($announcements as $announcement)
                    <x-managers.table.row wire:key="{{ $announcement->id }}">
                        {{-- Judul (diperbesar jadi w-1/4 atau 25%) --}}
                        <x-managers.table.cell class="w-1/3">
                            <span class="font-bold" style="word-break: break-word;">{{ $announcement->title }}</span>
                        </x-managers.table.cell>

                        {{-- Status (sedikit mengecil jadi w-1/12 atau ~8.33%) --}}
                        <x-managers.table.cell class="w-1/12">
                            @php
                                $statusEnum = \App\Enums\AnnouncementStatus::tryFrom($announcement->status->value);
                            @endphp
                            <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </x-managers.table.cell>

                        {{-- Tanggal Dibuat (sedikit mengecil jadi w-1/6 atau ~16.67%) --}}
                        <x-managers.table.cell class="w-1/6">
                            <span class="text-sm text-gray-700">
                                {{ $announcement->created_at->format('d M Y, H:i') }}
                            </span>
                        </x-managers.table.cell>

                        {{-- Deskripsi (sedikit mengecil jadi w-1/3 atau ~33.33%) --}}
                        <x-managers.table.cell class="w-1/3">
                            <div class="text-sm text-gray-700" style="word-break: break-word;">
                                @if (str_word_count(strip_tags($announcement->description)) > 30)
                                    {!! Str::words(strip_tags($announcement->description), 30, '...') !!}
                                @else
                                    {!! $announcement->description !!}
                                @endif
                            </div>
                        </x-managers.table.cell>

                        {{-- Aksi (tetap w-auto) --}}
                        <x-managers.table.cell class="w-auto">
                            <div class="flex gap-2 justify-start">
                                {{-- Detail Button --}}
                                <x-managers.ui.button wire:click="detail({{ $announcement->id }})" variant="info"
                                    size="sm" title="Lihat Detail">
                                    <flux:icon.eye class="w-4" />
                                </x-managers.ui.button>

                                {{-- Edit Button --}}
                                <x-managers.ui.button wire:click="edit({{ $announcement->id }})" variant="secondary"
                                    size="sm" title="Perbarui">
                                    <flux:icon.pencil class="w-4" />
                                </x-managers.ui.button>

                                {{-- Delete Button --}}
                                <x-managers.ui.button wire:click="confirmDelete({{ $announcement }})" id="delete-announcement"
                                    variant="danger" size="sm" title="Hapus">
                                    <flux:icon.trash class="w-4" />
                                </x-managers.ui.button>

                                {{-- Archive Button (New) --}}
                                @if ($announcement->status->value !== 'archived')
                                    <x-managers.ui.button wire:click="confirmArchive({{ $announcement }})" variant="warning"
                                        size="sm" title="Arsipkan">
                                        <flux:icon.archive-box class="w-4" />
                                    </x-managers.ui.button>
                                @endif

                                {{-- Publish Button (New) --}}
                                @if ($announcement->status->value === 'draft' || $announcement->status->value === 'archived')
                                    <x-managers.ui.button wire:click="confirmPublish({{ $announcement }})" variant="primary"
                                        size="sm" title="Terbitkan">
                                        <flux:icon.document-check class="w-4" />
                                    </x-managers.ui.button>
                                @endif
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data pengumuman ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>

        {{-- Pagination --}}
        <x-managers.ui.pagination :paginator="$announcements" />
    </x-managers.ui.card>

    {{-- Modal Create/Edit --}}
    <x-managers.ui.modal title="Form Pengumuman" :show="$showModal && $modalType === 'form'" class="max-w-md">
        <form wire:submit.prevent="save" class="space-y-4">
            <x-managers.form.label for="title">Judul Pengumuman</x-managers.form.label>
            <x-managers.form.input wire:model.live="title" placeholder="Masukkan judul pengumuman" id="title" />
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            <x-managers.form.label for="description">Deskripsi</x-managers.form.label>
            <x-managers.form.textarea wire:model.live="description" placeholder="Masukkan deskripsi pengumuman" id="description" />
            @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            <x-managers.form.label for="status">Status Pengumuman</x-managers.form.label>
            <x-managers.form.select wire:model.live="status" :options="$statusOptions" label="Pilih Status" id="status" />
            @error('status')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            {{-- Single Image for 'image' column --}}
            <x-managers.form.label>Gambar Utama (Opsional)</x-managers.form.label>
            @if ($existingImage && !$image)
                <div class="relative w-full h-32 mb-2">
                    <img src="{{ asset('storage/' . $existingImage) }}" alt="Gambar Utama"
                        class="w-full h-full object-contain rounded border" />
                    <button type="button" wire:click="$set('existingImage', null); $set('image', null);"
                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            @elseif ($image)
                <div class="relative w-full h-32 mb-2">
                    <img src="{{ $image->temporaryUrl() }}" alt="Gambar Preview"
                        class="w-full h-full object-contain rounded border" />
                    <button type="button" wire:click="$set('image', null)"
                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            @endif
            <input type="file" wire:model="image" class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100" />
            @error('image')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @else
                <x-managers.form.small>Max 2MB. Tipe: JPG, PNG, GIF, WEBP. Hanya satu gambar.</x-managers.form.small>
            @enderror

            {{-- Attachments for 'attachments' morphMany --}}
            <x-managers.form.label>Lampiran (Gambar atau File Lain)</x-managers.form.label>

            {{-- Existing Attachments while Editing --}}
            @if ($existingAttachments && count($existingAttachments) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-300 rounded p-2 mb-2">
                    <x-managers.form.small class="col-span-full">Lampiran Saat Ini</x-managers.form.small>
                    @foreach ($existingAttachments as $attachment)
                        <div class="relative" wire:key="existing-attachment-{{ $attachment['id'] }}">
                            @if (str_starts_with($attachment['mime_type'], 'image/'))
                                <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}"
                                    class="w-full h-16 object-cover rounded border" />
                            @else
                                <div class="w-full h-16 flex items-center justify-center bg-gray-100 rounded border text-gray-500 text-xs text-center p-1 overflow-hidden">
                                    <flux:icon.document class="w-5 h-5 mr-1" /> {{ $attachment['name'] }}
                                </div>
                            @endif
                            <button type="button" wire:click="queueAttachmentForDeletion({{ $attachment['id'] }})"
                                wire:loading.attr="disabled" wire:target="queueAttachmentForDeletion({{ $attachment['id'] }})"
                                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                                <flux:icon name="x-mark" class="w-3 h-3" />
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div wire:key="filepond-attachments-wrapper">
                <x-filepond::upload wire:model.live="attachments" multiple max-file-size="5MB" />
            </div>

            <div class="mt-2">
                @error('attachments.*')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @else
                    <x-managers.form.small>Max 5MB per file. Bisa upload banyak gambar atau file (PDF, DOC, dll).</x-managers.form.small>
                @enderror
            </div>

            <div class="flex justify-end gap-2 mt-10">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button wire:click="save()" variant="primary">
                    Simpan
                </x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.modal>

    {{-- Modal Detail --}}
    @if ($announcementIdBeingEdited)
        <x-managers.ui.modal title="Detail Pengumuman" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
            <div class="space-y-6">
                {{-- Announcement Header --}}
                <div
                    class="flex items-center gap-4 p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-lg border border-teal-100">
                    <div class="p-3 bg-teal-500 rounded-full">
                        <flux:icon.megaphone class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $title }}</h3>
                        <p class="text-sm text-gray-600">Detail informasi pengumuman</p>
                    </div>
                </div>


                {{-- Main Image --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.photo class="w-5 h-5 text-indigo-500" />
                        Gambar Utama
                    </h4>
                    @if ($existingImage)
                        <div class="flex justify-center">
                            <img src="{{ asset('storage/' . $existingImage) }}" alt="Gambar Utama Pengumuman"
                                class="max-w-full h-auto max-h-64 object-contain rounded-lg border border-gray-200" />
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-4">
                            Tidak ada gambar utama.
                        </div>
                    @endif
                </div>

                {{-- Announcement Information --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-500" />
                        Informasi Detail
                    </h4>
                    <div class="space-y-3">
                        {{-- Status --}}
                        <div class="py-2">
                            <span class="text-gray-600 font-semibold block mb-1">Status</span>
                            @php
                                $statusEnum = \App\Enums\AnnouncementStatus::tryFrom($status);
                            @endphp
                            <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        {{-- Judul --}}
                        <div class="py-2">
                            <span class="text-gray-600 font-semibold block mb-1">Judul</span>
                            <p class="font-bold text-lg text-blue-600">{{ $title }}</p>
                        </div>
                        {{-- Deskripsi --}}
                        <div class="py-2">
                            <span class="text-gray-600 font-semibold block mb-1">Deskripsi</span>
                            <p class="text-gray-800 text-base leading-relaxed">{!! $description ?? '-' !!}</p>
                        </div>
                    </div>
                </div>

                {{-- Attachments --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.paper-clip class="w-5 h-5 text-orange-500" />
                        Lampiran
                    </h4>
                    @if (!empty($existingAttachments) && count($existingAttachments) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($existingAttachments as $index => $attachment)
                                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"
                                    class="relative group block w-full h-24 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center overflow-hidden hover:shadow-md transition-shadow">
                                    @if (str_starts_with($attachment->mime_type, 'image/'))
                                        <img src="{{ asset('storage/' . $attachment->path) }}" alt="{{ $attachment->name }}"
                                            class="w-full h-full object-cover" />
                                    @else
                                        <div class="flex flex-col items-center justify-center text-gray-500 p-2 text-center text-sm">
                                            <flux:icon.document-text class="w-6 h-6 mb-1" />
                                            <span class="truncate w-full">{{ $attachment->name }}</span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-4">
                            Tidak ada lampiran.
                        </div>
                    @endif
                </div>

                {{-- Timestamps Card (masih di modal detail) --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.clock class="w-5 h-5 text-gray-500" />
                        Informasi Waktu
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tanggal Dibuat</span>
                            <span class="font-medium">
                                @if ($createdAt)
                                    {{ $createdAt->format('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Terakhir Diperbarui</span>
                            <span class="font-medium">
                                @if ($updatedAt)
                                    {{ $updatedAt->format('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-managers.ui.button type="button" variant="danger" wire:click="$set('showModal', false)">
                    Tutup
                </x-managers.ui.button>
            </div>
        </x-managers.ui.modal>
    @endif

</div>
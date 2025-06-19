<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Judul', 'Kategori', 'Status', 'Tanggal Dibuat', 'Isi Pengumuman', 'Aksi']">
        <x-managers.table.body>
            @forelse ($announcements as $announcement)
            <x-managers.table.row wire:key="{{ $announcement->id }}">
                {{-- Judul (diperbesar jadi w-1/4 atau 25%) --}}
                <x-managers.table.cell class="w-1/4"> {{-- Ubah width agar ada ruang untuk kategori --}}
                    <span class="font-bold" style="word-break: break-word;">{{ $announcement->title }}</span>
                </x-managers.table.cell>

                {{-- Kategori (ukuran baru) --}}
                <x-managers.table.cell class="w-1/12"> {{-- Sesuaikan lebar kolom --}}
                    @php
                    $categoryEnum = \App\Enums\AnnouncementCategory::tryFrom($announcement->category->value);
                    @endphp
                    <x-managers.ui.badge :type="$categoryEnum?->value ?? 'default'" :color="$categoryEnum?->color()">
                        {{ $categoryEnum?->label() }}
                    </x-managers.ui.badge>
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

                {{-- Deskripsi (sedikit mengecil jadi w-1/4 atau ~25%, disesuaikan setelah penambahan kategori) --}}
                <x-managers.table.cell class="w-1/4"> {{-- Ubah width agar ada ruang untuk kategori --}}
                    <div class="trix-content" style="word-break: break-word;">
                        @php
                        $description = $announcement->description;
                        $maxLength = 100; // Adjust this character limit as needed for smaller column

                        // Check if the description is longer than the max length
                        if (mb_strlen(strip_tags($description)) > $maxLength) {
                            // Find the first space after the max length to avoid breaking words
                            $truncatedDescription = mb_substr($description, 0, $maxLength);
                            $lastSpace = mb_strrpos($truncatedDescription, ' ');
                            if ($lastSpace !== false) {
                                $truncatedDescription = mb_substr($truncatedDescription, 0, $lastSpace);
                            }
                            $truncatedDescription .= '...';
                        } else {
                            $truncatedDescription = $description;
                        }
                        @endphp
                        {!! $truncatedDescription !!}
                    </div>
                </x-managers.table.cell>

                {{-- Aksi (tetap w-auto) --}}
                <x-managers.table.cell class="w-auto">
                    <div class="flex gap-2 justify-start">
                        {{-- Detail Button --}}
                        <x-managers.ui.button wire:click="detail({{ $announcement->id }})" variant="info"
                            size="sm" title="Lihat Detail Pengumuman">
                            <flux:icon.eye class="w-4" />
                        </x-managers.ui.button>

                        {{-- Edit Button --}}
                        <x-managers.ui.button wire:click="edit({{ $announcement->id }})" variant="secondary"
                            size="sm" title="Edit Pengumuman">
                            <flux:icon.pencil class="w-4" />
                        </x-managers.ui.button>

                        {{-- Delete Button --}}
                        <x-managers.ui.button wire:click="confirmDelete({{ $announcement }})" id="delete-announcement"
                            variant="danger" size="sm" title="Hapus Pengumuman">
                            <flux:icon.trash class="w-4" />
                        </x-managers.ui.button>

                        {{-- Archive Button --}}
                        {{-- Tombol Arsipkan hanya muncul jika status BUKAN 'draft' dan BUKAN 'archived' --}}
                        @if ($announcement->status->value !== 'draft' && $announcement->status->value !== 'archived')
                        <x-managers.ui.button wire:click="confirmArchive({{ $announcement }})" variant="warning"
                            size="sm" title="Arsipkan Pengumuman">
                            <flux:icon.archive-box class="w-4" />
                        </x-managers.ui.button>
                        @endif

                        {{-- Publish Button --}}
                        {{-- Tombol Terbitkan hanya muncul jika status adalah 'draft' ATAU 'archived' --}}
                        @if ($announcement->status->value === 'draft' || $announcement->status->value === 'archived')
                        <x-managers.ui.button wire:click="confirmPublish({{ $announcement }})" variant="primary"
                            size="sm" title="Terbitkan Pengumuman">
                            <flux:icon.document-check class="w-4" />
                        </x-managers.ui.button>
                        @endif
                    </div>
                </x-managers.table.cell>
            </x-managers.table.row>
            @empty
            <x-managers.table.row>
                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                    Tidak ada data pengumuman ditemukan.
                </td>
            </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$announcements" />
</x-managers.ui.card>
<x-managers.ui.card class="p-0">
    {{-- Menambahkan kelas w-1/4 dan w-2/5 pada header untuk mengatur lebar kolom Nama Lengkap dan Pesan --}}
    <x-managers.table.table :headers="['Nama Lengkap', 'Telepon', 'Email', 'Pesan']"
        :headerWidths="['w-1/4', 'w-auto', 'w-auto', 'w-2/5', 'w-auto', 'w-auto']"> {{-- 'w-auto' untuk kolom lainnya agar lebar disesuaikan secara otomatis --}}
        <x-managers.table.body>
            {{-- Loop melalui setiap pertanyaan tamu --}}
            @forelse ($guestQuestions as $question)
                {{-- Setiap baris tabel diwakili oleh x-managers.table.row --}}
                {{-- `wire:key` penting untuk kinerja Livewire saat mengulang elemen --}}
                {{-- Menambahkan kelas bg-blue-50 jika pertanyaan belum dibaca untuk penandaan visual --}}
                <x-managers.table.row wire:key="{{ $question->id }}" class="@if(!$question->is_read) bg-blue-50 @endif hover:bg-gray-100 transition duration-150 ease-in-out">
                    {{-- Nama Lengkap --}}
                    {{-- Menambahkan kelas w-1/4 pada cell agar konsisten dengan header --}}
                    <x-managers.table.cell class="w-1/4">
                        <span class="font-bold" style="word-break: break-word;">{{ $question->fullName }}</span>
                    </x-managers.table.cell>

                    {{-- Telepon --}}
                    <x-managers.table.cell class="w-auto">
                        <span class="text-sm text-gray-700">{{ $question->formPhoneNumber ?? '-' }}</span>
                    </x-managers.table.cell>

                    {{-- Email --}}
                    <x-managers.table.cell class="w-auto">
                        <span class="text-sm text-gray-700">{{ $question->formEmail ?? '-' }}</span>
                    </x-managers.table.cell>

                    {{-- Pesan --}}
                    {{-- Menambahkan kelas w-2/5 pada cell agar konsisten dengan header --}}
                    <x-managers.table.cell class="w-2/5">
                        {{-- Menampilkan pesan lengkap tanpa pemotongan --}}
                        <span class="text-sm text-gray-700" style="word-break: break-word;">
                            {{ $question->message }}
                        </span>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                {{-- Tampilan jika tidak ada pertanyaan tamu --}}
                <x-managers.table.row>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        Tidak ada pertanyaan tamu yang tersedia.
                    </td>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    {{-- Meneruskan objek paginator ($guestQuestions) ke komponen paginasi --}}
    <x-managers.ui.pagination :paginator="$guestQuestions" />
</x-managers.ui.card>
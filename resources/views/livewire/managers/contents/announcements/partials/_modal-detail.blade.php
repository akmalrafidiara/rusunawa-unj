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
                Gambar Banner
            </h4>
            @if ($existingImage)
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $existingImage) }}" alt="Gambar Utama Pengumuman"
                    class="max-w-full h-auto max-h-64 object-contain rounded-lg border border-gray-200" />
            </div>
            @else
            <div class="text-center text-gray-500 py-4">
                Tidak ada gambar banner.
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
                    <span class="text-gray-600 font-semibold block mb-1">Isi Pengumuman</span>
                    <div class="trix-content">
                        {!! $description ?? '-' !!}
                    </div>
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
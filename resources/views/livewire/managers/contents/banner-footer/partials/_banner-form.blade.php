{{-- BAGIAN BANNER --}}
<x-managers.ui.card>
    <h5 class="mb-4 font-bold">Banner</h5>

    <form wire:submit.prevent="saveBanner">
        {{-- Judul Banner (input form) --}}
        <div class="mb-3">
            <label for="bannerTitleInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                Judul Banner <span class='text-red-500'>*</span>
            </label>
            <flux:input
                id="bannerTitleInput"
                type="text"
                wire:model="bannerTitle"
                placeholder="Rusunawa Universitas Negeri Jakarta"
                class="placeholder-gray-400"
            />
            @error('bannerTitle')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Teks Banner (TEXTAREA BIASA DENGAN AUTO-RESIZE) --}}
        <div class="mb-3">
            <label for="bannerTextInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                Teks Banner <span class='text-red-500'>*</span>
            </label>
            <textarea
                id="bannerTextInput"
                wire:model.live="bannerText"
                placeholder="Sebuah solusi tempat tinggal praktis di lingkungan kampus, ideal untuk mendukung aktivitas harian Anda"
                maxlength="200"
                rows="3" {{-- Tinggi awal --}}
                class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        overflow-hidden resize-none placeholder-gray-400"
                x-data="{
                    resize() {
                        $el.style.height = 'auto'; // Reset height to recalculate
                        $el.style.height = $el.scrollHeight + 'px'; // Set height based on content
                    }
                }"
                x-init="resize()" {{-- Panggil saat inisialisasi --}}
                @input="resize()" {{-- Panggil setiap kali input berubah --}}
            ></textarea>
            <div class="text-right text-gray-500 mt-1">
                <small>{{ strlen($bannerText) }}/200</small>
            </div>
            @error('bannerText')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Daya Tarik Banner --}}
        <div class="mb-3">
            <x-managers.form.label>Daya Tarik</x-managers.form.label>
            @if (!empty($dayaTariks))
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mb-2">
                    @foreach ($dayaTariks as $index => $item)
                        <div class="flex items-center gap-2" wire:key="daya-tarik-item-wrapper-{{ $index }}">
                            <p class="text-gray-700 dark:text-gray-300 flex-grow py-2 px-3 border border-gray-300 rounded-md">
                                {{ $item['value'] ?? '' }} | {{ $item['label'] ?? '' }}
                            </p>
                            <x-managers.ui.button
                                wire:click="removeDayaTarik({{ $index }})"
                                variant="danger"
                                size="sm"
                                icon="trash" />
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada daya tarik.</p>
            @endif

            <div class="mt-3 flex gap-2">
                <div class="flex-grow">
                    <label for="newDayaTarikValueInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Value
                    </label>
                    <flux:input
                        id="newDayaTarikValueInput"
                        wire:model.live="newDayaTarikValue"
                        placeholder="Contoh: 50+"
                        class="placeholder-gray-400"
                    />
                    @error('newDayaTarikValue')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-grow">
                    <label for="newDayaTarikLabelInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Label
                    </label>
                    <flux:input
                        id="newDayaTarikLabelInput"
                        wire:model.live="newDayaTarikLabel"
                        placeholder="Contoh: Kamar Siap Huni"
                        class="placeholder-gray-400"
                    />
                    @error('newDayaTarikLabel')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <x-managers.ui.button wire:click="addDayaTarik()" variant="primary" size="sm" icon="plus" class="self-end">
                    Tambah Daya Tarik
                </x-managers.ui.button>
            </div>
        </div>

        {{-- Foto Update Banner (Kustom Tailwind CSS untuk input file) --}}
        <div class="mb-3">
            <x-managers.form.label for="bannerImageInput">Foto Update <span class="text-red-500">*</span></x-managers.form.label>
            <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1">
                <input
                    type="file"
                    wire:model="bannerImage"
                    id="bannerImageInput"
                    accept="image/jpeg,image/png"
                    maxlength="2048"
                    class="hidden">
                <button type="button"
                    onclick="document.getElementById('bannerImageInput').click()"
                    class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Choose File
                </button>
                <span class="text-gray-700 dark:text-gray-300 text-sm truncate">
                    {{ $bannerImage ? $bannerImage->getClientOriginalName() : 'No file chosen' }}
                </span>
            </div>
            @error('bannerImage')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @else
                <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p>
            @enderror

            @if ($bannerImage)
            <div class="mt-2">
                <img src="{{ $bannerImage->temporaryUrl() }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                <p class="text-gray-500 text-sm mt-1">Preview gambar baru</p>
            </div>
            @elseif ($existingBannerImageUrl)
            <div class="mt-2">
                <img src="{{ $existingBannerImageUrl }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                <p class="text-gray-500 text-sm mt-1">Gambar saat ini</p>
            </div>
            @endif
        </div>

        <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
    </form>
</x-managers.ui.card>
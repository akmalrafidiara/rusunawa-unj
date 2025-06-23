<div>
    <x-managers.ui.card title="Tentang Kami">
        <form wire:submit.prevent="save">
            {{-- Foto Tentang Kami (Kustom Tailwind CSS untuk input file) --}}
            <div class="mb-3">
                <x-managers.form.label for="aboutImageInput">Foto Tentang Kami <span class="text-red-500">*</span></x-managers.form.label>
                <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1">
                    <input
                        type="file"
                        wire:model="aboutImage"
                        id="aboutImageInput"
                        accept="image/jpeg,image/png"
                        maxlength="2048"
                        class="hidden"
                    >
                    <button type="button"
                            onclick="document.getElementById('aboutImageInput').click()"
                            class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Choose File
                    </button>
                    <span class="text-gray-700 dark:text-gray-300 text-sm truncate">
                        {{ $aboutImage ? $aboutImage->getClientOriginalName() : 'No file chosen' }}
                    </span>
                </div>
                @error('aboutImage')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p>

                @if ($aboutImage)
                    <div class="mt-2">
                        <img src="{{ $aboutImage->temporaryUrl() }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Preview gambar baru</p>
                    </div>
                @elseif ($existingAboutImageUrl)
                    <div class="mt-2">
                        <img src="{{ $existingAboutImageUrl }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Gambar saat ini</p>
                    </div>
                @endif
            </div>

            {{-- Judul Tentang Kami --}}
            <div class="mb-3">
                <label for="aboutTitleInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Judul Tentang Kami <span class='text-red-500'>*</span>
                </label>
                <flux:input
                    id="aboutTitleInput"
                    type="text"
                    wire:model="aboutTitle"
                    placeholder="Hunian Ideal & Nyaman di Area Kampus"
                />
                @error('aboutTitle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teks Tentang Kami --}}
            <div class="mb-3">
                <label for="aboutDescriptionInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Teks Tentang Kami <span class='text-red-500'>*</span>
                </label>
                <textarea
                    id="aboutDescriptionInput"
                    wire:model.live="aboutDescription"
                    placeholder="Rusunawa UNJ merupakan fasilitas hunian..."
                    maxlength="500"
                    rows="5"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                           overflow-hidden resize-none"
                    x-data="{
                        resize() {
                            $el.style.height = 'auto';
                            $el.style.height = $el.scrollHeight + 'px';
                        }
                    }"
                    x-init="resize()"
                    @input="resize()"
                ></textarea>
                <div class="text-right text-gray-500 mt-1">
                    <small>{{ strlen($aboutDescription) }}/500</small>
                </div>
                @error('aboutDescription')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Daya Tarik --}} {{-- Label diubah dari Fasilitas menjadi Daya Tarik --}}
            <div class="mb-3">
                <x-managers.form.label>Daya Tarik</x-managers.form.label>
                @if (!empty($dayaTariks))
                    @php
                        // Bagi menjadi 3 kolom ke kanan, urutannya tetap, dan jika lebih dari 3, lanjut ke baris berikutnya (grid)
                        $colCount = 3;
                        $rows = [];
                        foreach ($dayaTariks as $i => $dayaTarik) {
                            $row = intdiv($i, $colCount);
                            $col = $i % $colCount;
                            $rows[$row][$col] = ['index' => $i, 'value' => $dayaTarik];
                        }
                    @endphp
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($rows as $row)
                            <div class="flex gap-3">
                                @for ($col = 0; $col < $colCount; $col++)
                                    <div class="flex-1">
                                        @if (isset($row[$col]))
                                            <div class="flex items-center gap-2" wire:key="daya-tarik-{{ $row[$col]['index'] }}">
                                                <flux:input
                                                    wire:model.live="dayaTariks.{{ $row[$col]['index'] }}"
                                                    placeholder="Contoh: Lokasi Strategis, Fasilitas Lengkap"
                                                    :error="$errors->first('dayaTariks.' . $row[$col]['index'])"
                                                    class="flex-grow"
                                                />
                                                <x-managers.ui.button
                                                    wire:click="removeDayaTarik({{ $row[$col]['index'] }})"
                                                    variant="danger"
                                                    size="sm"
                                                    icon="trash"
                                                />
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        @endforeach
                    </div>
                    @if (empty($dayaTariks))
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada daya tarik.</p>
                    @endif
                @endif

                <div class="mt-3 flex gap-2">
                    <flux:input
                        wire:model.live="newDayaTarik" {{-- Menggunakan newDayaTarik --}}
                        placeholder="Masukkan daya tarik baru..."
                        :error="$errors->first('newDayaTarik')" {{-- Menggunakan newDayaTarik --}}
                        class="flex-grow"
                    />
                    <x-managers.ui.button wire:click="addDayaTarik()" variant="primary" size="sm" icon="plus"> {{-- Mengubah metode --}}
                        Tambah Daya Tarik {{-- Mengubah teks tombol --}}
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Tombol Update --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
<div>
    <x-managers.ui.card title="Layanan Pengaduan"> {{-- Mengubah judul card --}}
        <form wire:submit.prevent="save">
            {{-- Foto Layanan Pengaduan (Kustom Tailwind CSS untuk input file) --}}
            <div class="mb-3">
                <x-managers.form.label for="complaintImageInput">Foto Layanan Pengaduan <span class="text-red-500">*</span></x-managers.form.label> {{-- Mengubah label --}}
                <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1">
                    <input
                        type="file"
                        wire:model="complaintImage"
                        id="complaintImageInput"
                        accept="image/jpeg,image/png"
                        maxlength="2048"
                        class="hidden"
                    >
                    <button type="button"
                            onclick="document.getElementById('complaintImageInput').click()"
                            class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Choose File
                    </button>
                    <span class="text-gray-700 dark:text-gray-300 text-sm truncate">
                        {{ $complaintImage ? $complaintImage->getClientOriginalName() : 'No file chosen' }}
                    </span>
                </div>
                @error('complaintImage')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p>

                @if ($complaintImage)
                    <div class="mt-2">
                        <img src="{{ $complaintImage->temporaryUrl() }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Preview gambar baru</p>
                    </div>
                @elseif ($existingComplaintImageUrl)
                    <div class="mt-2">
                        <img src="{{ $existingComplaintImageUrl }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Gambar saat ini</p>
                    </div>
                @endif
            </div>

            {{-- Judul Layanan Pengaduan --}}
            <div class="mb-3">
                <label for="complaintTitleInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Judul Layanan Pengaduan <span class='text-red-500'>*</span>
                </label>
                <flux:input
                    id="complaintTitleInput"
                    type="text"
                    wire:model="complaintTitle"
                    placeholder="Layanan Aduan Cepat dan Efektif"
                />
                @error('complaintTitle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi Layanan Pengaduan --}}
            <div class="mb-3">
                <label for="complaintDescriptionInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Deskripsi Layanan Pengaduan <span class='text-red-500'>*</span>
                </label>
                <textarea
                    id="complaintDescriptionInput"
                    wire:model.live="complaintDescription"
                    placeholder="Kami menyediakan layanan pengaduan yang mudah dan transparan..."
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
                    <small>{{ strlen($complaintDescription) }}/500</small>
                </div>
                @error('complaintDescription')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keunggulan --}} {{-- Label diubah dari Daya Tarik menjadi Keunggulan --}}
            <div class="mb-3">
                <x-managers.form.label>Keunggulan</x-managers.form.label>
                @if (!empty($advantages))
                    @php
                        $colCount = 3;
                        $rows = [];
                        foreach ($advantages as $i => $advantage) { // This is fine
                            $row = intdiv($i, $colCount);
                            $col = $i % $colCount;
                            $rows[$row][$col] = ['index' => $i, 'value' => $advantage]; // This is fine
                        }
                    @endphp
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($rows as $row)
                            <div class="flex gap-3">
                                @for ($col = 0; $col < $colCount; $col++)
                                    <div class="flex-1">
                                        @if (isset($row[$col]))
                                            <div class="flex items-center gap-2" wire:key="advantage-{{ $row[$col]['index'] }}">
                                                <flux:input
                                                    wire:model.live="advantages.{{ $row[$col]['index'] }}"
                                                    placeholder="Contoh: Proses Cepat, Respon Tanggap"
                                                    :error="$errors->first('advantages.' . $row[$col]['index'])"
                                                    class="flex-grow"
                                                />
                                                <x-managers.ui.button
                                                    wire:click="removeAdvantage({{ $row[$col]['index'] }})"
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
                    @if (empty($advantages))
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada keunggulan.</p>
                    @endif
                @endif

                <div class="mt-3 flex gap-2">
                    <flux:input
                        wire:model.live="newAdvantage"
                        placeholder="Masukkan keunggulan baru..."
                        :error="$errors->first('newAdvantage')"
                        class="flex-grow"
                    />
                    <x-managers.ui.button wire:click="addAdvantage()" variant="primary" size="sm" icon="plus">
                        Tambah Keunggulan
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Tombol Update --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
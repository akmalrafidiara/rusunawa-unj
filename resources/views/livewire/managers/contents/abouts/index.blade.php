<div>
    <x-managers.ui.card title="Tentang Kami">
        <form wire:submit.prevent="save">
            {{-- Foto Tentang Kami (Kustom Tailwind CSS untuk input file) --}}
            <div class="mb-3">
                <x-managers.form.label for="aboutImageInput">Foto Tentang Kami <span class="text-red-500">*</span></x-managers.form.label>
                <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1"> {{-- Outline dan padding --}}
                    <input
                        type="file"
                        wire:model="aboutImage"
                        id="aboutImageInput" {{-- Ganti ID agar unik --}}
                        accept="image/jpeg,image/png"
                        maxlength="2048" {{-- Pastikan maxlength di sini jika ada validasi front-end --}}
                        class="hidden" {{-- Sembunyikan input asli --}}
                    >
                    <button type="button"
                            onclick="document.getElementById('aboutImageInput').click()"
                            class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Choose File
                    </button>
                    <span class="text-gray-700 dark:text-gray-300 text-sm truncate"> {{-- Nama file --}}
                        {{ $aboutImage ? $aboutImage->getClientOriginalName() : 'No file chosen' }}
                    </span>
                </div>
                @error('aboutImage')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> {{-- Styling error --}}
                @enderror
                <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p> {{-- Hint --}}

                {{-- Pratinjau Gambar Sementara (saat diupload) --}}
                @if ($aboutImage)
                    <div class="mt-2">
                        <img src="{{ $aboutImage->temporaryUrl() }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Preview gambar baru</p>
                    </div>
                {{-- Pratinjau Gambar yang Sudah Ada --}}
                @elseif ($existingAboutImageUrl)
                    <div class="mt-2">
                        <img src="{{ $existingAboutImageUrl }}" style="max-width: 200px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Gambar saat ini</p>
                    </div>
                @endif
            </div>

            {{-- Judul Tentang Kami --}}
            <div class="mb-3">
                <flux:input
                    label="Judul Tentang Kami"
                    type="text"
                    wire:model="aboutTitle"
                    placeholder="Hunian Ideal & Nyaman di Area Kampus"
                    required
                    :error="$errors->first('aboutTitle')"
                />
            </div>

            {{-- Teks Tentang Kami --}}
            <div class="mb-3">
                <flux:input
                    label="Teks Tentang Kami" {{-- Label diubah agar lebih spesifik --}}
                    type="textarea"
                    rows="5"
                    wire:model.live="aboutDescription"
                    placeholder="Rusunawa UNJ merupakan fasilitas hunian..."
                    required
                    maxlength="500"
                    :error="$errors->first('aboutDescription')"
                >
                    <x-slot name="after">
                        <div class="text-right text-gray-500 mt-1">
                            <small>{{ strlen($aboutDescription) }}/500</small>
                        </div>
                    </x-slot>
                </flux:input>
            </div>

            {{-- Fasilitas --}}
            <div class="mb-3">
                <x-managers.form.label>Fasilitas</x-managers.form.label>
                @if (!empty($facilities))
                    @foreach ($facilities as $index => $facility)
                        <div class="flex items-center gap-2 mb-2" wire:key="facility-{{ $index }}">
                            <flux:input
                                wire:model.live="facilities.{{ $index }}"
                                placeholder="Contoh: AC, Dapur"
                                :error="$errors->first('facilities.' . $index)"
                                class="flex-grow" {{-- flex-grow-1 diubah menjadi flex-grow untuk Tailwind --}}
                            />
                            <x-managers.ui.button
                                wire:click="removeFacility({{ $index }})"
                                variant="danger"
                                size="sm"
                                icon="trash"
                            />
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada fasilitas.</p>
                @endif

                <div class="mt-3 flex gap-2">
                    <flux:input
                        wire:model.live="newFacility"
                        placeholder="Masukkan fasilitas baru..."
                        :error="$errors->first('newFacility')"
                        class="flex-grow" {{-- flex-grow-1 diubah menjadi flex-grow untuk Tailwind --}}
                    />
                    <x-managers.ui.button wire:click="addFacility()" variant="primary" size="sm" icon="plus">
                        Tambah
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Tombol Update --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
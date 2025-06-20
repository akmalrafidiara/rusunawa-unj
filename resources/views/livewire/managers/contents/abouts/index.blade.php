<div>
    <x-managers.ui.card title="Tentang Kami">
        <form wire:submit.prevent="save">
            {{-- Foto Tentang Kami (Standard Livewire Upload) --}}
            <div class="mb-3">
                <x-managers.form.label for="aboutImage">Foto Tentang Kami <span class="text-danger">*</span></x-managers.form.label>
                <div class="input-group">
                    {{-- wire:model langsung ke properti aboutImage --}}
                    <input type="file" class="form-control" id="aboutImage" wire:model="aboutImage" accept="image/jpeg,image/png">
                    <span class="input-group-text">Pilih File</span> {{-- Ganti label Upload menjadi Pilih File --}}
                </div>
                <div class="form-text text-muted">
                    Format file .jpg, .jpeg, .png (Maksimal 2MB)
                </div>
                @error('aboutImage') <span class="text-danger">{{ $message }}</span> @enderror

                {{-- Pratinjau Gambar Sementara (saat diupload) --}}
                @if ($aboutImage)
                    <div class="mt-2">
                        <img src="{{ $aboutImage->temporaryUrl() }}" style="max-width: 200px; height: auto;" class="img-thumbnail">
                        <p class="text-muted mt-1"><small>Preview gambar baru</small></p>
                    </div>
                {{-- Pratinjau Gambar yang Sudah Ada --}}
                @elseif ($existingAboutImageUrl)
                    <div class="mt-2">
                        <img src="{{ $existingAboutImageUrl }}" style="max-width: 200px; height: auto;" class="img-thumbnail">
                        <p class="text-muted mt-1"><small>Gambar saat ini</small></p>
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

            {{-- Teks Banner --}}
            <div class="mb-3">
                <flux:input
                    label="Teks Banner"
                    type="textarea"
                    rows="5"
                    wire:model.live="aboutDescription"
                    placeholder="Rusunawa UNJ merupakan fasilitas hunian..."
                    required
                    maxlength="500"
                    :error="$errors->first('aboutDescription')"
                >
                    <x-slot name="after">
                        <div class="text-end text-muted mt-1">
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
                                class="flex-grow-1"
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
                        class="flex-grow-1"
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
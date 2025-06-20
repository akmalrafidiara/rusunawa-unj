<div>
    {{-- BAGIAN BANNER --}}
    <x-managers.ui.card title="Banner">
        <form wire:submit.prevent="saveBanner">
            {{-- Judul Banner --}}
            <div class="mb-3">
                <flux:input
                    label="Judul Banner"
                    type="text"
                    wire:model="bannerTitle"
                    placeholder="Rusunawa Universitas Negeri Jakarta"
                    required
                    :error="$errors->first('bannerTitle')"
                />
            </div>

            {{-- Teks Banner --}}
            <div class="mb-3">
                <flux:input
                    label="Teks Banner"
                    type="textarea"
                    rows="3"
                    wire:model.live="bannerText"
                    placeholder="Sebuah solusi tempat tinggal praktis di lingkungan kampus, ideal untuk mendukung aktivitas harian Anda"
                    required
                    maxlength="200"
                    :error="$errors->first('bannerText')"
                >
                    <x-slot name="after">
                        <div class="text-end text-muted mt-1">
                            <small>{{ strlen($bannerText) }}/200</small>
                        </div>
                    </x-slot>
                </flux:input>
            </div>

            {{-- Daya Tarik Banner (Previously Fasilitas) --}}
            <div class="mb-3">
                <x-managers.form.label>Daya Tarik</x-managers.form.label>
                @if (!empty($dayaTariks))
                    @foreach ($dayaTariks as $index => $dayaTarik)
                        <div class="flex items-center gap-2 mb-2" wire:key="daya-tarik-banner-{{ $index }}">
                            <flux:input
                                wire:model.live="dayaTariks.{{ $index }}"
                                placeholder="Contoh: 50+ Kamar Siap Huni"
                                :error="$errors->first('dayaTariks.' . $index)"
                                class="flex-grow-1"
                            />
                            <x-managers.ui.button
                                wire:click="removeDayaTarik({{ $index }})"
                                variant="danger"
                                size="sm"
                                icon="trash"
                            />
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada daya tarik.</p>
                @endif

                <div class="mt-3 flex gap-2">
                    <flux:input
                        wire:model.live="newDayaTarik"
                        placeholder="Deskripsi Daya Tarik..."
                        :error="$errors->first('newDayaTarik')"
                        class="flex-grow-1"
                    />
                    {{-- Tombol "Tambah" diubah variant="secondary" menjadi variant="primary" --}}
                    <x-managers.ui.button wire:click="addDayaTarik()" variant="primary" size="sm" icon="plus">
                        Tambah Daya Tarik
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Foto Update Banner --}}
            <div class="mb-3">
                <x-managers.form.label for="bannerImage">Foto Update <span class="text-danger">*</span></x-managers.form.label>
                <div class="input-group">
                    <input type="file" class="form-control" id="bannerImage" wire:model="bannerImage" accept="image/jpeg,image/png">
                    <span class="input-group-text">Upload</span>
                </div>
                <div class="form-text text-muted">
                    Format file .jpg, .jpeg, .png (Maksimal 2MB)
                </div>
                @error('bannerImage') <span class="text-danger">{{ $message }}</span> @enderror

                @if ($bannerImage)
                    <div class="mt-2">
                        <img src="{{ $bannerImage->temporaryUrl() }}" style="max-width: 200px; height: auto;" class="img-thumbnail">
                        <p class="text-muted mt-1"><small>Preview gambar baru</small></p>
                    </div>
                @elseif ($existingBannerImageUrl)
                    <div class="mt-2">
                        <img src="{{ $existingBannerImageUrl }}" style="max-width: 200px; height: auto;" class="img-thumbnail">
                        <p class="text-muted mt-1"><small>Gambar saat ini</small></p>
                    </div>
                @endif
            </div>

            {{-- Tombol "Update" Banner --}}
            {{-- Mengubah variant="success" menjadi variant="primary" --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>

    {{-- BAGIAN FOOTER --}}
    <x-managers.ui.card title="Footer" class="mt-4">
        <form wire:submit.prevent="saveFooter">
            {{-- Logo Footer --}}
            <div class="mb-3">
                <x-managers.form.label for="footerLogo">Logo Footer <span class="text-danger">*</span></x-managers.form.label>
                <div class="input-group">
                    <input type="file" class="form-control" id="footerLogo" wire:model="footerLogo" accept="image/jpeg,image/png">
                    <span class="input-group-text">Upload</span>
                </div>
                <div class="form-text text-muted">
                    Format file .jpg, .jpeg, .png (Maksimal 2MB)
                </div>
                @error('footerLogo') <span class="text-danger">{{ $message }}</span> @enderror

                @if ($footerLogo)
                    <div class="mt-2">
                        <img src="{{ $footerLogo->temporaryUrl() }}" style="max-width: 150px; height: auto;" class="img-thumbnail">
                        <p class="text-muted mt-1"><small>Preview logo baru</small></p>
                    </div>
                @elseif ($existingFooterLogoUrl)
                    <div class="mt-2">
                        <img src="{{ $existingFooterLogoUrl }}" style="max-width: 150px; height: auto;" class="img-thumbnail">
                        <p class="text-muted mt-1"><small>Logo saat ini</small></p>
                    </div>
                @endif
            </div>

            {{-- Judul Footer --}}
            <div class="mb-3">
                <flux:input
                    label="Judul Footer"
                    type="text"
                    wire:model="footerTitle"
                    placeholder="Rusunawa UNJ"
                    required
                    :error="$errors->first('footerTitle')"
                />
            </div>

            {{-- Teks Footer --}}
            <div class="mb-3">
                <flux:input
                    label="Teks Footer"
                    type="textarea"
                    rows="3"
                    wire:model.live="footerText"
                    placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, DKI Jakarta 13220"
                    required
                    maxlength="200"
                    :error="$errors->first('footerText')"
                >
                    <x-slot name="after">
                        <div class="text-end text-muted mt-1">
                            <small>{{ strlen($footerText) }}/200</small>
                        </x-slot>
                </flux:input>
            </div>

            {{-- Tombol "Update" Footer --}}
            {{-- Mengubah variant="success" menjadi variant="primary" --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
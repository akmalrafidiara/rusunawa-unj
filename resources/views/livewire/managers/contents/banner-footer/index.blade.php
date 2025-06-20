<div>
    {{-- BAGIAN BANNER --}}
    <x-managers.ui.card>
        <h5 class="mb-4 font-bold">Banner</h5>

        <form wire:submit.prevent="saveBanner">
            {{-- Judul Banner (input form) --}}
            <div class="mb-3">
                <flux:input
                    label="Judul Banner"
                    type="text"
                    wire:model="bannerTitle"
                    placeholder="Rusunawa Universitas Negeri Jakarta"
                    required
                    :error="$errors->first('bannerTitle')" />
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
                    :error="$errors->first('bannerText')">
                    <x-slot name="after">
                        <div class="text-right text-gray-500 mt-1">
                            <small>{{ strlen($bannerText) }}/200</small>
                        </div>
                    </x-slot>
                </flux:input>
            </div>

            {{-- Daya Tarik Banner (Add: 2 input; Display: 2 input) --}}
            <div class="mb-3">
                <x-managers.form.label>Daya Tarik</x-managers.form.label>
                @if (!empty($dayaTariks))
                @foreach ($dayaTariks as $index => $item)
                <div class="flex items-center gap-2 mb-2" wire:key="daya-tarik-item-wrapper-{{ $index }}">
                    {{-- Tampilan dan Edit item yang sudah ada: DUA INPUT TERPISAH --}}
                    <flux:input
                        wire:key="daya-tarik-value-{{ $index }}"
                        wire:model.live="dayaTariks.{{ $index }}.value"
                        placeholder="Value (Contoh: 50+)"
                        :error="$errors->first('dayaTariks.' . $index . '.value')"
                        class="flex-grow" />
                    <flux:input
                        wire:key="daya-tarik-label-{{ $index }}"
                        wire:model.live="dayaTariks.{{ $index }}.label"
                        placeholder="Label (Contoh: Kamar Siap Huni)"
                        :error="$errors->first('dayaTariks.' . $index . '.label')"
                        class="flex-grow" />
                    <x-managers.ui.button
                        wire:click="removeDayaTarik({{ $index }})"
                        variant="danger"
                        size="sm"
                        icon="trash" />
                </div>
                @endforeach
                @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada daya tarik.</p>
                @endif

                <div class="mt-3 flex gap-2">
                    {{-- Form Tambah item baru: DUA INPUT --}}
                    <flux:input
                        wire:model.live="newDayaTarikValue"
                        placeholder="Value (Contoh: 50+)"
                        :error="$errors->first('newDayaTarikValue')"
                        class="flex-grow" />
                    <flux:input
                        wire:model.live="newDayaTarikLabel"
                        placeholder="Label (Contoh: Kamar Siap Huni)"
                        :error="$errors->first('newDayaTarikLabel')"
                        class="flex-grow" />
                    <x-managers.ui.button wire:click="addDayaTarik()" variant="primary" size="sm" icon="plus">
                        Tambah Daya Tarik
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Foto Update Banner (Kustom Tailwind CSS untuk input file) --}}
            <div class="mb-3">
                <x-managers.form.label for="bannerImageInput">Foto Update</x-managers.form.label>
                <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1"> {{-- Tambahkan outline di sini --}}
                    <input
                        type="file"
                        wire:model="bannerImage"
                        id="bannerImageInput"
                        accept="image/jpeg,image/png"
                        maxlength="2048"
                        class="hidden">
                    <button type="button"
                        onclick="document.getElementById('bannerImageInput').click()"
                        class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"> {{-- px-3 py-1 text-sm untuk ukuran kecil --}}
                        Choose File
                    </button>
                    <span class="text-gray-700 dark:text-gray-300 text-sm truncate"> {{-- text-sm dan truncate untuk nama file --}}
                        {{ $bannerImage ? $bannerImage->getClientOriginalName() : 'No file chosen' }}
                    </span>
                </div>
                @error('bannerImage')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p>

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

    {{-- BAGIAN FOOTER --}}
    <div class="mt-12">
        <x-managers.ui.card>
            <h5 class="mb-4 font-bold">Footer</h5>

            <form wire:submit.prevent="saveFooter">
                {{-- Logo Footer (Kustom Tailwind CSS untuk input file) --}}
                <div class="mb-3">
                    <x-managers.form.label for="footerLogoInput">Logo Footer</x-managers.form.label>
                    <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1"> {{-- Tambahkan outline di sini --}}
                        <input
                            type="file"
                            wire:model="footerLogo"
                            id="footerLogoInput"
                            accept="image/jpeg,image/png"
                            maxlength="2048"
                            class="hidden">
                        <button type="button"
                            onclick="document.getElementById('footerLogoInput').click()"
                            class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"> {{-- px-3 py-1 text-sm untuk ukuran kecil --}}
                            Choose File
                        </button>
                        <span class="text-gray-700 dark:text-gray-300 text-sm truncate"> {{-- text-sm dan truncate untuk nama file --}}
                            {{ $footerLogo ? $footerLogo->getClientOriginalName() : 'No file chosen' }}
                        </span>
                    </div>
                    @error('footerLogo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p>

                    @if ($footerLogo)
                    <div class="mt-2">
                        <img src="{{ $footerLogo->temporaryUrl() }}" style="max-width: 150px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Preview logo baru</p>
                    </div>
                    @elseif ($existingFooterLogoUrl)
                    <div class="mt-2">
                        <img src="{{ $existingFooterLogoUrl }}" style="max-width: 150px; height: auto;" class="rounded-md border border-gray-200">
                        <p class="text-gray-500 text-sm mt-1">Logo saat ini</p>
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
                        :error="$errors->first('footerTitle')" />
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
                        :error="$errors->first('footerText')">
                        <x-slot name="after">
                            <div class="text-right text-gray-500 mt-1">
                                <small>{{ strlen($footerText) }}/200</small>
                            </div>
                        </x-slot>
                    </flux:input>
                </div>

                <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
            </form>
        </x-managers.ui.card>
    </div>
</div>
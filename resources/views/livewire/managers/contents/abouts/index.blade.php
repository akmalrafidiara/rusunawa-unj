<div>
    <x-managers.ui.card title="Tentang Kami">
        <form wire:submit.prevent="save">
            {{-- Foto Tentang Kami --}}
            <div class="mb-3">
                <x-managers.form.label for="aboutImageInput">Foto Tentang Kami <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.image
                    model="aboutImage"
                    label="Pilih File"
                    :existing-image-url="$existingAboutImageUrl"
                    helper-text="Upload gambar Tentang Kami Anda. Format .jpg, .jpeg, .png (Maksimal 2MB)." />
            </div>

            {{-- Judul Tentang Kami --}}
            <div class="mb-3">
                <x-managers.form.label for="aboutTitleInput">
                    Judul Tentang Kami <span class="text-red-500">*</span>
                </x-managers.form.label>
                <x-managers.form.input
                    id="aboutTitleInput"
                    type="text"
                    wire:model.live="aboutTitle"
                    placeholder="Hunian Ideal & Nyaman di Area Kampus"
                    :error="$errors->first('aboutTitle')"
                />
            </div>

            {{-- Teks Tentang Kami --}}
            <div class="mb-3">
                <x-managers.form.label for="aboutDescriptionInput">
                    Teks Tentang Kami <span class="text-red-500">*</span>
                </x-managers.form.label>
                <x-managers.form.textarea
                    id="aboutDescriptionInput"
                    wire:model.live="aboutDescription"
                    placeholder="Rusunawa UNJ merupakan fasilitas hunian..."
                    maxlength="500"
                    rows="5"
                    :error="$errors->first('aboutDescription')"
                    class="overflow-hidden resize-none"
                    x-data="{
                        resize() {
                            $el.style.height = 'auto';
                            $el.style.height = $el.scrollHeight + 'px';
                        }
                    }"
                    x-init="resize()"
                    @input="resize()"
                ></x-managers.form.textarea>
                {{-- Menambahkan hitungan karakter --}}
                <div class="text-right text-gray-500 mt-1" wire:ignore>
                    <small x-data="{ count: @entangle('aboutDescription').live }" 
                           x-text="(count ? count.length : 0) + '/500'">
                        {{ strlen($aboutDescription ?? '') }}/500
                    </small>
                </div>
            </div>

            {{-- Daya Tarik --}}
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
                                                <x-managers.form.input
                                                    wire:model.live="dayaTariks.{{ $row[$col]['index'] }}"
                                                    placeholder="Contoh: Lokasi Strategis, Fasilitas Lengkap"
                                                    readonly
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
                @endif

                <div class="mt-3 flex gap-2">
                    <x-managers.form.input
                        wire:model.live="newDayaTarik" 
                        placeholder="Masukkan daya tarik baru..."
                        :error="$errors->first('newDayaTarik')"
                        class="flex-grow"
                    />
                    <x-managers.ui.button wire:click="addDayaTarik()" variant="primary" size="sm" icon="plus">
                        Tambah Daya Tarik
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Tombol Update --}}
            <x-managers.ui.button variant="primary" type="submit" class="mt-4">
                Update
            </x-managers.ui.button>
        </form>
    </x-managers.ui.card>
</div>
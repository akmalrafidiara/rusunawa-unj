<div>
    <x-managers.ui.card title="Layanan Pengaduan">
        <form wire:submit.prevent="save">
            {{-- Foto Layanan Pengaduan --}}
            <div class="mb-3">
                <x-managers.form.label for="complaintImageInput">Foto Layanan Pengaduan <span class="text-red-500">*</span></x-managers.form.label> {{-- Mengubah label --}}
                <x-managers.form.image
                    model="complaintImage"
                    label="Pilih File"
                    :existing-image-url="$existingComplaintImageUrl"
                    helper-text="Upload gambar bagian layanan pengaduan Anda. Format .jpg, .jpeg, .png (Maksimal 2MB)." />
            </div>

            {{-- Judul Layanan Pengaduan --}}
            <div class="mb-3">
                <x-managers.form.label for="complaintTitleInput">Judul Layanan Pengaduan <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.input
                    id="complaintTitleInput"
                    type="text"
                    wire:model.live="complaintTitle"
                    placeholder="Layanan Aduan Cepat dan Efektif"
                    :error="$errors->first('complaintTitle')" 
                />
            </div>

            {{-- Deskripsi Layanan Pengaduan --}}
            <div class="mb-3">
                <x-managers.form.label for="complaintDescriptionInput">Deskripsi Layanan Pengaduan <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.textarea
                    id="complaintDescriptionInput"
                    wire:model.live="complaintDescription"
                    placeholder="Kami menyediakan layanan pengaduan yang mudah dan transparan..."
                    maxlength="500"
                    rows="5"
                    :error="$errors->first('complaintDescription')"
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
                ></x-managers.form.textarea>
                {{-- Menambahkan hitungan karakter --}}
                <div class="text-right text-gray-500 mt-1" wire:ignore>
                    <small x-data="{ count: @entangle('complaintDescription').live }" 
                           x-text="(count ? count.length : 0) + '/500'">
                        {{ strlen($complaintDescription ?? '') }}/500
                    </small>
                </div>
            </div>

            {{-- Keunggulan --}} {{-- Label diubah dari Daya Tarik menjadi Keunggulan --}}
            <div class="mb-3">
                <x-managers.form.label>Keunggulan</x-managers.form.label>
                @if (!empty($advantages))
                    @php
                        $colCount = 3;
                        $rows = [];
                        foreach ($advantages as $i => $advantage) { 
                            $row = intdiv($i, $colCount);
                            $col = $i % $colCount;
                            $rows[$row][$col] = ['index' => $i, 'value' => $advantage];
                        }
                    @endphp
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($rows as $row)
                            <div class="flex gap-3">
                                @for ($col = 0; $col < $colCount; $col++)
                                    <div class="flex-1">
                                        @if (isset($row[$col]))
                                            <div class="flex items-center gap-2" wire:key="advantage-{{ $row[$col]['index'] }}">
                                                <x-managers.form.input
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
                    <x-managers.form.input
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
            <x-managers.ui.button variant="primary" type="submit" class="mt-4">Update</x-managers.ui.button>
        </form>
    </x-managers.ui.card>
</div>
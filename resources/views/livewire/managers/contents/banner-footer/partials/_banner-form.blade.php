{{-- BAGIAN BANNER --}}
<x-managers.ui.card>
    <h5 class="mb-4 font-bold">Banner</h5>

    <form wire:submit.prevent="saveBanner">
        {{-- Judul Banner --}}
        <div class="mb-3">
            <x-managers.form.label for="bannerTitleInput">Judul Banner <span class="text-red-500">*</span></x-managers.form.label>
            <x-managers.form.input
                id="bannerTitleInput"
                type="text"
                wire:model.live="bannerTitle"
                placeholder="Rusunawa Universitas Negeri Jakarta"
                :error="$errors->first('bannerTitle')" />
        </div>

        {{-- Teks Banner --}}
        <div class="mb-3">
            <x-managers.form.label for="bannerTextInput">Teks Banner <span class="text-red-500">*</span>
            </x-managers.form.label>
            <x-managers.form.textarea
                id="bannerTextInput"
                wire:model.live="bannerText"
                placeholder="Sebuah solusi tempat tinggal praktis di lingkungan kampus, ideal untuk mendukung aktivitas harian Anda"
                maxlength="200"
                rows="3" 
                class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        overflow-hidden resize-none placeholder-gray-400"
                x-data="{
                    resize() {
                        $el.style.height = 'auto'; // Reset height to recalculate
                        $el.style.height = $el.scrollHeight + 'px'; // Set height based on content
                    }
                }"
                x-init="resize()"
                @input="resize()" 
            ></x-managers.form.textarea>
            {{-- Menambahkan hitungan karakter --}}
            <div class="text-right text-gray-500 mt-1" wire:ignore>
                <small x-data="{ count: @entangle('bannerText').live }"
                    x-text="(count ? count.length : 0) + '/200'">
                    {{ strlen($bannerText ?? '') }}/200
                </small>
            </div>
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
                    <x-managers.form.label for="newDayaTarikValueInput">Value</x-managers.form.label>
                    <x-managers.form.input
                        id="newDayaTarikValueInput"
                        type="text"
                        wire:model.live="newDayaTarikValue"
                        placeholder="Contoh: 50+"
                        :error="$errors->first('newDayaTarikValue')"
                        class="placeholder-gray-400" />
                </div>
                <div class="flex-grow">
                    <x-managers.form.label for="newDayaTarikLabelInput">Label</x-managers.form.label>
                    <x-managers.form.input
                        id="newDayaTarikLabelInput"
                        type="text"
                        wire:model.live="newDayaTarikLabel"
                        placeholder="Contoh: Kamar Siap Huni"
                        :error="$errors->first('newDayaTarikLabel')"
                        class="placeholder-gray-400" />
                </div>
                <x-managers.ui.button wire:click="addDayaTarik()" variant="primary" size="sm" icon="plus" class="self-end">
                    Tambah Daya Tarik
                </x-managers.ui.button>
            </div>
        </div>

        {{-- Foto Update Banner --}}
        <div class="mb-3">
            <x-managers.form.label for="bannerImageInput">Foto Update <span class="text-red-500">*</span></x-managers.form.label>
            <x-managers.form.image
                    model="bannerImage"
                    label="Pilih File"
                    :existing-image-url="$existingBannerImageUrl"
                    helper-text="Upload logo footer Anda. Format .jpg, .jpeg, .png (Maksimal 2MB)." />
        </div>
        <x-managers.ui.button variant="primary" type="submit" class="mt-4">
            Update
        </x-managers.ui.button>
    </form>
</x-managers.ui.card>
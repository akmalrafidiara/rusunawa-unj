<div>
    <x-managers.ui.card title="Lokasi Kami">
        <form wire:submit.prevent="save">
            {{-- Judul dan Subjudul Lokasi Kami --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-3">
                    <x-managers.form.label>Judul Lokasi Kami <span class="text-red-500">*</span></x-managers.form.label>
                    <x-managers.form.input
                        id="mainLocationTitleInput"
                        type="text"
                        wire:model.live="mainLocationTitle"
                        placeholder="Lokasi Kami"
                        :error="$errors->first('mainLocationTitle')" />
                </div>
                <div class="mb-3">
                    <x-managers.form.label>Subjudul Lokasi Kami <span class="text-red-500">*</span></x-managers.form.label>
                    <x-managers.form.input
                        id="subLocationTitleInput"
                        type="text"
                        wire:model.live="subLocationTitle"
                        placeholder="Rusunawa UNJ"
                        :error="$errors->first('subLocationTitle')" />
                </div>
            </div>

            {{-- Alamat --}}
            <div class="mb-3">
                <x-managers.form.label>Alamat<span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.textarea
                    id="locationAddressInput"
                    wire:model.live="locationAddress"
                    placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, Dki Jakarta 13220"
                    maxlength="200"
                    rows="3"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                        overflow-hidden resize-none placeholder-gray-400"
                    x-data="{
                    resize() {
                        $el.style.height = 'auto'; // Reset height to recalculate
                        $el.style.height = $el.scrollHeight + 'px';
                    }
                }"
                    x-init="resize()"
                    @input="resize()"></x-managers.form.textarea>
                {{-- Menambahkan hitungan karakter --}}
                <div class="text-right text-gray-500 mt-1" wire:ignore>
                    <small x-data="{ count: @entangle('locationAddress').live }"
                        x-text="(count ? count.length : 0) + '/200'">
                        {{ strlen($locationAddress ?? '') }}/200
                    </small>
                </div>
            </div>

            {{-- Lokasi Terdekat --}}
            <div class="mb-3">
                <x-managers.form.label>Lokasi Terdekat</x-managers.form.label>
                @if (!empty($nearbyLocations))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 mb-2">
                    @foreach ($nearbyLocations as $index => $location)
                    <div class="flex items-center gap-2" wire:key="nearby-location-item-wrapper-{{ $index }}">
                        <x-managers.form.input-disable-data
                            wire:key="nearby-location-{{ $index }}"
                            class="flex-grow"
                            value="{{ $location }}"
                            readonly
                            placeholder="Lokasi Terdekat {{ $index + 1 }}" />
                        <x-managers.ui.button
                            wire:click="removeNearbyLocation({{ $index }})"
                            variant="danger"
                            size="sm"
                            icon="trash" />
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada lokasi terdekat.</p>
                @endif

                <div class="mt-3 flex gap-2">
                    <x-managers.form.input
                        wire:model.live="newNearbyLocation"
                        id="newNearbyLocationInput"
                        placeholder="Masukkan lokasi terdekat baru..."
                        :error="$errors->first('newNearbyLocation')"
                        class="flex-grow" />
                    <x-managers.ui.button wire:click="addNearbyLocation()" variant="primary" size="sm" icon="plus">
                        Tambah Lokasi Terdekat
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Link Embed Lokasi --}}
            <div class="mb-3">
                <x-managers.form.label for="locationEmbedLinkInput">Link Embed Lokasi <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.textarea
                    id="locationEmbedLinkInput"
                    wire:model.live="locationEmbedLink"
                    placeholder="<iframe src='https://www.google.com/maps/embed?...'</iframe>"
                    rows="6"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                           overflow-hidden resize-none placeholder-gray-400"
                    x-data="{
                        resize() {
                            $el.style.height = 'auto';
                            $el.style.height = $el.scrollHeight + 'px';
                        }
                    }"
                    x-init="resize()"
                    @input="resize()"></x-managers.form.textarea>
            </div>

            {{-- Tombol Update --}}
            <x-managers.ui.button variant="primary" type="submit" class="mt-4">Update</x-managers.ui.button>
        </form>
    </x-managers.ui.card>
</div>
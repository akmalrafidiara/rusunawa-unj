<div>
    <x-managers.ui.card title="Lokasi Kami">
        <form wire:submit.prevent="save">
            {{-- Judul dan Subjudul Lokasi Kami (menggunakan grid Tailwind) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-3">
                    <label for="mainLocationTitleInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Judul Lokasi Kami <span class='text-red-500'>*</span>
                    </label>
                    <flux:input
                        id="mainLocationTitleInput"
                        type="text"
                        wire:model="mainLocationTitle"
                        placeholder="Akses Mudah ke Segala Arah" />
                    @error('mainLocationTitle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="subLocationTitleInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Subjudul Lokasi Kami <span class='text-red-500'>*</span>
                    </label>
                    <flux:input
                        id="subLocationTitleInput"
                        type="text"
                        wire:model="subLocationTitle"
                        placeholder="Rusunawa UNJ" />
                    @error('subLocationTitle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Alamat (textarea dengan auto-resize) --}}
            <div class="mb-3">
                <label for="locationAddressInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Alamat <span class='text-red-500'>*</span>
                </label>
                <textarea
                    id="locationAddressInput"
                    wire:model.live="locationAddress"
                    placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, Dki Jakarta 13220"
                    maxlength="200"
                    rows="3"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                           overflow-hidden resize-none placeholder-gray-400"
                    x-data="{
                        resize() {
                            $el.style.height = 'auto';
                            $el.style.height = $el.scrollHeight + 'px';
                        }
                    }"
                    x-init="resize()"
                    @input="resize()"></textarea>
                <div class="text-right text-gray-500 mt-1">
                    <small>{{ strlen($locationAddress) }}/200</small>
                </div>
                @error('locationAddress')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lokasi Terdekat --}}
            <div class="mb-3">
                <x-managers.form.label>Lokasi Terdekat</x-managers.form.label>
                @if (!empty($nearbyLocations))
                {{-- Ganti flex-wrap dengan grid untuk 3 kolom --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 mb-2">
                    @foreach ($nearbyLocations as $index => $location)
                    {{-- Setiap lokasi terdekat menjadi item grid --}}
                    <span class="inline-flex items-center justify-between px-3 py-1 border border-gray-300 rounded-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm">
                        {{ $location ?? '-' }}
                        <x-managers.ui.button
                            wire:click="removeNearbyLocation({{ $index }})"
                            variant="danger"
                            size="sm"
                            icon="trash" />
                    </span>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada lokasi terdekat.</p>
                @endif

                <div class="mt-3 flex gap-2">
                    <flux:input
                        wire:model.live="newNearbyLocation"
                        placeholder="Masukkan lokasi terdekat baru..."
                        class="flex-grow" />
                    <x-managers.ui.button wire:click="addNearbyLocation()" variant="primary" size="sm" icon="plus">
                        Tambah Lokasi Terdekat
                    </x-managers.ui.button>
                </div>
                @error('newNearbyLocation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Embed Lokasi (textarea dengan auto-resize) --}}
            <div class="mb-3">
                <label for="locationEmbedLinkInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Link Embed Lokasi <span class='text-red-500'>*</span>
                </label>
                <textarea
                    id="locationEmbedLinkInput"
                    wire:model="locationEmbedLink"
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
                    @input="resize()"></textarea>
                <p class="text-gray-500 text-sm mt-1">Paste kode iframe dari Google Maps atau penyedia peta lainnya di sini.</p>
                @error('locationEmbedLink')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Update --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
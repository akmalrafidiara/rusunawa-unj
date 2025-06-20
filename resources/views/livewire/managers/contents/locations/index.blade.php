<div>
    <x-managers.ui.card title="Lokasi Kami">
        <form wire:submit.prevent="save">
            <div class="row">
                {{-- Judul Lokasi Kami --}}
                <div class="col-md-6 mb-3">
                    <flux:input
                        label="Judul Lokasi Kami"
                        type="text"
                        wire:model="mainLocationTitle"
                        placeholder="Akses Mudah ke Segala Arah"
                        required
                        :error="$errors->first('mainLocationTitle')"
                    />
                </div>

                {{-- Subjudul Lokasi Kami --}}
                <div class="col-md-6 mb-3">
                    <flux:input
                        label="Subjudul Lokasi Kami"
                        type="text"
                        wire:model="subLocationTitle"
                        placeholder="Rusunawa UNJ"
                        required
                        :error="$errors->first('subLocationTitle')"
                    />
                </div>
            </div>

            {{-- Alamat --}}
            <div class="mb-3">
                <flux:input
                    label="Alamat"
                    type="textarea"
                    rows="3"
                    wire:model.live="locationAddress"
                    placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, Dki Jakarta 13220"
                    required
                    maxlength="200"
                    :error="$errors->first('locationAddress')"
                >
                    <x-slot name="after">
                        <div class="text-end text-muted mt-1">
                            <small>{{ strlen($locationAddress) }}/200</small>
                        </div>
                    </x-slot>
                </flux:input>
            </div>

            {{-- Lokasi Terdekat (Previously Fasilitas) --}}
            <div class="mb-3">
                <x-managers.form.label>Lokasi Terdekat</x-managers.form.label>
                @if (!empty($nearbyLocations))
                    @foreach ($nearbyLocations as $index => $location)
                        <div class="flex items-center gap-2 mb-2" wire:key="nearby-location-{{ $index }}">
                            <flux:input
                                wire:model.live="nearbyLocations.{{ $index }}"
                                placeholder="Contoh: Halte Trans.Jakarta Pemuda Rawamangun - 100 m"
                                :error="$errors->first('nearbyLocations.' . $index)"
                                class="flex-grow-1"
                            />
                            <x-managers.ui.button
                                wire:click="removeNearbyLocation({{ $index }})"
                                variant="danger"
                                size="sm"
                                icon="trash"
                            />
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada lokasi terdekat.</p>
                @endif

                <div class="mt-3 flex gap-2">
                    <flux:input
                        wire:model.live="newNearbyLocation"
                        placeholder="Masukkan lokasi terdekat baru..."
                        :error="$errors->first('newNearbyLocation')"
                        class="flex-grow-1"
                    />
                    <x-managers.ui.button wire:click="addNearbyLocation()" variant="primary" size="sm" icon="plus">
                        Tambah Lokasi Terdekat
                    </x-managers.ui.button>
                </div>
            </div>

            {{-- Link Embed Lokasi --}}
            <div class="mb-3">
                <flux:input
                    label="Link Embed Lokasi"
                    type="textarea"
                    rows="6"
                    wire:model="locationEmbedLink"
                    placeholder="<iframe src='https://www.google.com/maps/embed?...'</iframe>"
                    :error="$errors->first('locationEmbedLink')"
                >
                    <x-slot name="hint">
                        Paste kode iframe dari Google Maps atau penyedia peta lainnya di sini.
                    </x-slot>
                </flux:input>
            </div>

            {{-- Tombol Update --}}
            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
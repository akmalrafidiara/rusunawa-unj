<div>
    <x-managers.ui.card title="Kontak">
        {{-- Hapus x-data dari form karena tombol akan selalu aktif --}}
        <form wire:submit.prevent="save">
            {{-- Nomor Telepon dan Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-3">
                    <x-managers.form.label for="phoneNumberInput">Nomor Telepon <span class="text-red-500">*</span></x-managers.form.label>
                    <x-managers.form.input
                        id="phoneNumberInput"
                        type="text"
                        wire:model="phoneNumber"
                        placeholder="622112345678"
                        :error="$errors->first('phoneNumber')"
                        class="placeholder-gray-400"
                    />
                </div>

                <div class="mb-3">
                    <x-managers.form.label for="emailInput">Email <span class="text-red-500">*</span></x-managers.form.label>
                    <x-managers.form.input
                        id="emailInput"
                        type="text"
                        wire:model="email"
                        placeholder="bpu@unj.ac.id"
                        :error="$errors->first('email')"
                        class="placeholder-gray-400"
                    />
                </div>
            </div>

            {{-- Jam Operasional --}}
            <div class="mb-3">
                <x-managers.form.label for="operationalHoursInput">Jam Operasional <span class='text-red-500'>*</span></x-managers.form.label>
                <x-managers.form.input
                    id="operationalHoursInput"
                    type="text"
                    wire:model="operationalHours"
                    placeholder="Senin - Jumat, 08:00 - 16:00"
                    :error="$errors->first('operationalHours')"
                    class="placeholder-gray-400"
                />
            </div>

            {{-- Alamat --}}
            <div class="mb-3">
                <x-managers.form.label for="addressInput">Alamat <span class='text-red-500'>*</span></x-managers.form.label>
                <x-managers.form.textarea
                    id="addressInput"
                    wire:model.live="address"
                    placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, Dki Jakarta 13220"
                    maxlength="200"
                    rows="3" {{-- Tinggi awal --}}
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                            overflow-hidden resize-none placeholder-gray-400"
                    x-data="{
                        resize() {
                            $el.style.height = 'auto'; // Reset height to recalculate
                            $el.style.height = $el.scrollHeight + 'px'; // Set height based on content
                        }
                    }"
                    x-init="resize()" {{-- Panggil saat inisialisasi --}}
                    @input="resize()" {{-- Panggil setiap kali input berubah --}}
                ></x-managers.form.textarea>
                {{-- Menambahkan hitungan karakter --}}
                <div class="text-right text-gray-500 mt-1" wire:ignore>
                    <small x-data="{ count: @entangle('address').live }"
                        x-text="(count ? count.length : 0) + '/200'">
                        {{ strlen($address ?? '') }}/200
                    </small>
                </div>
            </div>

            <div class="mb-3">
                <x-managers.form.label for="mapAddressInput">Link Maps Alamat <span class='text-red-500'>*</span></x-managers.form.label>
                <x-managers.form.input
                    id="mapAddressInput"
                    type="text"
                    wire:model="map_address"
                    placeholder="https://maps.google.com/?q=Jl.+Pemuda+No.+10,+Rawamangun,+Jakarta+Timur,+Dki+Jakarta+13220"
                    :error="$errors->first('map_address')"
                    class="placeholder-gray-400"
                />
            </div>

            {{-- Tombol Update --}}
            <div class="flex justify-end">
                <x-managers.ui.button variant="primary" type="submit">
                    Update
                </x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.card>
</div>
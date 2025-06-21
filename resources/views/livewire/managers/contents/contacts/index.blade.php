<div>
    <x-managers.ui.card title="Kontak">
        {{-- Hapus x-data dari form karena tombol akan selalu aktif --}}
        <form wire:submit.prevent="save">
            {{-- Nomor Telepon dan Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-3">
                    <label for="phoneNumberInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Nomor Telepon <span class='text-red-500'>*</span>
                    </label>
                    <flux:input
                        id="phoneNumberInput"
                        type="text"
                        wire:model="phoneNumber"
                        placeholder="+62 21 1234 5678"
                        class="placeholder-gray-400"
                    />
                    @error('phoneNumber')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="emailInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        Email <span class='text-red-500'>*</span>
                    </label>
                    <flux:input
                        id="emailInput"
                        type="text"
                        wire:model="email"
                        placeholder="bpu@unj.ac.id"
                        class="placeholder-gray-400"
                    />
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Jam Operasional --}}
            <div class="mb-3">
                <label for="operationalHoursInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Jam Operasional <span class='text-red-500'>*</span>
                </label>
                <flux:input
                    id="operationalHoursInput"
                    type="text"
                    wire:model="operationalHours"
                    placeholder="Senin - Jumat, 08:00 - 16:00"
                    class="placeholder-gray-400"
                />
                @error('operationalHours')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat (textarea dengan auto-resize) --}}
            <div class="mb-3">
                <label for="addressInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Alamat <span class='text-red-500'>*</span>
                </label>
                <textarea
                    id="addressInput"
                    wire:model.live="address"
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
                    @input="resize()"
                ></textarea>
                <div class="text-right text-gray-500 mt-1">
                    <small>{{ strlen($address) }}/200</small>
                </div>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Update --}}
            <div class="flex justify-end">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="mt-4"
                    {{-- HAPUS x-bind:disabled AGAR TOMBOL SELALU AKTIF --}}
                >
                    Update
                </flux:button>
            </div>
        </form>
    </x-managers.ui.card>
</div>
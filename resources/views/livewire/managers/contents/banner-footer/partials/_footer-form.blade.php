{{-- BAGIAN FOOTER --}}
<div class="mt-12">
    <x-managers.ui.card>
        <h5 class="mb-4 font-bold">Footer</h5>

        <form wire:submit.prevent="saveFooter">
            {{-- Logo Footer (Kustom Tailwind CSS untuk input file) --}}
            <div class="mb-3">
                <x-managers.form.label for="footerLogoInput">Logo Footer <span class="text-red-500">*</span></x-managers.form.label>
                <div class="flex items-center space-x-2 border border-gray-300 rounded-md p-1">
                    <input
                        type="file"
                        wire:model="footerLogo"
                        id="footerLogoInput"
                        accept="image/jpeg,image/png"
                        maxlength="2048"
                        class="hidden">
                    <button type="button"
                        onclick="document.getElementById('footerLogoInput').click()"
                        class="px-3 py-1 text-sm bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Choose File
                    </button>
                    <span class="text-gray-700 dark:text-gray-300 text-sm truncate">
                        {{ $footerLogo ? $footerLogo->getClientOriginalName() : 'No file chosen' }}
                    </span>
                </div>
                @error('footerLogo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @else
                    <p class="text-gray-500 text-sm mt-1">Format file .jpg, .jpeg, .png (Maksimal 2MB)</p>
                @enderror

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
                <label for="footerTitleInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Judul Footer <span class='text-red-500'>*</span>
                </label>
                <flux:input
                    id="footerTitleInput"
                    type="text"
                    wire:model="footerTitle"
                    placeholder="Rusunawa UNJ"
                    class="placeholder-gray-400"
                />
                @error('footerTitle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teks Footer (DIUBAH MENJADI TEXTAREA BIASA DENGAN AUTO-RESIZE) --}}
            <div class="mb-3">
                <label for="footerTextInput" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Teks Footer <span class='text-red-500'>*</span>
                </label>
                <textarea
                    id="footerTextInput"
                    wire:model.live="footerText"
                    placeholder="Jl. Pemuda No. 10, Rawamangun, Jakarta Timur, DKI Jakarta 13220"
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
                ></textarea>
                <div class="text-right text-gray-500 mt-1">
                    <small>{{ strlen($footerText) }}/200</small>
                </div>
                @error('footerText')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <flux:button variant="primary" type="submit" class="mt-4">Update</flux:button>
        </form>
    </x-managers.ui.card>
</div>
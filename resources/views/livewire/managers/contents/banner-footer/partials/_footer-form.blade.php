{{-- BAGIAN FOOTER --}}
<div class="mt-12">
    <x-managers.ui.card>
        <h5 class="mb-4 font-bold">Footer</h5>

        <form wire:submit.prevent="saveFooter">
            {{-- Logo Footer --}}
            <div class="mb-3">
                <x-managers.form.label for="footerLogoInput">Logo Footer <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.image
                    model="footerLogo"
                    label="Pilih File"
                    :existing-image-url="$existingFooterLogoUrl"
                    helper-text="Upload logo footer Anda. Format .jpg, .jpeg, .png (Maksimal 2MB)." />
            </div>

            {{-- Judul Footer --}}
            <div class="mb-3">
                <x-managers.form.label for="footerTitleInput">Judul Footer <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.input
                    id="footerTitleInput"
                    type="text"
                    wire:model.live="footerTitle"
                    placeholder="Hunian Ideal & Nyaman di Area Kampus"
                    :error="$errors->first('footerTitle')" />
            </div>

            {{-- Teks Footer --}}
            <div class="mb-3">
                <x-managers.form.label for="footerTextInput">Teks Footer <span class="text-red-500">*</span></x-managers.form.label>
                <x-managers.form.textarea
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
                    @input="resize()" {{-- Panggil setiap kali input berubah --}}></x-managers.form.textarea>
                {{-- Menambahkan hitungan karakter --}}
                <div class="text-right text-gray-500 mt-1" wire:ignore>
                    <small x-data="{ count: @entangle('footerText').live }"
                        x-text="(count ? count.length : 0) + '/200'">
                        {{ strlen($footerText ?? '') }}/200
                    </small>
                </div>
            </div>
            <x-managers.ui.button variant="primary" type="submit" class="mt-4">
                Update
            </x-managers.ui.button>
        </form>
    </x-managers.ui.card>
</div>
<x-managers.ui.card>
    <h5 class="mb-4 font-bold">Logo Utama Website Anda</h5>

    <form wire:submit.prevent="saveLogo">
        {{-- Upload Foto Utama Logo --}}
        <div class="mb-3">
            <x-managers.form.label for="logoImageInput">Logo Utama Anda <span class="text-red-500">*</span></x-managers.form.label>
            <x-managers.form.image
                    model="logoImage"
                    label="Pilih File"
                    :existing-image-url="$existingLogoImageUrl"
                    helper-text="Upload logo footer Anda. Format .jpg, .jpeg, .png (Maksimal 2MB)." />
        </div>
        <div class="mb-3">
            <x-managers.form.label for="logoTitleInput">Judul Logo <span class="text-red-500">*</span></x-managers.form.label>
            <x-managers.form.input
                id="logoTitleInput"
                type="text"
                wire:model.live="logoTitle"
                placeholder="Contoh: Rusunawa UNJ"
                :error="$errors->first('logoTitle')"
                class="placeholder-gray-400" />
        </div>
        <x-managers.ui.button variant="primary" type="submit" class="mt-4">
            Update
        </x-managers.ui.button>
    </form>
</x-managers.ui.card>
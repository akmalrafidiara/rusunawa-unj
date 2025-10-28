<div class="flex flex-col gap-6 w-full mx-auto items-center my-6 mb-6">
    <div class="text-left w-full">
        <a onclick="history.back(); return false;"
            class="inline-flex items-center text-sm lg:text-m text-black hover:text-green-800 dark:text-white dark:hover:text-zinc-900 mb-6 cursor-pointer">
            <flux:icon name="chevron-left" class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
            Kembali
        </a>

        <h1 class="text-2xl font-bold text-gray-900 mb-2 dark:text-gray-100">Akses Portal Penghuni Anda</h1>
        <p class="text-gray-600 text-sm lg:text-m dark:text-gray-50">Portal ini adalah pusat kendali Anda. Lihat tagihan,
            lacak pembayaran, ajukan keluhan, dan kelola informasi sewa Anda dengan mudah di satu tempat.</p>
    </div>

    {{-- Session status akan tetap berfungsi untuk pesan lain jika ada --}}
    <x-default.auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6 w-full">
        {{-- Input untuk Kode Kontrak --}}
        <div>
            {{-- ID 'contractCodeInput' ditambahkan untuk dihubungkan dengan label dan helper text --}}
            <flux:input wire:model="contractCode" id="contractCodeInput" name="contractCode" :label="__('ID Pemesanan')"
                type="text" required autofocus autocomplete="off" placeholder="Masukkan ID Pemesanan Anda"
                aria-describedby="contractCodeHelper" />

            <p id="contractCodeHelper"
                class="text-left w-full text-gray-600 text-sm lg:text-m dark:text-gray-50 italic mt-2">
                Cek email Anda setelah melakukan pemesanan untuk melihat ID pemesanan.
            </p>
        </div>

        {{-- Input untuk 5 Digit Terakhir Nomor HP (Sudah diperbaiki) --}}
        <div x-data="{
            digits: Array(5).fill(''),
        
            updateLivewire() {
                this.$wire.set('phoneNumberSuffix', this.digits.join(''));
            },
        
            handleInput(event) {
                const currentInput = event.target;
                const index = Array.from(currentInput.parentElement.children).indexOf(currentInput);
        
                this.digits[index] = currentInput.value.slice(0, 1);
        
                if (currentInput.value.length === 1 && currentInput.nextElementSibling) {
                    currentInput.nextElementSibling.focus();
                }
        
                this.updateLivewire();
            },
        
            handleBackspace(event) {
                const currentInput = event.target;
                const index = Array.from(currentInput.parentElement.children).indexOf(currentInput);
        
                if (event.key === 'Backspace' && currentInput.value === '' && currentInput.previousElementSibling) {
                    currentInput.previousElementSibling.focus();
                }
        
                this.updateLivewire();
            },
        
            handlePaste(event) {
                const pasteData = event.clipboardData.getData('text');
                const inputs = Array.from(event.target.parentElement.children);
        
                for (let i = 0; i < pasteData.length && i < inputs.length; i++) {
                    inputs[i].value = pasteData[i];
                    this.digits[i] = pasteData[i];
                }
        
                event.preventDefault();
                this.updateLivewire();
            }
        }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('5 Digit Terakhir No. HP') }}
            </label>

            <div class="flex items-center gap-2" @paste="handlePaste">
                @for ($i = 0; $i < 5; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                        class="w-12 h-12 md:w-14 md:h-14 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                        @input="handleInput($event)" @keydown="handleBackspace($event)">
                @endfor
            </div>

            @error('phoneNumberSuffix')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol Submit --}}
        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-left w-full text-sm text-zinc-400">
        <span>{{ __('Belum melakukan pemesanan?') }}</span>
        <flux:link :href="route('tenancy.index')" wire:navigate>{{ __('Pesan Kamar Sekarang') }}</flux:link>
    </div>
</div>

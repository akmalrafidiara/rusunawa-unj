<div class="relative w-full py-0 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="flex items-start max-md:flex-col mb-15">
        <div class="me-6 w-full pb-4 md:!w-96 bg-white rounded-lg shadow-md p-4">
            <div x-data="{
                open: false,
                activeMenuItem: '{{ request()->routeIs('complaint.track-complaint') ? __('Lacak Pengaduan') : (request()->routeIs('complaint.create-complaint') ? __('Buat Pengaduan') : (request()->routeIs('complaint.ongoing-complaint') ? __('Pengaduan Berjalan') : (request()->routeIs('complaint.complaint-history') ? __('Riwayat Pengaduan') : 'Pilih Menu'))) }}'
            }" class="md:hidden relative">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-black bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-300">
                    <span class="font-semibold" x-text="activeMenuItem"></span>
                    <flux:icon name="chevron-down" x-show="!open" class="w-5 h-5" />
                    <flux:icon name="chevron-up" x-show="open" class="w-5 h-5" />
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-10 mt-2 w-full bg-white rounded-md shadow-lg overflow-hidden">
                    <flux:navlist>
                        <flux:navlist.item :href="route('complaint.track-complaint')" wire:navigate :class="request()->routeIs('complaint.track-complaint') ? 'bg-gray-200 !text-black' : 'text-black'" @click="activeMenuItem = '{{ __('Lacak Pengaduan') }}'; open = false">
                            <flux:icon name="magnifying-glass" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Lacak Pengaduan') }}</span>
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('complaint.create-complaint')" wire:navigate :class="request()->routeIs('complaint.create-complaint') ? 'bg-gray-200 !text-black' : 'text-black'" @click="activeMenuItem = '{{ __('Buat Pengaduan') }}'; open = false">
                            <flux:icon name="flag" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Buat Pengaduan') }}</span>
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('complaint.ongoing-complaint')" wire:navigate :class="request()->routeIs('complaint.ongoing-complaint') ? 'bg-gray-200 !text-black' : 'text-black'" @click="activeMenuItem = '{{ __('Pengaduan Berjalan') }}'; open = false">
                            <flux:icon name="arrow-path" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Pengaduan Berjalan') }}</span>
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('complaint.complaint-history')" wire:navigate :class="request()->routeIs('complaint.complaint-history') ? 'bg-gray-200 !text-black' : 'text-black'" @click="activeMenuItem = '{{ __('Riwayat Pengaduan') }}'; open = false">
                            <flux:icon name="check" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Riwayat Pengaduan') }}</span>
                        </flux:navlist.item>
                    </flux:navlist>
                </div>
            </div>

            <div class="hidden md:block">
                <flux:navlist>
                    <flux:navlist.item :href="route('complaint.track-complaint')" wire:navigate :class="request()->routeIs('complaint.track-complaint') ? 'bg-gray-200 !text-black' : 'text-black'">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Lacak Pengaduan') }}</span>
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('complaint.create-complaint')" wire:navigate :class="request()->routeIs('complaint.create-complaint') ? 'bg-gray-200 !text-black' : 'text-black'">
                        <flux:icon name="flag" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Buat Pengaduan') }}</span>
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('complaint.ongoing-complaint')" wire:navigate :class="request()->routeIs('complaint.ongoing-complaint') ? 'bg-gray-200 !text-black' : 'text-black'">
                        <flux:icon name="arrow-path" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Pengaduan Berjalan') }}</span>
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('complaint.complaint-history')" wire:navigate :class="request()->routeIs('complaint.complaint-history') ? 'bg-gray-200 !text-black' : 'text-black'">
                        <flux:icon name="check" class="w-5 h-5 inline-block me-2 text-black" /> <span class="text-m">{{ __('Riwayat Pengaduan') }}</span>
                    </flux:navlist.item>
                </flux:navlist>
            </div>
        </div>

        <flux:separator class="md:hidden" />

        <div class="w-full flex-grow mt-6 lg:mt-0 md:bg-white md:rounded-lg md:shadow-lg p-6 max-md:px-4">
            {{ $slot }}
        </div>
    </div>
</div>
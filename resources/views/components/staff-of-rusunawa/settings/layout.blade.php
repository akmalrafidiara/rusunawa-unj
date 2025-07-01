<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('staff-of-rusunawa.settings.profile')" wire:navigate>{{ __('Profil') }}</flux:navlist.item>
            <flux:navlist.item :href="route('staff-of-rusunawa.settings.password')" wire:navigate>{{ __('Kata Sandi') }}</flux:navlist.item>
            <flux:navlist.item :href="route('staff-of-rusunawa.settings.appearance')" wire:navigate>{{ __('Mode Tampilan') }}
            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>

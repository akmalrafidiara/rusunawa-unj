<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('components.layouts.managers'), Title('Dashboard | Pengaturan Akun - Mode Tampilan')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-managers.settings.layout :heading="__('Appearance')" :subheading="__('Perbarui pengaturan tampilan untuk akun Anda')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Mode Terang') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Mode Gelap') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('Mode Sistem') }}</flux:radio>
        </flux:radio.group>
    </x-managers.settings.layout>
</section>

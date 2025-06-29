<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.managers')] class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-managers.settings.layout :heading="__('Perbarui Kata Sandi')" :subheading="__('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input wire:model="current_password" :label="__('Kata sandi saat ini')" type="password" required
                autocomplete="current-password" viewable/>
            <flux:input wire:model="password" :label="__('Kata sandi baru')" type="password" required
                autocomplete="new-password" viewable/>
            <flux:input wire:model="password_confirmation" :label="__('Konfirmasi Kata Sandi')" type="password" required
                autocomplete="new-password" viewable/>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Simpan') }}</flux:button>
                </div>

                <x-default.action-message class="me-3" on="password-updated">
                    {{ __('Tersimpan.') }}
                </x-default.action-message>
            </div>
        </form>
    </x-managers.settings.layout>
</section>

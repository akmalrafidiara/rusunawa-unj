<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Enums\RoleUser;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';
    #[Validate('required|string')]
    public string $password = '';
    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        try {
            $this->validate();
            $this->ensureIsNotRateLimited();

            if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
                RateLimiter::hit($this->throttleKey());

                LivewireAlert::error()->title('Login Gagal!')->text('Email atau kata sandi yang Anda masukkan tidak sesuai. Silakan coba lagi.')->toast()->position('top-end')->show();
                return;
            }

            $user = Auth::user();

            // Definisi role yang diizinkan untuk masuk ke dashboard managers
            $allowedRoles = [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value, RoleUser::STAFF_OF_RUSUNAWA->value];

            if (!$user->hasAnyRole($allowedRoles)) {
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();

                LivewireAlert::warning()->title('Akses Dibatasi!')->text('Akun Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator.')->toast()->position('top-end')->show();
                return;
            }

            RateLimiter::clear($this->throttleKey());
            Session::regenerate();
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        } catch (ValidationException $e) {
            LivewireAlert::error()->title('Input Tidak Valid!')->text('Mohon periksa kembali input yang Anda masukkan.')->toast()->position('top-end')->show();
            throw $e;
        } catch (\Exception $e) {
            LivewireAlert::error()->title('Terjadi Kesalahan!')->text('Sistem sedang mengalami masalah. Mohon coba beberapa saat lagi.')->toast()->position('top-end')->show();
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        LivewireAlert::warning()
            ->title('Terlalu Banyak Percobaan!')
            ->text('Anda telah mencoba login terlalu sering. Mohon coba lagi dalam ' . ceil($seconds / 60) . ' menit.')
            ->toast()
            ->position('top-end')
            ->show();

        abort(403, 'Forbidden');
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>


<div class="flex flex-col gap-6 w-full mx-auto items-center my-6 mb-6">
    <div class="text-left w-full">
        <h1 class="text-2xl font-bold text-gray-900 mb-2 dark:text-gray-100">Masuk ke Dashboard Pengelola Rusunawa UNJ
        </h1>
        <p class="text-gray-600 text-sm lg:text-m dark:text-gray-50">Silakan login menggunakan akun yang telah terdaftar
            untuk mengelola
            data penghuni, kamar, pengaduan, dan operasional Rusunawa Universitas Negeri Jakarta.
            Pastikan informasi akun Anda tetap aman dan tidak dibagikan kepada pihak lain.</p>
    </div>

    {{-- Session status akan tetap berfungsi untuk pesan lain jika ada --}}
    <x-default.auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6 w-full">
        <flux:input wire:model="email" :label="__('Alamat Email')" type="email" required autofocus autocomplete="email"
            placeholder="email@example.com" />

        <div class="relative">
            <flux:input wire:model="password" :label="__('Kata Sandi')" type="password" required
                autocomplete="current-password" :placeholder="__('Masukkan Kata Sandi Anda')" viewable />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <flux:checkbox wire:model="remember" :label="__('Ingat Saya')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>
</div>

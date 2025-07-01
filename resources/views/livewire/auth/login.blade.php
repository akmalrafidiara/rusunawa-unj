<?php

use App\Enums\RoleUser;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

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
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = Auth::user();

        // Logika redireksi berdasarkan peran pengguna
        // Order of checking matters: Check more specific roles first if they can also have broader roles (e.g., admin)
        if ($user->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value)) {
            $this->redirectIntended(default: route('head_of_rusunawa.dashboard', absolute: false), navigate: true);
        } elseif ($user->hasRole(RoleUser::STAFF_OF_RUSUNAWA->value)) {
            $this->redirectIntended(default: route('staff_of_rusunawa.dashboard', absolute: false), navigate: true);
        } elseif ($user->hasRole(RoleUser::ADMIN->value)) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true); // 'dashboard' adalah untuk Admin/Kepala BPU
        }
        // Jika user tidak memiliki salah satu dari peran di atas, ini adalah fallback.
        // Jika penghuni mencoba login dari sini, mereka tidak akan cocok dengan peran di atas.
        // Anda bisa menambahkan pesan error atau redirect ke halaman login penghuni jika memang ada skenario ini.
        else {
             // Fallback jika tidak ada peran yang cocok atau peran penghuni (yang tidak seharusnya login dari sini)
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => 'Akses ditolak atau akun tidak memiliki peran yang valid untuk masuk dari halaman ini.',
            ]);
            // Atau redirect ke halaman login utama atau halaman login penghuni:
            // $this->redirect(route('login'), navigate: true);
        }
    }

    // ... sisa metode ensureIsNotRateLimited dan throttleKey tetap sama
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6 w-full mx-auto items-center my-6 mb-6">
<div class="text-left w-full">
  <h1 class="text-2xl font-bold text-gray-900 mb-2 dark:text-gray-100">Masuk ke Dashboard Pengelola Rusunawa UNJ</h1>
  <p class="text-gray-600 text-sm lg:text-m dark:text-gray-50">Silakan login menggunakan akun yang telah terdaftar untuk mengelola 
    data penghuni, kamar, pengaduan, dan operasional Rusunawa Universitas Negeri Jakarta. 
    Pastikan informasi akun Anda tetap aman dan tidak dibagikan kepada pihak lain.</p>
 </div>

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
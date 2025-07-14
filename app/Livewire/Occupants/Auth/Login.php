<?php

namespace App\Livewire\Occupants\Auth;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Login extends Component
{
    public string $contractCode = '';
    public string $phoneNumberSuffix = '';
    public bool $remember = false;

    public function login()
    {
        try {
            $this->validate([
                'contractCode' => 'required|string|size:8',
                'phoneNumberSuffix' => 'required|string|digits:5',
            ]);

            $this->ensureIsNotRateLimited();

            $contract = Contract::where('contract_code', strtoupper($this->contractCode))
                ->whereNot('status', ContractStatus::EXPIRED)
                ->first();

            $matchingOccupant = null;
            if ($contract) {
                $matchingOccupant = $contract->occupants()
                    ->whereRaw('RIGHT(whatsapp_number, 5) = ?', [$this->phoneNumberSuffix])
                    ->first();
            }

            if (!$matchingOccupant) {
                RateLimiter::hit($this->throttleKey());
                LivewireAlert::error()
                    ->title('Kode kontrak tidak ditemukan')
                    ->text('Kombinasi ID Pemesanan dan Nomor HP tidak valid atau kontrak tidak aktif.')
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }

            Auth::guard('occupant')->login($matchingOccupant, $this->remember);

            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            $this->redirect(route('occupant.dashboard'), navigate: true);
        } catch (ValidationException $e) {
            LivewireAlert::error()
                    ->title('Input Tidak Valid!')
                    ->text('Mohon periksa kembali input Anda.')
                    ->toast()
                    ->position('top-end')
                    ->show();
            throw $e;
        } catch (\Exception $e) {
            Log::error('Occupant login error: ' . $e->getMessage(), ['exception' => $e]);
            LivewireAlert::error()
                    ->title('Terjadi Kesalahan!')
                    ->text('Sistem sedang mengalami masalah. Mohon coba beberapa saat lagi.')
                    ->toast()
                    ->position('top-end')
                    ->show();
        }
    }

    protected function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->alert('warning', 'Terlalu Banyak Percobaan!', [
            'text' => 'Mohon coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            'toast' => true,
            'position' => 'top-end',
        ]);

        throw ValidationException::withMessages([]);
    }

    protected function throttleKey()
    {
        return Str::transliterate(Str::lower($this->contractCode) . '|' . request()->ip());
    }

    public function render()
    {
        return view('livewire.occupants.auth.index');
    }
}

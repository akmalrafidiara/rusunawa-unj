<?php

namespace App\Livewire\Frontend;

use App\Enums\PricingBasis;
use App\Models\OccupantType;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination; // Tambahkan ini untuk paginasi

class UnitAvaibilityCheckForm extends Component
{
    use WithPagination; // Aktifkan fitur paginasi

    public string $mode = 'redirect';

    // Properti untuk filter form
    public $occupantType = '';
    public $pricingBasis = '';
    public $startDate = '';
    public $endDate = '';

    // Properti untuk data dinamis di form
    public $totalDays;
    public $pricingBasisOptions = [];
    public $occupantTypeOptions = [];

    // Sinkronkan properti dengan query string di URL
    public $queryString = [
        'occupantType' => ['except' => ''],
        'pricingBasis' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount()
    {
        // Mengisi opsi pricing basis (Harian/Bulanan)
        $this->pricingBasisOptions = PricingBasis::options();

        // PERUBAHAN: Ambil data dari tabel occupant_types yang baru
        $this->occupantTypeOptions = OccupantType::all(['id', 'name'])->toArray();
    }

    public function render()
    {
        return view('livewire.frontend.unit-avaibility-check-form');
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate'])) {
            $this->calculateTotalDays();
        }
    }

    public function checkAvailability()
    {
        $validatedData = $this->validate($this->rules());

        if ($this->mode === 'redirect') {
            // Jika di homepage, redirect ke halaman sewa kamar dengan membawa filter
            return redirect()->route('tenancy.index', $validatedData);
        } else {
            // Jika di halaman sewa kamar, kirim event ke komponen induk
            $this->dispatch('filtersApplied', filters: $validatedData);
        }
    }

    public function rules()
    {
        $rules = [
            'occupantType' => ['required', 'exists:occupant_types,id'],
            'pricingBasis' => ['required', Rule::in(array_column(PricingBasis::options(), 'value'))],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
        ];

        if ($this->pricingBasis !== 'per_month') {
            $rules['startDate'] = ['required', 'date'];
            $rules['endDate'] = ['required', 'date', 'after_or_equal:startDate'];
        }
        return $rules;
    }

    private function calculateTotalDays()
    {
        if ($this->startDate && $this->endDate) {
            $start = \Carbon\Carbon::parse($this->startDate);
            $end = \Carbon\Carbon::parse($this->endDate);
            $this->totalDays = $start->diffInDays($end) + 1; // +1 untuk inklusif
        } else {
            $this->totalDays = 0;
        }
    }
}

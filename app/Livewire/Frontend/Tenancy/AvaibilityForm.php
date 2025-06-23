<?php

namespace App\Livewire\Frontend\Tenancy;

use App\Enums\PricingBasis;
use App\Models\OccupantType;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination; // Tambahkan ini untuk paginasi

class AvaibilityForm extends Component
{
    use WithPagination; // Aktifkan fitur paginasi

    public string $mode = 'redirect';

    // Properti untuk filter form
    public $occupantType = '';
    public $pricingBasis = '';
    public $startDate = '';
    public $endDate = '';

    // ED (Encrypted Data)
    public $ed = '';
    
    // Properti untuk data dinamis di form
    public $totalDays;
    public $pricingBasisOptions = [];
    public $occupantTypeOptions = [];

    public $queryString = [
        'ed' => ['except' => ''],
    ];

    public function mount()
    {
        try {
            $data = $this->ed ? decrypt($this->ed) : [];
        } catch (\Exception $e) {
            $data = [];
        }

        $this->occupantType = $data['occupantType'] ?? null;
        $this->pricingBasis = $data['pricingBasis'] ?? null;
        $this->startDate = $data['startDate'] ?? null;
        $this->endDate = $data['endDate'] ?? null;

        $this->pricingBasisOptions = PricingBasis::options();

        $this->occupantTypeOptions = OccupantType::all(['id', 'name'])->toArray();
    }

    public function render()
    {
        return view('livewire.frontend.tenancy.avaibility-form');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate'])) {
            $this->calculateTotalDays();
        }

        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    public function checkAvailability()
    {
        $validatedData = $this->validate($this->rules());

        $this->ed = encrypt($validatedData);
        
        if ($this->mode === 'redirect') {
            $this->redirect(route('tenancy.index', ['ed' => $this->ed]), navigate: true);
        }
        
        $this->dispatch('filtersApplied', filters: $validatedData);
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
            $this->totalDays = $start->diffInDays($end);
        } else {
            $this->totalDays = 0;
        }
    }
}
